<?php
require_once __DIR__ . '/../controllers/PretController.php';

Flight::route('GET /prets', ['PretController', 'getAll']);


Flight::route('GET /prets/non-valides', ['PretController', 'getAllNotValidate']);
Flight::route('GET /prets/valides', ['PretController', 'getAllValidate']);

// Flight::route('PUT /prets/valider/@id', ['PretController', 'validerPret']);
// Flight::route('PUT /prets/rejeter/@id', ['PretController', 'rejeterPret']);

Flight::route('GET /prets/valider/@id', function ($id) {
    include __DIR__ . '\..\..\validation_pret.html'; // ta page de validation
});


Flight::route('PUT /prets/valider/@id', function ($id) {
    // Récupérer les données envoyées depuis le formulaire JS
    $data = Flight::request()->data;

    // Appeler la méthode du modèle pour valider
    try {
        PretController::validerPret($id, $data);
        echo json_encode(['message' => 'Validation enregistrée avec succès']);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
});


Flight::route('GET /prets/rejeter/@id', function ($id) {
    include __DIR__ . '\..\..\rejeter_pret.html'; // ta page de rejet
});


Flight::route('GET /prets/@id', ['PretController', 'getById']);
Flight::route('POST /prets', ['PretController', 'create']);
Flight::route('PUT /prets/@id', ['PretController', 'update']);
Flight::route('DELETE /prets/@id', ['PretController', 'delete']);


Flight::route('GET /pret/@id/pdf', ['PretController', 'genererPDF']);
