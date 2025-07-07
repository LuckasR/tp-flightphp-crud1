<?php
require 'vendor/autoload.php';
require 'db.php';

$routesPath = __DIR__ . '/routes';

foreach (glob($routesPath . '/*.php') as $routeFile) {
    require_once $routeFile;
}

// Démarrer Flight
Flight::start();