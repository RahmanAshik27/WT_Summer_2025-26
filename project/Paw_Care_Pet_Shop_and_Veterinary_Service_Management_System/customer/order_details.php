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
    header("Location: profile.php");
    exit;
}


// ==========================================
// FETCH ORDER
// ==========================================

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

$order_stmt =
    mysqli_prepare($conn, $order_sql);

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
    header("Location: profile.php");
    exit;
}


// ==========================================
// FETCH ORDER ITEMS
// ==========================================

$item_sql = "
    SELECT
        item_type,
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
        Order Details | PawCare
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/order-details.css"
    >

</head>

<body>

<div class="order-details-page">

    <header class="order-details-header">

        <div>

            <h1>
                ORDER DETAILS
            </h1>

            <p>
                PawCare Customer Order Information
            </p>

        </div>


        <div class="header-order-id">

            ORDER
            <strong>
                #<?php echo $order["order_id"]; ?>
            </strong>

        </div>

    </header>


    <main class="order-details-content">


        <!-- =========================
             ORDER OVERVIEW
        ========================== -->

        <section class="details-card">

            <h2>
                Order Overview
            </h2>


            <div class="overview-grid">

                <div>

                    <span>
                        Order Date
                    </span>

                    <strong>
                        <?php echo date(
                            "d M Y, h:i A",
                            strtotime(
                                $order["order_date"]
                            )
                        ); ?>
                    </strong>

                </div>


                <div>

                    <span>
                        Order Status
                    </span>

                    <strong class="status-badge">
                        <?php echo htmlspecialchars(
                            $order["order_status"]
                        ); ?>
                    </strong>

                </div>


                <div>

                    <span>
                        Payment Status
                    </span>

                    <strong>
                        <?php echo htmlspecialchars(
                            $order["payment_status"]
                        ); ?>
                    </strong>

                </div>


                <div>

                    <span>
                        Total Amount
                    </span>

                    <strong class="total-highlight">

                        ৳<?php echo number_format(
                            $order["total_amount"],
                            2
                        ); ?>

                    </strong>

                </div>

            </div>

        </section>


        <!-- =========================
             CUSTOMER INFO
        ========================== -->

        <section class="details-card">

            <h2>
                Customer Information
            </h2>


            <div class="information-grid">

                <div>

                    <span>
                        Full Name
                    </span>

                    <strong>
                        <?php echo htmlspecialchars(
                            $order["full_name"]
                        ); ?>
                    </strong>

                </div>


                <div>

                    <span>
                        Username
                    </span>

                    <strong>
                        @<?php echo htmlspecialchars(
                            $order["username"]
                        ); ?>
                    </strong>

                </div>


                <div>

                    <span>
                        Email
                    </span>

                    <strong>
                        <?php echo htmlspecialchars(
                            $order["email"]
                        ); ?>
                    </strong>

                </div>


                <div>

                    <span>
                        Phone
                    </span>

                    <strong>
                        <?php echo htmlspecialchars(
                            $order["phone"] ?? ""
                        ); ?>
                    </strong>

                </div>

            </div>

        </section>


        <!-- =========================
             DELIVERY / PAYMENT
        ========================== -->

        <section class="details-card">

            <h2>
                Delivery & Payment
            </h2>


            <div class="information-grid">

                <div>

                    <span>
                        Delivery Method
                    </span>

                    <strong>
                        <?php echo htmlspecialchars(
                            $order["delivery_method"]
                        ); ?>
                    </strong>

                </div>


                <div>

                    <span>
                        Payment Method
                    </span>

                    <strong>
                        <?php echo htmlspecialchars(
                            $order["payment_method"]
                        ); ?>
                    </strong>

                </div>


                <div class="full-width">

                    <span>
                        Delivery Address
                    </span>

                    <strong>
                        <?php echo htmlspecialchars(
                            $order["delivery_address"]
                        ); ?>
                    </strong>

                </div>

            </div>

        </section>


        <!-- =========================
             ORDERED ITEMS
        ========================== -->

        <section class="details-card">

            <h2>
                Ordered Items
            </h2>


            <div class="table-wrapper">

                <table class="order-items-table">

                    <thead>

                        <tr>
                            <th>ITEM</th>
                            <th>TYPE</th>
                            <th>PRICE</th>
                            <th>QTY</th>
                            <th>SUBTOTAL</th>
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
                                <?php echo ucfirst(
                                    htmlspecialchars(
                                        $item["item_type"]
                                    )
                                ); ?>
                            </td>

                            <td>
                                ৳<?php echo number_format(
                                    $item["price"],
                                    2
                                ); ?>
                            </td>

                            <td>
                                <?php echo
                                    (int) $item["quantity"];
                                ?>
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

            </div>


            <div class="details-grand-total">

                <span>
                    GRAND TOTAL
                </span>

                <strong>
                    ৳<?php echo number_format(
                        $order["total_amount"],
                        2
                    ); ?>
                </strong>

            </div>

        </section>


        <div class="details-actions">

            <a
                href="profile.php"
                class="back-profile-btn"
            >
                ← BACK TO PROFILE
            </a>

            <a
                href="order_success.php?order_id=<?php
                    echo $order["order_id"];
                ?>"
                class="receipt-btn"
            >
                VIEW RECEIPT
            </a>

        </div>

    </main>

</div>

</body>

</html>