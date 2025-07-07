<?php
require_once __DIR__ . '/../db.php';

class Admin {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT a.*, r.nom AS role FROM admin a JOIN role r ON a.role_id = r.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT a.*, r.nom AS role FROM admin a JOIN role r ON a.role_id = r.id WHERE a.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO admin (nom, email, mot_de_passe, role_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data->nom,
            $data->email,
            password_hash($data->mot_de_passe, PASSWORD_BCRYPT),
            $data->role_id
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE admin SET nom = ?, email = ?, role_id = ? WHERE id = ?");
        $stmt->execute([
            $data->nom,
            $data->email,
            $data->role_id,
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM admin WHERE id = ?");
        $stmt->execute([$id]);
    }
}
