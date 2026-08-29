<?php

require_once "../config/database.php";
require_once "../includes/session.php";

$company = trim($_GET["company"] ?? "");
$error_message = "";

$allowed_companies = ["Pathao Fast", "PetPanda Go", "Speed Fast", "Jhinku BD"];

if (!in_array($company, $allowed_companies)) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($username) || empty($password)) {
        $error_message = "Please enter username and password.";
    } else {

        $sql = "SELECT u.user_id, u.full_name, u.username, u.password, u.role, u.status, da.company_name FROM users u JOIN delivery_agents da ON u.user_id = da.user_id WHERE u.username = ? AND u.role = 'delivery' AND u.status = 'active' AND da.status = 'Active' AND da.company_name = ? LIMIT 1";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $username, $company);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $agent = mysqli_fetch_assoc($result);

        if ($agent && password_verify($password, $agent["password"])) {

            $_SESSION["user_id"] = $agent["user_id"];
            $_SESSION["full_name"] = $agent["full_name"];
            $_SESSION["username"] = $agent["username"];
            $_SESSION["role"] = "delivery";
            $_SESSION["delivery_company"] = $agent["company_name"];

            header("Location: dashboard.php");
            exit;

        } else {
            $error_message = "Invalid username or password for this delivery company.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($company); ?> Agent Login | PawCare</title>
    <link rel="stylesheet" href="../assets/css/delivery_login.css">
</head>

<body>

    <header class="delivery-top-bar">
        <div>
            <span class="paw-icon">🐾</span>
            <span>PawCare – Delivery Management Portal</span>
        </div>

        <div class="window-dots">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </header>

    <main class="login-container">

        <div class="login-card">

            <div class="login-logo">
                <img src="../assets/images/petLogo.jpeg" alt="PawCare Logo">
            </div>

            <p class="portal-label">DELIVERY AGENT PORTAL</p>

            <h1><?php echo htmlspecialchars($company); ?></h1>

            <p class="login-subtitle">Delivery Agent Login</p>

            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php?company=<?php echo urlencode($company); ?>">

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Enter agent username" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter password" required>
                </div>

                <button type="submit" class="login-submit-btn">LOGIN TO DASHBOARD</button>

            </form>

            <div class="login-links">
                <a href="index.php">← Change Delivery Company</a>
                <a href="../index.php">Back to Home</a>
            </div>

        </div>

    </main>

    <footer class="delivery-footer">
        POWERFUL PET MANAGEMENT ENGINE V2.0 LIVE | SYSTEM SECURED
    </footer>

</body>

</html>