<?php

require_once "../includes/session.php";
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "delivery") {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$delivery_id = (int)($_GET["id"] ?? 0);

$agent_sql = "SELECT u.full_name, da.company_name FROM users u JOIN delivery_agents da ON u.user_id = da.user_id WHERE u.user_id = ? AND da.status = 'Active' LIMIT 1";
$agent_stmt = mysqli_prepare($conn, $agent_sql);
mysqli_stmt_bind_param($agent_stmt, "i", $user_id);
mysqli_stmt_execute($agent_stmt);

$agent_result = mysqli_stmt_get_result($agent_stmt);
$agent = mysqli_fetch_assoc($agent_result);

if (!$agent) {
    header("Location: index.php");
    exit;
}

$agent_name = $agent["full_name"];
$company_name = $agent["company_name"];

if ($delivery_id <= 0) {
    header("Location: assigned_orders.php");
    exit;
}

$detail_sql = "SELECT d.delivery_id, d.delivery_status, d.assigned_at, d.delivered_at, d.delivery_note, o.order_id, o.total_amount, o.delivery_address, o.delivery_method, o.payment_method, o.payment_status, o.order_status, o.order_date, u.full_name AS customer_name, u.email AS customer_email, u.phone AS customer_phone FROM deliveries d JOIN orders o ON d.order_id = o.order_id JOIN users u ON o.customer_id = u.user_id WHERE d.delivery_id = ? AND d.delivery_agent_id = ? AND o.delivery_method = ? LIMIT 1";
$detail_stmt = mysqli_prepare($conn, $detail_sql);
mysqli_stmt_bind_param($detail_stmt, "iis", $delivery_id, $user_id, $company_name);
mysqli_stmt_execute($detail_stmt);

$detail_result = mysqli_stmt_get_result($detail_stmt);
$order = mysqli_fetch_assoc($detail_result);

if (!$order) {
    header("Location: assigned_orders.php");
    exit;
}

$item_sql = "SELECT item_name, item_type, price, quantity, subtotal FROM order_items WHERE order_id = ? ORDER BY order_item_id ASC";
$item_stmt = mysqli_prepare($conn, $item_sql);
mysqli_stmt_bind_param($item_stmt, "i", $order["order_id"]);
mysqli_stmt_execute($item_stmt);

$item_result = mysqli_stmt_get_result($item_stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details | PawCare</title>
    <link rel="stylesheet" href="../assets/css/delivery_order_details.css">
</head>

<body>

<div class="delivery-layout">

    <aside class="sidebar">

        <div class="sidebar-brand">

            <img src="../assets/images/petLogo.jpeg" alt="PawCare Logo">

            <h2>PAWCARE</h2>

            <p><?php echo htmlspecialchars($company_name); ?></p>

        </div>

        <nav class="sidebar-menu">

            <a href="dashboard.php">Dashboard</a>
            <a href="assigned_orders.php" class="active">Assigned Orders</a>
            <a href="history.php">History</a>
            <a href="profile.php">Profile Settings</a>

        </nav>

        <div class="sidebar-logout">
            <a href="../logout.php">Logout</a>
        </div>

    </aside>

    <div class="main-area">

        <header class="topbar">

            <div>

                <h3>
                    WELCOME BACK, <?php echo strtoupper(htmlspecialchars($agent_name)); ?>!
                </h3>

                <p>View assigned order information.</p>

            </div>

            <div class="topbar-time">

                <span id="currentDate"></span>
                <span id="currentTime"></span>

            </div>

        </header>

        <main class="content">

            <div class="page-heading">

                <div>

                    <a href="assigned_orders.php" class="back-link">
                        ← BACK TO ASSIGNED ORDERS
                    </a>

                    <h1>
                        Order #<?php echo (int)$order["order_id"]; ?>
                    </h1>

                    <p>Complete delivery and customer information.</p>

                </div>

                <span class="status-badge">
                    <?php echo htmlspecialchars($order["delivery_status"]); ?>
                </span>

            </div>

            <div class="details-grid">

                <section class="info-card">

                    <div class="card-title">
                        CUSTOMER INFORMATION
                    </div>

                    <div class="info-row">

                        <span>Customer Name</span>

                        <strong>
                            <?php echo htmlspecialchars($order["customer_name"]); ?>
                        </strong>

                    </div>

                    <div class="info-row">

                        <span>Phone Number</span>

                        <strong>
                            <?php echo htmlspecialchars($order["customer_phone"] ?: "Not provided"); ?>
                        </strong>

                    </div>

                    <div class="info-row">

                        <span>Email Address</span>

                        <strong>
                            <?php echo htmlspecialchars($order["customer_email"]); ?>
                        </strong>

                    </div>

                    <div class="info-row address-row">

                        <span>Delivery Address</span>

                        <strong>
                            <?php echo htmlspecialchars($order["delivery_address"]); ?>
                        </strong>

                    </div>

                </section>

                <section class="info-card">

                    <div class="card-title">
                        DELIVERY INFORMATION
                    </div>

                    <div class="info-row">

                        <span>Delivery Company</span>

                        <strong>
                            <?php echo htmlspecialchars($order["delivery_method"]); ?>
                        </strong>

                    </div>

                    <div class="info-row">

                        <span>Delivery Status</span>

                        <strong>
                            <?php echo htmlspecialchars($order["delivery_status"]); ?>
                        </strong>

                    </div>

                    <div class="info-row">

                        <span>Order Status</span>

                        <strong>
                            <?php echo htmlspecialchars($order["order_status"]); ?>
                        </strong>

                    </div>

                    <div class="info-row">

                        <span>Assigned At</span>

                        <strong>
                            <?php echo date("d M Y, h:i A", strtotime($order["assigned_at"])); ?>
                        </strong>

                    </div>

                    <div class="info-row">

                        <span>Delivered At</span>

                        <strong>
                            <?php echo !empty($order["delivered_at"]) ? date("d M Y, h:i A", strtotime($order["delivered_at"])) : "Not delivered yet"; ?>
                        </strong>

                    </div>

                </section>

                <section class="info-card">

                    <div class="card-title">
                        PAYMENT INFORMATION
                    </div>

                    <div class="info-row">

                        <span>Payment Method</span>

                        <strong>
                            <?php echo htmlspecialchars($order["payment_method"]); ?>
                        </strong>

                    </div>

                    <div class="info-row">

                        <span>Payment Status</span>

                        <strong>
                            <?php echo htmlspecialchars($order["payment_status"]); ?>
                        </strong>

                    </div>

                    <div class="info-row">

                        <span>Order Date</span>

                        <strong>
                            <?php echo date("d M Y, h:i A", strtotime($order["order_date"])); ?>
                        </strong>

                    </div>

                    <div class="info-row">

                        <span>Total Bill</span>

                        <strong class="total-amount">
                            <?php echo number_format($order["total_amount"], 2); ?> BDT
                        </strong>

                    </div>

                </section>

                <section class="info-card">

                    <div class="card-title">
                        DELIVERY NOTE
                    </div>

                    <div class="note-box">

                        <?php if (!empty($order["delivery_note"])): ?>

                            <?php echo nl2br(htmlspecialchars($order["delivery_note"])); ?>

                        <?php else: ?>

                            No delivery note added.

                        <?php endif; ?>

                    </div>

                </section>

            </div>

            <section class="items-card">

                <div class="card-title">
                    ORDER ITEMS
                </div>

                <div class="table-wrapper">

                    <table>

                        <thead>

                            <tr>
                                <th>ITEM</th>
                                <th>TYPE</th>
                                <th>PRICE</th>
                                <th>QUANTITY</th>
                                <th>SUBTOTAL</th>
                            </tr>

                        </thead>

                        <tbody>

                        <?php if (mysqli_num_rows($item_result) > 0): ?>

                            <?php while ($item = mysqli_fetch_assoc($item_result)): ?>

                                <tr>

                                    <td>
                                        <?php echo htmlspecialchars($item["item_name"]); ?>
                                    </td>

                                    <td>
                                        <?php echo ucfirst(htmlspecialchars($item["item_type"])); ?>
                                    </td>

                                    <td>
                                        <?php echo number_format($item["price"], 2); ?> BDT
                                    </td>

                                    <td>
                                        <?php echo (int)$item["quantity"]; ?>
                                    </td>

                                    <td>
                                        <?php echo number_format($item["subtotal"], 2); ?> BDT
                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="5" class="empty-data">
                                    No item information found for this order.
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