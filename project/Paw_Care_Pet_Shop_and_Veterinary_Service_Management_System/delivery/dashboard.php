<?php

require_once "../includes/session.php";
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "delivery") {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$sql = "SELECT u.full_name, u.username, da.company_name FROM users u JOIN delivery_agents da ON u.user_id = da.user_id WHERE u.user_id = ? AND da.status = 'Active' LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$agent = mysqli_fetch_assoc($result);

if (!$agent) {
    header("Location: index.php");
    exit;
}

$agent_name = $agent["full_name"];
$company_name = $agent["company_name"];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Dashboard | PawCare</title>
    <link rel="stylesheet" href="../assets/css/delivery_dashboard.css">
</head>

<body>

<div class="dashboard-layout">

    <aside class="sidebar">

        <div class="sidebar-brand">
            <img src="../assets/images/petLogo.jpeg" alt="PawCare Logo">

            <h2>PAWCARE</h2>

            <p><?php echo htmlspecialchars($company_name); ?></p>
        </div>

        <nav class="sidebar-menu">
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="#">Assigned Orders</a>
            <a href="#">History</a>
            <a href="#">Profile Settings</a>
        </nav>

        <div class="sidebar-logout">
            <a href="../logout.php">Logout</a>
        </div>

    </aside>

    <div class="main-area">

        <header class="dashboard-topbar">

            <div>
                <h3>WELCOME BACK, <?php echo strtoupper(htmlspecialchars($agent_name)); ?>!</h3>
                <p>Ready for today's deliveries?</p>
            </div>

            <div class="topbar-time">
                <span id="currentDate"></span>
                <span id="currentTime"></span>
            </div>

        </header>

        <main class="dashboard-content">

            <div class="page-heading">
                <h1>DELIVERY AGENT DASHBOARD</h1>
                <p>Live Data: Connected to PawCare Database</p>
            </div>

            <div class="stats-container">

                <div class="stat-card">
                    <p>TOTAL ASSIGNED</p>
                    <h2>0</h2>
                    <span>Total delivery orders</span>
                </div>

                <div class="stat-card">
                    <p>OUT FOR DELIVERY</p>
                    <h2>0</h2>
                    <span>Currently on the way</span>
                </div>

                <div class="stat-card">
                    <p>DELIVERED TODAY</p>
                    <h2>0</h2>
                    <span>Completed today</span>
                </div>

                <div class="stat-card">
                    <p>SUCCESS RATE</p>
                    <h2>0%</h2>
                    <span>Delivery performance</span>
                </div>

            </div>

            <section class="recent-orders">

                <div class="section-header">
                    <div>
                        <h2>Recent Assigned Orders</h2>
                        <p>Your latest delivery assignments</p>
                    </div>

                    <a href="#">VIEW ALL ORDERS</a>
                </div>

                <div class="table-wrapper">

                    <table>

                        <thead>
                            <tr>
                                <th>ORDER ID</th>
                                <th>CUSTOMER</th>
                                <th>ADDRESS</th>
                                <th>BILL</th>
                                <th>STATUS</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td colspan="6" class="empty-data">
                                    Live assigned orders will appear here.
                                </td>
                            </tr>
                        </tbody>

                    </table>

                </div>

            </section>

        </main>

    </div>

</div>

<footer class="delivery-footer">
    POWERFUL PET MANAGEMENT ENGINE V2.0 LIVE | SYSTEM SECURED
</footer>

<script>
function updateDateTime() {
    const now = new Date();

    document.getElementById("currentDate").textContent = now.toLocaleDateString("en-US", {
        weekday: "short",
        month: "short",
        day: "numeric",
        year: "numeric"
    });

    document.getElementById("currentTime").textContent = now.toLocaleTimeString("en-US", {
        hour: "2-digit",
        minute: "2-digit"
    });
}

updateDateTime();
setInterval(updateDateTime, 1000);
</script>

</body>

</html>