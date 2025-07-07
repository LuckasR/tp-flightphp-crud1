<?php
require_once __DIR__ . '/../controllers/TypeClientController.php';

Flight::route('GET /type_clients', ['TypeClientController', 'getAll']);
Flight::route('GET /type_clients/@id', ['TypeClientController', 'getById']);
Flight::route('POST /type_clients', ['TypeClientController', 'create']);
Flight::route('PUT /type_clients/@id', ['TypeClientController', 'update']);
Flight::route('DELETE /type_clients/@id', ['TypeClientController', 'delete']);