<?php
require_once __DIR__ . '/../models/EtablissementFinancier.php';

class EtablissementFinancierController {

    public static function getAll() {
        $etablissements = EtablissementFinancier::getAll();
        Flight::json($etablissements);
    }

    public static function getById($id) {
        $etablissement = EtablissementFinancier::getById($id);
        if ($etablissement) {
            Flight::json($etablissement);
        } else {
            Flight::halt(404, 'Etablissement non trouvé');
        }
    }

    public static function create() {
        $data = Flight::request()->data;
        $id = EtablissementFinancier::create($data);
        Flight::json(['message' => 'Etablissement créé', 'id' => $id]);
    }

public static function update($id) {
    // Récupérer les données PUT
    parse_str(file_get_contents("php://input"), $put_vars);

    // $put_vars est un tableau associatif
    // Exemple d’accès : $put_vars['nom']

    $db = getDB();

    $stmt = $db->prepare("UPDATE etablissementFinancier SET nom = ?, adresse = ?, telephone = ?, email = ?, curr_montant = ? WHERE id = ?");
    
    $stmt->execute([
        $put_vars['nom'] ?? null,
        $put_vars['adresse'] ?? null,
        $put_vars['telephone'] ?? null,
        $put_vars['email'] ?? null,
        $put_vars['curr_montant'] ?? 0,
        $id
    ]);
    
    Flight::json(['message' => 'Mise à jour réussie']);
}

    public static function delete($id) {
        EtablissementFinancier::delete($id);
        Flight::json(['message' => 'Etablissement supprimé']);
    }
}
