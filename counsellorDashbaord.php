<?php
session_start();
require 'counsellor_db.php';

if (!isset($_SESSION['counsellor_id'])) {
    header("Location: counsellor_login.php");
    exit();
}

$counsellor_id = $_SESSION['counsellor_id'];

/* UPDATE session data */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_session'])) {
    $stmt = $conn->prepare(
        "UPDATE sessions 
         SET medicine = ?, dosage = ?, progress = ?
         WHERE session_id = ? AND counsellor_id = ?"
    );

    $stmt->bind_param(
        "sssii",
        $_POST['medicine'],
        $_POST['dosage'],
        $_POST['progress'],
        $_POST['session_id'],
        $counsellor_id
    );

    $stmt->execute();
}

/* FETCH accepted sessions */
$query = "
SELECT 
    s.session_id,
    s.session_time,
    s.session_type,
    s.progress,
    s.medicine,
    s.dosage,
    u.first_name
FROM sessions s
JOIN users u ON s.user_id = u.user_id
WHERE s.status = 'accepted'
AND s.counsellor_id = ?
ORDER BY s.session_time DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $counsellor_id);
$stmt->execute();
$result = $stmt->get_result();

/* ===== DASHBOARD COUNTS ===== */

$today = date('Y-m-d');

/* Today's appointments */
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total 
    FROM sessions 
    WHERE counsellor_id = ? 
    AND DATE(session_time) = ?
    AND status = 'accepted'
");
$stmt->bind_param("is", $counsellor_id, $today);
$stmt->execute();
$appointments = $stmt->get_result()->fetch_assoc()['total'];

/* Active patients */
$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT user_id) AS total 
    FROM sessions 
    WHERE counsellor_id = ? 
    AND status = 'accepted'
");
$stmt->bind_param("i", $counsellor_id);
$stmt->execute();
$patients = $stmt->get_result()->fetch_assoc()['total'];

/* Pending AI Reviews */
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total 
    FROM ai_analysis 
    WHERE user_id IN (
        SELECT user_id FROM sessions WHERE counsellor_id = ?
    )
");
$stmt->bind_param("i", $counsellor_id);
$stmt->execute();
$ai_reviews = $stmt->get_result()->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Counsellor Dashboard</title>

<style>
:root {
    --primary:#2563eb;
    --bg:#f1f5f9;
    --card:#ffffff;
    --text:#1e293b;
    --muted:#64748b;
    --border:#e5e7eb;
}

* { box-sizing:border-box; }

body {
    margin:0;
    font-family:"Segoe UI", Tahoma, sans-serif;
    background:var(--bg);
    color:var(--text);
}

/* NAVBAR */
nav {
    background:#1e293b;
    padding:14px 24px;
}

nav ul {
    list-style:none;
    display:flex;
    align-items:center;
    gap:20px;
    margin:0;
    padding:0;
}

nav li {
    color:white;
    font-weight:500;
}

nav button {
    background:none;
    border:none;
    color:white;
    cursor:pointer;
    font-size:15px;
}

nav button:hover {
    text-decoration:underline;
}

.logout {
    margin-left:auto;
}

/* CONTAINER */
.container {
    max-width:1300px;
    margin:30px auto;
    padding:0 20px;
}

/* STATS */
.stats {
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
    margin-bottom:40px;
}

.stat-card {
    background:var(--card);
    padding:22px;
    border-radius:14px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.stat-card h3 {
    margin:0 0 10px;
    font-size:16px;
    color:var(--muted);
}

.stat-card .value {
    font-size:34px;
    font-weight:700;
}

/* TABLE */
.table-card {
    background:white;
    border-radius:14px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    overflow:hidden;
}

.table-card h2 {
    padding:20px;
    margin:0;
    border-bottom:1px solid var(--border);
}

table {
    width:100%;
    border-collapse:collapse;
}

th, td {
    padding:14px;
    border-bottom:1px solid var(--border);
    text-align:left;
    font-size:14px;
}

th {
    background:#f8fafc;
    font-weight:600;
}

input[type=text] {
    width:100%;
    padding:8px;
    border-radius:6px;
    border:1px solid var(--border);
}

.btn {
    background:var(--primary);
    color:white;
    padding:8px 16px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:600;
}

.btn:hover {
    opacity:.9;
}
</style>
</head>

<body>

<nav>
<ul>
    <li><button>Dashboard</button></li>
    <li><button>My Patients</button></li>
    <li><button>Sessions</button></li>
    <li><button>AI Predictions</button></li>
    <li><button>Clinic Info</button></li>
    <li class="logout">
        <button onclick="location.href='logout.php'">Logout</button>
    </li>
</ul>
</nav>

<div class="container">

<h1>Welcome, Counsellor</h1>

<!-- STATS -->
<div class="stats">
    <div class="stat-card">
        <h3>Today’s Appointments</h3>
        <div class="value"><?= $appointments ?></div>
    </div>

    <div class="stat-card">
        <h3>Active Patients</h3>
        <div class="value"><?= $patients ?></div>
    </div>

    <div class="stat-card">
        <h3>Pending AI Reviews</h3>
        <div class="value"><?= $ai_reviews ?></div>
    </div>
</div>

<!-- SESSIONS TABLE -->
<div class="table-card">
<h2>Accepted Sessions</h2>

<form method="post">
<table>
<tr>
    <th>ID</th>
    <th>Patient</th>
    <th>Time</th>
    <th>Type</th>
    <th>Medicine</th>
    <th>Dosage</th>
    <th>Progress</th>
    <th>Action</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['session_id'] ?></td>
    <td><?= htmlspecialchars($row['first_name']) ?></td>
    <td><?= $row['session_time'] ?></td>
    <td><?= $row['session_type'] ?></td>

    <td><input type="text" name="medicine" value="<?= htmlspecialchars($row['medicine']) ?>"></td>
    <td><input type="text" name="dosage" value="<?= htmlspecialchars($row['dosage']) ?>"></td>
    <td><input type="text" name="progress" value="<?= htmlspecialchars($row['progress']) ?>"></td>

    <td>
        <input type="hidden" name="session_id" value="<?= $row['session_id'] ?>">
        <button class="btn" name="update_session">Save</button>
    </td>
</tr>
<?php endwhile; ?>
</table>
</form>
</div>

</div>
</body>
</html>
