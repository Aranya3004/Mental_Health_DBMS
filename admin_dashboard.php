<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

/* Daily log mood summary */
$moodData = $conn->query(
    "SELECT mood, COUNT(*) as total 
     FROM daily_logs 
     GROUP BY mood"
);

/* Sessions graph */
$sessionData = $conn->query(
    "SELECT DATE(session_time) as day, COUNT(*) as total 
     FROM sessions 
     GROUP BY day"
);

/* Summary counts */
$users = $conn->query("SELECT COUNT(*) total FROM users")->fetch_assoc()['total'];
$counsellors = $conn->query("SELECT COUNT(*) total FROM counsellors")->fetch_assoc()['total'];
$sessions = $conn->query("SELECT COUNT(*) total FROM sessions")->fetch_assoc()['total'];
$logs = $conn->query("SELECT COUNT(*) total FROM daily_logs")->fetch_assoc()['total'];
$alerts = $conn->query("SELECT COUNT(*) total FROM crisis_alert")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body {
    font-family: Arial, sans-serif;
    background:#f4f7fb;
    margin:0;
}

/* Navbar */
nav {
    background:#333;
    padding:12px 20px;
}
nav a {
    color:white;
    margin-right:18px;
    text-decoration:none;
    font-weight:600;
}
nav a:hover {
    text-decoration:underline;
}

/* Layout */
.container {
    max-width:1200px;
    margin:25px auto;
    padding:0 15px;
}

h2 {
    margin-bottom:18px;
}

/* Summary cards */
.cards {
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(180px,1fr));
    gap:16px;
    margin-bottom:30px;
}

.card {
    background:white;
    padding:18px;
    border-radius:10px;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
    text-align:center;
}

.card b {
    font-size:26px;
    display:block;
    margin-top:6px;
}

/* Charts section */
.charts {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}

.chart-box {
    background:white;
    padding:18px;
    border-radius:10px;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
}

/* Chart size control */
canvas {
    max-height:260px;
}

/* Responsive */
@media (max-width:900px) {
    .charts {
        grid-template-columns:1fr;
    }
}
</style>
</head>

<body>

<nav>
  <a href="#">Dashboard</a>
  <a href="admin_users.php">Users</a>
  <a href="admin_sessions.php">Sessions</a>
  <a href="admin_insurance_plans.php">Insurance Plan</a>
  <a href="admin_daily_logs.php">Daily Logs</a>
  <a href="admin_crisis_alert.php">Crisis Alert</a>
  <a href="admin_login.php">Logout</a>
</nav>

<div class="container">

<h2>📊 System Dashboard</h2>

<!-- Summary Cards -->
<div class="cards">
  <div class="card">Users<b><?= $users ?></b></div>
  <div class="card">Counsellors<b><?= $counsellors ?></b></div>
  <div class="card">Sessions<b><?= $sessions ?></b></div>
  <div class="card">Daily Logs<b><?= $logs ?></b></div>
  <div class="card">Crisis Alerts<b><?= $alerts ?></b></div>
</div>

<!-- Charts -->
<div class="charts">

  <div class="chart-box">
    <h3>Daily Log Mood Distribution</h3>
    <canvas id="moodChart"></canvas>
  </div>

  <div class="chart-box">
    <h3>Sessions Over Time</h3>
    <canvas id="sessionChart"></canvas>
  </div>

</div>

</div>

<script>
/* Pie Chart – Mood Summary */
new Chart(document.getElementById('moodChart'), {
    type: 'pie',
    data: {
        labels: [
            <?php while($m = $moodData->fetch_assoc()) echo "'".$m['mood']."',"; ?>
        ],
        datasets: [{
            data: [
                <?php mysqli_data_seek($moodData, 0);
                while($m = $moodData->fetch_assoc()) echo $m['total'].","; ?>
            ]
        }]
    },
    options: {
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

/* Line Chart – Sessions */
new Chart(document.getElementById('sessionChart'), {
    type: 'line',
    data: {
        labels: [
            <?php while($s = $sessionData->fetch_assoc()) echo "'".$s['day']."',"; ?>
        ],
        datasets: [{
            label: 'Sessions',
            data: [
                <?php mysqli_data_seek($sessionData, 0);
                while($s = $sessionData->fetch_assoc()) echo $s['total'].","; ?>
            ],
            borderWidth: 2,
            fill: false
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

</body>
</html>
