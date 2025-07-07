<?php
require_once __DIR__ . '/../models/Admin.php';

class AdminController {
    public static function getAll() {
        Flight::json(Admin::getAll());
    }

    public static function getById($id) {
        Flight::json(Admin::getById($id));
    }

    public static function create() {
        $data = Flight::request()->data;
        $id = Admin::create($data);
        Flight::json(['message' => 'Admin ajouté', 'id' => $id]);
    }

    public static function update($id) {
        $data = Flight::request()->data;
        Admin::update($id, $data);
        Flight::json(['message' => 'Admin mis à jour']);
    }

    public static function delete($id) {
        Admin::delete($id);
        Flight::json(['message' => 'Admin supprimé']);
    }
}
