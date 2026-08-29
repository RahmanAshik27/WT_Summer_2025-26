<?php

require_once "../includes/session.php";
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "doctor") {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION["user_id"];


$stmt = mysqli_prepare($conn, "SELECT d.doctor_id, d.specialization, u.full_name, u.profile_image FROM doctors d JOIN users u ON d.user_id = u.user_id WHERE d.user_id = ? LIMIT 1");

if (!$stmt) {
    die("Failed to prepare doctor query: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) !== 1) {
    mysqli_stmt_close($stmt);
    header("Location: login.php");
    exit;
}

$doctor = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

$doctor_id = (int)$doctor["doctor_id"];


$stmt = mysqli_prepare($conn, "SELECT mr.record_id, mr.appointment_id, mr.diagnosis, mr.prescription, mr.treatment_notes, mr.next_visit_date, mr.created_at, a.pet_name, a.pet_type, a.appointment_date, a.appointment_time, u.full_name AS customer_name, u.phone AS customer_phone FROM medical_records mr JOIN appointments a ON mr.appointment_id = a.appointment_id JOIN users u ON a.customer_id = u.user_id WHERE a.doctor_id = ? ORDER BY mr.created_at DESC");

if (!$stmt) {
    die("Failed to prepare medical record query: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $doctor_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$medical_records = [];

while ($row = mysqli_fetch_assoc($result)) {
    $medical_records[] = $row;
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

    <title>Medical Records | PawCare Doctor</title>

    <link rel="stylesheet" href="../assets/css/doctor-medical-records.css">
</head>

<body class="medical-records-page">

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
        <a href="dashboard.php">Dashboard</a>
        <a href="appointments.php">Appointments</a>
        <a href="medical_records.php" class="active">Medical Records</a>
        <a href="../logout.php">Logout</a>
    </nav>

</aside>

<main class="records-content">

    <div class="page-heading">

        <div>
            <p>Doctor Portal</p>
            <h1>Medical Records</h1>
            <span>View previous patient diagnosis, prescriptions and treatment history.</span>
        </div>

        <div class="record-count">
            <?php echo count($medical_records); ?> Records
        </div>

    </div>

    <?php if (!empty($medical_records)): ?>

        <section class="records-grid">

            <?php foreach ($medical_records as $record): ?>

                <article class="record-card">

                    <div class="record-header">

                        <div>
                            <span class="record-label">Medical Record</span>

                            <h2>
                                #<?php echo str_pad($record["record_id"], 4, "0", STR_PAD_LEFT); ?>
                            </h2>
                        </div>

                        <div class="appointment-token">
                            Appointment
                            #<?php echo str_pad($record["appointment_id"], 4, "0", STR_PAD_LEFT); ?>
                        </div>

                    </div>

                    <div class="patient-info">

                        <div>
                            <span>Guardian</span>
                            <strong><?php echo htmlspecialchars($record["customer_name"]); ?></strong>
                            <small><?php echo htmlspecialchars($record["customer_phone"]); ?></small>
                        </div>

                        <div>
                            <span>Pet</span>
                            <strong><?php echo htmlspecialchars($record["pet_name"]); ?></strong>
                            <small><?php echo htmlspecialchars($record["pet_type"]); ?></small>
                        </div>

                        <div>
                            <span>Appointment</span>

                            <strong>
                                <?php echo date("d M Y", strtotime($record["appointment_date"])); ?>
                            </strong>

                            <small>
                                <?php echo date("h:i A", strtotime($record["appointment_time"])); ?>
                            </small>
                        </div>

                    </div>

                    <div class="record-section">
                        <span>Diagnosis</span>

                        <p>
                            <?php echo nl2br(htmlspecialchars($record["diagnosis"])); ?>
                        </p>
                    </div>

                    <div class="record-section">
                        <span>Prescription</span>

                        <p>
                            <?php echo nl2br(htmlspecialchars($record["prescription"])); ?>
                        </p>
                    </div>

                    <div class="record-section">
                        <span>Treatment Notes</span>

                        <p>
                            <?php
                            if (!empty($record["treatment_notes"])) {
                                echo nl2br(htmlspecialchars($record["treatment_notes"]));
                            } else {
                                echo "No treatment notes available.";
                            }
                            ?>
                        </p>
                    </div>

                    <div class="record-footer">

                        <div>
                            <span>Next Visit</span>

                            <strong>
                                <?php
                                if (!empty($record["next_visit_date"])) {
                                    echo date("d M Y", strtotime($record["next_visit_date"]));
                                } else {
                                    echo "Not Scheduled";
                                }
                                ?>
                            </strong>
                        </div>

                        <div>
                            <span>Record Created</span>

                            <strong>
                                <?php echo date("d M Y", strtotime($record["created_at"])); ?>
                            </strong>
                        </div>

                    </div>

                </article>

            <?php endforeach; ?>

        </section>

    <?php else: ?>

        <section class="empty-records">

            <div class="empty-icon">📋</div>

            <h2>No Medical Records Found</h2>

            <p>
                There are currently no medical records available for your patients.
            </p>

            <a href="appointments.php">View Appointments</a>

        </section>

    <?php endif; ?>

</main>

<footer class="doctor-footer">
    🐾 PawCare Doctor Portal © 2026
</footer>

</body>

</html>