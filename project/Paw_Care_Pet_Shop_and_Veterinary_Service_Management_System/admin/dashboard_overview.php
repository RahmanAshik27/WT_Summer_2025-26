<?php
require_once "../includes/session.php";
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: ../login.php");
    exit;
}

$total_revenue = 0;
$total_orders = 0;
$pets_in_stock = 0;
$low_stock = 0;
$recent_orders = [];

$result = mysqli_query($conn, "SELECT COALESCE(SUM(total_amount), 0) AS total_revenue FROM orders WHERE payment_status = 'Paid'");
if ($result) $total_revenue = mysqli_fetch_assoc($result)["total_revenue"];

$result = mysqli_query($conn, "SELECT COUNT(*) AS total_orders FROM orders");
if ($result) $total_orders = mysqli_fetch_assoc($result)["total_orders"];

$result = mysqli_query($conn, "SELECT COALESCE(SUM(stock), 0) AS pets_in_stock FROM pets WHERE status = 'Available'");
if ($result) $pets_in_stock = mysqli_fetch_assoc($result)["pets_in_stock"];

$result = mysqli_query($conn, "SELECT COUNT(*) AS low_stock FROM products WHERE stock <= 5");
if ($result) $low_stock = mysqli_fetch_assoc($result)["low_stock"];

$sql = "SELECT o.order_id, o.total_amount, o.payment_status, u.full_name
        FROM orders o
        JOIN users u ON o.customer_id = u.user_id
        ORDER BY o.order_id DESC
        LIMIT 5";

$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $recent_orders[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Overview - PawCare</title>
    <link rel="stylesheet" href="../assets/css/admin-overview.css">
</head>
<body>

<div class="admin-page">

    <header class="top-header">
        <div>
            <h1>WELCOME BACK, MASTER!</h1>
            <p>👑 Your Pet Kingdom is under your command, King!</p>
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
                <h2>🐾 PET SHOP</h2>
            </div>

            <nav class="sidebar-menu">
                <a href="dashboard_overview.php" class="active">▣ Dashboard Overview</a>
                <a href="inventory.php">▤ Inventory Stock</a>
                <a href="sales_analytics.php" class="active">💰 Sales Analytics</a>
                <a href="#">🚚 Delivery Tracking</a>
                <a href="#">★ Customer Reviews</a>
            </nav>
        </aside>

        <main class="main-content">

            <div class="overview-heading">
                <h2>♟ SOMRAJJO OVERVIEW</h2>
                <p>Live Data: Connected to PawCare Database</p>
            </div>

            <div class="stat-grid">

                <div class="stat-card revenue-card">
                    <div class="stat-top">
                        <strong>৳<?= number_format((float)$total_revenue, 2) ?></strong>
                        <span>💰</span>
                    </div>
                    <p>TOTAL REVENUE</p>
                </div>

                <div class="stat-card orders-card">
                    <div class="stat-top">
                        <strong><?= (int)$total_orders ?></strong>
                        <span>📦</span>
                    </div>
                    <p>TOTAL ORDERS</p>
                </div>

                <div class="stat-card pets-card">
                    <div class="stat-top">
                        <strong><?= (int)$pets_in_stock ?></strong>
                        <span>🐾</span>
                    </div>
                    <p>PETS IN STOCK</p>
                </div>

                <div class="stat-card stock-card">
                    <div class="stat-top">
                        <strong><?= (int)$low_stock ?></strong>
                        <span>🔥</span>
                    </div>
                    <p>LOW STOCK ALERTS</p>
                </div>

            </div>

            <section class="activity-panel">
                <div class="activity-title">⚡ RECENT EMPIRE ACTIVITIES (LIVE)</div>

                <div class="activity-table">
                    <div class="table-row table-head">
                        <span>INV ID</span>
                        <span>CUSTOMER</span>
                        <span>BILL</span>
                        <span>STATUS</span>
                    </div>

                    <?php if (!empty($recent_orders)): ?>
                        <?php foreach ($recent_orders as $order): ?>
                            <div class="table-row">
                                <span>INV-<?= (int)$order["order_id"] ?></span>
                                <span><?= htmlspecialchars($order["full_name"]) ?></span>
                                <span>৳<?= number_format((float)$order["total_amount"], 2) ?></span>
                                <span><?= htmlspecialchars(ucfirst($order["payment_status"])) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-row">No recent orders found.</div>
                    <?php endif; ?>
                </div>
            </section>

        </main>

    </div>

    <footer class="admin-footer">
        🛡 POWERFUL PET MANAGEMENT ENGINE V2.0 LIVE | SYSTEM SECURED
    </footer>

</div>

<script src="../assets/js/admin-dashboard.js"></script>

</body>
</html>