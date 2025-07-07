<?php
require_once __DIR__ . '/../models/Paiement.php';

class PaiementController {

    public static function getAll() {
        Flight::json(Paiement::getAll());
    }

    public static function getById($id) {
        Flight::json(Paiement::getById($id));
    }

    public static function create() {
        $data = Flight::request()->data;
        $id = Paiement::create($data);
        Flight::json(['success' => true, 'id' => $id]);
    }

    public static function update($id) {
        $data = Flight::request()->data;
        Paiement::update($id, $data);
        Flight::json(['success' => true]);
    }

    public static function delete($id) {
        Paiement::delete($id);
        Flight::json(['success' => true]);
    }
}
