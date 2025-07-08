<?php
require_once __DIR__ . '/../models/SimulationPret.php';

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
        $data = Flight::request()->data;
        $id = SimulationPret::create($data);
        Flight::json(['message' => 'Simulation créée', 'id' => $id]);
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
