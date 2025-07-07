<?php
require_once __DIR__ . '/../db.php';

class TypeMouvement {
    
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT tm.*, tc.type_name FROM type_mouvement tm JOIN type_categorie tc ON tm.id_type = tc.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT tm.*, tc.type_name FROM type_mouvement tm JOIN type_categorie tc ON tm.id_type = tc.id WHERE tm.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO type_mouvement (id_type, nom) VALUES (?, ?)");
        $stmt->execute([
            $data->id_type,
            $data->nom
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE type_mouvement SET id_type = ?, nom = ? WHERE id = ?");
        $stmt->execute([
            $data->id_type,
            $data->nom,
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM type_mouvement WHERE id = ?");
        $stmt->execute([$id]);
    }
}