<?php
require_once __DIR__ . '/../controllers/TypeMouvementController.php';

Flight::route('GET /mouvements', ['TypeMouvementController', 'getAll']);
Flight::route('GET /mouvements/@id', ['TypeMouvementController', 'getById']);
Flight::route('POST /mouvements', ['TypeMouvementController', 'create']);
Flight::route('PUT /mouvements/@id', ['TypeMouvementController', 'update']);
Flight::route('DELETE /mouvements/@id', ['TypeMouvementController', 'delete']);