<?php
// Professional Footer
?>
<footer class="footer">
    <div class="footer-main">
        <div class="container">
            <div class="footer-content">
                <!-- About Section -->
                <div class="footer-col">
                    <div class="footer-logo-section">
                        <i class="fas fa-droplet"></i>
                        <h3 class="footer-brand">Blood Bank</h3>
                    </div>
                    <p class="footer-text">Dedicated to saving lives through efficient blood donation management and distribution services.</p>
                    <div class="footer-social">
                        <a href="#" class="footer-social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="footer-social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="footer-social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="footer-social-link"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="footer-col">
                    <h4 class="footer-col-title">Quick Links</h4>
                    <ul class="footer-list">
                        <li><a href="/blood-bank/">Home</a></li>
                        <li><a href="/blood-bank/index.php#about">About Us</a></li>
                        <li><a href="/blood-bank/index.php#services">Services</a></li>
                        <li><a href="/blood-bank/index.php#contact">Contact</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div class="footer-col">
                    <h4 class="footer-col-title">Services</h4>
                    <ul class="footer-list">
                        <li><a href="#">Register as Donor</a></li>
                        <li><a href="#">Request Blood</a></li>
                        <li><a href="#">Blood Search</a></li>
                        <li><a href="#">Donation History</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="footer-col">
                    <h4 class="footer-col-title">Contact Info</h4>
                    <div class="footer-contact">
                        <div class="contact-item-footer">
                            <i class="fas fa-phone"></i>
                            <span>+1 800-BLOOD-1</span>
                        </div>
                        <div class="contact-item-footer">
                            <i class="fas fa-envelope"></i>
                            <span>info@bloodbank.com</span>
                        </div>
                        <div class="contact-item-footer">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>123 Medical Center</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-content">
                <p class="footer-copyright">&copy; <?php echo date('Y'); ?> Blood Bank Management System. All rights reserved.</p>
                <div class="footer-bottom-links">
                    <a href="#">Terms of Service</a>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Cookie Policy</a>
                </div>
            </div>
        </div>
    </div>
</footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery (Optional) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- AOS (Animate On Scroll) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

    <!-- GSAP for Advanced Animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <!-- Chart.js for Graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>

    <!-- Custom JS -->
    <script src="/blood-bank/assets/js/main.js"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            offset: 50,
            once: true,
        });

        // Dark Mode Toggle
        function initDarkMode() {
            const darkModeToggle = localStorage.getItem('darkMode');
            if (darkModeToggle === 'enabled') {
                document.body.style.filter = 'invert(1)';
                localStorage.setItem('darkMode', 'enabled');
            }
        }

        function toggleDarkMode() {
            const isDarkMode = document.body.style.filter === 'invert(1)';
            if (isDarkMode) {
                document.body.style.filter = 'none';
                localStorage.setItem('darkMode', 'disabled');
            } else {
                document.body.style.filter = 'invert(1)';
                localStorage.setItem('darkMode', 'enabled');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', initDarkMode);

        // Display Toast Notification
        function showToast(message, type = 'info') {
            const toastHTML = `
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-${type}">
                        <strong class="me-auto text-white">
                            <i class="fas fa-${type === 'error' ? 'exclamation' : type === 'success' ? 'check' : 'info'}-circle me-2"></i>
                            ${type.charAt(0).toUpperCase() + type.slice(1)}
                        </strong>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            `;
            
            const container = document.querySelector('.toast-container') || document.createElement('div');
            container.className = 'toast-container';
            container.innerHTML += toastHTML;
            if (!document.querySelector('.toast-container')) {
                document.body.appendChild(container);
            }

            const toast = container.lastElementChild;
            setTimeout(() => toast.remove(), 4000);
        }

        // Global Error Handler
        window.addEventListener('error', (event) => {
            console.error('Error:', event.error);
            showToast('An unexpected error occurred. Please try again.', 'error');
        });
    </script>

</body>
</html>
