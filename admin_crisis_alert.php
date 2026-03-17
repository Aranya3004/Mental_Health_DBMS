<?php
session_start();
require 'db.php';

/* ================= AUTH ================= */
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

/* ================= CSRF ================= */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* ================= ADD ALERT ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $user_id         = (int)$_POST['user_id'];
    $alert_timestamp = $_POST['alert_timestamp'];
    $severity_level  = trim($_POST['severity_level']);
    $message         = trim($_POST['message'] ?? '');
    $status          = $_POST['status'];

    $stmt = $conn->prepare(
        "INSERT INTO crisis_alert
        (user_id, alert_timestamp, severity_level, message, status)
        VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "issss",
        $user_id,
        $alert_timestamp,
        $severity_level,
        $message,
        $status
    );

    $stmt->execute();
    header("Location: crisis_alerts.php");
    exit();
}

/* ================= UPDATE ALERT ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $alert_id        = (int)$_POST['alert_id'];
    $alert_timestamp = $_POST['alert_timestamp'];
    $severity_level  = trim($_POST['severity_level']);
    $message         = trim($_POST['message'] ?? '');
    $status          = $_POST['status'];

    $stmt = $conn->prepare(
        "UPDATE crisis_alert SET
            alert_timestamp=?,
            severity_level=?,
            message=?,
            status=?
         WHERE alert_id=?"
    );

    $stmt->bind_param(
        "ssssi",
        $alert_timestamp,
        $severity_level,
        $message,
        $status,
        $alert_id
    );

    $stmt->execute();
    header("Location: crisis_alerts.php");
    exit();
}

/* ================= DELETE ALERT ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $delete_id = (int)$_POST['delete_id'];

    $stmt = $conn->prepare("DELETE FROM crisis_alert WHERE alert_id=?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    header("Location: crisis_alerts.php");
    exit();
}

/* ================= SEARCH ================= */
$search = $_GET['search'] ?? '';

$query = "
SELECT ca.*, u.first_name, u.last_name
FROM crisis_alert ca
JOIN users u ON ca.user_id = u.user_id
WHERE
    ca.severity_level LIKE ?
 OR ca.status LIKE ?
ORDER BY ca.alert_timestamp DESC
";

$stmt = $conn->prepare($query);
$like = "%$search%";
$stmt->bind_param("ss", $like, $like);
$stmt->execute();
$alerts = $stmt->get_result();

/* ================= USERS ================= */
$users = $conn->query("SELECT user_id, first_name, last_name FROM users");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin – Crisis Alerts</title>
<style>
body { font-family:Arial; background:#f4f7fb; margin:0; }
.container { max-width:1200px; margin:20px auto; }

.back { text-decoration:none; font-weight:600; color:#2b6ef6; }

.card {
    background:white;
    padding:18px;
    border-radius:10px;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
    margin-bottom:20px;
}

input, textarea, select {
    padding:8px;
    margin:5px 0;
    width:100%;
}

button {
    padding:8px 14px;
    background:#2b6ef6;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
}

button.delete { background:#e74c3c; }

table { width:100%; border-collapse:collapse; }

th, td {
    padding:10px;
    border-bottom:1px solid #ddd;
    text-align:center;
}

th { background:#f0f3fa; }
</style>
</head>

<body>
<div class="container">

<a href="admin_dashboard.php" class="back">← Back to Dashboard</a>
<h2>🚨 Crisis Alerts Management</h2>

<!-- SEARCH -->
<div class="card">
<form method="GET">
<input type="text" name="search" placeholder="Search severity or status"
       value="<?= htmlspecialchars($search) ?>">
<button>Search</button>
</form>
</div>

<!-- ADD ALERT -->
<div class="card">
<form method="POST">
<h3>Add Crisis Alert</h3>

<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

<select name="user_id" required>
<option value="">Select User</option>
<?php while ($u = $users->fetch_assoc()): ?>
<option value="<?= $u['user_id'] ?>">
<?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?>
</option>
<?php endwhile; ?>
</select>

<input type="datetime-local" name="alert_timestamp" required>

<input name="severity_level" placeholder="Severity (Low / High / Critical)" required>

<textarea name="message" placeholder="Alert message"></textarea>

<select name="status">
<option value="Pending">Pending</option>
<option value="In Progress">In Progress</option>
<option value="Resolved">Resolved</option>
</select>

<br><br>
<button name="add">Add Alert</button>
</form>
</div>

<!-- ALERT TABLE -->
<div class="card">
<table>
<tr>
<th>ID</th>
<th>User</th>
<th>Time</th>
<th>Severity</th>
<th>Message</th>
<th>Status</th>
<th>Actions</th>
</tr>

<?php while ($row = $alerts->fetch_assoc()): ?>
<tr>
<form method="POST">

<td><?= $row['alert_id'] ?></td>
<td><?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?></td>

<td>
<input type="datetime-local" name="alert_timestamp"
value="<?= date('Y-m-d\TH:i', strtotime($row['alert_timestamp'])) ?>">
</td>

<td><input name="severity_level" value="<?= htmlspecialchars($row['severity_level']) ?>"></td>

<td><textarea name="message"><?= htmlspecialchars($row['message']) ?></textarea></td>

<td>
<select name="status">
<option <?= $row['status']=='Pending'?'selected':'' ?>>Pending</option>
<option <?= $row['status']=='In Progress'?'selected':'' ?>>In Progress</option>
<option <?= $row['status']=='Resolved'?'selected':'' ?>>Resolved</option>
</select>
</td>

<td>
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
<input type="hidden" name="alert_id" value="<?= $row['alert_id'] ?>">

<button name="update">Update</button>

<button class="delete"
        name="delete_id"
        value="<?= $row['alert_id'] ?>"
        onclick="return confirm('Delete this alert?')">
Delete
</button>
</td>

</form>
</tr>
<?php endwhile; ?>
</table>
</div>

</div>
</body>
</html>
