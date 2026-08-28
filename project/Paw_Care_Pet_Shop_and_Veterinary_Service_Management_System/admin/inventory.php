<?php
require_once "../includes/session.php";
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: ../login.php");
    exit;
}

$pets = [];
$products = [];

$pet_result = mysqli_query($conn, "SELECT p.*, pc.category_name FROM pets p LEFT JOIN pet_categories pc ON p.category_id = pc.category_id ORDER BY p.pet_id DESC");

if ($pet_result) {
    while ($row = mysqli_fetch_assoc($pet_result)) $pets[] = $row;
}

$product_result = mysqli_query($conn, "SELECT p.*, pc.category_name FROM products p LEFT JOIN product_categories pc ON p.category_id = pc.category_id ORDER BY p.product_id DESC");

if ($product_result) {
    while ($row = mysqli_fetch_assoc($product_result)) $products[] = $row;
}

$total_pet_stock = 0;
$total_product_stock = 0;
$low_stock = 0;
$out_of_stock = 0;

foreach ($pets as $pet) {
    $stock = (int)$pet["stock"];
    $total_pet_stock += $stock;

    if ($stock === 0) $out_of_stock++;
    elseif ($stock <= 5) $low_stock++;
}

foreach ($products as $product) {
    $stock = (int)$product["stock"];
    $total_product_stock += $stock;

    if ($stock === 0) $out_of_stock++;
    elseif ($stock <= 5) $low_stock++;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Stock - PawCare</title>
    <link rel="stylesheet" href="../assets/css/admin-inventory.css">
</head>
<body>

<div class="admin-page">

    <header class="top-header">
        <div>
            <h1>WELCOME BACK, MASTER!</h1>
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
                <h2>🐾 PET SHOP</h2>
            </div>

            <nav class="sidebar-menu">
                <a href="dashboard_overview.php">▣ Dashboard Overview</a>
                <a href="inventory.php" class="active">▤ Inventory Stock</a>
                <a href="sales_analytics.php" class="active">💰 Sales Analytics</a>
                <a href="delivery_tracking.php">🚚 Delivery Tracking</a>
                <a href="customer_reviews.php" class="active">★ Customer Reviews</a>
            </nav>
        </aside>

        <main class="main-content">

            <div class="inventory-heading">
                <div>
                    <h2>📦 INVENTORY STOCK</h2>
                    <p>Manage and monitor PawCare pets and products</p>
                </div>

                <div class="inventory-tools">
                    <input type="text" id="inventorySearch" placeholder="Search inventory...">
                    <a href="manage_inventory.php" class="manage-btn">+ Manage Inventory</a>
                </div>
            </div>

            <div class="summary-grid">
                <div class="summary-card">
                    <span>🐾</span>
                    <div>
                        <strong><?= $total_pet_stock ?></strong>
                        <p>Pet Stock</p>
                    </div>
                </div>

                <div class="summary-card">
                    <span>📦</span>
                    <div>
                        <strong><?= $total_product_stock ?></strong>
                        <p>Product Stock</p>
                    </div>
                </div>

                <div class="summary-card">
                    <span>⚠️</span>
                    <div>
                        <strong><?= $low_stock ?></strong>
                        <p>Low Stock</p>
                    </div>
                </div>

                <div class="summary-card">
                    <span>❌</span>
                    <div>
                        <strong><?= $out_of_stock ?></strong>
                        <p>Out of Stock</p>
                    </div>
                </div>
            </div>

            <div class="inventory-tabs">
                <button type="button" class="inventory-tab active" data-target="petsTable">Pets</button>
                <button type="button" class="inventory-tab" data-target="productsTable">Products</button>
            </div>

            <section class="inventory-panel active-panel" id="petsTable">
                <div class="panel-title">
                    <h3>🐾 Pet Inventory</h3>
                    <span><?= count($pets) ?> Records</span>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pet</th>
                                <th>Category</th>
                                <th>Breed</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pets)): ?>
                                <?php foreach ($pets as $pet): ?>
                                    <?php
                                    $stock = (int)$pet["stock"];
                                    $stock_class = $stock === 0 ? "out-stock" : ($stock <= 5 ? "low-stock" : "good-stock");
                                    ?>
                                    <tr class="inventory-row">
                                        <td>#<?= (int)$pet["pet_id"] ?></td>
                                        <td><?= htmlspecialchars($pet["pet_name"]) ?></td>
                                        <td><?= htmlspecialchars($pet["category_name"] ?? "N/A") ?></td>
                                        <td><?= htmlspecialchars($pet["breed"] ?? "N/A") ?></td>
                                        <td>৳<?= number_format((float)$pet["price"], 2) ?></td>
                                        <td><span class="stock-badge <?= $stock_class ?>"><?= $stock ?></span></td>
                                        <td><?= htmlspecialchars($pet["status"]) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="empty-message">No pets found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="inventory-panel" id="productsTable">
                <div class="panel-title">
                    <h3>📦 Product Inventory</h3>
                    <span><?= count($products) ?> Records</span>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($products)): ?>
                                <?php foreach ($products as $product): ?>
                                    <?php
                                    $stock = (int)$product["stock"];
                                    $stock_class = $stock === 0 ? "out-stock" : ($stock <= 5 ? "low-stock" : "good-stock");
                                    ?>
                                    <tr class="inventory-row">
                                        <td>#<?= (int)$product["product_id"] ?></td>
                                        <td><?= htmlspecialchars($product["product_name"]) ?></td>
                                        <td><?= htmlspecialchars($product["category_name"] ?? "N/A") ?></td>
                                        <td>৳<?= number_format((float)$product["price"], 2) ?></td>
                                        <td><span class="stock-badge <?= $stock_class ?>"><?= $stock ?></span></td>
                                        <td><?= htmlspecialchars($product["status"]) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="empty-message">No products found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        </main>

    </div>

    <footer class="admin-footer">
        🛡 POWERFUL PET MANAGEMENT ENGINE V2.0 LIVE | SYSTEM SECURED
    </footer>

</div>

<script src="../assets/js/admin-dashboard.js"></script>
<script src="../assets/js/admin-inventory.js"></script>

</body>
</html>