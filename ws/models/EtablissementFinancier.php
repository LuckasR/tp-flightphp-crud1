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
}
