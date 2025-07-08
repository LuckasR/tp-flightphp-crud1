<?php
require_once __DIR__ . '/../models/Pret.php';

class PretController {

    public static function getAll() {
        echo json_encode(Pret::getAll());
    }


    public static function getAllNotValidate() {
        echo json_encode(Pret::getAllNotValidate());
    }

    public static function getAllValidate()  {
        echo json_encode(Pret::getAllValidate() );
    }


    

    public static function validerPret($id, $data) {
        Pret::validerPret($id, $data);
    }

    public static function getById($id) {
        echo json_encode(Pret::getById($id));
    }

    public static function create() {
        $data = Flight::request()->data;
        echo json_encode(Pret::create($data));
    }

    public static function update($id) {
        $data = Flight::request()->data;
        Pret::update($id, $data);
        echo json_encode(['message' => 'Pret mis a jour']);
    }

    public static function delete($id) {
        Pret::delete($id);
        echo json_encode(['message' => 'Pret supprime']);
    }


public static function genererPDF($idPret) {
    require_once __DIR__ . '\..\..\vendor\setasign\fpdf\fpdf.php';
    $db = getDB();
    
    // 1. Recuperation des donnees completes du pret
    $stmt = $db->prepare("
        SELECT 
            p.id, p.numero_pret, p.montant_accorde, p.duree_accordee, p.mensualite,
            p.taux_applique, p.date_decision, p.frais_dossier, p.frais_assurance, 
            p.montant_total, p.date_premiere_echeance, p.date_derniere_echeance,
            tp.nom AS type_pret, tp.taux_interet, tp.taux_interet_retard,
            c.nom AS nom_client, c.email 
        FROM pret p
        JOIN type_pret tp ON p.id_type_pret = tp.id
        JOIN client c ON p.id_client = c.id
        WHERE p.id = ?
    ");
    $stmt->execute([$idPret]);
    $pret = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pret) {
        Flight::json(["error" => "Pret introuvable"], 404);
        return;
    }
    
    // 2. Creation du PDF avec design elegant
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(true, 20);
    
    // === HEADER eLeGANT ===
    // Couleur de fond pour l'en-tete (bleu marine)
    $pdf->SetFillColor(26, 26, 46);
    $pdf->Rect(0, 0, 210, 40, 'F');
    
    // Logo et nom de l'entreprise
    $pdf->SetTextColor(212, 175, 55); // Couleur or
    $pdf->SetFont('Arial', 'B', 24);
    $pdf->SetXY(20, 12);
    $pdf->Cell(0, 10, 'BANKELITE', 0, 1, 'L');
    
    // Sous-titre
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(20, 25);
    $pdf->Cell(0, 6, 'Excellence bancaire & Solutions financieres', 0, 1, 'L');
    
    // Date et numero de contrat a droite
    $pdf->SetXY(130, 12);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 6, 'CONTRAT DE PReT', 0, 1, 'R');
    $pdf->SetXY(130, 20);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, 'N° ' . $pret['numero_pret'], 0, 1, 'R');
    $pdf->SetXY(130, 28);
    $pdf->Cell(0, 6, 'Date: ' . date('d/m/Y'), 0, 1, 'R');
    
    // === TITRE PRINCIPAL ===
    $pdf->SetY(55);
    $pdf->SetTextColor(26, 26, 46);
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->Cell(0, 12, 'CONTRAT DE FINANCEMENT', 0, 1, 'C');
    
    // Ligne decorative
    $pdf->SetDrawColor(212, 175, 55);
    $pdf->SetLineWidth(1);
    $pdf->Line(70, 72, 140, 72);
    
    // === SECTION INFORMATIONS CLIENT ===
    $pdf->SetY(85);
    $pdf->SetTextColor(26, 26, 46);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'INFORMATIONS CLIENT', 0, 1, 'L');
    
    // Cadre pour les informations client
    $pdf->SetDrawColor(230, 230, 230);
    $pdf->SetLineWidth(0.5);
    $pdf->Rect(20, 98, 170, 35);
    
    // Fond leger pour le cadre client
    $pdf->SetFillColor(248, 249, 250);
    $pdf->Rect(20, 98, 170, 35, 'F');
    
    $pdf->SetFont('Arial', '', 11);
    $pdf->SetY(105);
    $pdf->SetX(25);
    $pdf->Cell(80, 6, 'Nom du client:', 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 6, $pret['nom_client'], 0, 1, 'L');
    
    if (!empty($pret['email'])) {
        $pdf->SetX(25);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(80, 6, 'Email:', 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 6, $pret['email'], 0, 1, 'L');
    }
    
    if (!empty($pret['telephone'])) {
        $pdf->SetX(25);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(80, 6, 'Telephone:', 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 6, $pret['telephone'], 0, 1, 'L');
    }
    
    $pdf->SetX(25);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(80, 6, 'Type de pret:', 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 6, $pret['type_pret'], 0, 1, 'L');
    
    // === SECTION DeTAILS DU PReT ===
    $pdf->SetY(145);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetTextColor(26, 26, 46);
    $pdf->Cell(0, 10, 'DETAILS DU FINANCEMENT', 0, 1, 'L');
    
    // Tableau stylise pour les details
    $pdf->SetY(158);
    $pdf->SetDrawColor(212, 175, 55);
    $pdf->SetLineWidth(1);
    
    // En-tete du tableau
    $pdf->SetFillColor(212, 175, 55);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(90, 10, 'DESCRIPTION', 1, 0, 'C', true);
    $pdf->Cell(80, 10, 'MONTANT / VALEUR', 1, 1, 'C', true);
    
    // Lignes du tableau
    $pdf->SetFillColor(248, 249, 250);
    $pdf->SetTextColor(26, 26, 46);
    $pdf->SetFont('Arial', '', 10);
    
    $details = [
        ['Montant accorde', number_format($pret['montant_accorde'], 2, ',', ' ') . ' Ariary'],
        ['Duree du pret', $pret['duree_accordee'] . ' mois'],
        ['Mensualite', number_format($pret['mensualite'], 2, ',', ' ') . ' Ariary'],
        ['Taux d\'interet applique', $pret['taux_applique'] . ' %'],
        ['Taux d\'interet de retard', $pret['taux_interet_retard'] . ' %'],
        ['Frais de dossier', number_format($pret['frais_dossier'], 2, ',', ' ') . ' Ariary'],
        ['Frais d\'assurance', number_format($pret['frais_assurance'], 2, ',', ' ') . ' Ariary']
    ];
    
    foreach ($details as $i => $detail) {
        $fill = ($i % 2 == 0) ? true : false;
        $pdf->Cell(90, 8, $detail[0], 1, 0, 'L', $fill);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(80, 8, $detail[1], 1, 1, 'R', $fill);
        $pdf->SetFont('Arial', '', 10);
    }
    
    // Ligne du montant total (mise en evidence)
    $pdf->SetFillColor(26, 26, 46);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(90, 10, 'MONTANT TOTAL a REMBOURSER', 1, 0, 'L', true);
    $pdf->Cell(80, 10, number_format($pret['montant_total'], 2, ',', ' ') . ' Ariary', 1, 1, 'R', true);
    
    // === SECTION DATES IMPORTANTES ===
    $pdf->SetY($pdf->GetY() + 10);
    $pdf->SetTextColor(26, 26, 46);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'ECHEANCIER', 0, 1, 'L');
    
    // Cadre pour les dates
    $pdf->SetDrawColor(230, 230, 230);
    $pdf->SetLineWidth(0.5);
    $y_start = $pdf->GetY() + 3;
    $pdf->Rect(20, $y_start, 170, 25);
    $pdf->SetFillColor(248, 249, 250);
    $pdf->Rect(20, $y_start, 170, 25, 'F');
    
    $pdf->SetY($y_start + 5);
    $pdf->SetFont('Arial', '', 11);
    
    $pdf->SetX(25);
    $pdf->Cell(80, 6, 'Date de decision:', 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 6, date("d/m/Y", strtotime($pret['date_decision'])), 0, 1, 'L');
    
    $pdf->SetX(25);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(80, 6, 'Premiere echeance:', 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 6, date("d/m/Y", strtotime($pret['date_premiere_echeance'])), 0, 1, 'L');
    
    $pdf->SetX(25);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(80, 6, 'Derniere echeance:', 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 6, date("d/m/Y", strtotime($pret['date_derniere_echeance'])), 0, 1, 'L');
    
    // === SECTION SIGNATURES ===
    $pdf->SetY($pdf->GetY() + 20);
    $pdf->SetTextColor(26, 26, 46);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'SIGNATURES', 0, 1, 'C');
    
    // Ligne decorative
    $pdf->SetDrawColor(212, 175, 55);
    $pdf->SetLineWidth(0.5);
    $pdf->Line(85, $pdf->GetY(), 125, $pdf->GetY());
    
    $pdf->SetY($pdf->GetY() + 10);
    $pdf->SetFont('Arial', '', 10);
    
    // Signature client
    $pdf->SetX(30);
    $pdf->Cell(70, 6, 'Signature du client', 0, 0, 'C');
    $pdf->Cell(30, 6, '', 0, 0, 'C');
    $pdf->Cell(70, 6, 'Signature de la banque', 0, 1, 'C');
    
    $pdf->SetY($pdf->GetY() + 15);
    $pdf->SetDrawColor(180, 180, 180);
    $pdf->SetLineWidth(0.5);
    $pdf->Line(30, $pdf->GetY(), 100, $pdf->GetY());
    $pdf->Line(140, $pdf->GetY(), 210, $pdf->GetY());
    
    $pdf->SetY($pdf->GetY() + 5);
    $pdf->SetX(30);
    $pdf->Cell(70, 6, 'Date: ___________', 0, 0, 'C');
    $pdf->Cell(30, 6, '', 0, 0, 'C');
    $pdf->Cell(70, 6, 'Date: ___________', 0, 1, 'C');
    
    // === FOOTER eLeGANT ===
    $pdf->SetY(-30);
    $pdf->SetDrawColor(212, 175, 55);
    $pdf->SetLineWidth(0.5);
    $pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
    
    $pdf->SetY(-25);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 4, 'BankElite - 123 Avenue des Champs-elysees, Paris | Tel: +33 1 23 45 67 89 | Email: contact@bankelite.fr', 0, 1, 'C');
    $pdf->Cell(0, 4, 'Ce document est confidentiel et ne peut etre reproduit sans autorisation ecrite de BankElite', 0, 1, 'C');
    
    // === NUMeROTATION DES PAGES ===
    $pdf->SetY(-15);
    $pdf->SetTextColor(150, 150, 150);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 4, 'Page 1/1 - Genere le ' . date('d/m/Y a H:i'), 0, 0, 'R');
    
    // 3. Sauvegarde avec nom de fichier optimise
    $outputDir = __DIR__ . '/../public/pdf/';
    if (!file_exists($outputDir)) {
        mkdir($outputDir, 0777, true);
    }
    
    $fileName = "contrat_pret_" . $pret['numero_pret'] . "_" . date('Ymd_His') . ".pdf";
    $filePath = $outputDir . $fileName;
    $pdf->Output('I', "contrat_pret_" . $pret['numero_pret'] . ".pdf");
    exit;
    
    // 4. Reponse JSON avec informations detaillees
    Flight::json([
        "success" => true,
        "message" => "Contrat PDF genere avec succes",
        "fichier" => "pdf/" . $fileName,
        "details" => [
            "numero_pret" => $pret['numero_pret'],
            "client" => $pret['nom_client'],
            "montant" => number_format($pret['montant_accorde'], 2, ',', ' ') . ' Ariary',
            "taille_fichier" => filesize($filePath) . ' bytes',
            "date_generation" => date('d/m/Y H:i:s')
        ]
    ]);
}

}
