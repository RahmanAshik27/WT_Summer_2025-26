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

$customer_id = $_SESSION["user_id"];
$customer_name = $_SESSION["full_name"] ?? "Customer";


// ==========================================
// CUSTOMER INFO
// ==========================================

$user_sql = "
    SELECT
        full_name,
        username,
        email,
        phone,
        address
    FROM users
    WHERE user_id = ?
    LIMIT 1
";

$user_stmt = mysqli_prepare($conn, $user_sql);

mysqli_stmt_bind_param(
    $user_stmt,
    "i",
    $customer_id
);

mysqli_stmt_execute($user_stmt);

$user_result =
    mysqli_stmt_get_result($user_stmt);

$customer =
    mysqli_fetch_assoc($user_result);

mysqli_stmt_close($user_stmt);


// ==========================================
// FETCH CART ITEMS
// ==========================================

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
$subtotal = 0;

while ($item = mysqli_fetch_assoc($cart_result)) {

    $item["subtotal"] =
        $item["item_price"] *
        $item["quantity"];

    $subtotal += $item["subtotal"];

    $cart_items[] = $item;
}

mysqli_stmt_close($cart_stmt);


// ==========================================
// EMPTY CART PROTECTION
// ==========================================

if (empty($cart_items)) {

    header("Location: dashboard.php");
    exit;
}


// For now
$discount = 0;
$grand_total = $subtotal - $discount;

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Secure Checkout | PawCare</title>

    <link rel="stylesheet"
          href="../assets/css/billing.css">

</head>

<body>

<div class="checkout-page">


    <!-- =========================
         HEADER
    ========================== -->

    <header class="checkout-header">

        <div class="checkout-heading">

            <h1>
                PREMIUM SECURE CHECKOUT
            </h1>

            <p>
                Delivery → Payment → Confirmation
            </p>

        </div>


        <div class="checkout-time">

            <div id="checkoutClock">
                00:00:00
            </div>

            <small id="checkoutDate">
                Loading date...
            </small>

        </div>


        <div class="checkout-customer">

            <span>
                <?php echo htmlspecialchars(
                    $customer["username"]
                ); ?>
            </span>

            <div class="checkout-avatar">

                <?php echo strtoupper(
                    substr(
                        $customer["username"],
                        0,
                        1
                    )
                ); ?>

            </div>

        </div>

    </header>


    <!-- =========================
         MAIN CONTENT
    ========================== -->

    <main class="checkout-content">


        <!-- =====================
             ORDER SUMMARY
        ====================== -->

        <section class="checkout-panel order-summary receipt-panel">

            <h2>
                ORDER SUMMARY
            </h2>

            <div class="receipt-header">
                <h3>PAWCARE PET SHOP</h3>
                <p>Pet Shop & Veterinary Service</p>
                <p>Dhaka, Bangladesh</p>
                <p>-----------------------------</p>
            </div>

            <div class="customer-order-info">

                <p>
                    --- PAWCARE PET SHOP ---
                </p>

                <p>
                    Customer:
                    <?php echo htmlspecialchars(
                        $customer["full_name"]
                    ); ?>
                </p>

                <p>
                    Phone:
                    <?php echo htmlspecialchars(
                        $customer["phone"] ?? ""
                    ); ?>
                </p>

                <p>
                    Email:
                    <?php echo htmlspecialchars(
                        $customer["email"]
                    ); ?>
                </p>

                <p>
                    Address:
                    <?php echo htmlspecialchars(
                        $customer["address"] ?? ""
                    ); ?>
                </p>

            </div>


            <div class="order-table-wrapper">

                <table class="order-table">

                    <thead>

                        <tr>
                            <th>ITEM</th>
                            <th>QTY</th>
                            <th>PRICE</th>
                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($cart_items as $item): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars(
                                    $item["item_name"]
                                ); ?>
                            </td>

                            <td>
                                <?php echo (int)
                                    $item["quantity"];
                                ?>x
                            </td>

                            <td>
                                ৳
                                <?php echo number_format(
                                    $item["subtotal"],
                                    2
                                ); ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>


            <div class="order-totals">

                <div>
                    <span>SUBTOTAL:</span>

                    <strong>
                        ৳
                        <?php echo number_format(
                            $subtotal,
                            2
                        ); ?>
                    </strong>
                </div>


                <div>
                    <span>DISCOUNT:</span>

                    <strong>
                        ৳
                        <?php echo number_format(
                            $discount,
                            2
                        ); ?>
                    </strong>
                </div>


                <div class="grand-total">

                    <span>TOTAL:</span>

                    <strong>
                        ৳
                        <?php echo number_format(
                            $grand_total,
                            2
                        ); ?>
                    </strong>

                </div>

            </div>

        </section>



        <!-- =====================
             DELIVERY METHOD
        ====================== -->

        <section class="checkout-panel">

            <h2>
                🚚 DELIVERY METHOD
            </h2>


            <div class="delivery-options">

                <label class="checkout-option">

                    <input
                        type="radio"
                        name="delivery_method"
                        value="Pathao Fast"
                        checked>

                    <span>
                        Pathao Fast
                    </span>

                </label>


                <label class="checkout-option">

                    <input
                        type="radio"
                        name="delivery_method"
                        value="PetPanda Go">

                    <span>
                        PetPanda Go
                    </span>

                </label>


                <label class="checkout-option">

                    <input
                        type="radio"
                        name="delivery_method"
                        value="Speed Fast">

                    <span>
                        Speed Fast
                    </span>

                </label>


                <label class="checkout-option">

                    <input
                        type="radio"
                        name="delivery_method"
                        value="Jhinku BD">

                    <span>
                        Jhinku BD
                    </span>

                </label>


                <label class="checkout-option">

                    <input
                        type="radio"
                        name="delivery_method"
                        value="Shop Pickup">

                    <span>
                        Shop Pickup
                    </span>

                </label>

            </div>


            <div class="buy-more-box">

                <p>
                    Want to add something else
                    for your pet?
                </p>

                <a href="dashboard.php">
                    BUY MORE ITEMS
                </a>

            </div>

        </section>



        <!-- =====================
             PAYMENT
        ====================== -->

        <section class="checkout-panel">

            <h2>
                🔒 SECURE PAYMENT
            </h2>


            <div class="payment-options">

                <button
                    type="button"
                    class="payment-btn"
                    data-payment="bKash">

                    Pay with bKash

                </button>


                <button
                    type="button"
                    class="payment-btn"
                    data-payment="Nagad">

                    Pay with Nagad

                </button>


                <button
                    type="button"
                    class="payment-btn"
                    data-payment="Rocket">

                    Pay with Rocket

                </button>


                <button
                    type="button"
                    class="payment-btn"
                    data-payment="Credit Card">

                    Pay with Credit Card

                </button>


                <button
                    type="button"
                    class="payment-btn active"
                    data-payment="Cash on Delivery">

                    Pay with Cash on Delivery

                </button>

            </div>

        </section>

    </main>



    <!-- =========================
         FOOTER
    ========================== -->

    <footer class="checkout-footer">

        <div>
            🛡 256-bit SSL Encrypted |
            PawCare © 2026
        </div>


        <button
            type="button"
            class="confirm-order-btn"
            id="confirmOrderBtn">

            CONFIRM ORDER

        </button>

    </footer>

</div>

<div id="checkoutToast" class="checkout-toast">
    <span id="checkoutToastMessage">
        Selection updated.
    </span>
</div>


<script src="../assets/js/billing.js"></script>

</body>

</html>