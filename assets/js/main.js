/**
 * Blood Bank Management System - Main JavaScript
 * Handles animations, interactions, and utilities
 */

// ===================== UTILITY FUNCTIONS =====================

/**
 * Add event listener with fallback
 */
function on(element, event, handler) {
    if (element && element.addEventListener) {
        element.addEventListener(event, handler);
    }
}

/**
 * Query selector wrapper
 */
function select(el, all = false) {
    el = el.trim();
    if (all) {
        return [...document.querySelectorAll(el)];
    } else {
        return document.querySelector(el);
    }
}

/**
 * Format date
 */
function formatDate(date) {
    if (typeof date === 'string') {
        date = new Date(date);
    }
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

/**
 * Format currency
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

/**
 * Debounce function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Throttle function
 */
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// ===================== ANIMATIONS =====================

/**
 * Smooth scroll to element
 */
function smoothScroll(targetElement) {
    const target = typeof targetElement === 'string' ? select(targetElement) : targetElement;
    if (target) {
        const offsetTop = target.offsetTop;
        window.scrollTo({
            top: offsetTop,
            behavior: 'smooth'
        });
    }
}

/**
 * Animated counter
 */
function animateCounter(element, target, duration = 2000) {
    let current = 0;
    const increment = target / (duration / 16);
    
    const animate = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target.toLocaleString();
            clearInterval(animate);
        } else {
            element.textContent = Math.floor(current).toLocaleString();
        }
    }, 16);
}

/**
 * Animate elements on scroll
 */
function observeElements() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                
                // Animate counters
                if (entry.target.classList.contains('stat-number')) {
                    const target = parseInt(entry.target.textContent.replace(/,/g, ''));
                    if (!isNaN(target)) {
                        animateCounter(entry.target, target);
                    }
                }
                
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    select('[data-animate]', true).forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
}

// ===================== FORM VALIDATION =====================

/**
 * Validate email
 */
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Validate phone
 */
function isValidPhone(phone) {
    const re = /^[\d\s\-\+\(\)]+$/;
    return re.test(phone) && phone.replace(/\D/g, '').length >= 10;
}

/**
 * Validate form
 */
function validateForm(formId) {
    const form = select(formId);
    if (!form) return false;

    let isValid = true;
    const inputs = form.querySelectorAll('[required]');

    inputs.forEach(input => {
        let valid = true;

        // Check if empty
        if (!input.value.trim()) {
            valid = false;
        }

        // Email validation
        if (input.type === 'email' && !isValidEmail(input.value)) {
            valid = false;
        }

        // Phone validation
        if (input.type === 'tel' && !isValidPhone(input.value)) {
            valid = false;
        }

        // Password validation
        if (input.type === 'password' && input.value.length < 6) {
            valid = false;
        }

        if (!valid) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });

    return isValid;
}

// ===================== AJAX HELPERS =====================

/**
 * Make AJAX request
 */
async function ajaxRequest(url, method = 'GET', data = null, returnJSON = true) {
    try {
        const options = {
            method: method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        if (method !== 'GET' && data) {
            if (data instanceof FormData) {
                options.body = data;
            } else {
                options.headers['Content-Type'] = 'application/json';
                options.body = JSON.stringify(data);
            }
        }

        const response = await fetch(url, options);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return returnJSON ? await response.json() : await response.text();
    } catch (error) {
        console.error('AJAX Error:', error);
        showAlert('An error occurred. Please try again.', 'error');
        throw error;
    }
}

// ===================== ALERTS & NOTIFICATIONS =====================

/**
 * Show SweetAlert2
 */
function showAlert(message, type = 'info', title = '') {
    const config = {
        icon: type,
        title: title || type.charAt(0).toUpperCase() + type.slice(1),
        text: message,
        confirmButtonColor: '#e63946',
        confirmButtonText: 'OK'
    };

    if (type === 'confirm') {
        config.showCancelButton = true;
        config.confirmButtonText = 'Confirm';
        config.cancelButtonText = 'Cancel';
    }

    return Swal.fire(config);
}

/**
 * Show confirmation dialog
 */
function showConfirm(message, title = 'Confirm Action') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e63946',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, continue!',
        cancelButtonText: 'Cancel'
    });
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    Toast.fire({
        icon: type,
        title: message
    });
}

// ===================== TABLE OPERATIONS =====================

/**
 * Delete row with confirmation
 */
async function deleteRecord(id, tableName, endpoint) {
    const result = await showConfirm('Are you sure you want to delete this record?', 'Delete Record');
    
    if (result.isConfirmed) {
        try {
            const response = await ajaxRequest(endpoint, 'POST', { id: id });
            
            if (response.success) {
                showAlert('Record deleted successfully!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showAlert(response.message || 'Failed to delete record', 'error');
            }
        } catch (error) {
            showAlert('Error deleting record', 'error');
        }
    }
}

/**
 * Live search
 */
function liveSearch(inputSelector, tableSelector, columns = []) {
    const input = select(inputSelector);
    const table = select(tableSelector);

    if (!input || !table) return;

    on(input, 'keyup', debounce(function() {
        const searchTerm = input.value.toLowerCase().trim();
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            let match = false;
            
            if (searchTerm === '') {
                match = true;
            } else {
                const cells = row.querySelectorAll('td');
                for (let i = 0; i < cells.length; i++) {
                    if (columns.length === 0 || columns.includes(i)) {
                        if (cells[i].textContent.toLowerCase().includes(searchTerm)) {
                            match = true;
                            break;
                        }
                    }
                }
            }

            row.style.display = match ? '' : 'none';
        });
    }, 300));
}

/**
 * Export table to CSV
 */
function exportTableToCSV(tableId, filename = 'export.csv') {
    const table = select(tableId);
    if (!table) return;

    let csv = [];
    const rows = table.querySelectorAll('tr');

    rows.forEach(row => {
        let rowData = [];
        const cells = row.querySelectorAll('td, th');
        
        cells.forEach(cell => {
            rowData.push('"' + cell.textContent.trim().replace(/"/g, '""') + '"');
        });

        csv.push(rowData.join(','));
    });

    const csvContent = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv.join('\n'));
    const link = document.createElement('a');
    link.setAttribute('href', csvContent);
    link.setAttribute('download', filename);
    link.click();
}

/**
 * Print page
 */
function printPage(elementId = null) {
    if (elementId) {
        const element = select(elementId);
        if (!element) return;
        
        const printWindow = window.open('', '', 'height=500,width=900');
        printWindow.document.write(element.innerHTML);
        printWindow.document.close();
        printWindow.print();
    } else {
        window.print();
    }
}

// ===================== CHARTS =====================

/**
 * Create blood stock chart
 */
function createBloodStockChart(canvasId, labels, data) {
    const ctx = select(canvasId);
    if (!ctx) return;

    const colors = [
        '#e63946', // O+
        '#c1121f', // O-
        '#06d6a0', // A+
        '#118ab2', // A-
        '#ffd166', // B+
        '#f15a4a', // B-
        '#ef476f', // AB+
        '#6c757d'  // AB-
    ];

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors.slice(0, data.length),
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { family: "'Poppins', sans-serif" }
                    }
                }
            }
        }
    });
}

/**
 * Create donation chart
 */
function createDonationChart(canvasId, labels, data) {
    const ctx = select(canvasId);
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Donations',
                data: data,
                backgroundColor: 'rgba(230, 57, 70, 0.7)',
                borderColor: '#e63946',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        font: { family: "'Poppins', sans-serif" }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: { family: "'Poppins', sans-serif" }
                    }
                },
                x: {
                    ticks: {
                        font: { family: "'Poppins', sans-serif" }
                    }
                }
            }
        }
    });
}

// ===================== PAGE INITIALIZATION =====================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS if available
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            offset: 100,
            once: true
        });
    }

    // Observe elements for animation
    observeElements();

    // Initialize tooltips if using Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add active class to current navigation link
    const currentLocation = location.pathname;
    const navLinks = document.querySelectorAll('a.nav-link, a.sidebar-link');
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentLocation) {
            link.classList.add('active');
        }
    });

    // Remove invalid class on input
    const inputs = document.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        on(input, 'input', function() {
            this.classList.remove('is-invalid');
        });
    });
});

// ===================== SIDEBAR TOGGLE =====================

function initSidebar() {
    const sidebarToggle = select('[data-sidebar-toggle]');
    const sidebar = select('.sidebar');

    if (sidebarToggle && sidebar) {
        on(sidebarToggle, 'click', function() {
            sidebar.classList.toggle('show');
            this.classList.toggle('active');
        });

        // Close sidebar when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.sidebar') && !event.target.closest('[data-sidebar-toggle]')) {
                sidebar.classList.remove('show');
                if (sidebarToggle) sidebarToggle.classList.remove('active');
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', initSidebar);

// ===================== DARK MODE TOGGLE =====================

function initDarkMode() {
    const darkModeBtn = select('[data-dark-mode-toggle]');
    
    if (darkModeBtn) {
        on(darkModeBtn, 'click', function() {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', document.body.classList.contains('dark-mode') ? 'enabled' : 'disabled');
        });

        // Load previous preference
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
        }
    }
}

document.addEventListener('DOMContentLoaded', initDarkMode);

// ===================== KEYBOARD SHORTCUTS =====================

document.addEventListener('keydown', function(e) {
    // Ctrl+K for search
    if (e.ctrlKey && e.key === 'k') {
        e.preventDefault();
        const searchInput = select('[data-global-search]');
        if (searchInput) {
            searchInput.focus();
        }
    }

    // Escape to close modals
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            const bootstrapModal = bootstrap.Modal.getInstance(modal);
            if (bootstrapModal) {
                bootstrapModal.hide();
            }
        });
    }
});

console.log('Blood Bank Management System - Loaded Successfully');
