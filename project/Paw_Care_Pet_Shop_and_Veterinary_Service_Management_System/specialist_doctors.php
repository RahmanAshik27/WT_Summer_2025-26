<?php

require_once "includes/session.php";
require_once "config/database.php";

$doctors = [];

$sql = "SELECT
        d.doctor_id,
        d.specialization,
        d.qualification,
        d.experience_years,
        d.consultation_fee,
        d.available_days,
        d.available_time,
        d.bio,
        d.status,
        u.full_name,
        u.profile_image
        FROM doctors d
        JOIN users u ON d.user_id = u.user_id
        WHERE u.status = 'active'
        ORDER BY d.doctor_id ASC";

$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $doctors[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Specialist Doctors | PawCare</title>

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/specialist-doctors.css">
</head>

<body class="specialist-page">

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

<main class="doctor-container">

    <aside class="doctor-sidebar">

        <div class="sidebar-brand">

            <img src="assets/images/petLogo.jpeg" alt="PawCare Logo" class="sidebar-logo">

            <h2>🐾 PAWCARE</h2>

        </div>

        <nav class="sidebar-menu">

            <a href="index.php" class="menu-btn">Home</a>

            <a href="login.php" class="menu-btn">
                Admin Access
            </a>

            <a href="login.php" class="menu-btn">
                Customer Portal
            </a>

            <a href="#" class="menu-btn">
                Pet Reviews
            </a>

            <a href="specialist_doctors.php" class="menu-btn active">
                Specialist Doctors
            </a>

            <a href="login.php" class="menu-btn">
                Delivery Man Portal
            </a>

        </nav>

    </aside>

    <section class="doctor-content">

        <div class="doctor-heading">

            <span>🐾</span>

            <div>
                <h1>Our Specialist Doctors</h1>
                <p>Meet our trusted veterinary specialists and choose the right doctor for your pet.</p>
            </div>

        </div>

        <div class="doctor-grid">

            <?php if (!empty($doctors)): ?>

                <?php foreach ($doctors as $doctor): ?>

                    <?php

                    $profile_image = trim($doctor["profile_image"] ?? "");

                    if (empty($profile_image)) {
                        $profile_image = "default.png";
                    }

                    ?>

                    <article class="doctor-card">

                        <div class="doctor-photo">

                            <img
                                src="uploads/profiles/<?php echo htmlspecialchars($profile_image); ?>"
                                onerror="this.onerror=null; this.src='uploads/profiles/default.png';"
                                alt="<?php echo htmlspecialchars($doctor["full_name"]); ?>"
                            >

                            <span class="availability <?php echo strtolower($doctor["status"]); ?>">
                                <?php echo htmlspecialchars($doctor["status"]); ?>
                            </span>

                        </div>

                        <div class="doctor-info">

                            <h2>
                                <?php echo htmlspecialchars($doctor["full_name"]); ?>
                            </h2>

                            <h4>
                                <?php echo htmlspecialchars($doctor["specialization"]); ?>
                            </h4>

                            <p class="qualification">
                                <?php echo htmlspecialchars($doctor["qualification"]); ?>
                                •
                                <?php echo (int)$doctor["experience_years"]; ?> Years Experience
                            </p>

                            <div class="doctor-details">

                                <p>
                                    <strong>Available</strong>
                                    <?php echo htmlspecialchars($doctor["available_days"]); ?>
                                </p>

                                <p>
                                    <strong>Time</strong>
                                    <?php echo htmlspecialchars($doctor["available_time"]); ?>
                                </p>

                                <p>
                                    <strong>Consultation Fee</strong>
                                    ৳<?php echo number_format((float)$doctor["consultation_fee"], 2); ?>
                                </p>

                            </div>

                            <div class="doctor-actions">

                                <a
                                    href="doctor_profile.php?doctor_id=<?php echo (int)$doctor["doctor_id"]; ?>"
                                    class="profile-btn">
                                    View Profile
                                </a>

                                <a
                                    href="book_appointment.php?doctor_id=<?php echo (int)$doctor["doctor_id"]; ?>"
                                    class="appointment-btn">
                                    Book Appointment
                                </a>

                            </div>

                        </div>

                    </article>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="no-doctors">

                    <span>🐾</span>
                    <h2>No Doctors Available</h2>
                    <p>Doctor information will appear here.</p>

                </div>

            <?php endif; ?>

        </div>

    </section>

</main>

<footer class="doctor-footer">
    🐾 "Pets are not our whole life..." | PawCare Premium Pet Clinic © 2026
</footer>

</body>

</html>