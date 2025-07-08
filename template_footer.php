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