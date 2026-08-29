<?php

require_once "includes/session.php";
require_once "config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "customer") {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION["pending_appointment"])) {
    header("Location: specialist_doctors.php");
    exit;
}

$customer_id = (int)$_SESSION["user_id"];
$appointment = $_SESSION["pending_appointment"];

$doctor_id = (int)($appointment["doctor_id"] ?? 0);
$pet_name = trim($appointment["pet_name"] ?? "");
$pet_type = trim($appointment["pet_type"] ?? "");
$appointment_date = trim($appointment["appointment_date"] ?? "");
$appointment_time = trim($appointment["appointment_time"] ?? "");
$reason = trim($appointment["reason"] ?? "");

if ($doctor_id <= 0 || empty($pet_name) || empty($pet_type) || empty($appointment_date) || empty($appointment_time) || empty($reason)) {
    unset($_SESSION["pending_appointment"]);
    header("Location: specialist_doctors.php");
    exit;
}

$doctor_sql = "SELECT d.doctor_id FROM doctors d JOIN users u ON d.user_id = u.user_id WHERE d.doctor_id = $doctor_id AND d.status = 'Available' AND u.status = 'active' LIMIT 1";
$doctor_result = mysqli_query($conn, $doctor_sql);

if (!$doctor_result || mysqli_num_rows($doctor_result) !== 1) {
    unset($_SESSION["pending_appointment"]);
    header("Location: specialist_doctors.php");
    exit;
}

$check_sql = "SELECT appointment_id FROM appointments WHERE doctor_id = $doctor_id AND appointment_date = ? AND appointment_time = ? AND status != 'Cancelled' LIMIT 1";
$check_stmt = mysqli_prepare($conn, $check_sql);

mysqli_stmt_bind_param($check_stmt, "ss", $appointment_date, $appointment_time);
mysqli_stmt_execute($check_stmt);

$check_result = mysqli_stmt_get_result($check_stmt);

if ($check_result && mysqli_num_rows($check_result) > 0) {
    mysqli_stmt_close($check_stmt);

    $_SESSION["appointment_error"] = "This appointment time is already booked. Please choose another time.";

    header("Location: book_appointment.php?doctor_id=" . $doctor_id);
    exit;
}

mysqli_stmt_close($check_stmt);

$insert_sql = "INSERT INTO appointments (customer_id, doctor_id, pet_name, pet_type, appointment_date, appointment_time, reason, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";
$stmt = mysqli_prepare($conn, $insert_sql);

mysqli_stmt_bind_param($stmt, "iisssss", $customer_id, $doctor_id, $pet_name, $pet_type, $appointment_date, $appointment_time, $reason);

if (mysqli_stmt_execute($stmt)) {
    $appointment_id = mysqli_insert_id($conn);

    unset($_SESSION["pending_appointment"]);

    mysqli_stmt_close($stmt);

    header("Location: appointment_token.php?appointment_id=" . $appointment_id);
    exit;
}

mysqli_stmt_close($stmt);

header("Location: specialist_doctors.php");
exit;

?>