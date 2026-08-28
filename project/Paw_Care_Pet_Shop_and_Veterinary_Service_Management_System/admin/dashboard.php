<?php
require_once "../includes/session.php";
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: ../login.php");
    exit;
}

$admin_name = $_SESSION["full_name"] ?? "Master";
$username = $_SESSION["username"] ?? "Admin";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PawCare</title>
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
</head>
<body>

<div class="admin-page">

    <header class="top-header">
        <div>
            <h1>WELCOME BACK,<br>MASTER!</h1>
            <p>👑 Your PawCare Kingdom is under your command!</p>
        </div>

        <div class="header-time">
            <span id="currentDate"></span>
            <span id="currentTime"></span>
        </div>
    </header>

    <div class="admin-layout">

        <aside class="sidebar">
            <div class="brand-area">
                <div class="brand-logo">🐾</div>
                <h2>PAWCARE</h2>
            </div>

            <nav class="sidebar-menu">
                <a href="dashboard_overview.php">▣ Dashboard Overview</a>
                <a href="inventory.php">▤ Inventory Stock</a>
                <a href="sales_analytics.php" class="active">💰 Sales Analytics</a>
                <a href="delivery_tracking.php">🚚 Delivery Tracking</a>
                <a href="customer_reviews.php" class="active">★ Customer Reviews</a>
            </nav>

            <div class="sidebar-user">
                <p><?= htmlspecialchars($admin_name) ?></p>
                <span>@<?= htmlspecialchars($username) ?></span>
                <a href="../logout.php">Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <h2 class="overview-title">🏰 PAWCARE OVERVIEW</h2>

            <div class="overview-banner">
                <img src="../assets/images/admin_wallpaper.png" alt="PawCare Admin">
            </div>
        </main>

    </div>

    <footer class="admin-footer">
        🛡 POWERFUL PET MANAGEMENT ENGINE V2.0 &nbsp; | &nbsp; SYSTEM SECURED
    </footer>

</div>

<script src="../assets/js/admin-dashboard.js"></script>

</body>
</html>