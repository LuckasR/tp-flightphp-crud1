<?php
require_once __DIR__ . '/../db.php';

class Role {
    
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM role");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM role WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO role (nom, description) VALUES (?, ?)");
        $stmt->execute([
            $data->nom,
            $data->description ?? null
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE role SET nom = ?, description = ? WHERE id = ?");
        $stmt->execute([
            $data->nom,
            $data->description ?? null,
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM role WHERE id = ?");
        $stmt->execute([$id]);
    }
}
