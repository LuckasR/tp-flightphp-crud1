<?php
require_once __DIR__ . '/../db.php';

class MouvementEtablissement {
    
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT me.*, a.nom AS admin_nom, tm.nom AS type_nom, c.nom AS client_nom 
                           FROM mouvement_etablissement me
                           LEFT JOIN admin a ON me.id_admin = a.id
                           LEFT JOIN type_mouvement tm ON me.id_type = tm.id
                           LEFT JOIN client c ON me.id_client = c.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT me.*, a.nom AS admin_nom, tm.nom AS type_nom, c.nom AS client_nom 
                             FROM mouvement_etablissement me
                             LEFT JOIN admin a ON me.id_admin = a.id
                             LEFT JOIN type_mouvement tm ON me.id_type = tm.id
                             LEFT JOIN client c ON me.id_client = c.id 
                             WHERE me.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO mouvement_etablissement (id_admin, id_type, id_client, montant, description, reference_externe, date_mouvement) 
                             VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->id_admin,
            $data->id_type,
            $data->id_client,
            $data->montant,
            $data->description ?? null,
            $data->reference_externe ?? null,
            $data->date_mouvement ?? date('Y-m-d H:i:s')
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE mouvement_etablissement 
                             SET id_admin = ?, id_type = ?, id_client = ?, montant = ?, description = ?, reference_externe = ?, date_mouvement = ? 
                             WHERE id = ?");
        $stmt->execute([
            $data->id_admin,
            $data->id_type,
            $data->id_client,
            $data->montant,
            $data->description ?? null,
            $data->reference_externe ?? null,
            $data->date_mouvement ?? date('Y-m-d H:i:s'),
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM mouvement_etablissement WHERE id = ?");
        $stmt->execute([$id]);
    }
}