<?php
require_once __DIR__ . '/../models/TypeCategorie.php';

class TypeCategorieController {
    public static function getAll() {
        try {
            $categories = TypeCategorie::getAll();
            Flight::json($categories);
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }
}