<?php

require_once "../includes/session.php";
require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION["role"] !== "customer") {
    header("Location: ../login.php");
    exit;
}

$customer_id = (int) $_SESSION["user_id"];

$order_sql = "SELECT order_id, total_amount, delivery_method, payment_method, payment_status, order_status, order_date FROM orders WHERE customer_id = ? ORDER BY order_date DESC";

$order_stmt = mysqli_prepare($conn, $order_sql);
mysqli_stmt_bind_param($order_stmt, "i", $customer_id);
mysqli_stmt_execute($order_stmt);
$order_result = mysqli_stmt_get_result($order_stmt);

$orders = [];

while ($order = mysqli_fetch_assoc($order_result)) {
    $orders[] = $order;
}

mysqli_stmt_close($order_stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | PawCare</title>
    <link rel="stylesheet" href="../assets/css/customer-orders.css">
</head>

<body>

<div class="orders-page">

    <header class="orders-header">
        <div>
            <h1>MY ORDERS</h1>
            <p>Complete PawCare order history</p>
        </div>

        <a href="profile.php">← BACK TO PROFILE</a>
    </header>

    <main class="orders-content">

        <div class="orders-summary">
            <div>
                <span>Total Orders</span>
                <strong><?php echo count($orders); ?></strong>
            </div>
        </div>

        <?php if (empty($orders)): ?>

            <div class="empty-orders">
                <h2>No Orders Yet</h2>
                <p>Your PawCare orders will appear here.</p>
                <a href="dashboard.php">START SHOPPING</a>
            </div>

        <?php else: ?>

            <div class="orders-list">

                <?php foreach ($orders as $order): ?>

                    <article class="history-order-card">

                        <div class="order-main-info">

                            <div>
                                <small>ORDER ID</small>
                                <strong>#<?php echo (int) $order["order_id"]; ?></strong>
                            </div>

                            <div>
                                <small>DATE</small>
                                <strong><?php echo date("d M Y", strtotime($order["order_date"])); ?></strong>
                            </div>

                            <div>
                                <small>TOTAL</small>
                                <strong class="history-price">৳<?php echo number_format($order["total_amount"], 2); ?></strong>
                            </div>

                            <div>
                                <small>ORDER STATUS</small>
                                <span class="history-status"><?php echo htmlspecialchars($order["order_status"]); ?></span>
                            </div>

                        </div>

                        <div class="order-secondary-info">

                            <span>
                                🚚 <?php echo htmlspecialchars($order["delivery_method"]); ?>
                            </span>

                            <span>
                                💳 <?php echo htmlspecialchars($order["payment_method"]); ?>
                            </span>

                            <span>
                                Payment: <?php echo htmlspecialchars($order["payment_status"]); ?>
                            </span>

                        </div>

                        <div class="order-history-actions">
                            <a href="order_details.php?order_id=<?php echo (int) $order["order_id"]; ?>">VIEW DETAILS</a>
                            <a href="order_success.php?order_id=<?php echo (int) $order["order_id"]; ?>" class="receipt-history-btn">VIEW RECEIPT</a>
                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </main>

</div>

</body>

</html>