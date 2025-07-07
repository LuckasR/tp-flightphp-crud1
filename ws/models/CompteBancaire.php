<?php
require_once __DIR__ . '/../db.php';

class CompteBancaire {
    // Génère un numero_compte unique au format CB-YYYYMMDD-XXXXXX
    private static function generateNumeroCompte($db) {
        $date = date('Ymd'); // Format YYYYMMDD
        $prefix = "CB-{$date}-";
        
        // Compter les comptes créés aujourd'hui pour générer un suffixe unique
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM compte_bancaire WHERE numero_compte LIKE ?");
        $stmt->execute(["{$prefix}%"]);
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'] + 1;
        
        // Format du suffixe avec 6 chiffres (pad avec des zéros)
        $suffix = str_pad($count, 6, '0', STR_PAD_LEFT);
        return $prefix . $suffix;
    }

    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT cb.*, c.nom AS client_nom FROM compte_bancaire cb JOIN client c ON cb.id_client = c.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT cb.*, c.nom AS client_nom FROM compte_bancaire cb JOIN client c ON cb.id_client = c.id WHERE cb.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $numero_compte = self::generateNumeroCompte($db);
        
        $stmt = $db->prepare("INSERT INTO compte_bancaire (numero_compte, id_client, solde_compte, last_change) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $numero_compte,
            $data->id_client,
            $data->solde_compte ?? 0.00,
            date('Y-m-d H:i:s') // last_change défini à la date/heure actuelle
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE compte_bancaire SET id_client = ?, solde_compte = ?, last_change = ? WHERE id = ?");
        $stmt->execute([
            $data->id_client,
            $data->solde_compte,
            date('Y-m-d H:i:s'),
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM compte_bancaire WHERE id = ?");
        $stmt->execute([$id]);
    }
}