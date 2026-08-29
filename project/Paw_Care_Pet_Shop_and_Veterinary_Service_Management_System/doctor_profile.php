<?php

require_once "includes/session.php";
require_once "config/database.php";

$doctor_id = isset($_GET["doctor_id"]) ? (int)$_GET["doctor_id"] : 0;
$doctor = null;
$reviews = [];
$completed_treatments = 0;
$average_rating = 0;
$total_reviews = 0;

if ($doctor_id > 0) {
    $doctor_sql = "SELECT d.doctor_id, d.specialization, d.qualification, d.experience_years, d.consultation_fee, d.available_days, d.available_time, d.bio, d.status, u.full_name, u.profile_image FROM doctors d JOIN users u ON d.user_id = u.user_id WHERE d.doctor_id = $doctor_id AND u.status = 'active' LIMIT 1";
    $doctor_result = mysqli_query($conn, $doctor_sql);

    if ($doctor_result && mysqli_num_rows($doctor_result) === 1) {
        $doctor = mysqli_fetch_assoc($doctor_result);
    }

    $treatment_sql = "SELECT COUNT(*) AS total FROM appointments WHERE doctor_id = $doctor_id AND status = 'Completed'";
    $treatment_result = mysqli_query($conn, $treatment_sql);

    if ($treatment_result) {
        $treatment_data = mysqli_fetch_assoc($treatment_result);
        $completed_treatments = (int)$treatment_data["total"];
    }

    $rating_sql = "SELECT ROUND(AVG(rating), 1) AS average_rating, COUNT(*) AS total_reviews FROM reviews WHERE item_type = 'doctor' AND item_id = $doctor_id AND status = 'Visible'";
    $rating_result = mysqli_query($conn, $rating_sql);

    if ($rating_result) {
        $rating_data = mysqli_fetch_assoc($rating_result);
        $average_rating = $rating_data["average_rating"] ?? 0;
        $total_reviews = (int)($rating_data["total_reviews"] ?? 0);
    }

    $review_sql = "SELECT r.rating, r.comment, r.created_at, u.full_name FROM reviews r JOIN users u ON r.customer_id = u.user_id WHERE r.item_type = 'doctor' AND r.item_id = $doctor_id AND r.status = 'Visible' ORDER BY r.created_at DESC";
    $review_result = mysqli_query($conn, $review_sql);

    if ($review_result) {
        while ($row = mysqli_fetch_assoc($review_result)) {
            $reviews[] = $row;
        }
    }
}

if (!$doctor) {
    header("Location: specialist_doctors.php");
    exit;
}

$profile_image = trim($doctor["profile_image"] ?? "");

if (empty($profile_image)) {
    $profile_image = "default.png";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($doctor["full_name"]); ?> | PawCare</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/doctor-profile-public.css">
</head>

<body class="doctor-profile-page">

<header class="top-bar">

    <div class="top-brand">
        <span class="brand-icon">🐾</span>
        <span>PawCare – Pet Shop and Veterinary Service Management System</span>
    </div>

    <div class="window-dots">
        <span></span>
        <span></span>
        <span></span>
    </div>

</header>

<main class="profile-container">

    <aside class="profile-sidebar">

        <div class="sidebar-brand">
            <img src="assets/images/petLogo.jpeg" alt="PawCare Logo" class="sidebar-logo">
            <h2>🐾 PAWCARE</h2>
        </div>

        <nav class="sidebar-menu">
            <a href="index.php" class="menu-btn">Home</a>
            <a href="specialist_doctors.php" class="menu-btn active">Specialist Doctors</a>
            <a href="login.php" class="menu-btn">Customer Portal</a>
            <a href="doctor/login.php" class="menu-btn doctor-login-btn">Doctor Login</a>
        </nav>

    </aside>

    <section class="profile-content">

        <div class="profile-card">

            <div class="doctor-main-info">

                <div class="doctor-image-box">
                    <img src="uploads/profiles/<?php echo htmlspecialchars($profile_image); ?>" onerror="this.onerror=null; this.src='uploads/profiles/default.png';" alt="<?php echo htmlspecialchars($doctor["full_name"]); ?>">

                    <span class="doctor-status <?php echo strtolower($doctor["status"]); ?>">
                        <?php echo htmlspecialchars($doctor["status"]); ?>
                    </span>
                </div>

                <div class="doctor-details">

                    <p class="section-label">Veterinary Specialist</p>

                    <h1><?php echo htmlspecialchars($doctor["full_name"]); ?></h1>

                    <h3><?php echo htmlspecialchars($doctor["specialization"]); ?></h3>

                    <p class="qualification">
                        <?php echo htmlspecialchars($doctor["qualification"]); ?>
                    </p>

                    <div class="stats-row">

                        <div class="stat-box">
                            <span><?php echo (int)$doctor["experience_years"]; ?>+</span>
                            <p>Years Experience</p>
                        </div>

                        <div class="stat-box">
                            <span><?php echo $completed_treatments; ?></span>
                            <p>Completed Treatments</p>
                        </div>

                        <div class="stat-box">
                            <span><?php echo number_format((float)$average_rating, 1); ?> ★</span>
                            <p><?php echo $total_reviews; ?> Reviews</p>
                        </div>

                    </div>

                    <div class="availability-box">

                        <p>
                            <strong>Available Days:</strong>
                            <?php echo htmlspecialchars($doctor["available_days"]); ?>
                        </p>

                        <p>
                            <strong>Available Time:</strong>
                            <?php echo htmlspecialchars($doctor["available_time"]); ?>
                        </p>

                        <p>
                            <strong>Consultation Fee:</strong>
                            ৳<?php echo number_format((float)$doctor["consultation_fee"], 2); ?>
                        </p>

                    </div>

                    <a href="book_appointment.php?doctor_id=<?php echo (int)$doctor["doctor_id"]; ?>" class="book-btn">
                        Book Appointment
                    </a>

                </div>

            </div>

            <div class="about-section">

                <h2>About Doctor</h2>

                <p>
                    <?php echo nl2br(htmlspecialchars($doctor["bio"])); ?>
                </p>

            </div>

            <div class="review-section">

                <div class="review-heading">
                    <div>
                        <h2>Customer Reviews</h2>
                        <p>Feedback from customers who visited this doctor.</p>
                    </div>

                    <div class="rating-summary">
                        <?php echo number_format((float)$average_rating, 1); ?> ★
                    </div>
                </div>

                <?php if (!empty($reviews)): ?>

                    <div class="review-list">

                        <?php foreach ($reviews as $review): ?>

                            <div class="review-card">

                                <div class="review-top">

                                    <div>
                                        <h4><?php echo htmlspecialchars($review["full_name"]); ?></h4>

                                        <span class="review-date">
                                            <?php echo date("d M Y", strtotime($review["created_at"])); ?>
                                        </span>
                                    </div>

                                    <div class="review-stars">
                                        <?php echo str_repeat("★", (int)$review["rating"]); ?>
                                    </div>

                                </div>

                                <p>
                                    <?php echo htmlspecialchars($review["comment"]); ?>
                                </p>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php else: ?>

                    <div class="no-reviews">
                        <p>No reviews available for this doctor yet.</p>
                    </div>

                <?php endif; ?>

            </div>

        </div>

    </section>

</main>

<footer class="profile-footer">
    🐾 "Care, compassion and trust for every pet." | PawCare Premium Pet Clinic © 2026
</footer>

</body>

</html>