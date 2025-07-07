<?php
require_once __DIR__ . '/../models/Client.php';

class ClientController {
    public static function getAll() {
        try {
            $clients = Client::getAll();
            Flight::json($clients);
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public static function getById($id) {
        try {
            $client = Client::getById($id);
            if ($client) {
                Flight::json($client);
            } else {
                Flight::halt(404, 'Client non trouvé');
            }
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public static function create() {
        try {
            $data = Flight::request()->data;
            $id = Client::create($data);
            Flight::json(['message' => 'Client créé', 'id' => $id]);
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public static function update($id) {
        try {
            parse_str(file_get_contents("php://input"), $put_vars);
            $data = (object) $put_vars;
            Client::update($id, $data);
            Flight::json(['message' => 'Client mis à jour']);
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public static function delete($id) {
        try {
            Client::delete($id);
            Flight::json(['message' => 'Client supprimé']);
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' =>Ue->getMessage()]));
        }
    }
}