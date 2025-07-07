<?php
require_once __DIR__ . '/../controllers/CompteBancaireController.php';

Flight::route('GET /comptes_bancaires', ['CompteBancaireController', 'getAll']);
Flight::route('GET /comptes_bancaires/@id', ['CompteBancaireController', 'getById']);
Flight::route('POST /comptes_bancaires', ['CompteBancaireController', 'create']);
Flight::route('PUT /comptes_bancaires/@id', ['CompteBancaireController', 'update']);
Flight::route('DELETE /comptes_bancaires/@id', ['CompteBancaireController', 'delete']);