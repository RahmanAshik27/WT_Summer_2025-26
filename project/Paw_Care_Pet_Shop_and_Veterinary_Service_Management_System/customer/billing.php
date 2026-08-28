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

// CUSTOMER INFO er jonno sql 

$user_sql = "SELECT full_name, username, email, phone, address FROM users WHERE user_id = ? LIMIT 1";

$user_stmt = mysqli_prepare($conn, $user_sql);
mysqli_stmt_bind_param($user_stmt, "i", $customer_id);
mysqli_stmt_execute($user_stmt);
$user_result = mysqli_stmt_get_result($user_stmt);
$customer = mysqli_fetch_assoc($user_result);
mysqli_stmt_close($user_stmt);

// cart e akhon ache ta janaj jonno 

$cart_sql = "SELECT c.cart_id, c.item_type, c.item_id, c.quantity, p.pet_name AS item_name, p.price AS item_price FROM carts c JOIN pets p ON c.item_type = 'pet' AND c.item_id = p.pet_id WHERE c.customer_id = ? UNION ALL SELECT c.cart_id, c.item_type, c.item_id, c.quantity, pr.product_name AS item_name, pr.price AS item_price FROM carts c JOIN products pr ON c.item_type = 'product' AND c.item_id = pr.product_id WHERE c.customer_id = ?";

$cart_stmt = mysqli_prepare($conn, $cart_sql);
mysqli_stmt_bind_param($cart_stmt, "ii", $customer_id, $customer_id);
mysqli_stmt_execute($cart_stmt);
$cart_result = mysqli_stmt_get_result($cart_stmt);

$cart_items = [];
$subtotal = 0;

while ($item = mysqli_fetch_assoc($cart_result)) {
    $item["subtotal"] = $item["item_price"] * $item["quantity"];
    $subtotal += $item["subtotal"];
    $cart_items[] = $item;
}

mysqli_stmt_close($cart_stmt);

if (empty($cart_items)) {
    header("Location: dashboard.php");
    exit;
}

$discount = 0;
$grand_total = $subtotal - $discount;
$order_error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirm_order"])) {
    $delivery_method = $_POST["delivery_method"] ?? "";
    $payment_method = $_POST["payment_method"] ?? "";


    $allowed_delivery_methods = [ "Pathao Fast", "PetPanda Go", "Speed Fast", "Jhinku BD", "Shop Pickup" ];

    $allowed_payment_methods = [ "bKash",  "Nagad", "Rocket", "Credit Card", "Cash on Delivery" ];

    if (!in_array($delivery_method, $allowed_delivery_methods, true)) {
        $order_error = "Please select a valid delivery method.";
    }
    elseif (!in_array($payment_method, $allowed_payment_methods, true)) {
        $order_error = "Please select a valid payment method.";
    }
    else {
        mysqli_begin_transaction($conn);

        try {
            
            foreach ($cart_items as $item) {
                if ($item["item_type"] === "pet") {
                    $stock_sql = "SELECT stock FROM pets WHERE pet_id = ? FOR UPDATE";
                } else {
                    $stock_sql = "SELECT stock FROM products WHERE product_id = ? FOR UPDATE";
                }

                $stock_stmt = mysqli_prepare($conn, $stock_sql);
                mysqli_stmt_bind_param($stock_stmt, "i", $item["item_id"]);
                mysqli_stmt_execute($stock_stmt);
                $stock_result = mysqli_stmt_get_result($stock_stmt);
                $stock_row = mysqli_fetch_assoc($stock_result);
                mysqli_stmt_close($stock_stmt);

                if (!$stock_row || (int) $stock_row["stock"] < (int) $item["quantity"]) {
                    throw new Exception("One or more items do not have enough stock.");
                }
            }

            $order_sql = "INSERT INTO orders (customer_id, total_amount, delivery_address, delivery_method, payment_method, payment_status, order_status) VALUES (?, ?, ?, ?, ?, 'Pending', 'Pending')";

            $order_stmt = mysqli_prepare($conn, $order_sql);
            $delivery_address = $customer["address"] ?? "";

            mysqli_stmt_bind_param($order_stmt, "idsss", $customer_id, $grand_total, $delivery_address, $delivery_method, $payment_method);
            mysqli_stmt_execute($order_stmt);
            $order_id = mysqli_insert_id($conn);
            mysqli_stmt_close($order_stmt);


            foreach ($cart_items as $item) {
                $order_item_sql = "INSERT INTO order_items (order_id, item_type, item_id, item_name, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?)";

                $order_item_stmt = mysqli_prepare($conn, $order_item_sql);
                mysqli_stmt_bind_param($order_item_stmt, "isisdid", $order_id, $item["item_type"], $item["item_id"], $item["item_name"], $item["item_price"], $item["quantity"], $item["subtotal"]);
                mysqli_stmt_execute($order_item_stmt);
                mysqli_stmt_close($order_item_stmt);

                if ($item["item_type"] === "pet") {
                    $update_stock_sql = "UPDATE pets SET stock = stock - ? WHERE pet_id = ?";
                } else {
                    $update_stock_sql = "UPDATE products SET stock = stock - ? WHERE product_id = ?";
                }

                $stock_update_stmt = mysqli_prepare($conn, $update_stock_sql);
                mysqli_stmt_bind_param($stock_update_stmt, "ii", $item["quantity"], $item["item_id"]);
                mysqli_stmt_execute($stock_update_stmt);
                mysqli_stmt_close($stock_update_stmt);
            }

            $clear_cart_sql = "DELETE FROM carts WHERE customer_id = ?";

            $clear_cart_stmt = mysqli_prepare($conn, $clear_cart_sql);
            mysqli_stmt_bind_param($clear_cart_stmt, "i", $customer_id);
            mysqli_stmt_execute($clear_cart_stmt);
            mysqli_stmt_close($clear_cart_stmt);

            mysqli_commit($conn);
            header("Location: order_success.php?order_id=" . $order_id);
            exit;

        } catch (Throwable $e) {
            mysqli_rollback($conn);
            $order_error = "Order could not be completed. " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout | PawCare</title>
    <link rel="stylesheet" href="../assets/css/billing.css">
</head>

<body>

<?php if (!empty($order_error)): ?>
    <div class="checkout-error-toast">
        <?php echo htmlspecialchars($order_error); ?>
    </div>
<?php endif; ?>

<div class="checkout-page">

    <header class="checkout-header">
        <div class="checkout-heading">
            <h1>PREMIUM SECURE CHECKOUT</h1>
            <p>Delivery → Payment → Confirmation</p>
        </div>

        <div class="checkout-time">
            <div id="checkoutClock">00:00:00</div>
            <small id="checkoutDate">Loading date...</small>
        </div>

        <div class="checkout-customer">
            <span><?php echo htmlspecialchars($customer["username"]); ?></span>
            <div class="checkout-avatar">
                <?php echo strtoupper(substr($customer["username"], 0, 1)); ?>
            </div>
        </div>
    </header>

    <form method="POST" id="checkoutForm">
        <input type="hidden" name="payment_method" id="selectedPaymentMethod" value="Cash on Delivery">

        <main class="checkout-content">

            <section class="checkout-panel order-summary receipt-panel">
                <h2>ORDER SUMMARY</h2>

                <div class="receipt-header">
                    <h3>PAWCARE PET SHOP</h3>
                    <p>Pet Shop & Veterinary Service</p>
                    <p>Dhaka, Bangladesh</p>
                    <p>-----------------------------</p>
                </div>

                <div class="customer-order-info">
                    <p>--- PAWCARE PET SHOP ---</p>

                    <p>
                        Customer:
                        <?php echo htmlspecialchars($customer["full_name"]); ?>
                    </p>

                    <p>
                        Phone:
                        <?php echo htmlspecialchars($customer["phone"] ?? ""); ?>
                    </p>

                    <p>
                        Email:
                        <?php echo htmlspecialchars($customer["email"]); ?>
                    </p>

                    <p>
                        Address:
                        <?php echo htmlspecialchars($customer["address"] ?? ""); ?>
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
                                <td><?php echo htmlspecialchars($item["item_name"]); ?></td>
                                <td><?php echo (int) $item["quantity"]; ?>x</td>
                                <td>৳ <?php echo number_format($item["subtotal"], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="order-totals">
                    <div>
                        <span>SUBTOTAL:</span>
                        <strong>৳ <?php echo number_format($subtotal, 2); ?></strong>
                    </div>

                    <div>
                        <span>DISCOUNT:</span>
                        <strong>৳ <?php echo number_format($discount, 2); ?></strong>
                    </div>

                    <div class="grand-total">
                        <span>TOTAL:</span>
                        <strong>৳ <?php echo number_format($grand_total, 2); ?></strong>
                    </div>
                </div>
            </section>

            <section class="checkout-panel">
                <h2>🚚 DELIVERY METHOD</h2>

                <div class="delivery-options">
                    <label class="checkout-option">
                        <input type="radio" name="delivery_method" id="selectedPaymentMethod" value="Pathao Fast" checked>
                        <span>Pathao Fast</span>
                    </label>

                    <label class="checkout-option">
                        <input type="radio" name="delivery_method" value="PetPanda Go">
                        <span>PetPanda Go</span>
                    </label>

                    <label class="checkout-option">
                        <input type="radio" name="delivery_method" value="Speed Fast">
                        <span>Speed Fast</span>
                    </label>

                    <label class="checkout-option">
                        <input type="radio" name="delivery_method" value="Jhinku BD">
                        <span>Jhinku BD</span>
                    </label>

                    <label class="checkout-option">
                        <input type="radio" name="delivery_method" value="Shop Pickup">
                        <span>Shop Pickup</span>
                    </label>
                </div>

                <div class="buy-more-box">
                    <p>Want to add something else for your pet?</p>
                    <a href="dashboard.php">BUY MORE ITEMS</a>
                </div>
            </section>

            <section class="checkout-panel">
                <h2>🔒 SECURE PAYMENT</h2>

                <div class="payment-options">
                    <button type="button" class="payment-btn" data-payment="bKash">Pay with bKash</button>
                    <button type="button" class="payment-btn" data-payment="Nagad">Pay with Nagad</button>
                    <button type="button" class="payment-btn" data-payment="Rocket">Pay with Rocket</button>
                    <button type="button" class="payment-btn" data-payment="Credit Card">Pay with Credit Card</button>
                    <button type="button" class="payment-btn active" data-payment="Cash on Delivery">Pay with Cash on Delivery</button>
                </div>
            </section>

        </main>

        <footer class="checkout-footer">
            <div>
                🛡 256-bit SSL Encrypted |
                PawCare © 2026
            </div>

            <button type="submit" name="confirm_order" class="confirm-order-btn" id="confirmOrderBtn">CONFIRM ORDER</button>
        </footer>
    </form>

</div>

<div id="checkoutToast" class="checkout-toast">
    <span id="checkoutToastMessage">Selection updated.</span>
</div>

<script src="../assets/js/billing.js"></script>

</body>
</html>