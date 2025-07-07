<?php
require_once __DIR__ . '/../models/TypePret.php';

class TypePretController {
    
    public static function getAll() {
        Flight::json(TypePret::getAll());
    }

    public static function getById($id) {
        Flight::json(TypePret::getById($id));
    }

    public static function create() {
        $data = Flight::request()->data;
        $id = TypePret::create($data);
        Flight::json(['success' => true, 'id' => $id]);
    }

    public static function update($id) {
        $data = Flight::request()->data;
        TypePret::update($id, $data);
        Flight::json(['success' => true]);
    }

    public static function delete($id) {
        TypePret::delete($id);
        Flight::json(['success' => true]);
    }
}
