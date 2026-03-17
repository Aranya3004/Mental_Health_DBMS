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

/* Fetch recommendations */
$stmt = $conn->prepare(
    "SELECT notes, date, is_completed 
     FROM recommendations 
     WHERE user_id = ?
     ORDER BY date DESC"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Recommendations</title>

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

.status {
    font-weight:bold;
}

.completed {
    color:green;
}

.pending {
    color:#e63946;
}

.empty {
    text-align:center;
    padding:20px;
    color:#666;
}
</style>
</head>

<body>

<a href="UsersDashboard.php" class="back-btn">← Back to Dashboard</a>

<h2>📝 Analyst Recommendations</h2>

<table>
<tr>
    <th>Date</th>
    <th>Recommendation</th>
    <th>Status</th>
</tr>

<?php if ($result->num_rows === 0): ?>
<tr>
    <td colspan="3" class="empty">
        No recommendations available yet.
    </td>
</tr>
<?php endif; ?>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['date']) ?></td>
    <td><?= nl2br(htmlspecialchars($row['notes'])) ?></td>
    <td class="status <?= $row['is_completed'] === 'Yes' ? 'completed' : 'pending' ?>">
        <?= htmlspecialchars($row['is_completed']) ?>
    </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>
