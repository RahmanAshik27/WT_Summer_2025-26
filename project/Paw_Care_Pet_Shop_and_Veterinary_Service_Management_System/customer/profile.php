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


// ==========================================
// FETCH CUSTOMER INFORMATION
// ==========================================

$user_sql = "
    SELECT
        user_id,
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


if (!$customer) {
    header("Location: ../login.php");
    exit;
}


// ==========================================
// FETCH RECENT ORDERS
// ==========================================

$order_sql = "
    SELECT
        order_id,
        total_amount,
        delivery_method,
        payment_method,
        payment_status,
        order_status,
        order_date
    FROM orders
    WHERE customer_id = ?
    ORDER BY order_date DESC
    LIMIT 3
";

$order_stmt =
    mysqli_prepare($conn, $order_sql);

mysqli_stmt_bind_param(
    $order_stmt,
    "i",
    $customer_id
);

mysqli_stmt_execute($order_stmt);

$order_result =
    mysqli_stmt_get_result($order_stmt);

$recent_orders = [];

while ($order = mysqli_fetch_assoc($order_result)) {
    $recent_orders[] = $order;
}

mysqli_stmt_close($order_stmt);

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
        Customer Profile | PawCare
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/customer-profile.css"
    >

</head>

<body>

<div class="profile-page">

    <!-- =========================
         TOP NAVIGATION
    ========================== -->

    <header class="profile-header">

        <div class="profile-brand">
            PawCare
        </div>

        <nav class="profile-nav">

            <a href="dashboard.php">
                Dashboard
            </a>

            <a
                href="profile.php"
                class="active"
            >
                Profile
            </a>

        </nav>


        <div class="profile-header-right">

            <div class="profile-search">

                <input
                    type="text"
                    placeholder="Search..."
                >

            </div>

            <div class="header-avatar">

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
         MAIN PROFILE AREA
    ========================== -->

    <main class="profile-content">


        <!-- =====================
             LEFT COLUMN
        ====================== -->

        <aside class="profile-left">


            <section class="profile-card">

                <div class="profile-avatar-large">

                    <?php echo strtoupper(
                        substr(
                            $customer["username"],
                            0,
                            1
                        )
                    ); ?>

                </div>


                <h1>
                    <?php echo strtoupper(
                        htmlspecialchars(
                            $customer["full_name"]
                        )
                    ); ?>
                </h1>


                <p class="profile-username">

                    @<?php echo htmlspecialchars(
                        $customer["username"]
                    ); ?>

                </p>


                <span class="customer-badge">
                    CUSTOMER
                </span>


                <div class="profile-actions">

                    <button
                        type="button"
                        class="edit-profile-btn"
                    >
                        ✎ Edit Profile
                    </button>


                    <button
                        type="button"
                        class="change-password-btn"
                    >
                        🔒 Change Password
                    </button>


                    <button
                        type="button"
                        class="logout-btn"
                    >
                        ↪ Logout
                    </button>

                </div>

            </section>


            <section class="profile-shop-card">

                <img
                    src="../assets/images/petLogo.jpeg"
                    alt="PawCare Logo"
                >

                <h2>
                    🐾 PAWCARE
                </h2>

            </section>

        </aside>


        <!-- =====================
             RIGHT COLUMN
        ====================== -->

        <section class="profile-right">


            <!-- PERSONAL INFORMATION -->

            <section class="information-panel">

                <div class="panel-title">
                    👤 Personal Information
                </div>


                <div class="information-grid">

                    <div class="info-box">

                        <label>
                            EMAIL ADDRESS
                        </label>

                        <div>
                            ✉
                            <?php echo htmlspecialchars(
                                $customer["email"]
                            ); ?>
                        </div>

                    </div>


                    <div class="info-box">

                        <label>
                            PHONE NUMBER
                        </label>

                        <div>
                            ☎
                            <?php echo htmlspecialchars(
                                $customer["phone"] ?? ""
                            ); ?>
                        </div>

                    </div>


                    <div class="info-box full-width">

                        <label>
                            SHIPPING ADDRESS
                        </label>

                        <div>
                            📍
                            <?php echo htmlspecialchars(
                                $customer["address"] ?? ""
                            ); ?>
                        </div>

                    </div>

                </div>

            </section>


            <!-- RECENT ORDERS -->

            <section class="recent-orders-panel">

                <div class="orders-heading">

                    <div class="panel-title">
                        🚚 Recent Orders
                    </div>

                    <a href="#">
                        View All
                    </a>

                </div>


                <div class="recent-orders-list">

                    <?php if (empty($recent_orders)): ?>

                        <div class="no-orders">
                            No orders found yet.
                        </div>

                    <?php else: ?>


                        <?php foreach ($recent_orders as $order): ?>

                            <article class="order-card">

                                <div class="order-field">

                                    <small>
                                        Order ID
                                    </small>

                                    <strong>
                                        #<?php echo
                                            (int) $order["order_id"];
                                        ?>
                                    </strong>

                                </div>


                                <div class="order-field">

                                    <small>
                                        Date
                                    </small>

                                    <strong>
                                        <?php echo date(
                                            "d M Y",
                                            strtotime(
                                                $order["order_date"]
                                            )
                                        ); ?>
                                    </strong>

                                </div>


                                <div class="order-field">

                                    <small>
                                        Total
                                    </small>

                                    <strong class="order-price">
                                        ৳<?php echo number_format(
                                            $order["total_amount"],
                                            2
                                        ); ?>
                                    </strong>

                                </div>


                                <div class="order-field">

                                    <small>
                                        Status
                                    </small>

                                    <span class="order-status">

                                        <?php echo htmlspecialchars(
                                            $order["order_status"]
                                        ); ?>

                                    </span>

                                </div>


                                <div class="order-methods">

                                    <span>
                                        🚚
                                        <?php echo htmlspecialchars(
                                            $order["delivery_method"]
                                        ); ?>
                                    </span>

                                    <span>
                                        💳
                                        <?php echo htmlspecialchars(
                                            $order["payment_method"]
                                        ); ?>
                                    </span>

                                </div>


                                <a
                                    href="order_details.php?order_id=<?php
                                        echo (int) $order["order_id"];
                                    ?>"
                                    class="order-details-btn"
                                >
                                    [ VIEW DETAILS ]
                                </a>

                            </article>

                        <?php endforeach; ?>


                    <?php endif; ?>

                </div>

            </section>

        </section>

    </main>


    <!-- =========================
         FOOTER
    ========================== -->

    <footer class="profile-footer">

        <span>
            PawCare
        </span>

        <span>
            © 2026 PawCare Systems.
            Professional Pet Management.
        </span>

        <span>
            Privacy Policy · Help Desk
        </span>

    </footer>

</div>

</body>

</html>