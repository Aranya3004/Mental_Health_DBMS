<?php
session_start();
require 'db.php';

/* Block access if user not logged in */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

/* Get session user */
$userSession = $_SESSION['user'];
$user_id = $userSession['user_id'];

/* Fetch full user details */
$stmt = $conn->prepare(
    "SELECT first_name, last_name, area, city, country, preferences 
     FROM users WHERE user_id = ?"
);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

/* Safety */
if (!$user) {
    echo "<h2>User not found in database.</h2>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>MindCare | User Dashboard</title>

<style>
* {
    box-sizing:border-box;
    margin:0;
    padding:0;
    font-family:Arial, sans-serif;
}

body {
    background:#f4f7fb;
}

/* NAVBAR */
nav {
    background:#1e293b;
    padding:12px 20px;
}

nav ul {
    list-style:none;
    display:flex;
    gap:10px;
    align-items:center;
}

nav a button {
    background:#1e293b;
    color:white;
    border:none;
    padding:8px 14px;
    border-radius:6px;
    cursor:pointer;
}

nav a button:hover {
    background:#334155;
}

.logout {
    margin-left:auto;
}

/* CONTAINER */
.container {
    max-width:1200px;
    margin:30px auto;
    padding:0 20px;
}

/* GRID */
.grid {
    display:grid;
    grid-template-columns:1fr 2fr;
    gap:20px;
}

/* CARD */
.card {
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 6px 18px rgba(0,0,0,.08);
}

/* PROFILE */
.profile {
    display:flex;
    gap:15px;
    align-items:center;
}

.avatar {
    width:64px;
    height:64px;
    border-radius:50%;
    background:#2563eb;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
    font-weight:bold;
}

.muted {
    color:#64748b;
    font-size:14px;
}

.small {
    font-size:13px;
}

/* BUTTONS */
.btn {
    padding:10px 16px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:600;
}

.btn-primary {
    background:#2563eb;
    color:white;
}

.btn-primary:hover {
    background:#1e40af;
}

.btn-ghost {
    background:#e5e7eb;
    color:#111827;
}

.btn-ghost:hover {
    background:#d1d5db;
}

/* QUICK ACTIONS */
.quick-actions {
    margin-top:20px;
    display:flex;
    gap:10px;
}

/* MENU GRID */
.menu-grid {
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:15px;
}

.menu-item {
    background:#f8fafc;
    padding:20px;
    border-radius:10px;
    text-align:center;
    cursor:pointer;
    border:1px solid #e5e7eb;
}

.menu-item:hover {
    background:#eef2ff;
}

.menu-item h4 {
    margin-top:10px;
    font-size:16px;
}
</style>
</head>

<body>

<nav>
<ul>
<li><a href="#"><button>Home</button></a></li>
<li><a href="daily_log.php"><button>Daily Log</button></a></li>
<li><a href="user_mood_analysis.php"><button>AI Analysis</button></a></li>
<li><a href="user_insurance_plan.php"><button>Insurance</button></a></li>
<li><a href="user_recommendations.php"><button>Recommendations</button></a></li>
<li><a href="user_progress.php"><button>Progress</button></a></li>
<li><a href="MySessions.php"><button>Sessions</button></a></li>
<li><a href="user_emergency_sos.php"><button style="background:#dc2626;">SOS</button></a></li>
<li class="logout"><a href="logout.php"><button>Logout</button></a></li>
</ul>
</nav>

<div class="container">

<div class="grid">

<!-- PROFILE CARD -->
<div class="card">
<div class="profile">
<div class="avatar">
<?= strtoupper($user['first_name'][0] . $user['last_name'][0]) ?>
</div>
<div>
<h3>
<?= htmlspecialchars($user['first_name'].' '.$user['last_name']) ?>
<span class="small muted">(ID: <?= $user_id ?>)</span>
</h3>
<div class="muted">
<?= htmlspecialchars($user['area'].', '.$user['city'].' — '.$user['country']) ?>
</div>
<div class="muted">
Preferences: <?= htmlspecialchars($user['preferences']) ?>
</div>
</div>
</div>

<div class="quick-actions">
<a href="MySessions.php"><button class="btn btn-primary">Book Session</button></a>
<a href="daily_log.php"><button class="btn btn-ghost">Add Daily Log</button></a>
</div>
</div>

<!-- MENU -->
<div class="card">
<h3>Quick Access</h3>
<br>
<div class="menu-grid">
<a href="daily_log.php" class="menu-item"><h4>📝 Daily Log</h4></a>
<a href="user_mood_analysis.php" class="menu-item"><h4>📊 Mood Analysis</h4></a>
<a href="user_recommendations.php" class="menu-item"><h4>🧠 Recommendations</h4></a>
<a href="user_progress.php" class="menu-item"><h4>📈 Progress</h4></a>
<a href="user_insurance_plan.php" class="menu-item"><h4>🛡 Insurance</h4></a>
<a href="user_emergency_sos.php" class="menu-item"><h4>🚨 Emergency SOS</h4></a>
</div>
</div>

</div>
</div>

</body>
</html>
