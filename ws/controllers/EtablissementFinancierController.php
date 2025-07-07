<?php
require_once __DIR__ . '/../models/EtablissementFinancier.php';

class EtablissementFinancierController {

    public static function getAll() {
        $etablissements = EtablissementFinancier::getAll();
        Flight::json($etablissements);
    }

    public static function getById($id) {
        $etablissement = EtablissementFinancier::getById($id);
        if ($etablissement) {
            Flight::json($etablissement);
        } else {
            Flight::halt(404, 'Etablissement non trouvé');
        }
    }

    public static function create() {
        $data = Flight::request()->data;
        $id = EtablissementFinancier::create($data);
        Flight::json(['message' => 'Etablissement créé', 'id' => $id]);
    }

    public static function update($id) {
        parse_str(file_get_contents("php://input"), $put_vars);
        $db = getDB();
        $stmt = $db->prepare("UPDATE etablissementFinancier SET nom = ?, adresse = ?, telephone = ?, email = ?, curr_montant = ? WHERE id = ?");
        $stmt->execute([
            $put_vars['nom'] ?? null,
            $put_vars['adresse'] ?? null,
            $put_vars['telephone'] ?? null,
            $put_vars['email'] ?? null,
            $put_vars['curr_montant'] ?? 0,
            $id
        ]);
        Flight::json(['message' => 'Mise à jour réussie']);
    }

    public static function delete($id) {
        EtablissementFinancier::delete($id);
        Flight::json(['message' => 'Etablissement supprimé']);
    }


    public static function updateCurrMontant($id) {
        try {
            if (EtablissementFinancier::updateCurrMontant($id)) {
                Flight::json(['message' => 'curr_montant mis à jour avec succès']);
            } else {
                Flight::halt(500, 'Erreur lors de la mise à jour de curr_montant');
            }
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur serveur: ' . $e->getMessage());
        }
    }


    
   public static function getMonthlyInterest() {
        $data = Flight::request()->data;
        $annee_debut = $data->annee_debut ?? date('Y');
        $mois_debut = $data->mois_debut ?? 1;
        $annee_fin = $data->annee_fin ?? date('Y');
        $mois_fin = $data->mois_fin ?? 12;

        $interestData = EtablissementFinancier::getMonthlyInterest($annee_debut, $mois_debut, $annee_fin, $mois_fin);
        Flight::json($interestData);
    }

}
