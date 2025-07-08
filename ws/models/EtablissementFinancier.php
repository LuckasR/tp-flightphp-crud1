<?php
require_once __DIR__ . '/../db.php';

class EtablissementFinancier {
    
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM etablissementFinancier");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM etablissementFinancier WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO etablissementFinancier (nom, adresse, telephone, email, curr_montant) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->nom,
            $data->adresse,
            $data->telephone,
            $data->email,
            $data->curr_montant ?? 0
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE etablissementFinancier SET nom = ?, adresse = ?, telephone = ?, email = ?, curr_montant = ? WHERE id = ?");
        $stmt->execute([
            $data->nom,
            $data->adresse,
            $data->telephone,
            $data->email,
            $data->curr_montant ?? 0,
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM etablissementFinancier WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function updateCurrMontant($id) {
        $db = getDB();
        try {
            // Calculate net total: + for deposits, - for withdrawals
            $stmt = $db->query("
                SELECT COALESCE(SUM(
                    CASE 
                        WHEN tc.type_name = 'depot' THEN m.montant
                        WHEN tc.type_name = 'retrait' THEN -m.montant
                        ELSE 0
                    END
                ), 0) AS total_montant
                FROM mouvement_etablissement m
                JOIN type_mouvement tm ON m.id_type = tm.id
                JOIN type_categorie tc ON tm.id_type = tc.id
            ");
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total_montant'];

            // Update curr_montant
            $stmt = $db->prepare("UPDATE etablissementFinancier SET curr_montant = ? WHERE id = ?");
            $stmt->execute([$total, $id]);

            return true;
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de curr_montant: " . $e->getMessage());
            return false;
        }
    }
 


    public static function getMonthlyInterest($annee_debut, $mois_debut, $annee_fin, $mois_fin) {
        $db = getDB();
        $results = [];
    
        try {
            // Generate list of months in the range
            $startDate = new DateTime("$annee_debut-$mois_debut-01");
            $endDate = new DateTime("$annee_fin-$mois_fin-01");
            $interval = new DateInterval('P1M');
            $period = new DatePeriod($startDate, $interval, $endDate->modify('+1 month'));
    
            foreach ($period as $date) {
                $year = $date->format('Y');
                $month = $date->format('m');
                $monthName = $date->format('F');
    
                $stmt = $db->prepare("
                    SELECT 
                        ? AS annee,
                        ? AS mois,
                        ? AS mois_annee,
                        ROUND(SUM((p.montant_accorde * p.taux_applique / 100) / 12), 2) AS interet_mensuel
                    FROM 
                        pret p
                        JOIN type_pret tp ON p.id_type_pret = tp.id
                    WHERE 
                         
                         p.date_deblocage IS NOT NULL
                        AND p.date_derniere_echeance IS NOT NULL
                        AND p.date_deblocage <= ?
                        AND p.date_derniere_echeance >= ?
                ");
                $lastDayOfMonth = $date->format('Y-m-t');
                // Log the query for debugging
                error_log("Executing query for $year-$month: " . print_r([
                    'annee' => $year,
                    'mois' => $month,
                    'mois_annee' => "$monthName $year",
                    'start_date' => "$year-$month-01",
                    'end_date' => $lastDayOfMonth
                ], true));
                $stmt->execute([
                    $year,
                    $month,
                    "$monthName $year",
                    "$year-$month-01",
                    $lastDayOfMonth
                ]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result && $result['interet_mensuel'] !== null) {
                    $results[] = $result;
                }
            }
            return $results;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des intérêts mensuels: " . $e->getMessage());
            Flight::halt(500, json_encode(['error' => 'Erreur serveur: ' . $e->getMessage()]));
            return [];
        }
    }
    
    

}



