<?php
require_once __DIR__ . '/../controllers/MontantDisponibleController.php';

Flight::route('GET /montantsDisponibles', ['MontantDisponibleController', 'getMontants']);