<?php
require_once __DIR__ . '/../models/TypeClient.php';

class TypeClientController {
    public static function getAll() {
        try {
            $types = TypeClient::getAll();
            Flight::json($types);
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public static function getById($id) {
        try {
            $type = TypeClient::getById($id);
            if ($type) {
                Flight::json($type);
            } else {
                Flight::halt(404, 'Type de client non trouvé');
            }
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public static function create() {
        try {
            $data = Flight::request()->data;
            $id = TypeClient::create($data);
            Flight::json(['message' => 'Type de client créé', 'id' => $id]);
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public static function update($id) {
        try {
            parse_str(file_get_contents("php://input"), $put_vars);
            $data = (object) $put_vars;
            TypeClient::update($id, $data);
            Flight::json(['message' => 'Type de client mis à jour']);
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public static function delete($id) {
        try {
            TypeClient::delete($id);
            Flight::json(['message' => 'Type de client supprimé']);
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }
}