<?php
require_once __DIR__ . '/../models/SimulationPret.php';
require_once __DIR__ . '/../db.php';


class SimulationPretController {
    public static function getAll() {
        $simulations = SimulationPret::getAll();
        Flight::json($simulations);
    }

    public static function getById($id) {
        $simulation = SimulationPret::getById($id);
        if ($simulation) {
            Flight::json($simulation);
        } else {
            Flight::json(['error' => 'Simulation non trouvée'], 404);
        }
    }

public static function create() {
    try {
        $data = Flight::request()->data;
        $db = getDB();
        $typePret = $db->prepare("SELECT taux_assurance, frais_dossier_fixe FROM type_pret WHERE id = ?");
        $typePret->execute([$data['id_type_pret']]);
        $typeInfo = $typePret->fetch(PDO::FETCH_ASSOC);

        
        $capital = $data['montant_demande'];
        $duree = $data['duree_demandee'];
        $taux_assurance = $typeInfo['taux_assurance'];
        $frais_dossier = $typeInfo['frais_dossier_fixe'];

        $mensualite_capital = $capital / $duree;
        $mensualite_assurance = ($capital * ($taux_assurance / 100)) / 12;
        $mensualite_totale = $mensualite_capital + $mensualite_assurance;

        $montant_total_assurance = $mensualite_assurance * $duree;
        $montant_total_pret = $mensualite_totale * $duree;

        // SimulationPret::create((object)[
        //     'numero_simulation' => uniqid('SIM-'),
        //     'id_client' => $data['id_client'],
        //     'id_type_pret' => $data['id_type_pret'],
        //     'montant_demande' => $capital,
        //     'duree_demandee' => $duree,
        //     'taux_applique' => null,
        //     'taux_assurance' => $taux_assurance,
        //     'mensualite_capital' => $mensualite_capital,
        //     'mensualite_assurance' => $mensualite_assurance,
        //     'mensualite_totale' => $mensualite_totale,
        //     'montant_total_assurance' => $montant_total_assurance,
        //     'montant_total_pret' => $montant_total_pret,
        //     'frais_dossier' => $frais_dossier,
        //     'date_expiration' => $data['date_expiration'] ?? null,
        //     'statut' => 'active',
        //     'notes' => null
        // ]);

        Flight::json(['message' => 'Simulation ajoutée']);
    } catch (Exception $e) {
        Flight::halt(500, json_encode(['error' => $e->getMessage()]));
    }
}



    public static function update($id) {
        $data = Flight::request()->data;
        SimulationPret::update($id, $data);
        Flight::json(['message' => 'Simulation mise à jour']);
    }

    public static function delete($id) {
        SimulationPret::delete($id);
        Flight::json(['message' => 'Simulation supprimée']);
    }
}
