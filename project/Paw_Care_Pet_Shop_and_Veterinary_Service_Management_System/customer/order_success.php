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

$customer_id = (int) $_SESSION["user_id"];
$order_id = (int) ($_GET["order_id"] ?? 0);

if ($order_id <= 0) {
    header("Location: dashboard.php");
    exit;
}



// FETCH ORDER


$order_sql = "
    SELECT
        o.order_id,
        o.total_amount,
        o.delivery_address,
        o.delivery_method,
        o.payment_method,
        o.payment_status,
        o.order_status,
        o.order_date,

        u.full_name,
        u.username,
        u.email,
        u.phone

    FROM orders o

    JOIN users u
        ON o.customer_id = u.user_id

    WHERE o.order_id = ?
      AND o.customer_id = ?

    LIMIT 1
";

$order_stmt = mysqli_prepare($conn, $order_sql);

mysqli_stmt_bind_param(
    $order_stmt,
    "ii",
    $order_id,
    $customer_id
);

mysqli_stmt_execute($order_stmt);

$order_result =
    mysqli_stmt_get_result($order_stmt);

$order =
    mysqli_fetch_assoc($order_result);

mysqli_stmt_close($order_stmt);


if (!$order) {
    header("Location: dashboard.php");
    exit;
}



// FETCH ORDER ITEMS


$item_sql = "
    SELECT
        item_name,
        price,
        quantity,
        subtotal
    FROM order_items
    WHERE order_id = ?
    ORDER BY order_item_id ASC
";

$item_stmt =
    mysqli_prepare($conn, $item_sql);

mysqli_stmt_bind_param(
    $item_stmt,
    "i",
    $order_id
);

mysqli_stmt_execute($item_stmt);

$item_result =
    mysqli_stmt_get_result($item_stmt);

$order_items = [];

while ($item = mysqli_fetch_assoc($item_result)) {
    $order_items[] = $item;
}

mysqli_stmt_close($item_stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Order Confirmed | PawCare
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/order-success.css"
    >

</head>

<body>

<div class="success-page">

    <div class="success-card">

        <div class="success-icon">
            ✓
        </div>

        <h1>
            ORDER CONFIRMED
        </h1>

        <p class="success-message">
            Thank you for choosing PawCare!
            Your order has been placed successfully.
        </p>


        <div class="order-number">

            ORDER ID

            <strong>
                #<?php echo $order["order_id"]; ?>
            </strong>

        </div>


        <div class="receipt">

            <div class="receipt-header">

                <h2>
                    PAWCARE PET SHOP
                </h2>

                <p>
                    Pet Shop & Veterinary Service
                </p>

                <p>
                    ------------------------------
                </p>

            </div>


            <div class="receipt-info">

                <p>
                    <span>Customer:</span>

                    <?php echo htmlspecialchars(
                        $order["full_name"]
                    ); ?>
                </p>

                <p>
                    <span>Phone:</span>

                    <?php echo htmlspecialchars(
                        $order["phone"] ?? ""
                    ); ?>
                </p>

                <p>
                    <span>Date:</span>

                    <?php echo htmlspecialchars(
                        $order["order_date"]
                    ); ?>
                </p>

                <p>
                    <span>Delivery:</span>

                    <?php echo htmlspecialchars(
                        $order["delivery_method"]
                    ); ?>
                </p>

                <p>
                    <span>Payment:</span>

                    <?php echo htmlspecialchars(
                        $order["payment_method"]
                    ); ?>
                </p>

                <p>
                    <span>Address:</span>

                    <?php echo htmlspecialchars(
                        $order["delivery_address"]
                    ); ?>
                </p>

            </div>


            <div class="receipt-line">
                --------------------------------
            </div>


            <table class="receipt-table">

                <thead>

                    <tr>
                        <th>ITEM</th>
                        <th>QTY</th>
                        <th>PRICE</th>
                    </tr>

                </thead>

                <tbody>

                <?php foreach ($order_items as $item): ?>

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
                            ৳<?php echo number_format(
                                $item["subtotal"],
                                2
                            ); ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>


            <div class="receipt-line">
                --------------------------------
            </div>


            <div class="receipt-total">

                <span>
                    TOTAL
                </span>

                <strong>
                    ৳<?php echo number_format(
                        $order["total_amount"],
                        2
                    ); ?>
                </strong>

            </div>


            <div class="status-area">

                <div>
                    Payment Status

                    <strong>
                        <?php echo htmlspecialchars(
                            $order["payment_status"]
                        ); ?>
                    </strong>
                </div>


                <div>
                    Order Status

                    <strong>
                        <?php echo htmlspecialchars(
                            $order["order_status"]
                        ); ?>
                    </strong>
                </div>

            </div>


            <div class="receipt-footer">

                Thank you for shopping with PawCare 🐾

                <br>

                Keep your Order ID for future reference.

            </div>

        </div>


        <div class="success-actions">

            <a
                href="dashboard.php"
                class="dashboard-btn"
            >
                ← BACK TO DASHBOARD
            </a>

        </div>

    </div>

</div>

</body>

</html>