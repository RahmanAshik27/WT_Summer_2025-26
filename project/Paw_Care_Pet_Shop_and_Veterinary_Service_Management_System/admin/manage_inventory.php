<?php
require_once "../includes/session.php";
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: ../login.php");
    exit;
}

$pets = [];
$products = [];

$pet_sql = "SELECT p.*, pc.category_name
            FROM pets p
            LEFT JOIN pet_categories pc ON p.category_id = pc.category_id
            ORDER BY p.pet_id DESC";

$result = mysqli_query($conn, $pet_sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) $pets[] = $row;
}

$product_sql = "SELECT p.*, pc.category_name
                FROM products p
                LEFT JOIN product_categories pc ON p.category_id = pc.category_id
                ORDER BY p.product_id DESC";

$result = mysqli_query($conn, $product_sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inventory - PawCare</title>
    <link rel="stylesheet" href="../assets/css/manage-inventory.css">
</head>
<body>

<div class="management-page">

    <aside class="management-sidebar">
        <h1>▣ Add Inventory</h1>

        <div class="field">
            <label>Inventory Type</label>
            <select id="inventoryType">
                <option value="pet">Pet</option>
                <option value="product">Product</option>
            </select>
        </div>

        <div class="field">
            <label>Category</label>
            <select id="categorySelect">
                <option value="">Select Category</option>
            </select>
        </div>

        <div class="field">
            <label>Pet / Product</label>
            <select id="itemSelect">
                <option value="">Select Item</option>
            </select>
        </div>

        <div class="field">
            <label>Price (BDT)</label>
            <input type="number" id="itemPrice" placeholder="Enter Price">
        </div>

        <div class="field">
            <label>Stock Quantity</label>
            <input type="number" id="stockQuantity" value="1" min="1">
        </div>

        <button type="button" class="add-inventory-btn">ADD TO INVENTORY</button>

        <div class="managing-status">
            <div>🌐</div>
            <span>MANAGING INVENTORY</span>
        </div>
    </aside>

    <main class="management-content">

        <header class="management-header">
            <strong>▣ PAWCARE PREMIUM SYSTEM</strong>

            <div>
                <span id="currentDate"></span> |
                <span id="currentTime"></span>
            </div>
        </header>

        <section class="catalog-section">
            <h3>▣ SELECT FROM CATALOG</h3>

            <div class="catalog-tabs">
                <button type="button" class="catalog-tab active" data-type="pet">Pets</button>
                <button type="button" class="catalog-tab" data-type="product">Products</button>
            </div>

            <div class="catalog-grid" id="petCatalog">
                <?php foreach ($pets as $pet): ?>
                    <div class="catalog-card"
                         data-type="pet"
                         data-id="<?= (int)$pet["pet_id"] ?>"
                         data-category="<?= htmlspecialchars($pet["category_name"] ?? "") ?>"
                         data-name="<?= htmlspecialchars($pet["pet_name"]) ?>"
                         data-price="<?= (float)$pet["price"] ?>"
                         data-stock="<?= (int)$pet["stock"] ?>">

                        <img src="../uploads/pets/<?= htmlspecialchars($pet["image"] ?? "default_pet.jpg") ?>" alt="<?= htmlspecialchars($pet["pet_name"]) ?>">
                        <p><?= htmlspecialchars($pet["pet_name"]) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="catalog-grid hidden" id="productCatalog">
                <?php foreach ($products as $product): ?>
                    <div class="catalog-card"
                         data-type="product"
                         data-id="<?= (int)$product["product_id"] ?>"
                         data-category="<?= htmlspecialchars($product["category_name"] ?? "") ?>"
                         data-name="<?= htmlspecialchars($product["product_name"]) ?>"
                         data-price="<?= (float)$product["price"] ?>"
                         data-stock="<?= (int)$product["stock"] ?>">

                        <img src="../uploads/products/<?= htmlspecialchars($product["image"] ?? "default_product.jpg") ?>" alt="<?= htmlspecialchars($product["product_name"]) ?>">
                        <p><?= htmlspecialchars($product["product_name"]) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="selected-preview">
            <div class="preview-image">
                <span>🐾</span>
            </div>

            <div class="preview-info">
                <h2 id="selectedName">Select an inventory item</h2>
                <strong id="selectedStock">Database Stock: -- Units</strong>
                <p id="selectedCategory">Category: --</p>
                <p id="selectedPrice">Price: --</p>
            </div>
        </section>

        <section class="live-monitor">
            <h3>▣ LIVE TABLE MONITOR</h3>

            <div class="monitor-placeholder">
                Select a catalog item to view inventory information.
            </div>
        </section>

        <div class="management-actions">
            <button type="button" class="remove-btn">🗑 REMOVE SELECTED FROM INVENTORY</button>
            <a href="dashboard_overview.php" class="back-btn">▣ BACK TO ADMIN DASHBOARD</a>
        </div>

    </main>

</div>

<script src="../assets/js/admin-dashboard.js"></script>
<script src="../assets/js/manage-inventory.js"></script>

</body>
</html>