<?php

require_once "../config/database.php";

$companies = [
    "Pathao Fast",
    "PetPanda Go",
    "Speed Fast",
    "Jhinku BD"
];

$company_data = [];

foreach ($companies as $company) {
    $agent_sql = "SELECT u.full_name FROM delivery_agents da JOIN users u ON da.user_id = u.user_id WHERE da.company_name = ? AND da.status = 'Active' LIMIT 1";
    $agent_stmt = mysqli_prepare($conn, $agent_sql);
    mysqli_stmt_bind_param($agent_stmt, "s", $company);
    mysqli_stmt_execute($agent_stmt);
    $agent_result = mysqli_stmt_get_result($agent_stmt);
    $agent = mysqli_fetch_assoc($agent_result);

    $order_sql = "SELECT COUNT(*) AS total FROM orders WHERE delivery_method = ? AND order_status NOT IN ('Delivered', 'Cancelled')";
    $order_stmt = mysqli_prepare($conn, $order_sql);
    mysqli_stmt_bind_param($order_stmt, "s", $company);
    mysqli_stmt_execute($order_stmt);
    $order_result = mysqli_stmt_get_result($order_stmt);
    $order = mysqli_fetch_assoc($order_result);

    $company_data[] = [
        "name" => $company,
        "agent" => $agent["full_name"] ?? "No Agent Assigned",
        "pending" => $order["total"] ?? 0
    ];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Man Portal - PawCare</title>
    <link rel="stylesheet" href="../assets/css/delivery_portal.css">
</head>
<body>

<div class="delivery-page">

    <div class="portal-header">
        <h1>Delivery Man Portal</h1>
        <p>Select your delivery company to continue</p>
    </div>

    <div class="company-container">

        <?php foreach ($company_data as $company): ?>

            <div class="company-card">
                <div class="company-icon">🚚</div>

                <h2><?php echo htmlspecialchars($company["name"]); ?></h2>

                <p class="agent-name">
                    Agent: <?php echo htmlspecialchars($company["agent"]); ?>
                </p>

                <div class="order-count">
                    <span><?php echo $company["pending"]; ?></span>
                    <p>Pending Orders</p>
                </div>

                <a href="login.php?company=<?php echo urlencode($company["name"]); ?>" class="login-btn">
                    Login
                </a>
            </div>

        <?php endforeach; ?>

    </div>

    <div class="back-home">
        <a href="../index.php">← Back to Home</a>
    </div>

</div>

</body>
</html>