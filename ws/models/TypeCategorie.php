<?php
require_once __DIR__ . '/../db.php';

class TypeCategorie {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT id, type_name FROM type_categorie");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}