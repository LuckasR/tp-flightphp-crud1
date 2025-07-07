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

    public static function getById($id) {
        try {
            $category = TypeCategorie::getById($id);
            if ($category) {
                Flight::json($category);
            } else {
                Flight::halt(404, 'Type de catégorie non trouvé');
            }
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }
}