<?php
require_once 'includes/db_connect.php';
$pageTitle = 'Home';
?>
<?php include 'includes/header.php'; ?>

<style>
    /* Landing Page Specific Styles */
    .navbar {
        position: absolute;
        width: 100%;
        top: 0;
        z-index: 50;
        background: rgba(230, 57, 70, 0.95) !important;
        backdrop-filter: blur(10px);
    }

    .navbar.scrolled {
        position: fixed;
        background: linear-gradient(135deg, var(--primary-color) 0%, #c1121f 100%) !important;
        box-shadow: 0 4px 20px rgba(230, 57, 70, 0.2);
    }

    /* Hero Animation Text */
    .typing-animation {
        display: inline-block;
        border-right: 3px solid white;
        padding-right: 8px;
        animation: typing 5s steps(50, end), blink 0.7s infinite;
    }

    @keyframes typing {
        0% { width: 0; }
        100% { width: 500px; }
    }

    @keyframes blink {
        50% { border-color: transparent; }
    }

    .particle {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
    }

    /* Gradient Text */
    .gradient-text {
        background: linear-gradient(135deg, var(--primary-color) 0%, #c1121f 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Floating Animation */
    .floating {
        animation: floating 3s ease-in-out infinite;
    }

    @keyframes floating {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }

    /* Counter */
    .counter-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    /* Service Cards */
    .service-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.23, 1, 0.320, 1);
        border: 2px solid transparent;
        height: 100%;
    }

    .service-card:hover {
        transform: translateY(-15px);
        box-shadow: 0 15px 40px rgba(230, 57, 70, 0.15);
        border-color: var(--primary-color);
    }

    .service-icon {
        font-size: 3rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
        transition: transform 0.3s ease;
    }

    .service-card:hover .service-icon {
        transform: scale(1.1) rotate(10deg);
    }

    .service-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: var(--dark-color);
    }

    .service-desc {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    /* Blood Search Section */
    .search-section {
        background: linear-gradient(135deg, #f8f9fa 0%, white 100%);
        padding: 4rem 0;
    }

    .search-input-group {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    .search-input-group select,
    .search-input-group input {
        padding: 0.8rem 1.2rem;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .search-input-group select:focus,
    .search-input-group input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(230, 57, 70, 0.1);
        outline: none;
    }

    .search-btn {
        padding: 0.8rem 2rem;
        background: linear-gradient(135deg, var(--primary-color) 0%, #c1121f 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .search-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(230, 57, 70, 0.3);
    }

    /* Blood Availability Grid */
    .blood-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .blood-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid transparent;
    }

    .blood-card:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .blood-type {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }

    .blood-status {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        margin-bottom: 0.5rem;
        display: inline-block;
    }

    .status-high {
        background: rgba(6, 214, 160, 0.2);
        color: #06a070;
    }

    .status-medium {
        background: rgba(255, 209, 102, 0.2);
        color: #f59e0b;
    }

    .status-low {
        background: rgba(239, 71, 111, 0.2);
        color: #ef476f;
    }

    .blood-units {
        font-size: 0.9rem;
        color: #666;
    }

    /* Timeline */
    .timeline {
        position: relative;
        padding: 2rem 0;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        width: 3px;
        height: 100%;
        background: linear-gradient(to bottom, var(--primary-color), transparent);
    }

    .timeline-item {
        margin-bottom: 3rem;
        width: 48%;
    }

    .timeline-item:nth-child(odd) {
        margin-left: 0;
        text-align: right;
        padding-right: 3rem;
    }

    .timeline-item:nth-child(even) {
        margin-left: 52%;
        padding-left: 3rem;
    }

    .timeline-dot {
        position: absolute;
        width: 20px;
        height: 20px;
        background: white;
        border: 4px solid var(--primary-color);
        border-radius: 50%;
        left: 50%;
        transform: translateX(-50%);
        top: 0;
    }

    .timeline-content {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    /* FAQ Section */
    .faq-item {
        background: white;
        border-radius: 10px;
        margin-bottom: 1rem;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .faq-item.active {
        box-shadow: 0 8px 20px rgba(230, 57, 70, 0.15);
    }

    .faq-question {
        padding: 1.5rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
        border: none;
        width: 100%;
        text-align: left;
        font-weight: 600;
        color: var(--dark-color);
        transition: all 0.3s ease;
    }

    .faq-question:hover {
        background: var(--accent-color);
        color: var(--primary-color);
    }

    .faq-icon {
        font-size: 1.3rem;
        transition: transform 0.3s ease;
        color: var(--primary-color);
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        padding: 0 1.5rem;
    }

    .faq-item.active .faq-answer {
        max-height: 500px;
        padding: 0 1.5rem 1.5rem;
    }

    .faq-item.active .faq-icon {
        transform: rotate(45deg);
    }

    .faq-text {
        color: #666;
        line-height: 1.8;
    }

    /* Donor Form Styling */
    .form-control-lg, .form-select-lg {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        border: 2px solid #ddd;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control-lg:focus, .form-select-lg:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(230, 57, 70, 0.15);
    }

    .form-label {
        font-size: 0.95rem;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
    }

    .fw-600 {
        font-weight: 600;
    }

    .section-subtitle {
        font-size: 0.9rem;
        color: var(--primary-color);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    @media (max-width: 768px) {
        .timeline::before {
            left: 30px;
        }

        .timeline-item,
        .timeline-item:nth-child(odd),
        .timeline-item:nth-child(even) {
            width: 100%;
            margin-left: 0;
            padding-right: 0;
            padding-left: 80px;
            text-align: left;
        }

        .timeline-dot {
            left: 30px;
        }

        .blood-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="/blood-bank/">
            <i class="fas fa-droplet me-2" style="color: #ffd166;"></i>
            Blood Bank System
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#search">Search Blood</a></li>
                <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                <li class="nav-item ms-2">
                    <a class="nav-link btn btn-light text-primary rounded-pill px-3" href="/blood-bank/login.php">
                        <i class="fas fa-sign-in-alt me-1"></i> Admin Login
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section id="home" class="hero-section pt-5">
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6" data-aos="fade-right">
                <h1 class="hero-title display-3 fw-bold mb-4">
                    <span class="typing-animation">Donate Blood, Save Lives</span>
                </h1>
                <p class="hero-subtitle">Join our mission to ensure safe blood supply for everyone. 
                    One donation can save up to 3 lives. Be a hero today!</p>
                <div class="hero-buttons mt-4">
                    <button class="btn btn-light btn-lg me-2 mb-2" onclick="document.getElementById('donors').scrollIntoView({behavior: 'smooth'})">
                        <i class="fas fa-user-plus me-2"></i> Register as Donor
                    </button>
                    <button class="btn btn-outline-light btn-lg mb-2" onclick="document.getElementById('search').scrollIntoView({behavior: 'smooth'})">
                        <i class="fas fa-search me-2"></i> Search Blood
                    </button>
                </div>
            </div>
            <div class="col-lg-6 text-center" data-aos="fade-left">
                <i class="fas fa-heart" style="font-size: 10rem; color: rgba(255,255,255,0.3); animation: pulse 2s infinite;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Blood Donor Registration Section -->
<section id="donors" class="py-5" style="background: linear-gradient(135deg, rgba(230, 57, 70, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%);">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <p class="section-subtitle">Save Lives</p>
            <h2>Become a Blood Donor Today</h2>
            <p class="text-muted max-width-md mx-auto">
                Your blood donation can save up to 3 lives. Register now and join our community of heroes.
            </p>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto" data-aos="fade-up">
                <div class="card border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
                    <div style="background: linear-gradient(135deg, #e63946 0%, #c1121f 100%); padding: 3rem 2rem; color: white; text-align: center;">
                        <h3 style="font-weight: 700; margin: 0;"><i class="fas fa-heart me-2"></i>Register as a Donor</h3>
                        <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Complete the form below to get started</p>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <form id="donorForm" method="POST" action="/blood-bank/public/register_donor.php" class="needs-validation">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-600">Full Name *</label>
                                    <input type="text" name="full_name" class="form-control form-control-lg" 
                                           placeholder="e.g., Mohammad Ali" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600">Phone (017XXXXXXXX) *</label>
                                    <input type="tel" name="phone" class="form-control form-control-lg" 
                                           placeholder="e.g., 01711234567" pattern="017[0-9]{8}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-600">Date of Birth *</label>
                                    <input type="date" name="date_of_birth" class="form-control form-control-lg" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600">Blood Type *</label>
                                    <select name="blood_type_id" class="form-select form-select-lg" required>
                                        <option value="">Select your blood type</option>
                                        <option value="1">O+</option>
                                        <option value="2">O-</option>
                                        <option value="3">A+</option>
                                        <option value="4">A-</option>
                                        <option value="5">B+</option>
                                        <option value="6">B-</option>
                                        <option value="7">AB+</option>
                                        <option value="8">AB-</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-600">Gender *</label>
                                    <select name="gender" class="form-select form-select-lg" required>
                                        <option value="">Select gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600">City *</label>
                                    <input type="text" name="city" class="form-control form-control-lg" 
                                           placeholder="e.g., Dhaka" required>
                                </div>
                            </div>

                            <div class="d-grid mt-4 pt-3">
                                <button type="submit" class="btn btn-primary btn-lg fw-600" 
                                        style="background: linear-gradient(135deg, #e63946 0%, #c1121f 100%); padding: 12px; border-radius: 8px;">
                                    <i class="fas fa-check-circle me-2"></i> Register as Donor
                                </button>
                            </div>

                            <p class="text-muted text-center mt-4 mb-0">
                                <small>Your information is safe and secure. We'll contact you with donation schedules.</small>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5 pt-3">
            <div class="col-md-4 text-center" data-aos="fade-up" data-aos-delay="100">
                <div style="padding: 2rem; background: white; border-radius: 10px; box-shadow: 0 2px 15px rgba(0,0,0,0.08);">
                    <i class="fas fa-history fa-3x text-danger mb-3" style="opacity: 0.8;"></i>
                    <h5>Eligibility</h5>
                    <p class="text-muted small">You must be 18-65 years old, in good health, and able to donate</p>
                </div>
            </div>
            <div class="col-md-4 text-center" data-aos="fade-up" data-aos-delay="200">
                <div style="padding: 2rem; background: white; border-radius: 10px; box-shadow: 0 2px 15px rgba(0,0,0,0.08);">
                    <i class="fas fa-stethoscope fa-3x text-danger mb-3" style="opacity: 0.8;"></i>
                    <h5>Process</h5>
                    <p class="text-muted small">Simple registration, health screening, and donation in 30-45 minutes</p>
                </div>
            </div>
            <div class="col-md-4 text-center" data-aos="fade-up" data-aos-delay="300">
                <div style="padding: 2rem; background: white; border-radius: 10px; box-shadow: 0 2px 15px rgba(0,0,0,0.08);">
                    <i class="fas fa-heart fa-3x text-danger mb-3" style="opacity: 0.8;"></i>
                    <h5>Impact</h5>
                    <p class="text-muted small">Each donation can save up to 3 lives and help our community</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-5" style="background: white;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4" data-aos="fade-right">
                <h2 class="mb-4">About Our Blood Bank</h2>
                <p class="text-muted mb-3">
                    Our blood bank is dedicated to ensuring a safe, sufficient and sustainable supply of blood 
                    and blood products. We work with medical professionals to collect, test, and distribute blood 
                    to hospitals and medical centers.
                </p>
                <p class="text-muted mb-3">
                    Every donation is screened and tested to ensure safety. Our trained staff ensures that blood 
                    is stored under proper conditions to maintain its integrity and effectiveness.
                </p>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <strong>Safe & Tested Blood</strong>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <strong>24/7 Available</strong>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <strong>Professional Staff</strong>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <strong>Quick Delivery</strong>
                    </li>
                </ul>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="card border-0 shadow-lg">
                    <div style="background: linear-gradient(135deg, var(--primary-color) 0%, #c1121f 100%); 
                                height: 400px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-hospital" style="font-size: 15rem; color: rgba(255,255,255,0.1);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="py-5" style="background: var(--light-color);">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <p class="section-subtitle">Our Services</p>
            <h2>What We Offer</h2>
            <p class="text-muted max-width-md mx-auto">
                Complete blood banking services for hospitals, clinics, and individuals
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3" data-aos="fade-up">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-syringe"></i>
                    </div>
                    <h4 class="service-title">Blood Donation</h4>
                    <p class="service-desc">Register as a donor and contribute to saving lives in your community</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h4 class="service-title">Find Blood</h4>
                    <p class="service-desc">Search for available blood units of specific types in real-time</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-flask"></i>
                    </div>
                    <h4 class="service-title">Testing & Safety</h4>
                    <p class="service-desc">Advanced screening and testing for safety and quality assurance</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h4 class="service-title">Fast Delivery</h4>
                    <p class="service-desc">Quick and reliable delivery to hospitals and medical facilities</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Blood Search Section -->
<section id="search" class="search-section">
    <div class="container">
        <div class="text-center mb-4" data-aos="fade-up">
            <p class="section-subtitle">Search Blood</p>
            <h2>Find Available Blood Units</h2>
        </div>

        <div class="search-container" data-aos="fade-up">
            <div class="search-input-group">
                <select id="bloodType" class="form-select" style="flex: 1; min-width: 150px;">
                    <option value="">Select Blood Type</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                </select>
                <button class="search-btn" onclick="searchBlood()">
                    <i class="fas fa-search me-2"></i> Search
                </button>
            </div>

            <div id="searchResults" style="margin-top: 2rem;"></div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, #c1121f 100%); color: white;">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4" data-aos="fade-up">
                <div class="stats-card">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <div class="stat-number" data-target="5000">0</div>
                    <div class="stat-label">Active Donors</div>
                </div>
            </div>
            <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="stats-card">
                    <i class="fas fa-tint fa-3x mb-3"></i>
                    <div class="stat-number" data-target="25000">0</div>
                    <div class="stat-label">Units Stored</div>
                </div>
            </div>
            <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="stats-card">
                    <i class="fas fa-heart fa-3x mb-3"></i>
                    <div class="stat-number" data-target="50000">0</div>
                    <div class="stat-label">Lives Saved</div>
                </div>
            </div>
            <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="stats-card">
                    <i class="fas fa-hospital fa-3x mb-3"></i>
                    <div class="stat-number" data-target="150">0</div>
                    <div class="stat-label">Partner Hospitals</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section id="faq" class="py-5" style="background: white;">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <p class="section-subtitle">FAQ</p>
            <h2>Frequently Asked Questions</h2>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="accordion" id="faqAccordion">
                    <div class="faq-item" data-aos="fade-up">
                        <button class="faq-question" onclick="toggleFaq(this)">
                            <span>Who can donate blood?</span>
                            <i class="fas fa-plus faq-icon"></i>
                        </button>
                        <div class="faq-answer">
                            <p class="faq-text">
                                Generally, anyone between 18-65 years old, weighing at least 110 pounds, and in good health 
                                can donate blood. You must wait at least 2 months between donations.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item" data-aos="fade-up">
                        <button class="faq-question" onclick="toggleFaq(this)">
                            <span>How long does blood donation take?</span>
                            <i class="fas fa-plus faq-icon"></i>
                        </button>
                        <div class="faq-answer">
                            <p class="faq-text">
                                The entire process, including registration and post-donation rest, takes about 30-45 minutes. 
                                The actual donation process itself takes about 8-10 minutes.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item" data-aos="fade-up">
                        <button class="faq-question" onclick="toggleFaq(this)">
                            <span>Is blood donation safe?</span>
                            <i class="fas fa-plus faq-icon"></i>
                        </button>
                        <div class="faq-answer">
                            <p class="faq-text">
                                Yes, blood donation is completely safe. All equipment used is sterile and used only once. 
                                Your body replenishes the blood cells within 24 hours and plasma within 24-48 hours.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item" data-aos="fade-up">
                        <button class="faq-question" onclick="toggleFaq(this)">
                            <span>What are the eligibility criteria?</span>
                            <i class="fas fa-plus faq-icon"></i>
                        </button>
                        <div class="faq-answer">
                            <p class="faq-text">
                                You must be in good health, free from infectious diseases, have normal blood pressure, 
                                and not be on certain medications. A simple health screening will be done before donation.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item" data-aos="fade-up">
                        <button class="faq-question" onclick="toggleFaq(this)">
                            <span>How can I request blood?</span>
                            <i class="fas fa-plus faq-icon"></i>
                        </button>
                        <div class="faq-answer">
                            <p class="faq-text">
                                Hospitals and authorized medical professionals can submit blood requests through our admin panel. 
                                For emergency requests, please call our 24/7 hotline at 1-800-BLOOD-1.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// Search blood function
async function searchBlood() {
    const bloodType = document.getElementById('bloodType').value;
    
    if (!bloodType) {
        showAlert('Please select a blood type', 'warning');
        return;
    }

    const resultsDiv = document.getElementById('searchResults');
    resultsDiv.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';

    try {
        const response = await fetch(`/blood-bank/api/search_blood.php?blood_type=${encodeURIComponent(bloodType)}`);
        const data = await response.json();

        if (data.success && data.data.length > 0) {
            const item = data.data[0]; // Get the aggregated result
            const status = item.quantity_units > 20 ? 'status-high' : (item.quantity_units > 5 ? 'status-medium' : 'status-low');
            const statusText = item.quantity_units > 20 ? 'In Stock' : (item.quantity_units > 5 ? 'Low Stock' : 'Critical');
            
            // Get blood type ID for link
            const bloodTypeId = item.blood_group_id;
            
            let html = `
                <div class="result-item" style="padding: 1.5rem; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="flex: 1;">
                            <h5 style="margin: 0 0 0.5rem 0; color: #e63946;">
                                <i class="fas fa-tint me-2"></i>${item.blood_type}
                            </h5>
                            <p style="margin: 0; color: #666; font-size: 0.9rem;">
                                <span class="blood-status ${status}">${statusText}</span>
                            </p>
                        </div>
                        <div style="text-align: center; padding: 0 2rem;">
                            <div style="font-size: 2rem; font-weight: bold; color: #06d6a0;">
                                ${item.quantity_units}<br>
                                <span style="font-size: 0.9rem; color: #666;">Units Available</span>
                            </div>
                        </div>
                        <div>
                            <a href="/blood-bank/public/search_blood.php?blood_type_id=${bloodTypeId}" class="btn btn-primary" style="white-space: nowrap;">
                                <i class="fas fa-eye me-1"></i> View All
                            </a>
                        </div>
                    </div>
                </div>
            `;
            resultsDiv.innerHTML = html;
        } else {
            resultsDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>No blood available for this type. Please try another.</div>';
        }
    } catch (error) {
        console.error('Error:', error);
        resultsDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Error searching blood. Please try again.</div>';
    }
}

// FAQ toggle
function toggleFaq(element) {
    const faqItem = element.parentElement;
    
    // Close other items
    document.querySelectorAll('.faq-item.active').forEach(item => {
        if (item !== faqItem) {
            item.classList.remove('active');
        }
    });
    
    // Toggle current item
    faqItem.classList.toggle('active');
}

// Animate counters
function animateCounters() {
    const counters = document.querySelectorAll('[data-target]');
    const isInViewport = (element) => {
        const rect = element.getBoundingClientRect();
        return rect.top < window.innerHeight && rect.bottom > 0;
    };

    window.addEventListener('scroll', () => {
        counters.forEach(counter => {
            if (isInViewport(counter) && !counter.classList.contains('counted')) {
                counter.classList.add('counted');
                const target = parseInt(counter.getAttribute('data-target'));
                animateCounter(counter, target);
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', animateCounters);
</script>
