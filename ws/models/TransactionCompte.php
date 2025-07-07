<?php
require_once __DIR__ . '/../db.php';

class TransactionCompte {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT tc.*, cb.numero_compte, tc2.type_name 
                            FROM transaction_compte tc 
                            JOIN compte_bancaire cb ON tc.compte_id = cb.id 
                            JOIN type_categorie tc2 ON tc.id_type = tc2.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT tc.*, cb.numero_compte, tc2.type_name 
                              FROM transaction_compte tc 
                              JOIN compte_bancaire cb ON tc.compte_id = cb.id 
                              JOIN type_categorie tc2 ON tc.id_type = tc2.id 
                              WHERE tc.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $db->beginTransaction();

        try {
            // Vérifier si le compte existe
            $stmt = $db->prepare("SELECT id, solde_compte FROM compte_bancaire WHERE id = ?");
            $stmt->execute([$data->compte_id]);
            $compte = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$compte) {
                throw new Exception("Compte bancaire non trouvé");
            }

            // Vérifier si id_type est valide (1: depot, 2: retrait, 3: transfert)
            $stmt = $db->prepare("SELECT id, type_name FROM type_categorie WHERE id = ?");
            $stmt->execute([$data->id_type]);
            $type = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$type) {
                throw new Exception("Type de transaction non trouvé");
            }

            // Mettre à jour le solde en fonction du type
            $new_solde = $compte['solde_compte'];
            if ($type['type_name'] === 'depot') {
                $new_solde += $data->montant;
            } elseif ($type['type_name'] === 'retrait') {
                if ($new_solde < $data->montant) {
                    throw new Exception("Solde insuffisant pour le retrait");
                }
                $new_solde -= $data->montant;
            } elseif ($type['type_name'] === 'transfert') {
                // Pour un transfert, soustraire du compte source
                if ($new_solde < $data->montant) {
                    throw new Exception("Solde insuffisant pour le transfert");
                }
                $new_solde -= $data->montant;

                // Vérifier le compte cible pour le transfert
                if (!isset($data->compte_cible_id)) {
                    throw new Exception("Compte cible requis pour le transfert");
                }
                $stmt = $db->prepare("SELECT id, solde_compte FROM compte_bancaire WHERE id = ?");
                $stmt->execute([$data->compte_cible_id]);
                $compte_cible = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$compte_cible) {
                    throw new Exception("Compte cible non trouvé");
                }

                // Mettre à jour le solde du compte cible
                $stmt = $db->prepare("UPDATE compte_bancaire SET solde_compte = ?, last_change = ? WHERE id = ?");
                $stmt->execute([$compte_cible['solde_compte'] + $data->montant, date('Y-m-d H:i:s'), $data->compte_cible_id]);

                // Insérer la transaction de réception dans le compte cible
                $stmt = $db->prepare("INSERT INTO transaction_compte (compte_id, id_type, montant, description, date_transaction) 
                                      VALUES (?, 1, ?, ?, ?)");
                $stmt->execute([$data->compte_cible_id, $data->montant, "Reçu via transfert depuis compte {$compte['id']}", date('Y-m-d H:i:s')]);
            }

            // Insérer la transaction
            $stmt = $db->prepare("INSERT INTO transaction_compte (compte_id, id_type, montant, description, date_transaction) 
                                  VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $data->compte_id,
                $data->id_type,
                $data->montant,
                $data->description ?? null,
                date('Y-m-d H:i:s')
            ]);

            // Mettre à jour le solde du compte source
            $stmt = $db->prepare("UPDATE compte_bancaire SET solde_compte = ?, last_change = ? WHERE id = ?");
            $stmt->execute([$new_solde, date('Y-m-d H:i:s'), $data->compte_id]);

            $db->commit();
            return $db->lastInsertId();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function update($id, $data) {
        throw new Exception("La mise à jour des transactions n'est pas autorisée pour maintenir l'intégrité.");
    }

    public static function delete($id) {
        throw new Exception("La suppression des transactions n'est pas autorisée pour maintenir l'historique.");
    }
}