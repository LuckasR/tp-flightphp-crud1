<?php
$apiUrl = "http://localhost/tp-flightphp-crud1/ws/etablissements";
$response = file_get_contents($apiUrl);

// Vérifie si on a bien une réponse JSON
if ($response !== false) {
    $data = json_decode($response, true);

    // Si c’est une liste, on prend le premier établissement :
    if (is_array($data) && count($data) > 0) {
        $currMontant = $data[0]['curr_montant'];  // Banque Centrale
        $banque  = $data[0]['nom'].' , '.$data[0]['adresse'];                
    } else {
        $currMontant = 0;
        $banque  = "" ; 
    }
} else {
    $currMontant = 0;
    $banque  = "" ; 
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BankElite - Système Bancaire</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gold: #D4AF37;
            --primary-dark: #1a1a2e;
            --primary-blue: #16213e;
            --accent-light: #f8f9fa;
            --text-gold: #B8860B;
            --shadow-elegant: 0 10px 30px rgba(0,0,0,0.1);
            --gradient-gold: linear-gradient(135deg, #D4AF37 0%, #B8860B 100%);
            --gradient-dark: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .navbar {
            background: var(--gradient-dark) !important;
            box-shadow: var(--shadow-elegant);
            backdrop-filter: blur(10px);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--primary-gold) !important;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--primary-gold) !important;
            transform: translateY(-2px);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background: var(--primary-gold);
            transition: all 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
            left: 0;
        }

        .hero-section {
            background: var(--gradient-dark);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="grad1" cx="50%" cy="50%" r="50%"><stop offset="0%" style="stop-color:rgba(212,175,55,0.1);stop-opacity:1" /><stop offset="100%" style="stop-color:rgba(212,175,55,0);stop-opacity:0" /></radialGradient></defs><circle cx="200" cy="200" r="150" fill="url(%23grad1)"/><circle cx="800" cy="800" r="200" fill="url(%23grad1)"/></svg>') no-repeat center center;
            background-size: cover;
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .hero-subtitle {
            font-size: 1.3rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .btn-gold {
            background: var(--gradient-gold);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(212,175,55,0.3);
        }

        .btn-gold:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(212,175,55,0.4);
            color: white;
        }

        .dashboard-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow-elegant);
            border: none;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .card-header-gold {
            background: var(--gradient-gold);
            color: white;
            border: none;
            padding: 20px;
            font-weight: 600;
        }

        .card-header-dark {
            background: var(--gradient-dark);
            color: white;
            border: none;
            padding: 20px;
            font-weight: 600;
        }

        .balance-display {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-dark);
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .transaction-item {
            padding: 15px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .transaction-item:hover {
            background: rgba(212,175,55,0.05);
        }

        .transaction-amount {
            font-weight: 600;
        }

        .transaction-positive {
            color: #28a745;
        }

        .transaction-negative {
            color: #dc3545;
        }

        .quick-action-btn {
            background: white;
            border: 2px solid var(--primary-gold);
            color: var(--primary-gold);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
            display: block;
            margin-bottom: 15px;
        }

        .quick-action-btn:hover {
            background: var(--primary-gold);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212,175,55,0.3);
        }

        .quick-action-btn i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: var(--shadow-elegant);
            border: none;
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-3px);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-gold);
            margin-bottom: 10px;
        }

        .stats-label {
            color: var(--primary-dark);
            font-weight: 500;
        }

        .footer {
            background: var(--gradient-dark);
            color: white;
            padding: 50px 0;
            margin-top: 100px;
        }

        .footer-link {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-link:hover {
            color: var(--primary-gold);
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 3rem;
            text-align: center;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--gradient-gold);
            border-radius: 2px;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-gold);
            box-shadow: 0 0 0 0.2rem rgba(212,175,55,0.25);
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary-gold);
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .balance-display {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-university me-2"></i>BankElite</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="compte_bancaire.php">Comptes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transaction_compte.php">Transactions</a>
                    </li>
                      <li class="nav-item">
                        <a class="nav-link" href="gestion_remboursement.php">Remboursement</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="interest_report.php">Interest</a>
                    </li>
                      <li class="nav-item">
                        <a class="nav-link" href="service.php">Liste Fonctionnalite</a>
                    </li>

                        <li class="nav-item">
                        <a class="nav-link" href="prets_non_valides.php">Pret</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#profile"><i class="fas fa-user me-1"></i>Profil</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content animate-fade-in">
                    <h1 class="hero-title">Bienvenue, <span style="color: var(--primary-gold);">Jean Dupont</span></h1>
                    <p class="hero-subtitle">Gérez vos finances avec élégance et sécurité</p>
                    <button class="btn btn-gold">Voir mes comptes</button>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="stats-card">
                        <div class="stats-number"><?= $currMontant ?> Ariary </div>
                        <div class="stats-label">Solde Total chez <?= $banque ?> </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dashboard Section -->
    <section id="dashboard" class="py-5">
        <div class="container">
            <h2 class="section-title">Tableau de Bord</h2>
            
            <div class="row">
                <!-- Account Balance -->
                <div class="col-lg-8 mb-4">
                    <div class="dashboard-card">
                        <div class="card-header-gold">
                            <h4 class="mb-0"><i class="fas fa-wallet me-2"></i>Mes Comptes</h4>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h6 class="text-muted">Compte Courant</h6>
                                        <div class="balance-display">€ 12,450.75</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h6 class="text-muted">Compte Épargne</h6>
                                        <div class="balance-display">€ 32,779.75</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-4">
                                    <div class="stats-card">
                                        <div class="stats-number">+€ 2,340</div>
                                        <div class="stats-label">Ce mois</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-card">
                                        <div class="stats-number">-€ 1,890</div>
                                        <div class="stats-label">Dépenses</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-card">
                                        <div class="stats-number">23</div>
                                        <div class="stats-label">Transactions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col-lg-4 mb-4">
                    <div class="dashboard-card">
                        <div class="card-header-dark">
                            <h4 class="mb-0"><i class="fas fa-bolt me-2"></i>Actions Rapides</h4>
                        </div>
                        <div class="card-body p-4">
                            <a href="#" class="quick-action-btn">
                                <i class="fas fa-exchange-alt"></i>
                                <div>Virement</div>
                            </a>
                            <a href="#" class="quick-action-btn">
                                <i class="fas fa-credit-card"></i>
                                <div>Paiement</div>
                            </a>
                            <a href="#" class="quick-action-btn">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <div>Factures</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="row">
                <div class="col-12">
                    <div class="dashboard-card">
                        <div class="card-header-gold">
                            <h4 class="mb-0"><i class="fas fa-history me-2"></i>Transactions Récentes</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="transaction-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Salaire - Entreprise XYZ</h6>
                                        <small class="text-muted">8 Juillet 2025</small>
                                    </div>
                                    <div class="transaction-amount transaction-positive">+€ 3,200.00</div>
                                </div>
                            </div>
                            <div class="transaction-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Supermarché Carrefour</h6>
                                        <small class="text-muted">7 Juillet 2025</small>
                                    </div>
                                    <div class="transaction-amount transaction-negative">-€ 87.50</div>
                                </div>
                            </div>
                            <div class="transaction-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Virement vers Épargne</h6>
                                        <small class="text-muted">6 Juillet 2025</small>
                                    </div>
                                    <div class="transaction-amount transaction-negative">-€ 500.00</div>
                                </div>
                            </div>
                            <div class="transaction-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Remboursement Assurance</h6>
                                        <small class="text-muted">5 Juillet 2025</small>
                                    </div>
                                    <div class="transaction-amount transaction-positive">+€ 245.00</div>
                                </div>
                            </div>
                            <div class="transaction-item border-bottom-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Facture Électricité</h6>
                                        <small class="text-muted">4 Juillet 2025</small>
                                    </div>
                                    <div class="transaction-amount transaction-negative">-€ 125.30</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title">Nos Services</h2>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="dashboard-card text-center">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="fas fa-shield-alt" style="font-size: 3rem; color: var(--primary-gold);"></i>
                            </div>
                            <h4>Sécurité Maximale</h4>
                            <p class="text-muted">Vos données sont protégées par un chiffrement de niveau bancaire.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="dashboard-card text-center">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="fas fa-mobile-alt" style="font-size: 3rem; color: var(--primary-gold);"></i>
                            </div>
                            <h4>Application Mobile</h4>
                            <p class="text-muted">Gérez vos comptes depuis votre smartphone, où que vous soyez.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="dashboard-card text-center">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="fas fa-headset" style="font-size: 3rem; color: var(--primary-gold);"></i>
                            </div>
                            <h4>Support 24/7</h4>
                            <p class="text-muted">Notre équipe est disponible 24h/24 pour vous accompagner.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h4 class="mb-3" style="color: var(--primary-gold);">BankElite</h4>
                    <p class="text-muted">Votre partenaire financier de confiance pour une gestion bancaire d'excellence.</p>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5 class="mb-3">Liens Utiles</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="footer-link">Aide & Support</a></li>
                        <li><a href="#" class="footer-link">Sécurité</a></li>
                        <li><a href="#" class="footer-link">Conditions d'utilisation</a></li>
                        <li><a href="#" class="footer-link">Politique de confidentialité</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5 class="mb-3">Contact</h5>
                    <p class="text-muted">
                        <i class="fas fa-phone me-2"></i>+33 1 23 45 67 89<br>
                        <i class="fas fa-envelope me-2"></i>contact@bankelite.fr<br>
                        <i class="fas fa-map-marker-alt me-2"></i>123 Avenue des Champs-Élysées, Paris
                    </p>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
            <div class="text-center">
                <p class="mb-0" style="color: rgba(255,255,255,0.7);">© 2025 BankElite. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

<script>
    const apiBase = "http://localhost/tp-flightphp-crud1/ws";

    async function chargerSoldeTotal() {
        try {
            const response = await fetch(`${apiBase}/api/etablissements/solde-total`);
            const data = await response.json();

            // Affiche dans .stats-number ou autre élément
            const el = document.querySelector('.stats-number');
            if (el) {
                el.innerText = `€ ${parseFloat(data.solde_total).toFixed(2)}`;
            }
        } catch (error) {
            console.error("Erreur lors du chargement du solde total :", error);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        chargerSoldeTotal();
    });
</script>




    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add loading animation for actions
        document.querySelectorAll('.quick-action-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const originalContent = this.innerHTML;
                this.innerHTML = '<div class="loading-spinner"></div>Action en cours...';
                
                setTimeout(() => {
                    this.innerHTML = originalContent;
                }, 2000);
            });
        });

        // Animate numbers on scroll
        const observerOptions = {
            threshold: 0.1,
            triggerOnce: true
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.dashboard-card').forEach(card => {
            observer.observe(card);
        });

        // Update time periodically
        function updateTime() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('fr-FR');
            const dateStr = now.toLocaleDateString('fr-FR');
            
            // You can add this to a status bar if needed
            console.log(`${dateStr} ${timeStr}`);
        }

        setInterval(updateTime, 1000);
    </script>
</body>
</html>