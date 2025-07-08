<?php
require('vendor/setasign/fpdf/fpdf.php'); // chemin vers fpdf.php

function genererPdfPret($pret) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    // Titre
    $pdf->Cell(0, 10, 'Contrat de Prêt', 0, 1, 'C');

    $pdf->SetFont('Arial', '', 12);
    $pdf->Ln(10);

    // Infos client et prêt (exemple)
    $pdf->Cell(50, 10, 'Client : ');
    $pdf->Cell(0, 10, $pret['nom_client'], 0, 1);

    $pdf->Cell(50, 10, 'Montant accordé : ');
    $pdf->Cell(0, 10, number_format($pret['montant_accorde'], 2, ',', ' ') . ' €', 0, 1);

    $pdf->Cell(50, 10, 'Durée (mois) : ');
    $pdf->Cell(0, 10, $pret['duree_accordee'], 0, 1);

    $pdf->Cell(50, 10, 'Taux appliqué (%) : ');
    $pdf->Cell(0, 10, $pret['taux_applique'], 0, 1);

    $pdf->Cell(50, 10, 'Mensualité : ');
    $pdf->Cell(0, 10, number_format($pret['mensualite'], 2, ',', ' ') . ' €', 0, 1);

    // Date
    $pdf->Ln(10);
    $pdf->Cell(0, 10, 'Date de génération : ' . date('d/m/Y'), 0, 1);

    // Générer un fichier PDF dans un dossier (exemple)
    $filename = "contrat_pret_{$pret['id']}.pdf";
    $pdf->Output('F', __DIR__ . '/pdf/' . $filename);

    return $filename;
}

// Exemple d'utilisation (après avoir récupéré les données prêt dans $pret)
$pret = [
    'id' => 123,
    'nom_client' => 'Jean Dupont',
    'montant_accorde' => 10000,
    'duree_accordee' => 24,
    'taux_applique' => 5,
    'mensualite' => 438.71
];

$nomFichierPdf = genererPdfPret($pret);
echo "PDF généré : " . $nomFichierPdf;
?>
