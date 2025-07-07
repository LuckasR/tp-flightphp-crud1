<?php
require_once __DIR__ . '/../models/TypeMouvement.php';

class TypeMouvementController {

    public static function getAll() {
        $mouvements = TypeMouvement::getAll();
        Flight::json($mouvements);
    }

    public static function getById($id) {
        $mouvement = TypeMouvement::getById($id);
        if ($mouvement) {
            Flight::json($mouvement);
        } else {
            Flight::halt(404, 'Type de mouvement non trouvé');
        }
    }

    public static function create() {
        $data = Flight::request()->data;
        $id = TypeMouvement::create($data);
        Flight::json(['message' => 'Type de mouvement créé', 'id' => $id]);
    }

    public static function update($id) {
        parse_str(file_get_contents("php://input"), $put_vars);
        $data = (object) $put_vars;
        TypeMouvement::update($id, $data);
        Flight::json(['message' => 'Type de mouvement mis à jour']);
    }

    public static function delete($id) {
        TypeMouvement::delete($id);
        Flight::json(['message' => 'Type de mouvement supprimé']);
    }
}