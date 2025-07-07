<?php
require 'vendor/autoload.php';
require 'db.php';

// Inclure toutes les routes (y compris etudiant_route.php)
require_once __DIR__ . '/routes/etudiant_routes.php';
require_once __DIR__ . '/routes/etablissement_route.php';
require_once __DIR__ . '/routes/typeMouvement_route.php';
require_once __DIR__ . '/routes/type_categorie_routes.php';
// Démarrer Flight
Flight::start();