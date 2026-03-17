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

/* ================= ADD / UPDATE ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $log_id       = $_POST['log_id'] ?? '';
    $log_date     = $_POST['log_date'];
    $mood         = trim($_POST['mood']);
    $stress       = trim($_POST['stress_level']);
    $sleep_hours  = (int)$_POST['sleep_hours'];
    $note         = trim($_POST['note']);
    $user_id      = (int)$_POST['user_id'];

    if ($log_id === '') {
        /* ADD */
        $stmt = $conn->prepare(
            "INSERT INTO daily_logs
            (log_date, mood, stress_level, sleep_hours, note, user_id)
            VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "sssisi",
            $log_date, $mood, $stress, $sleep_hours, $note, $user_id
        );
    } else {
        /* UPDATE */
        $stmt = $conn->prepare(
            "UPDATE daily_logs SET
                log_date=?,
                mood=?,
                stress_level=?,
                sleep_hours=?,
                note=?
            WHERE log_id=?"
        );
        $stmt->bind_param(
            "sssisi",
            $log_date, $mood, $stress, $sleep_hours, $note, $log_id
        );
    }

    $stmt->execute();
    header("Location: admin_daily_logs.php");
    exit();
}

/* ================= DELETE ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $delete_id = (int)$_POST['delete_id'];

    $stmt = $conn->prepare("DELETE FROM daily_logs WHERE log_id=?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    header("Location: admin_daily_logs.php");
    exit();
}

/* ================= EDIT ================= */
$edit = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];

    $stmt = $conn->prepare("SELECT * FROM daily_logs WHERE log_id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
}

/* ================= SEARCH ================= */
$search = $_GET['search'] ?? '';

$stmt = $conn->prepare(
    "SELECT dl.*, u.first_name, u.last_name
     FROM daily_logs dl
     JOIN users u ON dl.user_id = u.user_id
     WHERE dl.mood LIKE ?
     ORDER BY dl.log_date DESC"
);
$like = "%$search%";
$stmt->bind_param("s", $like);
$stmt->execute();
$logs = $stmt->get_result();

/* ================= USERS ================= */
$users = $conn->query("SELECT user_id, first_name, last_name FROM users ORDER BY first_name");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin – Daily Logs</title>
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

textarea { resize:vertical; }

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
    vertical-align:top;
}

th { background:#f0f3fa; }
.actions { white-space:nowrap; }
</style>
</head>

<body>
<div class="container">

<a href="admin_dashboard.php" class="back">← Back to Dashboard</a>
<h2>📘 Daily Logs Management</h2>

<!-- ADD / EDIT -->
<div class="card">
<form method="POST">
<h3><?= $edit ? "Update Daily Log" : "Add New Daily Log" ?></h3>

<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
<input type="hidden" name="log_id" value="<?= htmlspecialchars($edit['log_id'] ?? '') ?>">

<input type="date" name="log_date" required
       value="<?= htmlspecialchars($edit['log_date'] ?? '') ?>">

<input name="mood" placeholder="Mood" required
       value="<?= htmlspecialchars($edit['mood'] ?? '') ?>">

<input name="stress_level" placeholder="Stress Level"
       value="<?= htmlspecialchars($edit['stress_level'] ?? '') ?>">

<input type="number" name="sleep_hours" placeholder="Sleep Hours"
       value="<?= htmlspecialchars($edit['sleep_hours'] ?? '') ?>">

<textarea name="note" placeholder="Notes"><?= htmlspecialchars($edit['note'] ?? '') ?></textarea>

<select name="user_id" required>
<option value="">Select User</option>
<?php
$users->data_seek(0);
while ($u = $users->fetch_assoc()):
?>
<option value="<?= $u['user_id'] ?>"
<?= isset($edit) && $edit['user_id'] == $u['user_id'] ? 'selected' : '' ?>>
<?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?>
</option>
<?php endwhile; ?>
</select>

<br>
<button name="save"><?= $edit ? "Update Log" : "Add Log" ?></button>
</form>
</div>

<!-- SEARCH -->
<div class="card">
<form method="GET">
<input name="search" placeholder="Search by mood"
       value="<?= htmlspecialchars($search) ?>">
<button>Search</button>
</form>
</div>

<!-- LOGS TABLE -->
<div class="card">
<table>
<tr>
<th>ID</th>
<th>Date</th>
<th>User</th>
<th>Mood</th>
<th>Stress</th>
<th>Sleep</th>
<th>Notes</th>
<th>Actions</th>
</tr>

<?php while ($l = $logs->fetch_assoc()): ?>
<tr>
<td><?= $l['log_id'] ?></td>
<td><?= htmlspecialchars($l['log_date']) ?></td>
<td><?= htmlspecialchars($l['first_name'].' '.$l['last_name']) ?></td>
<td><?= htmlspecialchars($l['mood']) ?></td>
<td><?= htmlspecialchars($l['stress_level']) ?></td>
<td><?= htmlspecialchars($l['sleep_hours']) ?></td>
<td><?= nl2br(htmlspecialchars($l['note'])) ?></td>
<td class="actions">

<a href="?edit=<?= $l['log_id'] ?>">
<button>Edit</button>
</a>

<form method="POST" style="display:inline" onsubmit="return confirm('Delete this log?')">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
<input type="hidden" name="delete_id" value="<?= $l['log_id'] ?>">
<button class="delete">Delete</button>
</form>

</td>
</tr>
<?php endwhile; ?>
</table>
</div>

</div>
</body>
</html>
