<?php
require_once __DIR__ . '/../controllers/TransactionCompteController.php';

Flight::route('GET /transactions_compte', ['TransactionCompteController', 'getAll']);
Flight::route('GET /transactions_compte/@id', ['TransactionCompteController', 'getById']);
Flight::route('POST /transactions_compte', ['TransactionCompteController', 'create']);
Flight::route('PUT /transactions_compte/@id', ['TransactionCompteController', 'update']);
Flight::route('DELETE /transactions_compte/@id', ['TransactionCompteController', 'delete']);