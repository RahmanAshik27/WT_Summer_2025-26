<?php
require_once "includes/session.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>PawCare | Pet Shop and Veterinary Service</title>

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/home.css">
</head>

<body class="home-page">

    
         <!-- TOP BAR -->
    
    <header class="top-bar">

        <div class="top-brand">
            <span class="brand-icon">🐾</span>
            <span>
                PawCare – Pet Shop and Veterinary Service Management System
            </span>
        </div>

        <div class="window-dots">
            <span></span>
            <span></span>
            <span></span>
        </div>

    </header>


    
         <!-- MAIN PAGE -->
    
    <main class="home-container">

        <!-- LEFT SIDEBAR -->
        <aside class="home-sidebar">

            <div class="sidebar-brand">

                <img
                    src="assets/images/petLogo.jpeg"
                    alt="PawCare Logo"
                    class="sidebar-logo">

                <h2>
                    <span>🐾</span>
                    PAWCARE
                </h2>

            </div>


            <nav class="sidebar-menu">

                <nav class="sidebar-menu">

                <a href="login.php" class="menu-btn">
                    Admin Access
                </a>

                <a href="login.php" class="menu-btn">
                    Customer Portal
                </a>

                <a href="#" class="menu-btn">
                    Pet Reviews
                </a>

                <a href="#" class="menu-btn">
                    Specialist Doctors
                </a>

                <a href="login.php" class="menu-btn">
                    Delivery Man Portal
                </a>

                <a href="#" class="menu-btn license-btn">
                    Pet License
                </a>


            </nav>

        </aside>


        <!-- RIGHT CONTENT -->
        <section class="home-content">

            <div class="welcome-header">

                <span class="welcome-icon">🐾</span>

                <h1>
                    Welcome to Our Premium
                    <span>Pet Shop</span>
                </h1>

            </div>


            <div class="hero-area">

                <img
                    src="assets/images/PetShopWallpaper.jpg"
                    alt="PawCare Pet Shop"
                    class="hero-image">

            </div>

        </section>

    </main>


    
         <!-- FOOTER -->
    
    <footer class="home-footer">

        🐾 "Pets are not our whole life..." |
        PawCare © 2026

    </footer>


    <script src="assets/js/main.js"></script>

</body>

</html>