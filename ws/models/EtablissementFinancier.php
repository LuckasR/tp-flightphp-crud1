<?php
require_once __DIR__ . '/../db.php';

class EtablissementFinancier {
    
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM etablissementFinancier");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM etablissementFinancier WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO etablissementFinancier (nom, adresse, telephone, email, curr_montant) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->nom,
            $data->adresse,
            $data->telephone,
            $data->email,
            $data->curr_montant ?? 0
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE etablissementFinancier SET nom = ?, adresse = ?, telephone = ?, email = ?, curr_montant = ? WHERE id = ?");
        $stmt->execute([
            $data->nom,
            $data->adresse,
            $data->telephone,
            $data->email,
            $data->curr_montant ?? 0,
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM etablissementFinancier WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function updateCurrMontant($id) {
        $db = getDB();
        try {
            // Calculate net total: + for deposits, - for withdrawals
            $stmt = $db->query("
                SELECT COALESCE(SUM(
                    CASE 
                        WHEN tc.type_name = 'depot' THEN m.montant
                        WHEN tc.type_name = 'retrait' THEN -m.montant
                        ELSE 0
                    END
                ), 0) AS total_montant
                FROM mouvement_etablissement m
                JOIN type_mouvement tm ON m.id_type = tm.id
                JOIN type_categorie tc ON tm.id_type = tc.id
            ");
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total_montant'];

            // Update curr_montant
            $stmt = $db->prepare("UPDATE etablissementFinancier SET curr_montant = ? WHERE id = ?");
            $stmt->execute([$total, $id]);

            return true;
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de curr_montant: " . $e->getMessage());
            return false;
        }
    }

}
