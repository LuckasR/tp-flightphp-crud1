<?php
require_once __DIR__ . '/../db.php';

class MontantDisponible {
    public static function getMontants($moisDebut, $anneeDebut, $moisFin, $anneeFin) {
        $db = getDB();
        $query = "
            SELECT 
                YEAR(p.date_paiement) AS annee,
                MONTH(p.date_paiement) AS mois,
                COALESCE(SUM(p.montant_paye), 0) AS remboursements,
                COALESCE((
                    SELECT curr_montant 
                    FROM etablissementFinancier 
                    WHERE id = 1
                ) - SUM(pr.montant_accorde), 0) AS montant_non_emprunte,
                COALESCE((
                    SELECT curr_montant 
                    FROM etablissementFinancier 
                    WHERE id = 1
                ) - SUM(pr.montant_accorde) + SUM(p.montant_paye), 0) AS montant_total
            FROM paiement p
            JOIN pret pr ON p.id_pret = pr.id
            WHERE p.date_paiement BETWEEN :date_debut AND :date_fin
            GROUP BY YEAR(p.date_paiement), MONTH(p.date_paiement)
            ORDER BY YEAR(p.date_paiement), MONTH(p.date_paiement)
        ";

        $stmt = $db->prepare($query);
        $stmt->execute([
            ':date_debut' => "$anneeDebut-$moisDebut-01",
            ':date_fin' => "$anneeFin-$moisFin-31"
        ]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function($row) {
            return [
                'annee' => $row['annee'],
                'mois' => sprintf('%02d', $row['mois']),
                'montant_non_emprunte' => number_format($row['montant_non_emprunte'], 2),
                'remboursements' => number_format($row['remboursements'], 2),
                'montant_total' => number_format($row['montant_total'], 2)
            ];
        }, $results);
    }
}