<?php
require_once __DIR__ . '/../db.php';

class TypePret {

    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM type_pret");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM type_pret WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO type_pret (
            nom, description, revenu_minimum, age_minimum, age_maximum,
            montant_min, montant_max, duree_min, duree_max, 
            taux_interet, taux_interet_retard, frais_dossier_fixe, 
            documents_requis, actif
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $data->nom,
            $data->description,
            $data->revenu_minimum,
            $data->age_minimum,
            $data->age_maximum,
            $data->montant_min,
            $data->montant_max,
            $data->duree_min,
            $data->duree_max,
            $data->taux_interet,
            $data->taux_interet_retard,
            $data->frais_dossier_fixe,
            $data->documents_requis,
            $data->actif ?? true
        ]);

        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE type_pret SET 
            nom = ?, description = ?, revenu_minimum = ?, age_minimum = ?, age_maximum = ?, 
            montant_min = ?, montant_max = ?, duree_min = ?, duree_max = ?, 
            taux_interet = ?, taux_interet_retard = ?, frais_dossier_fixe = ?, 
            documents_requis = ?, actif = ?
            WHERE id = ?");
        
        $stmt->execute([
            $data->nom,
            $data->description,
            $data->revenu_minimum,
            $data->age_minimum,
            $data->age_maximum,
            $data->montant_min,
            $data->montant_max,
            $data->duree_min,
            $data->duree_max,
            $data->taux_interet,
            $data->taux_interet_retard,
            $data->frais_dossier_fixe,
            $data->documents_requis,
            $data->actif,
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM type_pret WHERE id = ?");
        $stmt->execute([$id]);
    }
}
