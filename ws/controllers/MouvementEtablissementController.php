
<?php
require_once __DIR__ . '/../models/MouvementEtablissement.php';

class MouvementETablissementController {

    public static function getAll() {
        $mouvements = MouvementEtablissement::getAll();
        Flight::json($mouvements);
    }

    public static function getById($id) {
        $mouvement = MouvementEtablissement::getById($id);
        if ($mouvement) {
            Flight::json($mouvement);
        } else {
            Flight::halt(404, 'mouvement etablissement non trouvé');
        }
    }

    public static function create() {
        $data = Flight::request()->data;
        $id = MouvementEtablissement::create($data);
        Flight::json(['message' => 'mouvement etablissement créé', 'id' => $id]);
    }

    public static function update($id) {
        parse_str(file_get_contents("php://input"), $put_vars);
        $data = (object) $put_vars;
        MouvementEtablissement::update($id, $data);
        Flight::json(['message' => 'mouvement etablissement mis à jour']);
    }

    public static function delete($id) {
        MouvementEtablissement::delete($id);
        Flight::json(['message' => 'mouvement etablissement supprimé']);
    }
}