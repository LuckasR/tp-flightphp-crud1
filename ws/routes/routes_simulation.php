<?php
require_once __DIR__ . '/../controllers/SimulationPretController.php';

Flight::route('GET /simulations', ['SimulationPretController', 'getAll']);
Flight::route('GET /simulations/@id', ['SimulationPretController', 'getById']);
Flight::route('POST /simulations', ['SimulationPretController', 'create']);
Flight::route('PUT /simulations/@id', ['SimulationPretController', 'update']);
Flight::route('DELETE /simulations/@id', ['SimulationPretController', 'delete']);
