<?php

require_once "includes/session.php";
require_once "config/database.php";

$full_name = "";
$username = "";
$email = "";
$phone = "";
$address = "";

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get form data
    $full_name = trim($_POST["full_name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";
    $address = trim($_POST["address"] ?? "");

    if (
        empty($full_name) ||
        empty($username) ||
        empty($email) ||
        empty($phone) ||
        empty($password) ||
        empty($address)
    ) {
        $error_message = "Please fill in all fields.";
    }

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    }

    elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        $error_message =
            "Username can contain only letters, numbers and underscore.";
    }

    elseif (strlen($username) < 4) {
        $error_message =
            "Username must be at least 4 characters long.";
    }

    elseif (!preg_match("/^01[0-9]{9}$/", $phone)) {
        $error_message =
            "Please enter a valid 11-digit phone number.";
    }

    elseif (strlen($password) < 6) {
        $error_message =
            "Password must be at least 6 characters long.";
    }

    else {

        $check_sql = "
            SELECT user_id
            FROM users
            WHERE username = ?
               OR email = ?
               OR phone = ?
            LIMIT 1
        ";

        $check_stmt = mysqli_prepare($conn, $check_sql);

        mysqli_stmt_bind_param(
            $check_stmt,
            "sss",
            $username,
            $email,
            $phone
        );

        mysqli_stmt_execute($check_stmt);

        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {

            $error_message =
                "Username, email or phone number is already registered.";

        } else {

            $hashed_password =
                password_hash($password, PASSWORD_DEFAULT);

            $role = "customer";
            $status = "active";


            $insert_sql = "
                INSERT INTO users
                (
                    full_name,
                    username,
                    email,
                    phone,
                    password,
                    role,
                    address,
                    status
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ";

            $insert_stmt =
                mysqli_prepare($conn, $insert_sql);

            mysqli_stmt_bind_param(
                $insert_stmt,
                "ssssssss",
                $full_name,
                $username,
                $email,
                $phone,
                $hashed_password,
                $role,
                $address,
                $status
            );

            if (mysqli_stmt_execute($insert_stmt)) {

                $success_message =
                    "Account created successfully. You can now login.";

                $full_name = "";
                $username = "";
                $email = "";
                $phone = "";
                $address = "";

            } else {

                $error_message =
                    "Registration failed. Please try again.";
            }

            mysqli_stmt_close($insert_stmt);
        }

        mysqli_stmt_close($check_stmt);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Account | PawCare</title>

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/register.css">
</head>

<body class="auth-page">

    <header class="top-bar">
        <span class="brand-icon">🐾</span>
        <span>PawCare – Pet Shop and Veterinary Service Management System</span>

        <div class="window-dots">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </header>

    <main class="registration-container">

        <section class="registration-card">

            <div class="registration-brand">

               <div class="registration-logo">
                    <img src="assets/images/petLogo.jpeg" alt="PawCare Logo">
               </div>

                <h3><span>🐾</span> PAWCARE</h3>

            </div>

            <div class="registration-heading">
                <h1>Join the Pet Family</h1>
                <p>Create your account to access premium pet care</p>
            </div>
            <?php if (!empty($error_message)): ?>

    <div class="message error-message">
        <?php echo htmlspecialchars($error_message); ?>
    </div>

<?php endif; ?>


<?php if (!empty($success_message)): ?>

    <div class="message success-message">
        <?php echo htmlspecialchars($success_message); ?>
    </div>

<?php endif; ?>

            <form action="" method="POST" id="registrationForm">

                <div class="form-grid">

                    <div class="form-group">
                        <label for="full_name">Full Name</label>

                       <input
    type="text"
    id="full_name"
    name="full_name"
    placeholder="Enter your full name"
    value="<?php echo htmlspecialchars($full_name); ?>"
    required>
                    </div>

                    <div class="form-group">
                        <label for="username">Choose Username</label>

 <input
    type="text"
    id="username"
    name="username"
    placeholder="Choose a username"
    value="<?php echo htmlspecialchars($username); ?>"
    required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>

<input
    type="email"
    id="email"
    name="email"
    placeholder="example@email.com"
    value="<?php echo htmlspecialchars($email); ?>"
    required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>

<input
    type="tel"
    id="phone"
    name="phone"
    placeholder="01XXXXXXXXX"
    value="<?php echo htmlspecialchars($phone); ?>"
    required>
                    </div>

                    <div class="form-group">
                        <label for="password">Set Password</label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter password"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="address">Home Address</label>

<input
    type="text"
    id="address"
    name="address"
    placeholder="Please enter exact delivery address"
    value="<?php echo htmlspecialchars($address); ?>"
    required>
                    </div>

                </div>

                <button type="submit" class="create-account-btn">
                    Create Account
                </button>

            </form>

            <p class="login-link">
                Already have an account?
                <a href="login.php">Login here</a>
            </p>

        </section>

    </main>

    <footer class="footer">
        🐾 "Pets are not our whole life..." | PawCare © 2026
    </footer>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/register.js"></script>
</body>

</html>
