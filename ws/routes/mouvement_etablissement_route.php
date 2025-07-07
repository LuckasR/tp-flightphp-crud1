<?php
require_once __DIR__ . '/../controllers/MouvementEtablissementController.php';

Flight::route('GET /mouvementsEtablissement', ['MouvementEtablissementController', 'getAll']);
Flight::route('GET /mouvementsEtablissement/@id', ['MouvementEtablissementController', 'getById']);
Flight::route('POST /mouvementsEtablissement', ['MouvementEtablissementController', 'create']);
Flight::route('PUT /mouvementsEtablissement/@id', ['MouvementEtablissementController', 'update']);
Flight::route('DELETE /mouvementsEtablissement/@id', ['MouvementEtablissementController', 'delete']);