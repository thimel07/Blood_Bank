<?php
require_once __DIR__ . '/db_connect.php';
startSecureSession();

$currentUser = isLoggedIn() ? getCurrentUser() : null;
$isAdmin = $currentUser && in_array($currentUser['role'], ['admin', 'staff']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : ''; ?>Blood Bank Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- AOS (Animate On Scroll) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/blood-bank/assets/css/style.css">
    
    <style>
        :root {
            --primary-color: #e63946;
            --secondary-color: #ffffff;
            --accent-color: #f1f1f1;
            --dark-color: #2d3436;
            --light-color: #f8f9fa;
            --success-color: #06d6a0;
            --warning-color: #ffd166;
            --danger-color: #ef476f;
            --info-color: #118ab2;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--light-color);
            color: var(--dark-color);
            overflow-x: hidden;
        }

        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Loading Spinner */
        .spinner-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .spinner-circle {
            width: 50px;
            height: 50px;
            border: 4px solid var(--accent-color);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Navbar Styling */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #c1121f 100%);
            box-shadow: 0 4px 20px rgba(230, 57, 70, 0.15);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--secondary-color) !important;
            letter-spacing: 0.5px;
        }

        .navbar-brand i {
            margin-right: 8px;
            color: var(--accent-color);
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            margin: 0 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-link:hover {
            color: var(--secondary-color) !important;
            transform: translateY(-2px);
        }

        .nav-link.active {
            color: var(--accent-color) !important;
            border-bottom: 2px solid var(--accent-color);
        }

        /* Sidebar Styling */
        .sidebar {
            background: linear-gradient(180deg, var(--dark-color) 0%, #34495e 100%);
            min-height: 100vh;
            color: var(--secondary-color);
            padding: 2rem 0;
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-brand {
            padding: 2rem 1.5rem;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1rem;
        }

        .sidebar-brand h5 {
            margin: 0;
            font-weight: 700;
            color: var(--primary-color);
        }

        .sidebar-brand.collapsed h5 {
            display: none;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin: 0.5rem 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover {
            background: rgba(230, 57, 70, 0.1);
            color: var(--secondary-color);
            border-left-color: var(--primary-color);
            padding-left: 1.7rem;
        }

        .sidebar-menu a.active {
            background: rgba(230, 57, 70, 0.15);
            color: var(--secondary-color);
            border-left-color: var(--primary-color);
        }

        .sidebar-menu i {
            width: 24px;
            margin-right: 1rem;
            text-align: center;
        }

        .sidebar.collapsed .sidebar-menu a span {
            display: none;
        }

        /* Admin Layout */
        .admin-container {
            margin-left: 280px;
            transition: all 0.3s ease;
        }

        .admin-container.sidebar-collapsed {
            margin-left: 80px;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
            transform: translateY(-4px);
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #c1121f 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(230, 57, 70, 0.3);
            color: white;
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #06a070 100%);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(6, 214, 160, 0.3);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning-color) 0%, #fca311 100%);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #f15a4a 100%);
        }

        /* Tables */
        .table {
            margin-bottom: 0;
        }

        .table thead {
            background: var(--accent-color);
            font-weight: 600;
        }

        .table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #ddd;
        }

        .table tbody tr:hover {
            background: rgba(230, 57, 70, 0.05);
        }

        /* Forms */
        .form-control, .form-select {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(230, 57, 70, 0.25);
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }

        /* Badges */
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-active {
            background-color: rgba(6, 214, 160, 0.2);
            color: #06a070;
        }

        .badge-inactive {
            background-color: rgba(200, 200, 200, 0.2);
            color: #666;
        }

        .badge-pending {
            background-color: rgba(255, 209, 102, 0.2);
            color: #f59e0b;
        }

        .badge-approved {
            background-color: rgba(6, 214, 160, 0.2);
            color: #06a070;
        }

        .badge-rejected {
            background-color: rgba(239, 71, 111, 0.2);
            color: #ef476f;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .fadeInUp {
            animation: fadeInUp 0.6s ease forwards;
        }

        .slideInLeft {
            animation: slideInLeft 0.6s ease forwards;
        }

        /* Toast Notifications */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
            animation: slideInRight 0.3s ease;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(400px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                padding: 0;
            }

            .sidebar.show {
                width: 280px;
            }

            .admin-container {
                margin-left: 0;
            }

            .navbar-toggler {
                color: white;
            }
        }
    </style>
</head>
<body>
