<?php
require_once __DIR__ . '/../db.php';

class Pret {

    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM pret");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function getAllNotValidate() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM pret WHERE id_statut = 1"); // 1 = En attente de validation
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
public static function validerPret($id) {
    $db = getDB();
 

    // Récupérer les données PUT
    parse_str(file_get_contents("php://input"), $put_vars);

    // Vérifier que le prêt existe et est en attente
    $stmtCheck = $db->prepare("SELECT * FROM pret WHERE id = ? AND id_statut = 1");
    $stmtCheck->execute([$id]);
    $pret = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    if (!$pret) {
        throw new Exception("Prêt introuvable ou déjà validé/rejeté.");
    }

    // Récupérer et valider les données
    $montantAccorde = isset($put_vars['montant_accorde']) ? $put_vars['montant_accorde'] : 0;
    $fraisDossier = isset($put_vars['frais_dossier']) ? $put_vars['frais_dossier'] : 0;
    $fraisAssurance = isset($put_vars['frais_assurance']) ? $put_vars['frais_assurance'] : 0;
    $dureeAccordee = isset($put_vars['duree_accordee']) ? $put_vars['duree_accordee'] : 0;
    $tauxApplique = isset($put_vars['taux_applique']) ? $put_vars['taux_applique'] : 0;
    $idAdminValidateur = isset($put_vars['id_admin_validateur']) ? $put_vars['id_admin_validateur'] : null;

    // Calculs
    $montantTotal = $montantAccorde + $fraisDossier + $fraisAssurance;
    $mensualite = $dureeAccordee > 0 ? $montantTotal / $dureeAccordee : 0;
    $montantRestant = $montantTotal;

    $datePremiereEcheance = date('Y-m-d', strtotime('+1 month'));
    $dateDerniereEcheance = date('Y-m-d', strtotime("+$dureeAccordee month"));

    // Mise à jour
    $stmt = $db->prepare("
        UPDATE pret SET
            id_admin_validateur = :id_admin_validateur,
            taux_applique = :taux_applique,
            montant_accorde = :montant_accorde,
            duree_accordee = :duree_accordee,
            frais_dossier = :frais_dossier,
            frais_assurance = :frais_assurance,
            montant_total = :montant_total,
            mensualite = :mensualite,
            montant_restant = :montant_restant, 
            date_decision = NOW(),
            date_premiere_echeance = :date_premiere_echeance,
            date_derniere_echeance = :date_derniere_echeance
        WHERE id = :id
    ");

    $stmt->execute([
        ':id_admin_validateur' => $idAdminValidateur,
        ':taux_applique' => $tauxApplique,
        ':montant_accorde' => $montantAccorde,
        ':duree_accordee' => $dureeAccordee,
        ':frais_dossier' => $fraisDossier,
        ':frais_assurance' => $fraisAssurance,
        ':montant_total' => $montantTotal,
        ':mensualite' => $mensualite,
        ':montant_restant' => $montantRestant,
        ':date_premiere_echeance' => $datePremiereEcheance,
        ':date_derniere_echeance' => $dateDerniereEcheance,
        ':id' => $id
    ]);

    return true;
}



    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM pret WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
    $db = getDB();

    // Récupérer le nom du client (sans espaces)
    $stmtClient = $db->prepare("SELECT nom FROM client WHERE id = ?");
    $stmtClient->execute([$data->id_client]);
    $client = $stmtClient->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        throw new Exception("Client introuvable");
    }

    $nomClient = preg_replace('/\s+/', '', $client['nom']); // retirer espaces
    $datePart = date('Ymd'); // ex: 20250707

    // Obtenir la prochaine valeur séquentielle via insertion dans table dédiée
    $db->query("INSERT INTO numero_pret_seq () VALUES ()");
    $seq = $db->lastInsertId();

    // Construire le numéro de prêt selon format demandé
    $numeroPret = $nomClient . "_" . $datePart . "_" . str_pad($seq, 4, '0', STR_PAD_LEFT);

    // Insérer le prêt avec ce numéro généré
    $stmt = $db->prepare("
        INSERT INTO pret (
            numero_pret, id_client, id_type_pret, id_admin_createur,
            montant_demande, duree_demandee, motif_demande
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $numeroPret,
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
