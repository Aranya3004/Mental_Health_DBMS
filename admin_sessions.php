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

/* ================= ADD SESSION ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $session_time   = $_POST['session_time'];
    $session_type   = trim($_POST['session_type']);
    $progress       = trim($_POST['progress'] ?? '');
    $feedback       = trim($_POST['feedback'] ?? '');
    $medicine       = trim($_POST['medicine'] ?? '');
    $dosage         = trim($_POST['dosage'] ?? '');
    $counsellor_id  = (int)$_POST['counsellor_id'];
    $user_id        = (int)$_POST['user_id'];
    $status         = $_POST['status'];

    $stmt = $conn->prepare(
        "INSERT INTO sessions
        (session_time, session_type, progress, feedback, medicine, dosage, counsellor_id, user_id, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "ssssssiis",
        $session_time,
        $session_type,
        $progress,
        $feedback,
        $medicine,
        $dosage,
        $counsellor_id,
        $user_id,
        $status
    );

    $stmt->execute();
    header("Location: admin_sessions.php");
    exit();
}

/* ================= UPDATE SESSION ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $session_id   = (int)$_POST['session_id'];
    $session_time = $_POST['session_time'];
    $session_type = trim($_POST['session_type']);
    $progress     = trim($_POST['progress'] ?? '');
    $feedback     = trim($_POST['feedback'] ?? '');
    $medicine     = trim($_POST['medicine'] ?? '');
    $dosage       = trim($_POST['dosage'] ?? '');
    $status       = $_POST['status'];

    $stmt = $conn->prepare(
        "UPDATE sessions SET
            session_time=?,
            session_type=?,
            progress=?,
            feedback=?,
            medicine=?,
            dosage=?,
            status=?
        WHERE session_id=?"
    );

    $stmt->bind_param(
        "sssssssi",
        $session_time,
        $session_type,
        $progress,
        $feedback,
        $medicine,
        $dosage,
        $status,
        $session_id
    );

    $stmt->execute();
    header("Location: admin_sessions.php");
    exit();
}

/* ================= DELETE SESSION ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $delete_id = (int)$_POST['delete_id'];

    $stmt = $conn->prepare("DELETE FROM sessions WHERE session_id=?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    header("Location: admin_sessions.php");
    exit();
}

/* ================= SEARCH ================= */
$search = $_GET['search'] ?? '';

$query = "
SELECT s.*,
       u.first_name AS user_name,
       c.first_name AS counsellor_name
FROM sessions s
JOIN users u ON s.user_id = u.user_id
JOIN counsellors c ON s.counsellor_id = c.counsellor_id
WHERE
    s.session_type LIKE ?
 OR u.first_name LIKE ?
 OR c.first_name LIKE ?
ORDER BY s.session_time DESC
";

$stmt = $conn->prepare($query);
$like = "%$search%";
$stmt->bind_param("sss", $like, $like, $like);
$stmt->execute();
$sessions = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin – Sessions</title>
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

input, select {
    padding:8px;
    margin:5px 0;
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

table {
    width:100%;
    border-collapse:collapse;
}

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
<h2>🗂 Sessions Management</h2>

<!-- SEARCH -->
<div class="card">
<form method="GET">
<input type="text" name="search"
       placeholder="Search by session, user or counsellor"
       value="<?= htmlspecialchars($search) ?>">
<button>Search</button>
</form>
</div>

<!-- ADD SESSION -->
<div class="card">
<form method="POST">
<h3>Add New Session</h3>

<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

<input type="datetime-local" name="session_time" required>
<input name="session_type" placeholder="Type" required>
<input name="progress" placeholder="Progress">
<input name="feedback" placeholder="Feedback">
<input name="medicine" placeholder="Medicine">
<input name="dosage" placeholder="Dosage">
<input type="number" name="counsellor_id" placeholder="Counsellor ID" required>
<input type="number" name="user_id" placeholder="User ID" required>

<select name="status">
<option value="pending">pending</option>
<option value="accepted">accepted</option>
<option value="completed">completed</option>
</select>

<br><br>
<button name="add">Add Session</button>
</form>
</div>

<!-- TABLE -->
<div class="card">
<table>
<tr>
<th>ID</th>
<th>Date</th>
<th>Type</th>
<th>User</th>
<th>Counsellor</th>
<th>Status</th>
<th>Medicine</th>
<th>Dosage</th>
<th>Progress</th>
<th>Actions</th>
</tr>

<?php while ($row = $sessions->fetch_assoc()): ?>
<tr>
<form method="POST">

<td><?= $row['session_id'] ?></td>

<td>
<input type="datetime-local" name="session_time"
value="<?= date('Y-m-d\TH:i', strtotime($row['session_time'])) ?>">
</td>

<td><input name="session_type" value="<?= htmlspecialchars($row['session_type']) ?>"></td>
<td><?= htmlspecialchars($row['user_name']) ?></td>
<td><?= htmlspecialchars($row['counsellor_name']) ?></td>

<td>
<select name="status">
<option <?= $row['status']=='pending'?'selected':'' ?>>pending</option>
<option <?= $row['status']=='accepted'?'selected':'' ?>>accepted</option>
<option <?= $row['status']=='completed'?'selected':'' ?>>completed</option>
</select>
</td>

<td><input name="medicine" value="<?= htmlspecialchars($row['medicine']) ?>"></td>
<td><input name="dosage" value="<?= htmlspecialchars($row['dosage']) ?>"></td>
<td><input name="progress" value="<?= htmlspecialchars($row['progress']) ?>"></td>

<td>
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
<input type="hidden" name="session_id" value="<?= $row['session_id'] ?>">

<button name="update">Update</button>
<button class="delete" name="delete_id"
        onclick="return confirm('Delete this session?')"
        value="<?= $row['session_id'] ?>">
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
