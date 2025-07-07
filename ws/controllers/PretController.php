<?php
require_once __DIR__ . '/../models/Pret.php';

class PretController {

    public static function getAll() {
        echo json_encode(Pret::getAll());
    }


    public static function getAllNotValidate() {
        echo json_encode(Pret::getAllNotValidate());
    }


    public static function validerPret($id, $data) {
        Pret::validerPret($id, $data);
    }

    public static function getById($id) {
        echo json_encode(Pret::getById($id));
    }

    public static function create() {
        $data = Flight::request()->data;
        echo json_encode(Pret::create($data));
    }

    public static function update($id) {
        $data = Flight::request()->data;
        Pret::update($id, $data);
        echo json_encode(['message' => 'Prêt mis à jour']);
    }

    public static function delete($id) {
        Pret::delete($id);
        echo json_encode(['message' => 'Prêt supprimé']);
    }
}
