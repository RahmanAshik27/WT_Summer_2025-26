<?php

require_once "../includes/session.php";
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: ../login.php");
    exit;
}

$total_revenue = 0;
$total_orders = 0;
$paid_orders = 0;
$average_order_value = 0;

$result = mysqli_query($conn, "SELECT COALESCE(SUM(total_amount), 0) AS total_revenue FROM orders WHERE payment_status = 'Paid'");
if ($result) $total_revenue = mysqli_fetch_assoc($result)["total_revenue"];

$result = mysqli_query($conn, "SELECT COUNT(*) AS total_orders FROM orders");
if ($result) $total_orders = mysqli_fetch_assoc($result)["total_orders"];

$result = mysqli_query($conn, "SELECT COUNT(*) AS paid_orders FROM orders WHERE payment_status = 'Paid'");
if ($result) $paid_orders = mysqli_fetch_assoc($result)["paid_orders"];

if ($paid_orders > 0) $average_order_value = $total_revenue / $paid_orders;

$category_sales = [];

$sql = "SELECT
            CASE
                WHEN oi.item_type = 'pet' THEN pc.category_name
                WHEN oi.item_type = 'product' THEN prc.category_name
            END AS category_name,
            SUM(oi.quantity) AS total_sold
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.order_id
        LEFT JOIN pets p ON oi.item_type = 'pet' AND oi.item_id = p.pet_id
        LEFT JOIN pet_categories pc ON p.category_id = pc.category_id
        LEFT JOIN products pr ON oi.item_type = 'product' AND oi.item_id = pr.product_id
        LEFT JOIN product_categories prc ON pr.category_id = prc.category_id
        WHERE o.payment_status = 'Paid'
        GROUP BY category_name
        HAVING category_name IS NOT NULL
        ORDER BY total_sold DESC";

$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $category_sales[] = $row;
    }
}

$top_items = [];

$sql = "SELECT
            oi.item_type,
            oi.item_id,
            SUM(oi.quantity) AS total_sold,
            CASE
                WHEN oi.item_type = 'pet' THEN p.pet_name
                WHEN oi.item_type = 'product' THEN pr.product_name
            END AS item_name,
            CASE
                WHEN oi.item_type = 'pet' THEN pc.category_name
                WHEN oi.item_type = 'product' THEN prc.category_name
            END AS category_name,
            CASE
                WHEN oi.item_type = 'pet' THEN p.image
                WHEN oi.item_type = 'product' THEN pr.image
            END AS item_image
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.order_id
        LEFT JOIN pets p ON oi.item_type = 'pet' AND oi.item_id = p.pet_id
        LEFT JOIN pet_categories pc ON p.category_id = pc.category_id
        LEFT JOIN products pr ON oi.item_type = 'product' AND oi.item_id = pr.product_id
        LEFT JOIN product_categories prc ON pr.category_id = prc.category_id
        WHERE o.payment_status = 'Paid'
        GROUP BY oi.item_type, oi.item_id, item_name, category_name, item_image
        ORDER BY total_sold DESC
        LIMIT 6";

$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $top_items[] = $row;
    }
}

$recent_sales = [];

$sql = "SELECT
            o.order_id,
            u.full_name,
            o.total_amount,
            o.payment_method,
            o.order_status,
            o.order_date
        FROM orders o
        JOIN users u ON o.customer_id = u.user_id
        WHERE o.payment_status = 'Paid'
        ORDER BY o.order_id DESC
        LIMIT 5";

$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $recent_sales[] = $row;
    }
}

$category_names = [];
$category_values = [];

foreach ($category_sales as $category) {
    $category_names[] = $category["category_name"];
    $category_values[] = (int)$category["total_sold"];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Analytics - PawCare</title>
    <link rel="stylesheet" href="../assets/css/admin-sales.css">
</head>
<body>

<div class="admin-page">

    <header class="top-header">
        <div>
            <h1>WELCOME BACK, MASTER!</h1>
            <p>♛ Your Pet Empire is under your command, King!</p>
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
                <a href="sales_analytics.php" class="active">💰 Sales Analytics</a>
                <a href="delivery_tracking.php">🚚 Delivery Tracking</a>
                <a href="#">★ Customer Reviews</a>
            </nav>
        </aside>

        <main class="main-content">

            <section class="summary-grid">
                <div class="summary-card">
                    <span>Total Revenue</span>
                    <h2>৳<?php echo number_format($total_revenue, 2); ?></h2>
                    <p>Paid orders only</p>
                </div>

                <div class="summary-card">
                    <span>Total Orders</span>
                    <h2><?php echo $total_orders; ?></h2>
                    <p>All customer orders</p>
                </div>

                <div class="summary-card">
                    <span>Paid Orders</span>
                    <h2><?php echo $paid_orders; ?></h2>
                    <p>Successfully paid</p>
                </div>

                <div class="summary-card">
                    <span>Average Order Value</span>
                    <h2>৳<?php echo number_format($average_order_value, 2); ?></h2>
                    <p>Average paid order</p>
                </div>
            </section>

            <section class="analytics-top">

                <div class="intelligence-panel">
                    <h2>♛ EMPIRE LIVE INTELLIGENCE</h2>

                    <div class="chart-area">
                        <div class="donut-chart" id="salesChart">
                            <div class="donut-center">
                                <span id="totalSalesCount">0</span>
                                <small>Units Sold</small>
                            </div>
                        </div>

                        <div class="chart-legend">
                            <?php if (count($category_sales) > 0): ?>
                                <?php foreach ($category_sales as $category): ?>
                                    <div class="legend-item">
                                        <span class="legend-dot"></span>
                                        <p>
                                            <?php echo htmlspecialchars($category["category_name"]); ?>
                                            <strong><?php echo $category["total_sold"]; ?></strong>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="empty-text">No paid sales data found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="champions-panel">
                    <h2>🏆 CHAMPIONS</h2>

                    <div class="champion-grid">
                        <?php if (count($top_items) > 0): ?>
                            <?php foreach (array_slice($top_items, 0, 4) as $item): ?>

                                <?php
                                if ($item["item_type"] === "pet") {
                                    $image_path = "../uploads/pets/" . $item["item_image"];
                                } else {
                                    $image_path = "../uploads/products/" . $item["item_image"];
                                }
                                ?>

                                <div class="champion-card">
                                    <div class="champ-label">CHAMP</div>
                                    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($item["item_name"]); ?>">
                                    <h4><?php echo htmlspecialchars($item["item_name"]); ?></h4>
                                    <p><?php echo $item["total_sold"]; ?> Units Sold</p>
                                </div>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="empty-text">No champion items available.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </section>

            <section class="performance-section">
                <h2>📊 TOP PERFORMANCE BY PRODUCT</h2>

                <div class="performance-box">

                    <?php if (count($top_items) > 0): ?>

                        <?php
                        $max_sold = max(array_column($top_items, "total_sold"));
                        ?>

                        <?php foreach ($top_items as $item): ?>

                            <?php
                            $percentage = $max_sold > 0 ? ($item["total_sold"] / $max_sold) * 100 : 0;
                            ?>

                            <div class="performance-item">
                                <div class="performance-header">
                                    <span>
                                        <?php echo strtoupper(htmlspecialchars($item["category_name"])); ?>:
                                        <?php echo strtoupper(htmlspecialchars($item["item_name"])); ?>
                                    </span>

                                    <strong><?php echo $item["total_sold"]; ?> Units Sold</strong>
                                </div>

                                <div class="performance-bar">
                                    <div class="performance-fill" style="width: <?php echo $percentage; ?>%;"></div>
                                </div>
                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <p class="empty-text">No performance data available.</p>

                    <?php endif; ?>

                </div>
            </section>
        </main>

    </div>

    <footer>
        🔥 POWERFUL PET MANAGEMENT ENGINE V2.0 LIVE | SYSTEM SECURED
    </footer>

</div>

<script>
const salesCategoryNames = <?php echo json_encode($category_names); ?>;
const salesCategoryValues = <?php echo json_encode($category_values); ?>;
</script>

<script src="../assets/js/admin-dashboard.js"></script>
<script src="../assets/js/admin-sales.js"></script>
</body>
</html>