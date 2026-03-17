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

/* ================= ADD / UPDATE USER ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $user_id     = $_POST['user_id'] ?? '';
    $first_name  = trim($_POST['first_name']);
    $last_name   = trim($_POST['last_name']);
    $contact     = trim($_POST['contact_info']);
    $age         = (int)($_POST['age'] ?? 0);
    $preferences = trim($_POST['preferences'] ?? '');
    $area        = trim($_POST['area'] ?? '');
    $city        = trim($_POST['city'] ?? '');
    $country     = trim($_POST['country'] ?? '');

    if ($user_id === '') {
        /* ADD */
        $stmt = $conn->prepare(
            "INSERT INTO users
            (first_name, last_name, contact_info, age, preferences, area, city, country)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "sssissss",
            $first_name,
            $last_name,
            $contact,
            $age,
            $preferences,
            $area,
            $city,
            $country
        );
    } else {
        /* UPDATE */
        $stmt = $conn->prepare(
            "UPDATE users SET
                first_name=?,
                last_name=?,
                contact_info=?,
                age=?,
                preferences=?,
                area=?,
                city=?,
                country=?
            WHERE user_id=?"
        );

        $stmt->bind_param(
            "sssissssi",
            $first_name,
            $last_name,
            $contact,
            $age,
            $preferences,
            $area,
            $city,
            $country,
            $user_id
        );
    }

    $stmt->execute();
    header("Location: admin_users.php");
    exit();
}

/* ================= DELETE USER ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $delete_id = (int)$_POST['delete_id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    header("Location: admin_users.php");
    exit();
}

/* ================= EDIT USER ================= */
$edit = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
}

/* ================= SEARCH ================= */
$search = $_GET['search'] ?? '';

$stmt = $conn->prepare(
    "SELECT * FROM users
     WHERE first_name LIKE ?
        OR last_name LIKE ?
        OR contact_info LIKE ?
     ORDER BY user_id DESC"
);

$like = "%$search%";
$stmt->bind_param("sss", $like, $like, $like);
$stmt->execute();
$users = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin – Users</title>
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

input {
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

button.delete {
    background:#e74c3c;
}

table {
    width:100%;
    border-collapse:collapse;
}

th, td {
    padding:10px;
    border-bottom:1px solid #ddd;
}

th { background:#f0f3fa; }
.actions { white-space:nowrap; }
</style>
</head>

<body>
<div class="container">

<a href="admin_dashboard.php" class="back">← Back to Dashboard</a>
<h2>👥 Users Management</h2>

<!-- ADD / EDIT -->
<div class="card">
<form method="POST">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
<input type="hidden" name="user_id" value="<?= htmlspecialchars($edit['user_id'] ?? '') ?>">

<input name="first_name" placeholder="First Name" required
       value="<?= htmlspecialchars($edit['first_name'] ?? '') ?>">

<input name="last_name" placeholder="Last Name" required
       value="<?= htmlspecialchars($edit['last_name'] ?? '') ?>">

<input name="contact_info" placeholder="Contact Info"
       value="<?= htmlspecialchars($edit['contact_info'] ?? '') ?>">

<input type="number" name="age" placeholder="Age"
       value="<?= htmlspecialchars($edit['age'] ?? '') ?>">

<input name="preferences" placeholder="Preferences"
       value="<?= htmlspecialchars($edit['preferences'] ?? '') ?>">

<input name="area" placeholder="Area"
       value="<?= htmlspecialchars($edit['area'] ?? '') ?>">

<input name="city" placeholder="City"
       value="<?= htmlspecialchars($edit['city'] ?? '') ?>">

<input name="country" placeholder="Country"
       value="<?= htmlspecialchars($edit['country'] ?? '') ?>">

<br>
<button name="save"><?= $edit ? "Update User" : "Add User" ?></button>
</form>
</div>

<!-- SEARCH -->
<div class="card">
<form method="GET">
<input name="search" placeholder="Search by name or contact"
       value="<?= htmlspecialchars($search) ?>">
<button>Search</button>
</form>
</div>

<!-- USERS TABLE -->
<div class="card">
<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Contact</th>
<th>Age</th>
<th>Preferences</th>
<th>Location</th>
<th>Actions</th>
</tr>

<?php while ($u = $users->fetch_assoc()): ?>
<tr>
<td><?= $u['user_id'] ?></td>
<td><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?></td>
<td><?= htmlspecialchars($u['contact_info']) ?></td>
<td><?= htmlspecialchars($u['age']) ?></td>
<td><?= htmlspecialchars($u['preferences']) ?></td>
<td><?= htmlspecialchars($u['area'].', '.$u['city'].', '.$u['country']) ?></td>
<td class="actions">

<a href="?edit=<?= $u['user_id'] ?>">
<button>Edit</button>
</a>

<form method="POST" style="display:inline">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
<input type="hidden" name="delete_id" value="<?= $u['user_id'] ?>">
<button class="delete" onclick="return confirm('Delete this user?')">
Delete
</button>
</form>

</td>
</tr>
<?php endwhile; ?>
</table>
</div>

</div>
</body>
</html>
