<?php
require_once __DIR__ . '/../controllers/TypeCategorieController.php';

Flight::route('GET /categories', ['TypeCategorieController', 'getAll']);
Flight::route('GET /categories/@id', ['TypeCategorieController', 'getById']);