<?php

require_once "../includes/session.php";
require_once "../config/database.php";

if (isset($_SESSION["user_id"]) && ($_SESSION["role"] ?? "") === "doctor") {
    header("Location: dashboard.php");
    exit;
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($username) || empty($password)) {
        $error_message = "Please enter username and password.";
    } else {

        $sql = "SELECT user_id, full_name, username, password, role, status FROM users WHERE username = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {

            $user = mysqli_fetch_assoc($result);

            if ($user["status"] !== "active") {
                $error_message = "Your account is not active.";
            } elseif ($user["role"] !== "doctor") {
                $error_message = "Only doctors can login from this page.";
            } elseif (!password_verify($password, $user["password"])) {
                $error_message = "Invalid username or password.";
            } else {

                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["full_name"] = $user["full_name"];
                $_SESSION["role"] = $user["role"];

                header("Location: dashboard.php");
                exit;
            }

        } else {
            $error_message = "Invalid username or password.";
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

    <title>Doctor Login | PawCare</title>

    <link rel="stylesheet" href="../assets/css/doctor-login.css">
</head>

<body>

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

<div class="login-page">

    <div class="login-card">

        <div class="login-brand">

            <img src="../assets/images/petLogo.jpeg" alt="PawCare Logo">

            <h1>Doctor Login</h1>

            <p>Access your PawCare doctor portal</p>

        </div>

        <?php if (!empty($error_message)): ?>

            <div class="login-error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="login.php">

            <div class="form-group">

                <label for="username">Username</label>

                <input type="text" id="username" name="username"
                    value="<?php echo htmlspecialchars($_POST["username"] ?? ""); ?>"
                    placeholder="Enter doctor username" required>

            </div>

            <div class="form-group">

                <label for="password">Password</label>

                <input type="password" id="password" name="password"
                    placeholder="Enter password" required>

            </div>

            <button type="submit" class="login-btn">
                Login as Doctor
            </button>

        </form>

        <div class="login-links">

            <a href="../specialist_doctors.php">← Specialist Doctors</a>

            <a href="../index.php">Home</a>

        </div>

    </div>

</div>

<footer class="login-footer">
    🐾 PawCare Doctor Portal © 2026
</footer>

</body>

</html>