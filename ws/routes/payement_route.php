<?php
require_once __DIR__ . '/../controllers/PaiementController.php';

Flight::route('GET /paiements', ['PaiementController', 'getAll']);
Flight::route('GET /paiements/@id', ['PaiementController', 'getById']);
Flight::route('POST /paiements', ['PaiementController', 'create']);
Flight::route('PUT /paiements/@id', ['PaiementController', 'update']);
Flight::route('DELETE /paiements/@id', ['PaiementController', 'delete']);
