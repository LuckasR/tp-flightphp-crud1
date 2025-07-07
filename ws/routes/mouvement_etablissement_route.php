<?php
require_once __DIR__ . '/../controllers/MouvementEtablissementController.php';

Flight::route('GET /mouvements', ['MouvementEtablissementController', 'getAll']);
Flight::route('GET /mouvements/@id', ['MouvementEtablissementController', 'getById']);
Flight::route('POST /mouvements', ['MouvementEtablissementController', 'create']);
Flight::route('PUT /mouvements/@id', ['MouvementEtablissementController', 'update']);
Flight::route('DELETE /mouvements/@id', ['MouvementEtablissementController', 'delete']);