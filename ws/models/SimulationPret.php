<?php
require_once __DIR__ . '/../db.php';

class SimulationPret {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM simulations_pret ORDER BY date_simulation DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM simulations_pret WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("
            INSERT INTO simulations_pret (
                numero_simulation, id_client, id_type_pret,
                montant_demande, duree_demandee, taux_applique, taux_assurance,
                mensualite_capital, mensualite_assurance, mensualite_totale,
                montant_total_assurance, montant_total_pret, frais_dossier,
                date_expiration, statut, notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data->numero_simulation,
            $data->id_client,
            $data->id_type_pret,
            $data->montant_demande,
            $data->duree_demandee,
            $data->taux_applique,
            $data->taux_assurance,
            $data->mensualite_capital,
            $data->mensualite_assurance,
            $data->mensualite_totale,
            $data->montant_total_assurance,
            $data->montant_total_pret,
            $data->frais_dossier ?? 0,
            $data->date_expiration ?? null,
            $data->statut ?? 'active',
            $data->notes ?? null,
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("
            UPDATE simulations_pret SET
                numero_simulation = ?, id_client = ?, id_type_pret = ?,
                montant_demande = ?, duree_demandee = ?, taux_applique = ?, taux_assurance = ?,
                mensualite_capital = ?, mensualite_assurance = ?, mensualite_totale = ?,
                montant_total_assurance = ?, montant_total_pret = ?, frais_dossier = ?,
                date_expiration = ?, statut = ?, notes = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $data->numero_simulation,
            $data->id_client,
            $data->id_type_pret,
            $data->montant_demande,
            $data->duree_demandee,
            $data->taux_applique,
            $data->taux_assurance,
            $data->mensualite_capital,
            $data->mensualite_assurance,
            $data->mensualite_totale,
            $data->montant_total_assurance,
            $data->montant_total_pret,
            $data->frais_dossier ?? 0,
            $data->date_expiration ?? null,
            $data->statut ?? 'active',
            $data->notes ?? null,
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM simulations_pret WHERE id = ?");
        $stmt->execute([$id]);
    }
}
