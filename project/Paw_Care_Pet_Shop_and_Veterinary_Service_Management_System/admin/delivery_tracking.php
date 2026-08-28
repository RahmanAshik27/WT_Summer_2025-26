<?php

require_once "../includes/session.php";
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: ../login.php");
    exit;
}

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_delivery"])) {
    $delivery_id = (int)($_POST["delivery_id"] ?? 0);
    $delivery_status = $_POST["delivery_status"] ?? "";

    $allowed_status = ["Assigned", "Picked Up", "Out for Delivery", "Delivered", "Failed", "Cancelled"];

    if ($delivery_id <= 0 || !in_array($delivery_status, $allowed_status, true)) {
        $error_message = "Invalid delivery information.";
    } else {
        if ($delivery_status === "Delivered") {
            $stmt = mysqli_prepare($conn, "UPDATE deliveries SET delivery_status = ?, delivered_at = NOW() WHERE delivery_id = ?");
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE deliveries SET delivery_status = ?, delivered_at = NULL WHERE delivery_id = ?");
        }

        mysqli_stmt_bind_param($stmt, "si", $delivery_status, $delivery_id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) >= 0) {
            $success_message = "Delivery status updated successfully.";
        } else {
            $error_message = "Delivery status could not be updated.";
        }

        mysqli_stmt_close($stmt);
    }
}

$partners = [
    "Pathao Fast",
    "PetPanda Go",
    "Speed Fast",
    "Jhinku BD",
    "Shop Pickup"
];

$partner_stats = [];

foreach ($partners as $partner) {
    $partner_stats[$partner] = [
        "orders" => 0,
        "delivered" => 0
    ];
}

$sql = "SELECT delivery_method, COUNT(*) AS total_orders
        FROM orders
        GROUP BY delivery_method";

$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if (isset($partner_stats[$row["delivery_method"]])) {
            $partner_stats[$row["delivery_method"]]["orders"] = (int)$row["total_orders"];
        }
    }
}

$sql = "SELECT o.delivery_method, COUNT(*) AS delivered_count
        FROM deliveries d
        JOIN orders o ON d.order_id = o.order_id
        WHERE d.delivery_status = 'Delivered'
        GROUP BY o.delivery_method";

$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if (isset($partner_stats[$row["delivery_method"]])) {
            $partner_stats[$row["delivery_method"]]["delivered"] = (int)$row["delivered_count"];
        }
    }
}

$total_orders = 0;

foreach ($partner_stats as $partner) {
    $total_orders += $partner["orders"];
}

$deliveries = [];

$sql = "SELECT
            d.delivery_id,
            d.order_id,
            d.delivery_status,
            d.assigned_at,
            d.delivered_at,
            d.delivery_note,
            o.delivery_method,
            o.order_status,
            o.total_amount,
            u.full_name AS customer_name,
            agent.full_name AS agent_name
        FROM deliveries d
        JOIN orders o ON d.order_id = o.order_id
        JOIN users u ON o.customer_id = u.user_id
        JOIN users agent ON d.delivery_agent_id = agent.user_id
        ORDER BY d.delivery_id DESC";

$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $deliveries[] = $row;
    }
}

$partner_names = [];
$partner_values = [];

foreach ($partner_stats as $name => $data) {
    $partner_names[] = $name;
    $partner_values[] = $data["orders"];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Tracking - PawCare</title>
    <link rel="stylesheet" href="../assets/css/admin-delivery.css">
</head>
<body>

<div class="admin-page">

    <header class="top-header">
        <div>
            <h1>WELCOME BACK, MASTER!</h1>
            <p>✦ Your Pet Empire is under your command, King!</p>
        </div>

        <div class="date-time">
            <div id="currentDate"></div>
            <div id="currentTime"></div>
        </div>
    </header>

    <div class="admin-layout">

        <aside class="sidebar">
            <div class="logo-area">
                <img src="../assets/images/petLogo.jpeg" alt="PawCare Logo">
                <h3>🐾 PET SHOP</h3>
            </div>

            <nav>
                <a href="dashboard_overview.php">⌁ Dashboard Overview</a>
                <a href="inventory.php">▤ Inventory Stock</a>
                <a href="sales_analytics.php">💰 Sales Analytics</a>
                <a href="delivery_tracking.php" class="active">🚚 Delivery Tracking</a>
                <a href="customer_reviews.php" class="active">★ Customer Reviews</a>
            </nav>
        </aside>

        <main class="main-content">

            <h2 class="page-title">LOGISTICS INTELLIGENCE PARTNER HUB</h2>

            <?php if ($success_message): ?>
                <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <section class="delivery-top">

                <div class="analytics-card">
                    <h3>PARTNER ANALYTICS</h3>

                    <div class="partner-analytics">

                        <div class="donut-chart" id="deliveryChart">
                            <div class="donut-center">
                                <span id="deliveryTotal"><?php echo $total_orders; ?></span>
                                <small>TOTAL ORDERS</small>
                            </div>
                        </div>

                        <div class="partner-legend">
                            <?php foreach ($partner_stats as $partner_name => $data): ?>

                                <?php
                                $share = $total_orders > 0 ? round(($data["orders"] / $total_orders) * 100) : 0;
                                ?>

                                <div class="legend-row">
                                    <span class="legend-color"></span>

                                    <div>
                                        <strong><?php echo htmlspecialchars($partner_name); ?></strong>
                                        <p><?php echo $share; ?>% Share · <?php echo $data["orders"]; ?> Orders</p>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                        </div>

                    </div>
                </div>

                <div class="action-area">

                    <div class="action-card register-card">
                        <h3>REGISTER NEW CONTRACT</h3>
                        <input type="text" placeholder="Partner or contract name">
                        <button type="button">SEND ADD REQUEST</button>
                    </div>

                    <div class="action-card terminate-card">
                        <h3>TERMINATE CONTRACT</h3>
                        <input type="text" placeholder="Partner or contract name">
                        <button type="button">SEND TERMINATE REQUEST</button>
                    </div>

                </div>

            </section>

            <section class="metrics-section">
                <h3>REAL-TIME LOGISTICS PARTNER METRICS</h3>

                <div class="metrics-grid">

                    <?php foreach ($partner_stats as $partner_name => $data): ?>

                        <div class="metric-card">
                            <span><?php echo htmlspecialchars($partner_name); ?></span>
                            <h2><?php echo $data["delivered"]; ?></h2>
                            <p>COMPLETED JOBS</p>
                            <small><?php echo $data["orders"]; ?> Total Orders</small>
                        </div>

                    <?php endforeach; ?>

                </div>
            </section>

            <section class="tracking-section">

                <div class="tracking-heading">
                    <h3>ACTIVE DELIVERY TRACKING</h3>
                    <span><?php echo count($deliveries); ?> Records</span>
                </div>

                <div class="tracking-table-wrap">

                    <table>
                        <thead>
                            <tr>
                                <th>Delivery</th>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Agent</th>
                                <th>Partner</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Update</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php if (count($deliveries) > 0): ?>

                            <?php foreach ($deliveries as $delivery): ?>

                                <tr>
                                    <td>#<?php echo $delivery["delivery_id"]; ?></td>
                                    <td>#<?php echo $delivery["order_id"]; ?></td>
                                    <td><?php echo htmlspecialchars($delivery["customer_name"]); ?></td>
                                    <td><?php echo htmlspecialchars($delivery["agent_name"]); ?></td>
                                    <td><?php echo htmlspecialchars($delivery["delivery_method"]); ?></td>
                                    <td>৳<?php echo number_format($delivery["total_amount"], 2); ?></td>

                                    <td>
                                        <span class="status-badge status-<?php echo strtolower(str_replace(" ", "-", $delivery["delivery_status"])); ?>">
                                            <?php echo htmlspecialchars($delivery["delivery_status"]); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <form method="POST" class="status-form">
                                            <input type="hidden" name="delivery_id" value="<?php echo $delivery["delivery_id"]; ?>">

                                            <select name="delivery_status">
                                                <option value="Assigned" <?php if ($delivery["delivery_status"] === "Assigned") echo "selected"; ?>>Assigned</option>
                                                <option value="Picked Up" <?php if ($delivery["delivery_status"] === "Picked Up") echo "selected"; ?>>Picked Up</option>
                                                <option value="Out for Delivery" <?php if ($delivery["delivery_status"] === "Out for Delivery") echo "selected"; ?>>Out for Delivery</option>
                                                <option value="Delivered" <?php if ($delivery["delivery_status"] === "Delivered") echo "selected"; ?>>Delivered</option>
                                                <option value="Failed" <?php if ($delivery["delivery_status"] === "Failed") echo "selected"; ?>>Failed</option>
                                                <option value="Cancelled" <?php if ($delivery["delivery_status"] === "Cancelled") echo "selected"; ?>>Cancelled</option>
                                            </select>

                                            <button type="submit" name="update_delivery">UPDATE</button>
                                        </form>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="8" class="empty-row">No delivery records found.</td>
                            </tr>

                        <?php endif; ?>

                        </tbody>
                    </table>

                </div>

            </section>

        </main>

    </div>

    <footer>
        🔥 POWERFUL PET MANAGEMENT ENGINE V2.0 LIVE | SYSTEM SECURED
    </footer>

</div>

<script>
const deliveryPartnerNames = <?php echo json_encode($partner_names); ?>;
const deliveryPartnerValues = <?php echo json_encode($partner_values); ?>;
</script>

<script src="../assets/js/admin-dashboard.js"></script>
<script src="../assets/js/admin-delivery.js"></script>

</body>
</html>