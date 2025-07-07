<?php
require_once __DIR__ . '/../models/CompteBancaire.php';

class CompteBancaireController {
    public static function getAll() {
        try {
            $comptes = CompteBancaire::getAll();
            Flight::json($comptes);
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public static function getById($id) {
        try {
            $compte = CompteBancaire::getById($id);
            if ($compte) {
                Flight::json($compte);
            } else {
                Flight::halt(404, 'Compte bancaire non trouvé');
            }
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public static function create() {
        try {
            $data = Flight::request()->data;
            $id = CompteBancaire::create($data);
            Flight::json(['message' => 'Compte bancaire créé', 'id' => $id]);
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public static function update($id) {
        try {
            parse_str(file_get_contents("php://input"), $put_vars);
            $data = (object) $put_vars;
            CompteBancaire::update($id, $data);
            Flight::json(['message' => 'Compte bancaire mis à jour']);
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public static function delete($id) {
        try {
            CompteBancaire::delete($id);
            Flight::json(['message' => 'Compte bancaire supprimé']);
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }
}