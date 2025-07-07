<?php
require_once __DIR__ . '/../controllers/AdminController.php';

Flight::route('GET /admins', ['AdminController', 'getAll']);
Flight::route('GET /admins/@id', ['AdminController', 'getById']);
Flight::route('POST /admins', ['AdminController', 'create']);
Flight::route('PUT /admins/@id', ['AdminController', 'update']);
Flight::route('DELETE /admins/@id', ['AdminController', 'delete']);
