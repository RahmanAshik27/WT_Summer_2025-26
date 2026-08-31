<?php

require_once "../includes/session.php";
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "delivery") {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$sql = "SELECT u.full_name, u.username, da.company_name FROM users u JOIN delivery_agents da ON u.user_id = da.user_id WHERE u.user_id = ? AND da.status = 'Active' LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$agent = mysqli_fetch_assoc($result);

if (!$agent) {
    header("Location: index.php");
    exit;
}

$agent_name = $agent["full_name"];
$company_name = $agent["company_name"];

// Dashboard statistics
$stats_sql = "SELECT COUNT(*) AS total_assigned, SUM(CASE WHEN d.delivery_status = 'Out for Delivery' THEN 1 ELSE 0 END) AS out_for_delivery, SUM(CASE WHEN d.delivery_status = 'Delivered' AND DATE(d.delivered_at) = CURDATE() THEN 1 ELSE 0 END) AS delivered_today, SUM(CASE WHEN d.delivery_status = 'Delivered' THEN 1 ELSE 0 END) AS total_delivered FROM deliveries d JOIN orders o ON d.order_id = o.order_id WHERE d.delivery_agent_id = ? AND o.delivery_method = ?";

$stats_stmt = mysqli_prepare($conn, $stats_sql);
mysqli_stmt_bind_param($stats_stmt, "is", $user_id, $company_name);
mysqli_stmt_execute($stats_stmt);

$stats_result = mysqli_stmt_get_result($stats_stmt);
$stats = mysqli_fetch_assoc($stats_result);

$total_assigned = (int)($stats["total_assigned"] ?? 0);
$out_for_delivery = (int)($stats["out_for_delivery"] ?? 0);
$delivered_today = (int)($stats["delivered_today"] ?? 0);
$total_delivered = (int)($stats["total_delivered"] ?? 0);

if ($total_assigned > 0) {
    $success_rate = round(($total_delivered / $total_assigned) * 100);
} else {
    $success_rate = 0;
}

// Recent assigned orders
$order_sql = "SELECT o.order_id, o.total_amount, o.delivery_address, u.full_name AS customer_name, d.delivery_status FROM deliveries d JOIN orders o ON d.order_id = o.order_id JOIN users u ON o.customer_id = u.user_id WHERE d.delivery_agent_id = ? AND o.delivery_method = ? ORDER BY d.assigned_at DESC LIMIT 5";

$order_stmt = mysqli_prepare($conn, $order_sql);
mysqli_stmt_bind_param($order_stmt, "is", $user_id, $company_name);
mysqli_stmt_execute($order_stmt);

$order_result = mysqli_stmt_get_result($order_stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Dashboard | PawCare</title>
    <link rel="stylesheet" href="../assets/css/delivery_dashboard.css">
</head>

<body>

<div class="dashboard-layout">

    <aside class="sidebar">

        <div class="sidebar-brand">

            <img src="../assets/images/petLogo.jpeg" alt="PawCare Logo">

            <h2>PAWCARE</h2>

            <p>
                <?php echo htmlspecialchars($company_name); ?>
            </p>

        </div>

        <nav class="sidebar-menu">

            <a href="dashboard.php" class="active">
                Dashboard
            </a>

            <a href="assigned_orders.php">
                Assigned Orders
            </a>

            <a href="history.php">
                History
            </a>

            <a href="#">
                Profile Settings
            </a>

        </nav>

        <div class="sidebar-logout">

            <a href="../logout.php">
                Logout
            </a>

        </div>

    </aside>

    <div class="main-area">

        <header class="dashboard-topbar">

            <div>

                <h3>
                    WELCOME BACK, <?php echo strtoupper(htmlspecialchars($agent_name)); ?>!
                </h3>

                <p>
                    Ready for today's deliveries?
                </p>

            </div>

            <div class="topbar-time">

                <span id="currentDate"></span>

                <span id="currentTime"></span>

            </div>

        </header>

        <main class="dashboard-content">

            <div class="page-heading">

                <h1>
                    DELIVERY AGENT DASHBOARD
                </h1>

                <p>
                    Live Data: Connected to PawCare Database
                </p>

            </div>

            <div class="stats-container">

                <div class="stat-card">

                    <p>
                        TOTAL ASSIGNED
                    </p>

                    <h2>
                        <?php echo $total_assigned; ?>
                    </h2>

                    <span>
                        Total delivery orders
                    </span>

                </div>

                <div class="stat-card">

                    <p>
                        OUT FOR DELIVERY
                    </p>

                    <h2>
                        <?php echo $out_for_delivery; ?>
                    </h2>

                    <span>
                        Currently on the way
                    </span>

                </div>

                <div class="stat-card">

                    <p>
                        DELIVERED TODAY
                    </p>

                    <h2>
                        <?php echo $delivered_today; ?>
                    </h2>

                    <span>
                        Completed today
                    </span>

                </div>

                <div class="stat-card">

                    <p>
                        SUCCESS RATE
                    </p>

                    <h2>
                        <?php echo $success_rate; ?>%
                    </h2>

                    <span>
                        Delivery performance
                    </span>

                </div>

            </div>

            <section class="recent-orders">

                <div class="section-header">

                    <div>

                        <h2>
                            Recent Assigned Orders
                        </h2>

                        <p>
                            Your latest delivery assignments
                        </p>

                    </div>

                <a href="assigned_orders.php">
                    VIEW ALL ORDERS
                </a>

                </div>

                <div class="table-wrapper">

                    <table>

                        <thead>

                            <tr>
                                <th>ORDER ID</th>
                                <th>CUSTOMER</th>
                                <th>ADDRESS</th>
                                <th>BILL</th>
                                <th>STATUS</th>
                                <th>ACTION</th>
                            </tr>

                        </thead>

                        <tbody>

                        <?php if (mysqli_num_rows($order_result) > 0): ?>

                            <?php while ($order = mysqli_fetch_assoc($order_result)): ?>

                                <tr>

                                    <td>
                                        #<?php echo (int)$order["order_id"]; ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($order["customer_name"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($order["delivery_address"]); ?>
                                    </td>

                                    <td>
                                        <?php echo number_format($order["total_amount"], 2); ?> BDT
                                    </td>

                                    <td>

                                        <span class="delivery-status">
                                            <?php echo htmlspecialchars($order["delivery_status"]); ?>
                                        </span>

                                    </td>

                                    <td>

                                        <?php if ($order["delivery_status"] === "Assigned"): ?>

                                            <button type="button" class="action-btn">
                                                START DELIVERY
                                            </button>

                                        <?php elseif ($order["delivery_status"] === "Out for Delivery"): ?>

                                            <button type="button" class="action-btn">
                                                MARK DELIVERED
                                            </button>

                                        <?php elseif ($order["delivery_status"] === "Delivered"): ?>

                                            <span class="completed-text">
                                                COMPLETED
                                            </span>

                                        <?php else: ?>

                                            <span>-</span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="6" class="empty-data">
                                    No assigned orders found.
                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </section>

        </main>

    </div>

</div>

<footer class="delivery-footer">
    POWERFUL PET MANAGEMENT ENGINE V2.0 LIVE | SYSTEM SECURED
</footer>

<script>

function updateDateTime() {

    const now = new Date();

    document.getElementById("currentDate").textContent = now.toLocaleDateString("en-US", {
        weekday: "short",
        month: "short",
        day: "numeric",
        year: "numeric"
    });

    document.getElementById("currentTime").textContent = now.toLocaleTimeString("en-US", {
        hour: "2-digit",
        minute: "2-digit"
    });
}

updateDateTime();

setInterval(updateDateTime, 1000);

</script>

</body>

</html>