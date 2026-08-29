<?php

require_once "../includes/session.php";
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "doctor") {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION["user_id"];
$success_message = "";
$error_message = "";

// Get logged-in doctor
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



if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $appointment_id = (int)($_POST["appointment_id"] ?? 0);
    $new_status = trim($_POST["status"] ?? "");

    $allowed_status = ["Confirmed", "Completed", "Cancelled"];

    if ($appointment_id <= 0 || !in_array($new_status, $allowed_status, true)) {
        $error_message = "Invalid appointment update.";
    } else {

        $stmt = mysqli_prepare($conn, "SELECT appointment_id, status FROM appointments WHERE appointment_id = ? AND doctor_id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "ii", $appointment_id, $doctor_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $appointment = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        if (!$appointment) {
            $error_message = "Appointment not found.";
        } elseif ($appointment["status"] === "Completed" || $appointment["status"] === "Cancelled") {
            $error_message = "This appointment cannot be changed anymore.";
        } else {

            $stmt = mysqli_prepare($conn, "UPDATE appointments SET status = ? WHERE appointment_id = ? AND doctor_id = ?");
            mysqli_stmt_bind_param($stmt, "sii", $new_status, $appointment_id, $doctor_id);

            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Appointment status updated successfully.";
            } else {
                $error_message = "Failed to update appointment.";
            }

            mysqli_stmt_close($stmt);
        }
    }
}


$filter = $_GET["filter"] ?? "all";
$allowed_filters = ["all", "pending", "confirmed", "completed", "cancelled"];

if (!in_array($filter, $allowed_filters, true)) {
    $filter = "all";
}


$sql = "SELECT a.appointment_id, a.pet_name, a.pet_type, a.appointment_date, a.appointment_time, a.reason, a.status, a.created_at, u.full_name AS customer_name, u.phone AS customer_phone
        FROM appointments a
        JOIN users u ON a.customer_id = u.user_id
        WHERE a.doctor_id = ?";

$status_filter = "";

if ($filter !== "all") {
    $status_filter = ucfirst($filter);
    $sql .= " AND a.status = ?";
}

$sql .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";

$stmt = mysqli_prepare($conn, $sql);

if ($filter === "all") {
    mysqli_stmt_bind_param($stmt, "i", $doctor_id);
} else {
    mysqli_stmt_bind_param($stmt, "is", $doctor_id, $status_filter);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$appointments = [];

while ($row = mysqli_fetch_assoc($result)) {
    $appointments[] = $row;
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

    <title>Appointments | PawCare Doctor</title>

    <link rel="stylesheet" href="../assets/css/doctor-appointments.css">
</head>

<body class="doctor-appointments-page">

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
    <a href="appointments.php" class="active">Appointments</a>
    <a href="medical_records.php">Medical Records</a>
    <a href="../logout.php">Logout</a>
</nav>

</aside>

<main class="appointments-content">

    <div class="page-heading">

        <div>
            <p>Doctor Portal</p>
            <h1>Appointment Management</h1>
            <span>View and manage your patient appointments.</span>
        </div>

        <div class="appointment-count">
            <?php echo count($appointments); ?> Appointments
        </div>

    </div>

    <?php if (!empty($success_message)): ?>
        <div class="message success-message">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="message error-message">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <div class="filter-menu">

        <a href="appointments.php?filter=all"
           class="<?php echo $filter === "all" ? "active" : ""; ?>">
            All
        </a>

        <a href="appointments.php?filter=pending"
           class="<?php echo $filter === "pending" ? "active" : ""; ?>">
            Pending
        </a>

        <a href="appointments.php?filter=confirmed"
           class="<?php echo $filter === "confirmed" ? "active" : ""; ?>">
            Confirmed
        </a>

        <a href="appointments.php?filter=completed"
           class="<?php echo $filter === "completed" ? "active" : ""; ?>">
            Completed
        </a>

        <a href="appointments.php?filter=cancelled"
           class="<?php echo $filter === "cancelled" ? "active" : ""; ?>">
            Cancelled
        </a>

    </div>

    <section class="appointments-card">

        <?php if (!empty($appointments)): ?>

            <div class="table-wrapper">

                <table class="appointments-table">

                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Guardian</th>
                        <th>Pet</th>
                        <th>Schedule</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($appointments as $appointment): ?>

                        <tr>

                            <td>
                                #<?php echo str_pad($appointment["appointment_id"], 4, "0", STR_PAD_LEFT); ?>
                            </td>

                            <td>
                                <strong><?php echo htmlspecialchars($appointment["customer_name"]); ?></strong>
                                <span><?php echo htmlspecialchars($appointment["customer_phone"]); ?></span>
                            </td>

                            <td>
                                <strong><?php echo htmlspecialchars($appointment["pet_name"]); ?></strong>
                                <span><?php echo htmlspecialchars($appointment["pet_type"]); ?></span>
                            </td>

                            <td>
                                <strong>
                                    <?php echo date("d M Y", strtotime($appointment["appointment_date"])); ?>
                                </strong>

                                <span>
                                    <?php echo date("h:i A", strtotime($appointment["appointment_time"])); ?>
                                </span>
                            </td>

                            <td class="reason-cell">
                                <?php echo htmlspecialchars($appointment["reason"]); ?>
                            </td>

                            <td>
                                <span class="status status-<?php echo strtolower($appointment["status"]); ?>">
                                    <?php echo htmlspecialchars($appointment["status"]); ?>
                                </span>
                            </td>

                            <td>

                                <?php if ($appointment["status"] === "Pending"): ?>

                                    <div class="action-buttons">

                                        <form method="POST">
                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment["appointment_id"]; ?>">
                                            <input type="hidden" name="status" value="Confirmed">

                                            <button type="submit" class="confirm-btn">
                                                Confirm
                                            </button>
                                        </form>

                                        <form method="POST">
                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment["appointment_id"]; ?>">
                                            <input type="hidden" name="status" value="Cancelled">

                                            <button type="submit" class="cancel-btn">
                                                Cancel
                                            </button>
                                        </form>

                                    </div>

                                <?php elseif ($appointment["status"] === "Confirmed"): ?>

                                    <div class="action-buttons">

                                        <form method="POST">
                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment["appointment_id"]; ?>">
                                            <input type="hidden" name="status" value="Completed">

                                            <button type="submit" class="complete-btn">
                                                Complete
                                            </button>
                                        </form>

                                        <form method="POST">
                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment["appointment_id"]; ?>">
                                            <input type="hidden" name="status" value="Cancelled">

                                            <button type="submit" class="cancel-btn">
                                                Cancel
                                            </button>
                                        </form>

                                    </div>

                                <?php else: ?>

                                    <span class="no-action">
                                        No Action
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="empty-appointments">

                <h3>No appointments found</h3>

                <p>
                    There are no appointments available under this category.
                </p>

            </div>

        <?php endif; ?>

    </section>

</main>

<footer class="doctor-footer">
    🐾 PawCare Doctor Portal © 2026
</footer>

</body>

</html>