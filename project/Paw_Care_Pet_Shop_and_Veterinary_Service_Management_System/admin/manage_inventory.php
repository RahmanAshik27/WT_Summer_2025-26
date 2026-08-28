<?php
require_once "../includes/session.php";
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: ../login.php");
    exit;
}

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_inventory"])) {
    $type = $_POST["inventory_type"] ?? "";
    $item_id = (int)($_POST["item_id"] ?? 0);
    $price = (float)($_POST["price"] ?? 0);
    $quantity = (int)($_POST["quantity"] ?? 0);

    if (($type !== "pet" && $type !== "product") || $item_id <= 0 || $price <= 0 || $quantity <= 0) {
        $error_message = "Please select an item and enter valid information.";
    } else {
        if ($type === "pet") {
            $stmt = mysqli_prepare($conn, "UPDATE pets SET price = ?, stock = stock + ?, status = 'Available' WHERE pet_id = ?");
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE products SET price = ?, stock = stock + ? WHERE product_id = ?");
        }

        mysqli_stmt_bind_param($stmt, "dii", $price, $quantity, $item_id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $success_message = "Inventory stock added successfully.";
        } else {
            $error_message = "Inventory could not be updated.";
        }

        mysqli_stmt_close($stmt);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["remove_inventory"])) {
    $type = $_POST["remove_type"] ?? "";
    $item_id = (int)($_POST["remove_item_id"] ?? 0);
    $quantity = (int)($_POST["remove_quantity"] ?? 0);

    if ($type === "" || $item_id <= 0) {
        $error_message = "Please select an item from the catalog first.";
    } elseif ($quantity <= 0) {
        $error_message = "Please enter a valid remove quantity.";
    } else {
        if ($type === "pet") {
            $stmt = mysqli_prepare($conn, "UPDATE pets SET stock = stock - ? WHERE pet_id = ? AND stock >= ?");
        } elseif ($type === "product") {
            $stmt = mysqli_prepare($conn, "UPDATE products SET stock = stock - ? WHERE product_id = ? AND stock >= ?");
        } else {
            $stmt = false;
        }

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "iii", $quantity, $item_id, $quantity);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $success_message = "Inventory stock removed successfully.";
            } else {
                $error_message = "Not enough stock available to remove.";
            }

            mysqli_stmt_close($stmt);
        } else {
            $error_message = "Invalid inventory type.";
        }
    }
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
    <link rel="stylesheet" href="../assets/css/manage-inventory.css?v=3">
</head>
<body>

<div class="management-page">

    <aside class="management-sidebar">
        <h1>▣ Manage Stock</h1>
        <p class="sidebar-text">Select an item from the catalog, then add stock from here.</p>

        <?php if (!empty($success_message)): ?>
            <div class="inventory-message success-message"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="inventory-message error-message"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form method="POST" id="inventoryForm">

            <div class="field">
                <label>Inventory Type</label>
                <select id="inventoryType" name="inventory_type">
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
                <label>Selected Item</label>
                <select id="itemSelect" name="item_id" required>
                    <option value="">Select Item</option>
                </select>
            </div>

            <div class="field">
                <label>Price (BDT)</label>
                <input type="number" id="itemPrice" name="price" min="0.01" step="0.01" placeholder="Price" required>
            </div>

            <div class="field">
                <label>Quantity to Add</label>
                <input type="number" id="stockQuantity" name="quantity" value="1" min="1" required>
            </div>

            <button type="submit" name="add_inventory" class="add-inventory-btn">+ ADD STOCK</button>
        </form>

        <div class="managing-status">
            <div>🌐</div>
            <span>PAWCARE INVENTORY</span>
        </div>
    </aside>

    <main class="management-content">

        <header class="management-header">
            <div>
                <strong>▣ PAWCARE INVENTORY MANAGEMENT</strong>
                <small>Manage pets and products from one place</small>
            </div>

            <div class="header-time">
                <span id="currentDate"></span> |
                <span id="currentTime"></span>
            </div>
        </header>

        <section class="catalog-section">
            <div class="section-heading">
                <div>
                    <h3>SELECT FROM CATALOG</h3>
                    <p>Choose a pet or product to manage its stock.</p>
                </div>

                <div class="catalog-tabs">
                    <button type="button" class="catalog-tab active" data-type="pet">Pets</button>
                    <button type="button" class="catalog-tab" data-type="product">Products</button>
                </div>
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

                        <div class="catalog-card-info">
                            <p><?= htmlspecialchars($pet["pet_name"]) ?></p>
                            <span>Stock: <?= (int)$pet["stock"] ?></span>
                        </div>
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

                        <div class="catalog-card-info">
                            <p><?= htmlspecialchars($product["product_name"]) ?></p>
                            <span>Stock: <?= (int)$product["stock"] ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="selected-preview">
            <div class="preview-image" id="previewImage">
                <span>🐾</span>
            </div>

            <div class="preview-info">
                <span class="selected-label">SELECTED ITEM</span>
                <h2 id="selectedName">No item selected</h2>
                <strong id="selectedStock">Database Stock: -- Units</strong>
                <p id="selectedCategory">Category: --</p>
                <p id="selectedPrice">Price: --</p>
            </div>

            <div class="remove-panel">
                <h4>Remove Stock</h4>
                <p>Enter how many units you want to remove.</p>

            <form method="POST" id="removeInventoryForm">
                <input type="hidden" name="remove_type" id="removeInventoryType" value="">
                <input type="hidden" name="remove_item_id" id="removeItemId" value="">

                <label>Quantity to Remove</label>
                <input type="number" name="remove_quantity" id="removeQuantity" min="1" value="1" required>

                <button type="submit" name="remove_inventory" value="1" class="remove-btn">− REMOVE STOCK</button>
            </form>
            </div>
        </section>

        <section class="live-monitor">
            <div class="monitor-title">
                <div>
                    <h3>LIVE INVENTORY MONITOR</h3>
                    <p>Selected inventory information</p>
                </div>
            </div>

            <div class="monitor-placeholder" id="monitorContent">
                Select an item from the catalog to view its information.
            </div>
        </section>

        <div class="management-actions">
            <a href="inventory.php" class="back-btn">← INVENTORY OVERVIEW</a>
            <a href="dashboard_overview.php" class="back-btn">▣ ADMIN DASHBOARD</a>
        </div>

    </main>

</div>

<script src="../assets/js/admin-dashboard.js"></script>
<script src="../assets/js/manage-inventory.js"></script>

</body>
</html>