<?php
require_once __DIR__ . '/../models/Role.php';

class RoleController {

    public static function getAll() {
        $roles = Role::getAll();
        Flight::json($roles);
    }

    public static function getById($id) {
        $role = Role::getById($id);
        if ($role) {
            Flight::json($role);
        } else {
            Flight::halt(404, "Rôle non trouvé");
        }
    }

    public static function create() {
        $data = Flight::request()->data;
        $id = Role::create($data);
        Flight::json(['id' => $id]);
    }

    public static function update($id) {
        $data = Flight::request()->data;
        Role::update($id, $data);
        Flight::json(['message' => 'Rôle mis à jour']);
    }

    public static function delete($id) {
        Role::delete($id);
        Flight::json(['message' => 'Rôle supprimé']);
    }
}
