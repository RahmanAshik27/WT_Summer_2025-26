<?php

require_once "../includes/session.php";
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "doctor") {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION["user_id"];

$doctor_sql = "SELECT d.doctor_id, d.specialization, d.qualification, d.experience_years, d.consultation_fee, d.available_days, d.available_time, d.status, u.full_name, u.email, u.phone, u.profile_image FROM doctors d JOIN users u ON d.user_id = u.user_id WHERE d.user_id = $user_id LIMIT 1";
$doctor_result = mysqli_query($conn, $doctor_sql);

if (!$doctor_result || mysqli_num_rows($doctor_result) !== 1) {
    header("Location: login.php");
    exit;
}

$doctor = mysqli_fetch_assoc($doctor_result);
$doctor_id = (int)$doctor["doctor_id"];

$total_appointments = 0;
$pending_appointments = 0;
$completed_appointments = 0;
$today_appointments = 0;

// Total appointments
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM appointments WHERE doctor_id = $doctor_id");

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $total_appointments = (int)$row["total"];
}

// Pending appointments
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM appointments WHERE doctor_id = $doctor_id AND status = 'Pending'");

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $pending_appointments = (int)$row["total"];
}

// Completed appointments
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM appointments WHERE doctor_id = $doctor_id AND status = 'Completed'");

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $completed_appointments = (int)$row["total"];
}

// Today's appointments
$today = date("Y-m-d");

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM appointments WHERE doctor_id = ? AND appointment_date = ?");
mysqli_stmt_bind_param($stmt, "is", $doctor_id, $today);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $today_appointments = (int)$row["total"];
}

mysqli_stmt_close($stmt);

// Upcoming appointments
$upcoming_appointments = [];

$sql = "SELECT a.appointment_id, a.pet_name, a.pet_type, a.appointment_date, a.appointment_time, a.status, u.full_name AS customer_name
        FROM appointments a
        JOIN users u ON a.customer_id = u.user_id
        WHERE a.doctor_id = ?
        AND a.appointment_date >= CURDATE()
        AND a.status != 'Cancelled'
        ORDER BY a.appointment_date ASC, a.appointment_time ASC
        LIMIT 6";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $doctor_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

while ($result && $row = mysqli_fetch_assoc($result)) {
    $upcoming_appointments[] = $row;
}

mysqli_stmt_close($stmt);

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

    <title>Doctor Dashboard | PawCare</title>

    <link rel="stylesheet" href="../assets/css/doctor-dashboard.css">
</head>

<body class="doctor-dashboard-page">

<header class="top-bar">

    <div class="top-brand">
        <span>🐾</span>
        <span>PawCare – Doctor Portal</span>
    </div>

    <div class="window-dots">
        <span></span>
        <span></span>
        <span></span>
    </div>

</header>

<aside class="doctor-sidebar">

    <div class="doctor-profile">

        <img src="../uploads/profiles/<?php echo htmlspecialchars($profile_image); ?>"
             onerror="this.onerror=null; this.src='../uploads/profiles/default.png';"
             alt="<?php echo htmlspecialchars($doctor["full_name"]); ?>">

        <h2><?php echo htmlspecialchars($doctor["full_name"]); ?></h2>
        <p><?php echo htmlspecialchars($doctor["specialization"]); ?></p>

    </div>

<nav class="sidebar-menu">
    <a href="dashboard.php" class="active">Dashboard</a>
    <a href="appointments.php">Appointments</a>
    <a href="medical_records.php">Medical Records</a>
    <a href="../logout.php">Logout</a>
</nav>

</aside>

<main class="dashboard-content">
    <div class="dashboard-heading">
        <div>
            <p>Doctor Portal</p>
            <h1>Welcome, Dr. <?php echo htmlspecialchars($doctor["full_name"]); ?></h1>
            <span>Manage your appointments and patient care from one place.</span>
        </div>
        <div class="doctor-status">
            <?php echo htmlspecialchars($doctor["status"]); ?>
        </div>

    </div>

    <section class="stats-grid">

        <div class="stat-card">
            <span>Total Appointments</span>
            <strong><?php echo $total_appointments; ?></strong>
        </div>

        <div class="stat-card">
            <span>Pending</span>
            <strong><?php echo $pending_appointments; ?></strong>
        </div>

        <div class="stat-card">
            <span>Today's Appointments</span>
            <strong><?php echo $today_appointments; ?></strong>
        </div>

        <div class="stat-card">
            <span>Completed Treatments</span>
            <strong><?php echo $completed_appointments; ?></strong>
        </div>

    </section>

    <section class="dashboard-grid">

        <div class="dashboard-card">

            <div class="card-heading">
                <h2>Upcoming Appointments</h2>
                <a href="appointments.php">View All</a>
            </div>

            <?php if (!empty($upcoming_appointments)): ?>

                <div class="appointment-list">

                    <?php foreach ($upcoming_appointments as $appointment): ?>

                        <div class="appointment-item">

                            <div class="appointment-main">

                                <h3><?php echo htmlspecialchars($appointment["pet_name"]); ?></h3>

                                <p>
                                    <?php echo htmlspecialchars($appointment["pet_type"]); ?>
                                    ·
                                    Guardian: <?php echo htmlspecialchars($appointment["customer_name"]); ?>
                                </p>

                            </div>

                            <div class="appointment-date">

                                <strong>
                                    <?php echo date("d M Y", strtotime($appointment["appointment_date"])); ?>
                                </strong>

                                <span>
                                    <?php echo date("h:i A", strtotime($appointment["appointment_time"])); ?>
                                </span>

                            </div>

                            <div class="appointment-status">
                                <?php echo htmlspecialchars($appointment["status"]); ?>
                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="empty-message">
                    No upcoming appointments found.
                </div>

            <?php endif; ?>

        </div>

        <div class="dashboard-card doctor-info-card">

            <div class="card-heading">
                <h2>Doctor Information</h2>
            </div>

            <div class="doctor-info-row">
                <span>Qualification</span>
                <strong><?php echo htmlspecialchars($doctor["qualification"]); ?></strong>
            </div>

            <div class="doctor-info-row">
                <span>Experience</span>
                <strong><?php echo (int)$doctor["experience_years"]; ?> Years</strong>
            </div>

            <div class="doctor-info-row">
                <span>Consultation Fee</span>
                <strong>৳<?php echo number_format((float)$doctor["consultation_fee"], 2); ?></strong>
            </div>

            <div class="doctor-info-row">
                <span>Available Days</span>
                <strong><?php echo htmlspecialchars($doctor["available_days"]); ?></strong>
            </div>

            <div class="doctor-info-row">
                <span>Available Time</span>
                <strong><?php echo htmlspecialchars($doctor["available_time"]); ?></strong>
            </div>

            <div class="doctor-info-row">
                <span>Email</span>
                <strong><?php echo htmlspecialchars($doctor["email"]); ?></strong>
            </div>

            <div class="doctor-info-row">
                <span>Phone</span>
                <strong><?php echo htmlspecialchars($doctor["phone"]); ?></strong>
            </div>

        </div>

    </section>

</main>

<footer class="doctor-footer">
    🐾 PawCare Doctor Portal © 2026
</footer>

</body>

</html>