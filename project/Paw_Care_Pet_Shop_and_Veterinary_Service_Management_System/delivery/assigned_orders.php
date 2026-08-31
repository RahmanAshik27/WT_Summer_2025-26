<?php

require_once "../includes/session.php";
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "delivery") {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$agent_sql = "SELECT u.full_name, u.username, da.company_name FROM users u JOIN delivery_agents da ON u.user_id = da.user_id WHERE u.user_id = ? AND da.status = 'Active' LIMIT 1";
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

$search = trim($_GET["search"] ?? "");
$status = trim($_GET["status"] ?? "");

$allowed_status = [
    "Assigned",
    "Out for Delivery",
    "Delivered",
    "Failed",
    "Cancelled"
];

if ($status !== "" && !in_array($status, $allowed_status)) {
    $status = "";
}

$order_sql = "SELECT d.delivery_id, d.delivery_status, d.assigned_at, d.delivery_note, o.order_id, o.total_amount, o.delivery_address, o.order_status, o.payment_method, o.payment_status, o.order_date, u.full_name AS customer_name, u.phone AS customer_phone FROM deliveries d JOIN orders o ON d.order_id = o.order_id JOIN users u ON o.customer_id = u.user_id WHERE d.delivery_agent_id = ? AND o.delivery_method = ?";

$params = [$user_id, $company_name];
$types = "is";

if ($search !== "") {
    $order_sql .= " AND (CAST(o.order_id AS CHAR) LIKE ? OR u.full_name LIKE ?)";
    $search_value = "%" . ltrim($search, "#") . "%";
    $customer_search = "%" . $search . "%";

    $params[] = $search_value;
    $params[] = $customer_search;
    $types .= "ss";
}

if ($status !== "") {
    $order_sql .= " AND d.delivery_status = ?";
    $params[] = $status;
    $types .= "s";
}

$order_sql .= " ORDER BY d.assigned_at DESC";

$order_stmt = mysqli_prepare($conn, $order_sql);
mysqli_stmt_bind_param($order_stmt, $types, ...$params);
mysqli_stmt_execute($order_stmt);

$order_result = mysqli_stmt_get_result($order_stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigned Orders | PawCare</title>
    <link rel="stylesheet" href="../assets/css/delivery_assigned_orders.css">
</head>

<body>

<div class="delivery-layout">

    <aside class="sidebar">

        <div class="sidebar-brand">

            <img src="../assets/images/petLogo.jpeg" alt="PawCare Logo">

            <h2>PAWCARE</h2>

            <p>
                <?php echo htmlspecialchars($company_name); ?>
            </p>

        </div>

        <nav class="sidebar-menu">

            <a href="dashboard.php">
                Dashboard
            </a>

            <a href="assigned_orders.php" class="active">
                Assigned Orders
            </a>

            <a href="#">
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

        <header class="topbar">

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

        <main class="content">

            <div class="page-heading">

                <div>

                    <h1>
                        Assigned Orders
                    </h1>

                    <p>
                        Manage and update your assigned deliveries.
                    </p>

                </div>

                <div class="company-badge">
                    <?php echo htmlspecialchars($company_name); ?>
                </div>

            </div>

            <form method="GET" action="assigned_orders.php" class="filter-box">

                <input
                    type="text"
                    name="search"
                    placeholder="Search Order ID / Customer..."
                    value="<?php echo htmlspecialchars($search); ?>"
                >

                <select name="status">

                    <option value="">
                        All Statuses
                    </option>

                    <option value="Assigned" <?php if ($status === "Assigned") echo "selected"; ?>>
                        Assigned
                    </option>

                    <option value="Out for Delivery" <?php if ($status === "Out for Delivery") echo "selected"; ?>>
                        Out for Delivery
                    </option>

                    <option value="Delivered" <?php if ($status === "Delivered") echo "selected"; ?>>
                        Delivered
                    </option>

                    <option value="Failed" <?php if ($status === "Failed") echo "selected"; ?>>
                        Failed
                    </option>

                    <option value="Cancelled" <?php if ($status === "Cancelled") echo "selected"; ?>>
                        Cancelled
                    </option>

                </select>

                <button type="submit" class="filter-btn">
                    SEARCH
                </button>

                <a href="assigned_orders.php" class="reset-btn">
                    RESET
                </a>

            </form>

            <section class="orders-panel">

                <div class="table-wrapper">

                    <table>

                        <thead>

                            <tr>
                                <th>ORDER</th>
                                <th>CUSTOMER</th>
                                <th>ADDRESS</th>
                                <th>AMOUNT</th>
                                <th>STATUS</th>
                                <th>ACTION</th>
                            </tr>

                        </thead>

                        <tbody>

                        <?php if (mysqli_num_rows($order_result) > 0): ?>

                            <?php while ($order = mysqli_fetch_assoc($order_result)): ?>

                                <tr>

                                    <td>

                                        <div class="order-id">
                                            #<?php echo (int)$order["order_id"]; ?>
                                        </div>

                                        <span class="order-date">
                                            <?php echo date("d M Y", strtotime($order["order_date"])); ?>
                                        </span>

                                    </td>

                                    <td>

                                        <div class="customer-name">
                                            <?php echo htmlspecialchars($order["customer_name"]); ?>
                                        </div>

                                        <span class="customer-phone">
                                            <?php echo htmlspecialchars($order["customer_phone"] ?? ""); ?>
                                        </span>

                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($order["delivery_address"]); ?>
                                    </td>

                                    <td>

                                        <div class="amount">
                                            <?php echo number_format($order["total_amount"], 2); ?> BDT
                                        </div>

                                        <span class="payment-status">
                                            <?php echo htmlspecialchars($order["payment_status"]); ?>
                                        </span>

                                    </td>

                                    <td>

                                        <?php
                                        $status_class = "status-default";

                                        if ($order["delivery_status"] === "Assigned") {
                                            $status_class = "status-assigned";
                                        } elseif ($order["delivery_status"] === "Out for Delivery") {
                                            $status_class = "status-out";
                                        } elseif ($order["delivery_status"] === "Delivered") {
                                            $status_class = "status-delivered";
                                        } elseif ($order["delivery_status"] === "Failed") {
                                            $status_class = "status-failed";
                                        } elseif ($order["delivery_status"] === "Cancelled") {
                                            $status_class = "status-cancelled";
                                        }
                                        ?>

                                        <span class="status-badge <?php echo $status_class; ?>">
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

                                        <?php elseif ($order["delivery_status"] === "Failed"): ?>

                                            <span class="failed-text">
                                                FAILED
                                            </span>

                                        <?php elseif ($order["delivery_status"] === "Cancelled"): ?>

                                            <span class="cancelled-text">
                                                CANCELLED
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

                                    <?php if ($search !== "" || $status !== ""): ?>

                                        No matching orders found.

                                    <?php else: ?>

                                        No assigned orders found.

                                    <?php endif; ?>

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