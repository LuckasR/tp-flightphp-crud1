<?php
require_once __DIR__ . '/../models/TransactionCompte.php';

class TransactionCompteController {
    public static function getAll() {
        try {
            $transactions = TransactionCompte::getAll();
            Flight::json($transactions);
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public static function getById($id) {
        try {
            $transaction = TransactionCompte::getById($id);
            if ($transaction) {
                Flight::json($transaction);
            } else {
                Flight::halt(404, 'Transaction non trouvée');
            }
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public static function create() {
        try {
            $data = Flight::request()->data;
            $id = TransactionCompte::create($data);
            Flight::json(['message' => 'Transaction créée', 'id' => $id]);
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public static function update($id) {
        try {
            parse_str(file_get_contents("php://input"), $put_vars);
            $data = (object) $put_vars;
            TransactionCompte::update($id, $data);
            Flight::json(['message' => 'Transaction mise à jour']);
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }

    public static function delete($id) {
        try {
            TransactionCompte::delete($id);
            Flight::json(['message' => 'Transaction supprimée']);
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }
}