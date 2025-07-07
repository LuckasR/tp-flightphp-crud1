<?php
require_once __DIR__ . '/../db.php';

class TypeCategorie {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT id, type_name FROM type_categorie");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, type_name FROM type_categorie WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}