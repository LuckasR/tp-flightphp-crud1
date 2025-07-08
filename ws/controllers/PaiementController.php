<?php
require_once __DIR__ . '/../models/Paiement.php';

class PaiementController {

    public static function getAll() {
        Flight::json(Paiement::getAll());
    }

    public static function getById($id) {
        Flight::json(Paiement::getById($id));
    }

    public static function create() {
        $data = Flight::request()->data;
        if (!isset($data->id_pret) || !isset($data->id_admin) || !isset($data->montant_paye)) {
            Flight::json(['success' => false, 'error' => 'Missing required fields'], 400);
            return;
        }

        $db = getDB();
        $db->beginTransaction();
        try {
            // Insert payment
            $id = Paiement::create($data);
            
            // Get current loan details
            $stmt = $db->prepare("SELECT montant_restant, montant_total, montant_accorde, taux_applique, frais_dossier, montant_rembourse FROM pret WHERE id = ?");
            $stmt->execute([$data->id_pret]);
            $pret = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$pret) {
                throw new Exception("Loan not found for id_pret: " . $data->id_pret);
            }

            // Calculate montant_restant if NULL
            $montant_restant = $pret['montant_restant'] ?? ($pret['montant_total'] ?? ($pret['montant_accorde'] + ($pret['montant_accorde'] * ($pret['taux_applique'] ?? 0) / 100) + ($pret['frais_dossier'] ?? 0)));
            
            // Update prets table
            $stmt = $db->prepare("
                UPDATE pret SET
                    montant_rembourse = ? + ?,
                    montant_restant = ? - ?
                WHERE id = ?
            ");
            $stmt->execute([
                $pret['montant_rembourse'] ?? 0,
                $data->montant_paye,
                $montant_restant,
                $data->montant_paye,
                $data->id_pret
            ]);
            
            $db->commit();
            Flight::json(['success' => true, 'id' => $id]);
        } catch (Exception $e) {
            $db->rollBack();
            Flight::json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public static function update($id) {
        $data = Flight::request()->data;
        Paiement::update($id, $data);
        Flight::json(['success' => true]);
    }

    public static function delete($id) {
        Paiement::delete($id);
        Flight::json(['success' => true]);
    }
}
?>