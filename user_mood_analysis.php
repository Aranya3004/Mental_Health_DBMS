<?php
session_start();
require 'db.php';

/* Block access if not logged in */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userSession = $_SESSION['user'];
$user_id = $userSession['user_id'];

/* Fetch mood logs */
$stmt = $conn->prepare(
    "SELECT log_date, mood, stress_level
     FROM daily_logs
     WHERE user_id = ?
     ORDER BY log_date DESC"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Mood Analysis</title>

<style>
body {
    font-family: Arial, sans-serif;
    background:#f4f7fb;
    padding:30px;
}

.back-btn {
    display:inline-block;
    padding:10px 20px;
    background:#2e7dff;
    color:white;
    border-radius:6px;
    text-decoration:none;
    margin-bottom:20px;
}

.back-btn:hover {
    background:#1b52b5;
}

h2 {
    margin-bottom:15px;
}

table {
    width:100%;
    border-collapse:collapse;
    background:white;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}

th, td {
    padding:12px;
    border-bottom:1px solid #e5e7eb;
    text-align:left;
}

th {
    background:#2563eb;
    color:white;
}

tr:hover {
    background:#f1f5f9;
}

.badge {
    padding:5px 10px;
    border-radius:12px;
    font-size:13px;
    font-weight:bold;
}

.pending {
    background:#fde68a;
    color:#92400e;
}

.low { background:#dcfce7; color:#166534; }
.medium { background:#fef3c7; color:#92400e; }
.high { background:#fee2e2; color:#991b1b; }
</style>
</head>

<body>

<a href="UsersDashboard.php" class="back-btn">← Back to Dashboard</a>

<h2>📊 Mood & Stress Analysis</h2>

<table>
<tr>
    <th>Date</th>
    <th>Mood</th>
    <th>Stress Level</th>
</tr>

<?php if ($result->num_rows === 0): ?>
<tr>
    <td colspan="3" style="text-align:center; padding:20px;">
        No mood logs recorded yet.
    </td>
</tr>
<?php endif; ?>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['log_date']) ?></td>
    <td><?= htmlspecialchars($row['mood']) ?></td>
    <td>
        <?php if (empty($row['stress_level'])): ?>
            <span class="badge pending">Pending Analysis</span>
        <?php else: 
            $level = strtolower($row['stress_level']);
        ?>
            <span class="badge <?= $level ?>">
                <?= htmlspecialchars($row['stress_level']) ?>
            </span>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>
