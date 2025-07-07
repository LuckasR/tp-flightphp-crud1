<?php
require_once __DIR__ . '/../db.php';

class Paiement {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("
            SELECT p.*, a.nom AS admin_nom
            FROM paiement p
            JOIN admin a ON p.id_admin = a.id
            ORDER BY p.date_paiement DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT p.*, a.nom AS admin_nom
            FROM paiement p
            JOIN admin a ON p.id_admin = a.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("
            INSERT INTO paiement
            (id_pret, id_admin, montant_paye, reference_paiement, commentaire, date_paiement, date_valeur)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data->id_pret,
            $data->id_admin,
            $data->montant_paye,
            $data->reference_paiement ?? null,
            $data->commentaire ?? null,
            $data->date_paiement ?? date('Y-m-d H:i:s'),
            $data->date_valeur ?? null
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("
            UPDATE paiement SET
                id_pret = ?,
                id_admin = ?,
                montant_paye = ?,
                reference_paiement = ?,
                commentaire = ?,
                date_paiement = ?,
                date_valeur = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $data->id_pret,
            $data->id_admin,
            $data->montant_paye,
            $data->reference_paiement ?? null,
            $data->commentaire ?? null,
            $data->date_paiement ?? date('Y-m-d H:i:s'),
            $data->date_valeur ?? null,
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM paiement WHERE id = ?");
        $stmt->execute([$id]);
    }
}
