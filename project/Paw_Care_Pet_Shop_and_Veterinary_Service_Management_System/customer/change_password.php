<?php

require_once "../includes/session.php";
require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION["role"] !== "customer") {
    header("Location: ../login.php");
    exit;
}

$customer_id = (int) $_SESSION["user_id"];
$error = "";

$sql = "SELECT username, full_name, password FROM users WHERE user_id = ? LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$customer = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$customer) {
    header("Location: ../login.php");
    exit;
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $current_password = $_POST["current_password"] ?? "";
    $new_password = $_POST["new_password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    // EMPTY CHECK
    if ($current_password === "" || $new_password === "" || $confirm_password === "") {
        $error = "Please fill in all password fields.";
    }

    elseif (!password_verify($current_password, $customer["password"])) {
        $error = "Current password is incorrect.";
    }

    elseif (strlen($new_password) < 8) {
        $error = "New password must be at least 8 characters.";
    }

    elseif (password_verify($new_password, $customer["password"])) {
        $error = "New password must be different from current password.";
    }

    elseif ($new_password !== $confirm_password) {
        $error = "New password and confirm password do not match.";
    }

    else {
 
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        $update_sql = "UPDATE users SET password = ? WHERE user_id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);

        mysqli_stmt_bind_param($update_stmt, "si", $new_password_hash, $customer_id);

        if (mysqli_stmt_execute($update_stmt)) {
            mysqli_stmt_close($update_stmt);
            header("Location: profile.php?password_changed=1");
            exit;
        } else {
            $error = "Password could not be changed. Please try again.";
            mysqli_stmt_close($update_stmt);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password | PawCare</title>
    <link rel="stylesheet" href="../assets/css/change-password.css">
</head>

<body>

<div class="password-page">

    <header class="password-header">
        <div>
            <h1>CHANGE PASSWORD</h1>
            <p>Update your PawCare account password</p>
        </div>

        <a href="profile.php">← BACK TO PROFILE</a>
    </header>

    <main class="password-container">
        <section class="password-card">

            <div class="password-user">
                <div class="password-avatar">
                    <?php echo strtoupper(substr($customer["username"], 0, 1)); ?>
                </div>

                <div>
                    <h2><?php echo htmlspecialchars($customer["full_name"]); ?></h2>
                    <p>@<?php echo htmlspecialchars($customer["username"]); ?></p>
                </div>
            </div>

            <?php if ($error !== ""): ?>
                <div class="password-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="password-form">

                <div class="password-group">
                    <label>CURRENT PASSWORD</label>
                    <input type="password" name="current_password" placeholder="Enter current password" required>
                </div>

                <div class="password-group">
                    <label>NEW PASSWORD</label>
                    <input type="password" name="new_password" placeholder="Enter new password" minlength="8" required>
                    <small>Minimum 8 characters.</small>
                </div>

                <div class="password-group">
                    <label>CONFIRM NEW PASSWORD</label>
                    <input type="password" name="confirm_password" placeholder="Enter new password again" minlength="8" required>
                </div>

                <div class="password-actions">
                    <a href="profile.php">CANCEL</a>
                    <button type="submit">UPDATE PASSWORD</button>
                </div>

            </form>

        </section>
    </main>

</div>

</body>
</html>