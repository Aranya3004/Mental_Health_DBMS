<?php
session_start();
require 'sos_db.php';

if (!isset($_SESSION['sos_team'])) {
    header("Location: sos_team_login.php");
    exit();
}

$team = $_SESSION['sos_team'];

/* Resolve alert */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resolve_alert'])) {
    $stmt = $conn->prepare(
        "UPDATE crisis_alert SET status = 'Resolved' WHERE alert_id = ?"
    );
    $stmt->bind_param("i", $_POST['alert_id']);
    $stmt->execute();
}

/* Fetch alerts */
$query = "
SELECT 
    c.alert_id,
    c.alert_timestamp,
    c.severity_level,
    c.message,
    c.status,
    u.first_name,
    u.last_name,
    u.contact_info AS phone,
    CONCAT(u.area, ', ', u.city, ', ', u.country) AS location
FROM crisis_alert c
JOIN users u ON c.user_id = u.user_id
ORDER BY c.alert_timestamp DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
<title>SOS Team Dashboard</title>
<style>
body { font-family:Arial; background:#f4f7fb; padding:20px; }
h2 { margin-bottom:10px; }
table { width:100%; border-collapse:collapse; background:white; }
th, td { padding:10px; border-bottom:1px solid #ddd; }
th { background:#dc3545; color:white; }
.btn { padding:6px 12px; background:#198754; color:white; border:none; border-radius:5px; cursor:pointer; }
.badge { padding:4px 8px; border-radius:4px; color:white; }
.High { background:#fd7e14; }
.Critical { background:#dc3545; }
.Medium { background:#ffc107; color:black; }
.Low { background:#198754; }
</style>
</head>

<body>

<h2>🚑 Emergency SOS Dashboard</h2>
<p><strong>Team:</strong> <?= $team['team_name'] ?> (<?= $team['location'] ?>)</p>

<form method="post">
<table>
<tr>
    <th>ID</th>
    <th>User</th>
    <th>Phone</th>
    <th>Location</th>
    <th>Severity</th>
    <th>Message</th>
    <th>Time</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['alert_id'] ?></td>
    <td><?= $row['first_name'].' '.$row['last_name'] ?></td>
    <td><?= $row['phone'] ?></td>
    <td><?= $row['location'] ?></td>
    <td><span class="badge <?= $row['severity_level'] ?>"><?= $row['severity_level'] ?></span></td>
    <td><?= htmlspecialchars($row['message']) ?></td>
    <td><?= $row['alert_timestamp'] ?></td>
    <td><?= $row['status'] ?></td>
    <td>
        <?php if ($row['status'] !== 'Resolved'): ?>
            <input type="hidden" name="alert_id" value="<?= $row['alert_id'] ?>">
            <button class="btn" name="resolve_alert">Resolve</button>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>

</table>
</form>

</body>
</html>
