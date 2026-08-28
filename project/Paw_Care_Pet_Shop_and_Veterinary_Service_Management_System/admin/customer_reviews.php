<?php

require_once "../includes/session.php";
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: ../login.php");
    exit;
}

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_review_status"])) {
    $review_id = (int)($_POST["review_id"] ?? 0);
    $status = $_POST["status"] ?? "";

    if ($review_id <= 0 || !in_array($status, ["Visible", "Hidden"], true)) {
        $error_message = "Invalid review information.";
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE reviews SET status = ? WHERE review_id = ?");
        mysqli_stmt_bind_param($stmt, "si", $status, $review_id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) >= 0) {
            $success_message = "Review status updated successfully.";
        } else {
            $error_message = "Review status could not be updated.";
        }

        mysqli_stmt_close($stmt);
    }
}

$reviews = [];

$sql = "SELECT
            r.review_id,
            r.item_type,
            r.item_id,
            r.rating,
            r.comment,
            r.status,
            r.created_at,
            u.full_name AS customer_name,
            CASE
                WHEN r.item_type = 'pet' THEN p.pet_name
                WHEN r.item_type = 'product' THEN pr.product_name
            END AS item_name
        FROM reviews r
        JOIN users u ON r.customer_id = u.user_id
        LEFT JOIN pets p ON r.item_type = 'pet' AND r.item_id = p.pet_id
        LEFT JOIN products pr ON r.item_type = 'product' AND r.item_id = pr.product_id
        ORDER BY r.created_at DESC";

$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $reviews[] = $row;
    }
}

$champion_pet = null;

$champion_sql = "SELECT
                    p.pet_id,
                    p.pet_name,
                    p.breed,
                    p.image,
                    COALESCE(rv.average_rating, 0) AS average_rating,
                    COALESCE(rv.total_reviews, 0) AS total_reviews,
                    COALESCE(sl.total_sold, 0) AS total_sold
                 FROM pets p
                 LEFT JOIN (
                    SELECT
                        item_id,
                        ROUND(AVG(rating), 1) AS average_rating,
                        COUNT(*) AS total_reviews
                    FROM reviews
                    WHERE item_type = 'pet' AND status = 'Visible'
                    GROUP BY item_id
                 ) rv ON p.pet_id = rv.item_id
                 LEFT JOIN (
                    SELECT
                        oi.item_id,
                        SUM(oi.quantity) AS total_sold
                    FROM order_items oi
                    JOIN orders o ON oi.order_id = o.order_id
                    WHERE oi.item_type = 'pet' AND o.payment_status = 'Paid'
                    GROUP BY oi.item_id
                 ) sl ON p.pet_id = sl.item_id
                 WHERE rv.total_reviews > 0
                 ORDER BY rv.average_rating DESC, sl.total_sold DESC, rv.total_reviews DESC
                 LIMIT 1";

$champion_result = mysqli_query($conn, $champion_sql);

if ($champion_result && mysqli_num_rows($champion_result) > 0) {
    $champion_pet = mysqli_fetch_assoc($champion_result);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Reviews - PawCare</title>
    <link rel="stylesheet" href="../assets/css/admin-reviews.css">
</head>
<body>

<div class="admin-page">

    <header class="top-header">
        <div>
            <h1>WELCOME BACK, MASTER!</h1>
            <p>♛ Your Pet Somrajjo is under your command, King!</p>
        </div>

        <div class="date-time">
            <div id="currentDate"></div>
            <div id="currentTime"></div>
        </div>
    </header>

    <div class="admin-layout">

        <aside class="sidebar">

            <div class="logo-area">
                <img src="../assets/images/petLogo.jpeg" alt="Pet Shop">
                <h3>🐾 PET SHOP</h3>
            </div>

            <nav>
                <a href="dashboard_overview.php">⌁ Dashboard Overview</a>
                <a href="inventory.php">▥ Inventory Stock</a>
                <a href="sales_analytics.php">◔ Sales Analytics</a>
                <a href="delivery_tracking.php">🚚 Delivery Tracking</a>
                <a href="customer_reviews.php" class="active">★ Customer Reviews</a>
            </nav>

        </aside>

        <main class="main-content">

            <div class="welcome-strip">
                Welcome back, Boss! Your customers are talking about their pets today.
            </div>

            <?php if ($success_message): ?>
                <div class="success-message">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <div class="reviews-layout">

                <section class="reviews-column">

                    <?php if (count($reviews) > 0): ?>

                        <?php foreach ($reviews as $review): ?>

                            <div class="review-card">

                                <div class="review-avatar">
                                    <?php echo strtoupper(substr($review["customer_name"], 0, 1)); ?>
                                </div>

                                <div class="review-info">

                                    <div class="review-title">
                                        <h3><?php echo htmlspecialchars($review["customer_name"]); ?></h3>
                                        <span><?php echo date("d M", strtotime($review["created_at"])); ?></span>
                                    </div>

                                    <p class="review-product">
                                        <?php echo ucfirst($review["item_type"]); ?>:
                                        <?php echo htmlspecialchars($review["item_name"] ?? "Unknown Item"); ?>
                                    </p>

                                    <div class="stars">
                                        <?php
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $review["rating"] ? "★" : "☆";
                                        }
                                        ?>
                                    </div>

                                    <p class="review-comment">
                                        <?php echo htmlspecialchars($review["comment"] ?: "No written comment."); ?>
                                    </p>

                                    <div class="review-bottom">

                                        <span class="review-status <?php echo strtolower($review["status"]); ?>">
                                            <?php echo htmlspecialchars($review["status"]); ?>
                                        </span>

                                        <form method="POST">
                                            <input type="hidden" name="review_id" value="<?php echo $review["review_id"]; ?>">

                                            <?php if ($review["status"] === "Visible"): ?>
                                                <input type="hidden" name="status" value="Hidden">
                                                <button type="submit" name="update_review_status">Hide</button>
                                            <?php else: ?>
                                                <input type="hidden" name="status" value="Visible">
                                                <button type="submit" name="update_review_status">Show</button>
                                            <?php endif; ?>
                                        </form>

                                    </div>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="empty-reviews">
                            No customer reviews available.
                        </div>

                    <?php endif; ?>

                </section>

                <aside class="champion-panel">

                    <h3>🏆 CHAMPION PET</h3>

                    <?php if ($champion_pet): ?>

                        <?php
                        $champion_image = trim($champion_pet["image"] ?? "");

                        if (empty($champion_image)) {
                            $champion_image = "default_pet.jpg";
                        }
                        ?>

                        <div class="champion-image">
                            <img
                                src="../uploads/pets/<?php echo htmlspecialchars($champion_image); ?>"
                                onerror="this.onerror=null; this.src='../uploads/pets/default_pet.jpg';"
                                alt="<?php echo htmlspecialchars($champion_pet["pet_name"]); ?>"
                            >
                        </div>

                        <h2><?php echo strtoupper(htmlspecialchars($champion_pet["pet_name"])); ?></h2>

                        <p class="champion-breed">
                            <?php echo htmlspecialchars($champion_pet["breed"]); ?>
                        </p>

                        <div class="champion-stars">
                            ★ <?php echo number_format((float)$champion_pet["average_rating"], 1); ?>
                        </div>

                        <div class="champion-sales">
                            <?php echo (int)$champion_pet["total_sold"]; ?> Sold
                        </div>

                        <p class="champion-text">
                            Highest customer satisfaction and sales performance from the pet-lover community.
                        </p>

                        <small>
                            <?php echo (int)$champion_pet["total_reviews"]; ?> customer review(s)
                        </small>

                        <div class="champion-line"></div>

                    <?php else: ?>

                        <div class="no-champion">
                            <div>🐾</div>
                            <p>No pet review available yet.</p>
                        </div>

                    <?php endif; ?>

                </aside>

            </div>

        </main>

    </div>

    <footer>
        🔥 POWERFUL PET MANAGEMENT ENGINE V2.0 LIVE | SYSTEM SECURED
    </footer>

</div>

<script src="../assets/js/admin-dashboard.js"></script>

</body>
</html>