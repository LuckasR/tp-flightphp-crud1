<?php
require_once __DIR__ . '/../db.php';

class TypeClient {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM type_client");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM type_client WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO type_client (nom, description) VALUES (?, ?)");
        $stmt->execute([
            $data->nom,
            $data->description ?? null
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE type_client SET nom = ?, description = ? WHERE id = ?");
        $stmt->execute([
            $data->nom,
            $data->description ?? null,
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM type_client WHERE id = ?");
        $stmt->execute([$id]);
    }
}