<?php

require_once "includes/session.php";
require_once "config/database.php";

$doctor_id = isset($_GET["doctor_id"]) ? (int)$_GET["doctor_id"] : 0;
$doctor = null;

$pet_name = "";
$pet_type = "";
$guardian_name = "";
$appointment_date = "";
$appointment_time = "";
$reason = "";
$error_message = "";

// Load selected doctor
if ($doctor_id > 0) {
    $sql = "SELECT d.doctor_id, d.specialization, d.qualification, d.consultation_fee, d.available_days, d.available_time, d.status, u.full_name, u.profile_image
            FROM doctors d
            JOIN users u ON d.user_id = u.user_id
            WHERE d.doctor_id = $doctor_id AND u.status = 'active'
            LIMIT 1";

    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $doctor = mysqli_fetch_assoc($result);
    }
}

// Invalid doctor
if (!$doctor) {
    header("Location: specialist_doctors.php");
    exit;
}

// Doctor profile image
$profile_image = trim($doctor["profile_image"] ?? "");

if (empty($profile_image)) {
    $profile_image = "default.png";
}

// Appointment form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $pet_name = trim($_POST["pet_name"] ?? "");
    $pet_type = trim($_POST["pet_type"] ?? "");
    $guardian_name = trim($_POST["guardian_name"] ?? "");
    $appointment_date = trim($_POST["appointment_date"] ?? "");
    $appointment_time = trim($_POST["appointment_time"] ?? "");
    $reason = trim($_POST["reason"] ?? "");

    if (empty($pet_name) || empty($pet_type) || empty($guardian_name) || empty($appointment_date) || empty($appointment_time) || empty($reason)) {
        $error_message = "Please fill in all appointment information.";
    } elseif ($appointment_date < date("Y-m-d")) {
        $error_message = "Please select a valid appointment date.";
    } else {

        // Keep booking information until customer login is complete
        $_SESSION["pending_appointment"] = [
            "doctor_id" => $doctor_id,
            "pet_name" => $pet_name,
            "pet_type" => $pet_type,
            "guardian_name" => $guardian_name,
            "appointment_date" => $appointment_date,
            "appointment_time" => $appointment_time,
            "reason" => $reason
        ];

        // Customer must login before final booking
        if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "customer") {
            $_SESSION["redirect_after_login"] = "confirm_appointment.php";
            header("Location: login.php");
            exit;
        }

        header("Location: confirm_appointment.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Book Appointment | PawCare</title>

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/book-appointment.css">
</head>

<body class="appointment-page">

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

<main class="appointment-container">

    <aside class="appointment-sidebar">

        <div class="sidebar-brand">

            <img src="assets/images/petLogo.jpeg" alt="PawCare Logo" class="sidebar-logo">

            <h2>🐾 PAWCARE</h2>

        </div>

        <nav class="sidebar-menu">

            <a href="index.php" class="menu-btn">Home</a>

            <a href="specialist_doctors.php" class="menu-btn">
                Specialist Doctors
            </a>

            <a href="doctor_profile.php?doctor_id=<?php echo (int)$doctor["doctor_id"]; ?>" class="menu-btn">
                Doctor Profile
            </a>

            <a href="login.php" class="menu-btn">
                Customer Login
            </a>

        </nav>

    </aside>

    <section class="appointment-content">

        <div class="page-heading">

            <p>Premium Pet Clinic</p>

            <h1>Book Appointment</h1>

            <span>
                Choose your preferred date and time for your pet consultation.
            </span>

        </div>

        <div class="appointment-box">

            <div class="selected-doctor">

                <div class="doctor-image">

                    <img
                        src="uploads/profiles/<?php echo htmlspecialchars($profile_image); ?>"
                        onerror="this.onerror=null; this.src='uploads/profiles/default.png';"
                        alt="<?php echo htmlspecialchars($doctor["full_name"]); ?>"
                    >

                </div>

                <h2>
                    <?php echo htmlspecialchars($doctor["full_name"]); ?>
                </h2>

                <h4>
                    <?php echo htmlspecialchars($doctor["specialization"]); ?>
                </h4>

                <p>
                    <?php echo htmlspecialchars($doctor["qualification"]); ?>
                </p>

                <div class="doctor-info-row">

                    <strong>Available Days</strong>

                    <span>
                        <?php echo htmlspecialchars($doctor["available_days"]); ?>
                    </span>

                </div>

                <div class="doctor-info-row">

                    <strong>Available Time</strong>

                    <span>
                        <?php echo htmlspecialchars($doctor["available_time"]); ?>
                    </span>

                </div>

                <div class="doctor-info-row">

                    <strong>Consultation Fee</strong>

                    <span>
                        ৳<?php echo number_format((float)$doctor["consultation_fee"], 2); ?>
                    </span>

                </div>

            </div>

            <div class="appointment-form-box">

                <h2>Appointment Information</h2>

                <?php if (!empty($error_message)): ?>

                    <div class="appointment-error">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>

                <?php endif; ?>

                <form action="book_appointment.php?doctor_id=<?php echo (int)$doctor["doctor_id"]; ?>" method="POST">

                    <input type="hidden" name="doctor_id" value="<?php echo (int)$doctor["doctor_id"]; ?>">

                    <div class="form-group">

                        <label for="pet_name">Pet Name</label>

                        <input
                            type="text"
                            id="pet_name"
                            name="pet_name"
                            value="<?php echo htmlspecialchars($pet_name); ?>"
                            required
                        >

                    </div>

                    <div class="form-group">

                        <label for="pet_type">Pet Type</label>

                        <select id="pet_type" name="pet_type" required>

                            <option value="">Select Pet Type</option>

                            <option value="Dog" <?php echo $pet_type === "Dog" ? "selected" : ""; ?>>
                                Dog
                            </option>

                            <option value="Cat" <?php echo $pet_type === "Cat" ? "selected" : ""; ?>>
                                Cat
                            </option>

                            <option value="Bird" <?php echo $pet_type === "Bird" ? "selected" : ""; ?>>
                                Bird
                            </option>

                            <option value="Fish" <?php echo $pet_type === "Fish" ? "selected" : ""; ?>>
                                Fish
                            </option>

                            <option value="Rabbit" <?php echo $pet_type === "Rabbit" ? "selected" : ""; ?>>
                                Rabbit
                            </option>

                            <option value="Other" <?php echo $pet_type === "Other" ? "selected" : ""; ?>>
                                Other
                            </option>

                        </select>

                    </div>

                    <div class="form-group">

                        <label for="guardian_name">Guardian Name</label>

                        <input
                            type="text"
                            id="guardian_name"
                            name="guardian_name"
                            value="<?php echo htmlspecialchars($guardian_name); ?>"
                            required
                        >

                    </div>

                    <div class="form-row">

                        <div class="form-group">

                            <label for="appointment_date">
                                Preferred Date
                            </label>

                            <input
                                type="date"
                                id="appointment_date"
                                name="appointment_date"
                                min="<?php echo date("Y-m-d"); ?>"
                                value="<?php echo htmlspecialchars($appointment_date); ?>"
                                required
                            >

                        </div>

                        <div class="form-group">

                            <label for="appointment_time">
                                Preferred Time
                            </label>

                            <input
                                type="time"
                                id="appointment_time"
                                name="appointment_time"
                                value="<?php echo htmlspecialchars($appointment_time); ?>"
                                required
                            >

                        </div>

                    </div>

                    <div class="form-group">

                        <label for="reason">
                            Reason for Visit
                        </label>

                        <textarea
                            id="reason"
                            name="reason"
                            rows="4"
                            placeholder="Write a short reason for the appointment..."
                            required><?php echo htmlspecialchars($reason); ?></textarea>

                    </div>

                    <div class="form-actions">

                        <button type="submit" class="confirm-btn">
                            Confirm Booking
                        </button>

                        <a href="doctor_profile.php?doctor_id=<?php echo (int)$doctor["doctor_id"]; ?>" class="cancel-btn">
                            Cancel
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </section>

</main>

<footer class="appointment-footer">
    🐾 "Your pet deserves expert care." | PawCare Premium Pet Clinic © 2026
</footer>

</body>

</html>