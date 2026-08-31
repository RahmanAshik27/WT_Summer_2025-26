<?php

require_once "../includes/session.php";
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "delivery") {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION["user_id"];

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

$history_sql = "SELECT d.delivery_id, d.delivery_status, d.assigned_at, d.delivered_at, d.delivery_note, o.order_id, o.total_amount, o.delivery_address, o.order_date, u.full_name AS customer_name, u.phone AS customer_phone FROM deliveries d JOIN orders o ON d.order_id = o.order_id JOIN users u ON o.customer_id = u.user_id WHERE d.delivery_agent_id = ? AND o.delivery_method = ? AND d.delivery_status IN ('Delivered', 'Failed', 'Cancelled') ORDER BY d.assigned_at DESC";

$history_stmt = mysqli_prepare($conn, $history_sql);
mysqli_stmt_bind_param($history_stmt, "is", $user_id, $company_name);
mysqli_stmt_execute($history_stmt);

$history_result = mysqli_stmt_get_result($history_stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery History | PawCare</title>
    <link rel="stylesheet" href="../assets/css/delivery_history.css">
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

            <a href="assigned_orders.php">Assigned Orders</a>

            <a href="history.php" class="active">History</a>

            <a href="profile.php">
                Profile Settings
            </a>

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

                <p>Review your previous delivery activity.</p>

            </div>

            <div class="topbar-time">

                <span id="currentDate"></span>
                <span id="currentTime"></span>

            </div>

        </header>

        <main class="content">

            <div class="page-heading">

                <div>

                    <h1>Delivery History</h1>

                    <p>
                        View completed, failed and cancelled deliveries.
                    </p>

                </div>

                <div class="company-badge">
                    <?php echo htmlspecialchars($company_name); ?>
                </div>

            </div>

            <section class="history-panel">

                <div class="table-wrapper">

                    <table>

                        <thead>

                            <tr>
                                <th>ORDER</th>
                                <th>CUSTOMER</th>
                                <th>ADDRESS</th>
                                <th>AMOUNT</th>
                                <th>STATUS</th>
                                <th>DELIVERED AT</th>
                            </tr>

                        </thead>

                        <tbody>

                        <?php if (mysqli_num_rows($history_result) > 0): ?>

                            <?php while ($history = mysqli_fetch_assoc($history_result)): ?>

                                <tr>

                                    <td>

                                        <div class="order-id">
                                            #<?php echo (int)$history["order_id"]; ?>
                                        </div>

                                        <span class="small-text">
                                            <?php echo date("d M Y", strtotime($history["order_date"])); ?>
                                        </span>

                                    </td>

                                    <td>

                                        <div class="customer-name">
                                            <?php echo htmlspecialchars($history["customer_name"]); ?>
                                        </div>

                                        <span class="small-text">
                                            <?php echo htmlspecialchars($history["customer_phone"] ?? ""); ?>
                                        </span>

                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($history["delivery_address"]); ?>
                                    </td>

                                    <td>

                                        <div class="amount">
                                            <?php echo number_format($history["total_amount"], 2); ?> BDT
                                        </div>

                                    </td>

                                    <td>

                                        <?php
                                        $status_class = "status-default";

                                        if ($history["delivery_status"] === "Delivered") {
                                            $status_class = "status-delivered";
                                        } elseif ($history["delivery_status"] === "Failed") {
                                            $status_class = "status-failed";
                                        } elseif ($history["delivery_status"] === "Cancelled") {
                                            $status_class = "status-cancelled";
                                        }
                                        ?>

                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <?php echo htmlspecialchars($history["delivery_status"]); ?>
                                        </span>

                                    </td>

                                    <td>

                                        <?php if (!empty($history["delivered_at"])): ?>

                                            <?php echo date("d M Y, h:i A", strtotime($history["delivered_at"])); ?>

                                        <?php else: ?>

                                            <span class="not-available">N/A</span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="6" class="empty-data">
                                    No delivery history found.
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