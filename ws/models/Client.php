<?php
require_once __DIR__ . '/../db.php';

class Client {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT c.*, tc.nom AS type_client_nom FROM client c JOIN type_client tc ON c.id_type_client = tc.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT c.*, tc.nom AS type_client_nom FROM client c JOIN type_client tc ON c.id_type_client = tc.id WHERE c.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO client (nom, email, date_naissance, id_type_client) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data->nom ?? null,
            $data->email ?? null,
            $data->date_naissance ?? null,
            $data->id_type_client
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE client SET nom = ?, email = ?, date_naissance = ?, id_type_client = ? WHERE id = ?");
        $stmt->execute([
            $data->nom ?? null,
            $data->email ?? null,
            $data->date_naissance ?? null,
            $data->id_type_client,
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM client WHERE id = ?");
        $stmt->execute([$id]);
    }
}