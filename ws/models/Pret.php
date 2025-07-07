<?php
require_once __DIR__ . '/../db.php';

class Pret {

    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM pret");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM pret WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("
            INSERT INTO pret (
                numero_pret, id_client, id_type_pret, id_admin_createur,
                montant_demande, duree_demandee, motif_demande
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data->numero_pret,
            $data->id_client,
            $data->id_type_pret,
            $data->id_admin_createur,
            $data->montant_demande,
            $data->duree_demandee,
            $data->motif_demande ?? null
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("
            UPDATE pret SET 
                numero_pret = ?, id_client = ?, id_type_pret = ?, 
                id_admin_createur = ?, id_admin_validateur = ?, 
                montant_demande = ?, duree_demandee = ?, motif_demande = ?, 
                montant_accorde = ?, duree_accordee = ?, taux_applique = ?, 
                frais_dossier = ?, frais_assurance = ?, montant_total = ?, 
                mensualite = ?, id_statut = ?, date_etude = ?, 
                date_decision = ?, date_signature = ?, date_deblocage = ?, 
                date_premiere_echeance = ?, date_derniere_echeance = ?, 
                montant_rembourse = ?, montant_restant = ?, raison_rejet = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $data->numero_pret,
            $data->id_client,
            $data->id_type_pret,
            $data->id_admin_createur,
            $data->id_admin_validateur,
            $data->montant_demande,
            $data->duree_demandee,
            $data->motif_demande,
            $data->montant_accorde,
            $data->duree_accordee,
            $data->taux_applique,
            $data->frais_dossier,
            $data->frais_assurance,
            $data->montant_total,
            $data->mensualite,
            $data->id_statut,
            $data->date_etude,
            $data->date_decision,
            $data->date_signature,
            $data->date_deblocage,
            $data->date_premiere_echeance,
            $data->date_derniere_echeance,
            $data->montant_rembourse,
            $data->montant_restant,
            $data->raison_rejet,
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM pret WHERE id = ?");
        $stmt->execute([$id]);
    }
}
