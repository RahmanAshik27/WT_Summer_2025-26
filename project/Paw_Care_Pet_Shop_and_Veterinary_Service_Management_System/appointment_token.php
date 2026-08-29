<?php

require_once "includes/session.php";
require_once "config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "customer") {
    header("Location: login.php");
    exit;
}

$customer_id = (int)$_SESSION["user_id"];
$appointment_id = isset($_GET["appointment_id"]) ? (int)$_GET["appointment_id"] : 0;

if ($appointment_id <= 0) {
    header("Location: specialist_doctors.php");
    exit;
}

$sql = "SELECT a.appointment_id, a.pet_name, a.pet_type, a.appointment_date, a.appointment_time, a.reason, a.status, c.full_name AS customer_name, d.specialization, du.full_name AS doctor_name FROM appointments a JOIN users c ON a.customer_id = c.user_id JOIN doctors d ON a.doctor_id = d.doctor_id JOIN users du ON d.user_id = du.user_id WHERE a.appointment_id = $appointment_id AND a.customer_id = $customer_id LIMIT 1";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) !== 1) {
    header("Location: specialist_doctors.php");
    exit;
}

$appointment = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Appointment Token | PawCare</title>

    <link rel="stylesheet" href="assets/css/appointment-token.css">
</head>

<body>

<div class="token-page">

    <div class="token-card">

        <div class="token-header">
            <div class="paw-icon">🐾</div>
            <h1>PawCare</h1>
            <p>Pet Shop and Veterinary Service Management System</p>
        </div>

        <div class="success-message">
            ✓ Appointment Booked Successfully
        </div>

        <div class="token-number">
            <span>APPOINTMENT TOKEN</span>
            <strong>#<?php echo str_pad($appointment["appointment_id"], 4, "0", STR_PAD_LEFT); ?></strong>
        </div>

        <div class="token-info">

            <div class="info-row">
                <span>Doctor</span>
                <strong><?php echo htmlspecialchars($appointment["doctor_name"]); ?></strong>
            </div>

            <div class="info-row">
                <span>Specialization</span>
                <strong><?php echo htmlspecialchars($appointment["specialization"]); ?></strong>
            </div>

            <div class="info-row">
                <span>Guardian</span>
                <strong><?php echo htmlspecialchars($appointment["customer_name"]); ?></strong>
            </div>

            <div class="info-row">
                <span>Pet Name</span>
                <strong><?php echo htmlspecialchars($appointment["pet_name"]); ?></strong>
            </div>

            <div class="info-row">
                <span>Pet Type</span>
                <strong><?php echo htmlspecialchars($appointment["pet_type"]); ?></strong>
            </div>

            <div class="info-row">
                <span>Date</span>
                <strong><?php echo date("d M Y", strtotime($appointment["appointment_date"])); ?></strong>
            </div>

            <div class="info-row">
                <span>Time</span>
                <strong><?php echo date("h:i A", strtotime($appointment["appointment_time"])); ?></strong>
            </div>

            <div class="info-row">
                <span>Status</span>
                <strong class="status">
                    <?php echo htmlspecialchars($appointment["status"]); ?>
                </strong>
            </div>

        </div>

        <div class="reason-box">
            <span>Reason for Visit</span>
            <p><?php echo htmlspecialchars($appointment["reason"]); ?></p>
        </div>

        <div class="token-note">
            Please keep this token and arrive before your scheduled appointment time.
        </div>

        <div class="token-actions">
            <button type="button" onclick="window.print()">Print Token</button>
            <a href="specialist_doctors.php">Close</a>
        </div>

    </div>

</div>

</body>

</html>