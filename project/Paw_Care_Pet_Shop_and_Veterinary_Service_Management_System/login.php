<?php

require_once "includes/session.php";
require_once "config/database.php";

$login_identifier = $_COOKIE["remember_login"] ?? "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login_identifier = trim($_POST["login_identifier"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($login_identifier) || empty($password)) {
        $error_message = "Please enter your username/email and password.";
    } else {

        $sql = "SELECT user_id, full_name, username, email, password, role, status FROM users WHERE username = ? OR email = ? LIMIT 1";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $login_identifier, $login_identifier);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            if ($user["status"] !== "active") {
                $error_message = "Your account is currently inactive.";
            }

            elseif (password_verify($password, $user["password"])) {

                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["full_name"] = $user["full_name"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["role"] = $user["role"];

                if (isset($_POST["remember_me"])) {
                    setcookie("remember_login", $login_identifier, time() + (86400 * 30), "/");
                } else {
                    setcookie("remember_login", "", time() - 3600, "/");
                }

                if ($user["role"] === "customer" && ($_SESSION["redirect_after_login"] ?? "") === "confirm_appointment.php") {
                    unset($_SESSION["redirect_after_login"]);
                    header("Location: confirm_appointment.php");
                    exit;
                }
                switch ($user["role"]) {
                    case "admin":
                        header("Location: admin/dashboard.php");
                        exit;

                    case "customer":
                        header("Location: customer/dashboard.php");
                        exit;

                    case "doctor":
                        header("Location: doctor/dashboard.php");
                        exit;

                    case "delivery":
                        header("Location: delivery/dashboard.php");
                        exit;

                    default:
                        session_unset();
                        session_destroy();
                        $error_message = "Invalid account role.";
                }

            } else {
                $error_message = "Invalid username/email or password.";
            }

        } else {
            $error_message = "Invalid username/email or password.";
        }

        mysqli_stmt_close($stmt);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | PawCare</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body class="auth-page">

<?php if (isset($_GET["logout"]) && $_GET["logout"] === "1"): ?>

<script>
    alert("You have been logged out successfully!");
</script>

<?php endif; ?>

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

    <main class="login-container">

        <section class="login-card">

            <div class="login-brand">
                <img src="assets/images/petLogo.jpeg" alt="PawCare Logo" class="login-logo">

                <h3>
                    <span>🐾</span>
                    PAWCARE
                </h3>
            </div>

            <div class="login-heading">
                <h1>Welcome Back</h1>
                <p>Sign in to manage your premium pet shop</p>
            </div>

            <?php if (!empty($error_message)): ?>

                <div class="login-message error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>

            <?php endif; ?>

            <form action="" method="POST" id="loginForm">

                <div class="form-group">
                    <label for="login_identifier">Username or Email</label>

                    <input
                        type="text"
                        id="login_identifier"
                        name="login_identifier"
                        placeholder="Enter username or email"
                        value="<?php echo htmlspecialchars($login_identifier); ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>

                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter password" required>

                        <button
                            type="button"
                            class="password-toggle"
                            id="passwordToggle"
                            aria-label="Show password">
                            👁
                        </button>
                    </div>
                </div>

                <div class="login-options">

                    <label class="remember-option">
                        <input
                            type="checkbox"
                            name="remember_me"
                            id="remember_me"
                            <?php
                            if (isset($_COOKIE["remember_login"])) {
                                echo "checked";
                            }
                            ?>>

                        <span>Remember me</span>
                    </label>

                    <a href="#" class="forgot-password">Forgot Password?</a>

                </div>

                <button type="submit" class="login-btn">Login</button>

            </form>

            <p class="signup-link">
                Don't have an account?
                <a href="register.php">Sign Up</a>
            </p>

        </section>

    </main>

    <footer class="footer">
        🐾 "Pets are not our whole life..." |
        PawCare © 2026
    </footer>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/login.js"></script>

</body>

</html>