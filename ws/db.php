<?php
function getDB() {
    $host = 'localhost';
    $dbname = 'financier';
    $username = 'root';
    $password = 'a';
    $port = 3308;

    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

    try {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,     // Gestion des erreurs par exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Mode fetch associatif par défaut
            PDO::ATTR_EMULATE_PREPARES => false,             // Utiliser les vrais prepared statements
        ];
        return new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        // En prod, ne pas afficher l’erreur brute mais logger et renvoyer un message générique
        // Ici, pour dev, on affiche en JSON
        http_response_code(500);
        echo json_encode(['error' => 'Erreur de connexion à la base de données']);
        // exit pour éviter la suite du script
        exit;
    }
}
