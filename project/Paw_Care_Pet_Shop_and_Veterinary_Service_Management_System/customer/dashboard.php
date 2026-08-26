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

$customer_name = $_SESSION["full_name"] ?? "Customer";
$username = $_SESSION["username"] ?? "customer";


$customer_id = $_SESSION["user_id"];

$cart_message = $_SESSION["cart_message"] ?? "";
$cart_message_type = $_SESSION["cart_message_type"] ?? "";

unset($_SESSION["cart_message"]);
unset($_SESSION["cart_message_type"]);



// CART ACTIONS


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";


    
    // ADD ITEM TO CART
    

    if ($action === "add") {

    $item_type = $_POST["item_type"] ?? "";
    $item_id = (int) ($_POST["item_id"] ?? 0);


    if (
        !in_array($item_type, ["pet", "product"], true) ||
        $item_id <= 0
    ) {

        $_SESSION["cart_message"] =
            "Invalid item selected.";

        $_SESSION["cart_message_type"] =
            "error";

        header("Location: dashboard.php");
        exit;
    }


     
    // CHECK AVAILABLE STOCK
    

    if ($item_type === "pet") {

        $stock_sql = "
            SELECT stock
            FROM pets
            WHERE pet_id = ?
              AND status = 'Available'
            LIMIT 1
        ";

    } else {

        $stock_sql = "
            SELECT stock
            FROM products
            WHERE product_id = ?
              AND status = 'Available'
            LIMIT 1
        ";
    }


    $stock_stmt =
        mysqli_prepare($conn, $stock_sql);

    mysqli_stmt_bind_param(
        $stock_stmt,
        "i",
        $item_id
    );

    mysqli_stmt_execute($stock_stmt);

    $stock_result =
        mysqli_stmt_get_result($stock_stmt);

    $stock_row =
        mysqli_fetch_assoc($stock_result);

    mysqli_stmt_close($stock_stmt);


    if (!$stock_row) {

        $_SESSION["cart_message"] =
            "This item is currently unavailable.";

        $_SESSION["cart_message_type"] =
            "error";

        header("Location: dashboard.php");
        exit;
    }


    $available_stock =
        (int) $stock_row["stock"];


    
    // CHECK CURRENT CART QUANTITY
    

    $quantity_sql = "
        SELECT quantity
        FROM carts
        WHERE customer_id = ?
          AND item_type = ?
          AND item_id = ?
        LIMIT 1
    ";

    $quantity_stmt =
        mysqli_prepare(
            $conn,
            $quantity_sql
        );

    mysqli_stmt_bind_param(
        $quantity_stmt,
        "isi",
        $customer_id,
        $item_type,
        $item_id
    );

    mysqli_stmt_execute(
        $quantity_stmt
    );

    $quantity_result =
        mysqli_stmt_get_result(
            $quantity_stmt
        );

    $quantity_row =
        mysqli_fetch_assoc(
            $quantity_result
        );

    mysqli_stmt_close(
        $quantity_stmt
    );


    $current_quantity =
        $quantity_row
            ? (int) $quantity_row["quantity"]
            : 0;


    
    // STOCK LIMIT CHECK
    

    if (
        $available_stock <= 0 ||
        $current_quantity >= $available_stock
    ) {

        $_SESSION["cart_message"] =
            "Cannot add more. Available stock limit reached.";

        $_SESSION["cart_message_type"] =
            "error";

        header("Location: dashboard.php");
        exit;
    }


    
    // ADD / INCREASE CART
    

    $cart_sql = "
        INSERT INTO carts
        (
            customer_id,
            item_type,
            item_id,
            quantity
        )

        VALUES (?, ?, ?, 1)

        ON DUPLICATE KEY UPDATE
            quantity = quantity + 1
    ";


    $cart_stmt =
        mysqli_prepare(
            $conn,
            $cart_sql
        );

    mysqli_stmt_bind_param(
        $cart_stmt,
        "isi",
        $customer_id,
        $item_type,
        $item_id
    );

    mysqli_stmt_execute(
        $cart_stmt
    );

    mysqli_stmt_close(
        $cart_stmt
    );


    $_SESSION["cart_message"] =
        "Item added to bill successfully.";

    $_SESSION["cart_message_type"] =
        "success";


    header("Location: dashboard.php");
    exit;
}

    
    // REMOVE ONE CART ITEM
    

    if ($action === "remove") {

        $cart_id =
            (int) ($_POST["cart_id"] ?? 0);


        if ($cart_id > 0) {

            $remove_sql = "
                DELETE FROM carts
                WHERE cart_id = ?
                  AND customer_id = ?
            ";

            $remove_stmt =
                mysqli_prepare(
                    $conn,
                    $remove_sql
                );

            mysqli_stmt_bind_param(
                $remove_stmt,
                "ii",
                $cart_id,
                $customer_id
            );

            mysqli_stmt_execute(
                $remove_stmt
            );

            mysqli_stmt_close(
                $remove_stmt
            );
        }


        header("Location: dashboard.php");
        exit;
    }


 
    // CLEAR COMPLETE CART
 

    if ($action === "clear") {

        $clear_sql = "
            DELETE FROM carts
            WHERE customer_id = ?
        ";

        $clear_stmt =
            mysqli_prepare(
                $conn,
                $clear_sql
            );

        mysqli_stmt_bind_param(
            $clear_stmt,
            "i",
            $customer_id
        );

        mysqli_stmt_execute(
            $clear_stmt
        );

        mysqli_stmt_close(
            $clear_stmt
        );


        header("Location: dashboard.php");
        exit;
    }
}

// FETCH PETS


$pet_sql = "
    SELECT
        p.pet_id,
        p.pet_name,
        p.breed,
        p.price,
        p.stock,
        p.image,
        pc.category_name
    FROM pets p
    JOIN pet_categories pc
        ON p.category_id = pc.category_id
    WHERE p.status = 'Available'
    ORDER BY p.pet_id DESC
";

$pet_result = mysqli_query($conn, $pet_sql);



// FETCH PRODUCTS


$product_sql = "
    SELECT
        pr.product_id,
        pr.product_name,
        pr.brand,
        pr.price,
        pr.stock,
        pr.image,
        pc.category_name
    FROM products pr
    JOIN product_categories pc
        ON pr.category_id = pc.category_id
    WHERE pr.status = 'Available'
    ORDER BY pr.product_id DESC
";

$product_result = mysqli_query($conn, $product_sql);



// FETCH CUSTOMER CART


$cart_sql = "

    SELECT
        c.cart_id,
        c.item_type,
        c.item_id,
        c.quantity,

        p.pet_name AS item_name,
        p.price AS item_price

    FROM carts c

    JOIN pets p
        ON c.item_type = 'pet'
       AND c.item_id = p.pet_id

    WHERE c.customer_id = ?


    UNION ALL


    SELECT
        c.cart_id,
        c.item_type,
        c.item_id,
        c.quantity,

        pr.product_name AS item_name,
        pr.price AS item_price

    FROM carts c

    JOIN products pr
        ON c.item_type = 'product'
       AND c.item_id = pr.product_id

    WHERE c.customer_id = ?

";

$cart_stmt =
    mysqli_prepare($conn, $cart_sql);

mysqli_stmt_bind_param(
    $cart_stmt,
    "ii",
    $customer_id,
    $customer_id
);

mysqli_stmt_execute($cart_stmt);

$cart_result =
    mysqli_stmt_get_result($cart_stmt);


$cart_items = [];

$cart_total = 0;


while (
    $cart_item =
    mysqli_fetch_assoc($cart_result)
) {

    $cart_item["subtotal"] =
        $cart_item["item_price"] *
        $cart_item["quantity"];

    $cart_total +=
        $cart_item["subtotal"];

    $cart_items[] =
        $cart_item;
}

mysqli_stmt_close($cart_stmt);

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Customer Dashboard | PawCare</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/customer-dashboard.css">
</head>

<body>

<div class="customer-dashboard">

    
         <!-- TOP HEADER -->
    

    <header class="customer-header">

        <div class="customer-welcome">

            <h1>
                WELCOME,
                <?php echo strtoupper(htmlspecialchars($username)); ?>!
            </h1>

            <p>
                “To administer is the mark of a true master.”
            </p>

            <div class="dashboard-search">

                <input
                    type="text"
                    id="dashboardSearch"
                    placeholder="Search pets or accessories...">

            </div>

        </div>


        <div class="header-center">

            <div class="quality-badge">
                🐾 QUALITY · CARE · PASSION 🐾
            </div>

            <div class="current-time" id="currentTime">
                00:00:00
            </div>

        </div>


        <div class="customer-profile">

            <span class="profile-name">
                <?php echo htmlspecialchars($username); ?>
            </span>

            <a href="#" class="profile-avatar">

                <?php
                echo strtoupper(
                    substr($username, 0, 1)
                );
                ?>

            </a>

        </div>

    </header>


    
         <!-- MAIN BODY -->
    

    <main class="dashboard-body">


        
             <!-- LEFT SIDEBAR -->
        

        <aside class="customer-sidebar">

            <div class="sidebar-title">
                <span class="paw-box">🐾</span>
                <h2>PAWCARE</h2>
            </div>


            <nav class="customer-menu">

            <button
                class="dashboard-menu-btn active"
                data-filter="all">
                ▦ Dashboard
            </button>

            <button
                class="dashboard-menu-btn"
                data-filter="best">
                📊 Best Sellers
            </button>

            <button
                class="dashboard-menu-btn"
                data-filter="cat">
                🐱 Cat
            </button>

            <button
                class="dashboard-menu-btn"
                data-filter="dog">
                🐶 Dog
            </button>

            <button
                class="dashboard-menu-btn"
                data-filter="rabbit">
                🐰 Rabbit
            </button>

            <button
                class="dashboard-menu-btn"
                data-filter="bird">
                🐦 Bird
            </button>

            <button
                class="dashboard-menu-btn"
                data-filter="food">
                🍖 Pet Food
            </button>

            <button
                class="dashboard-menu-btn"
                data-filter="accessories">
                🎾 Pet Accessories
            </button>

            <button
                class="dashboard-menu-btn"
                data-filter="medicine">
                💊 Pet Medicine
            </button>

            </nav>

        </aside>


        
             <!-- CENTER CONTENT -->
        

        <section class="dashboard-content">

            <?php if (!empty($cart_message)): ?>

    <div
        id="cartToast"
        class="cart-toast <?php echo
            $cart_message_type === "success"
                ? "toast-success"
                : "toast-error";
        ?>">

        <span class="toast-icon">

            <?php echo
                $cart_message_type === "success"
                    ? "✓"
                    : "✕";
            ?>

        </span>

        <span>
            <?php echo htmlspecialchars($cart_message); ?>
        </span>

        <button
            type="button"
            class="toast-close"
            id="toastClose">
            ×
        </button>

    </div>

<?php endif; ?>


            <div class="discover-banner">

                🐾 DISCOVER YOUR NEW BEST FRIEND 🐾

            </div>


            <div class="items-grid" id="itemsGrid">

    
         <!-- PET CARDS -->
    

    <?php while ($pet = mysqli_fetch_assoc($pet_result)): ?>

        <article
            class="item-card"
            data-type="pet"
            data-category="<?php echo strtolower(
                htmlspecialchars($pet["category_name"])
            ); ?>"
            data-name="<?php echo strtolower(
                htmlspecialchars($pet["pet_name"])
            ); ?>"
            data-extra="<?php echo strtolower(
                htmlspecialchars($pet["breed"])
            ); ?>">

            <div class="item-image">

                <img
                    src="../uploads/pets/<?php echo htmlspecialchars(
                        $pet["image"]
                    ); ?>"
                    alt="<?php echo htmlspecialchars(
                        $pet["pet_name"]
                    ); ?>">

            </div>

            <div class="item-info">

                <h3>
                    <?php echo htmlspecialchars(
                        $pet["pet_name"]
                    ); ?>
                </h3>

                <p class="item-extra">
                    <?php echo htmlspecialchars(
                        $pet["breed"]
                    ); ?>
                </p>

                <div class="item-price">
                    ৳ <?php echo number_format(
                        $pet["price"],
                        2
                    ); ?>
                </div>

                <div class="item-stock">
                    📦 In Stock:
                    <?php echo (int) $pet["stock"]; ?>
                </div>

            </div>

                <form method="POST">

                    <input
                        type="hidden"
                        name="action"
                        value="add">

                    <input
                        type="hidden"
                        name="item_type"
                        value="pet">

                    <input
                        type="hidden"
                        name="item_id"
                        value="<?php echo (int) $pet["pet_id"]; ?>">

                    <button
                        type="submit"
                        class="add-bill-btn">

                        ADD TO BILL

                    </button>

                </form>

        </article>

    <?php endwhile; ?>


    
         <!-- PRODUCT CARDS -->
    

    <?php while ($product = mysqli_fetch_assoc($product_result)): ?>

            <article
                class="item-card"
                data-type="product"
                data-category="<?php echo strtolower(
                    htmlspecialchars($product["category_name"])
                ); ?>"
                data-name="<?php echo strtolower(
                    htmlspecialchars($product["product_name"])
                ); ?>"
                data-extra="<?php echo strtolower(
                    htmlspecialchars($product["brand"] ?? "")
                ); ?>">

            <div class="item-image">

                <img
                    src="../uploads/products/<?php echo htmlspecialchars(
                        $product["image"]
                    ); ?>"
                    alt="<?php echo htmlspecialchars(
                        $product["product_name"]
                    ); ?>">

            </div>

            <div class="item-info">

                <h3>
                    <?php echo htmlspecialchars(
                        $product["product_name"]
                    ); ?>
                </h3>

                <p class="item-extra">
                    <?php echo htmlspecialchars(
                        $product["brand"] ?? ""
                    ); ?>
                </p>

                <div class="item-price">
                    ৳ <?php echo number_format(
                        $product["price"],
                        2
                    ); ?>
                </div>

                <div class="item-stock">
                    📦 In Stock:
                    <?php echo (int) $product["stock"]; ?>
                </div>

            </div>

            <form method="POST">

                <input
                    type="hidden"
                    name="action"
                    value="add">

                <input
                    type="hidden"
                    name="item_type"
                    value="product">

                <input
                    type="hidden"
                    name="item_id"
                    value="<?php echo (int) $product["product_id"]; ?>">

                <button
                    type="submit"
                    class="add-bill-btn">

                    ADD TO BILL

                </button>

            </form>

        </article>

    <?php endwhile; ?>

</div>

        </section>


       
             <!-- MINI CASH MEMO -->
      

        <aside class="mini-cash-memo">

            <div class="cash-memo-title">
                MINI CASH MEMO
            </div>


        <div
            class="cash-memo-items"
            id="cashMemoItems">


            <?php if (empty($cart_items)): ?>

                <div class="empty-cart-message">
                    No items added yet.
                </div>


            <?php else: ?>


                <?php foreach ($cart_items as $item): ?>

                    <div class="memo-item">

                        <div class="memo-item-details">

                            <strong>
                                <?php echo htmlspecialchars(
                                    $item["item_name"]
                                ); ?>
                            </strong>

                            <small>
                                Qty:
                                <?php echo (int) $item["quantity"]; ?>
                            </small>

                            <span>
                                ৳
                                <?php echo number_format(
                                    $item["subtotal"],
                                    2
                                ); ?>
                            </span>

                        </div>


                        <form method="POST">

                            <input
                                type="hidden"
                                name="action"
                                value="remove">

                            <input
                                type="hidden"
                                name="cart_id"
                                value="<?php echo (int) $item["cart_id"]; ?>">

                            <button
                                type="submit"
                                class="remove-memo-item">

                                ×

                            </button>

                        </form>

                    </div>

                <?php endforeach; ?>


                <div class="memo-total">

                    <span>Total</span>

                    <strong>
                        ৳
                        <?php echo number_format(
                            $cart_total,
                            2
                        ); ?>
                    </strong>

                </div>


            <?php endif; ?>

        </div>


            <div class="cash-memo-actions">

                <button class="generate-bill-btn">
                    🧾 GENERATE BILL
                </button>

            <form
                method="POST"
                onsubmit="return confirm('Remove all items from the bill?');">

                <input
                    type="hidden"
                    name="action"
                    value="clear">

                <button
                    type="submit"
                    class="cancel-bill-btn">

                    CANCEL

                </button>

            </form>

            </div>

        </aside>

    </main>


    
          <!-- FOOTER -->
    

    <footer class="customer-footer">

        Trusted by Pet Parents |
        Quality Guaranteed 🐾

    </footer>

</div>


<script src="../assets/js/customer-dashboard.js"></script>

</body>

</html>