<?php
require_once __DIR__ . '/../models/MontantDisponible.php';

class MontantDisponibleController {
    public static function getMontants() {
        try {
            $moisDebut = Flight::request()->query->mois_debut;
            $anneeDebut = Flight::request()->query->annee_debut;
            $moisFin = Flight::request()->query->mois_fin;
            $anneeFin = Flight::request()->query->annee_fin;

            $montants = MontantDisponible::getMontants($moisDebut, $anneeDebut, $moisFin, $anneeFin);
            Flight::json($montants);
        } catch (Exception $e) {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }
}