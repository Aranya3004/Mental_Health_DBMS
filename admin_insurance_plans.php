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

    $plan_id     = $_POST['plan_id'] ?? '';
    $plan_name   = trim($_POST['plan_name']);
    $coverage    = trim($_POST['coverage_details']);
    $premium     = (float)$_POST['premium_amount'];
    $company_id  = (int)$_POST['insurance_company_id'];

    if ($plan_id === '') {
        /* ADD */
        $stmt = $conn->prepare(
            "INSERT INTO insurance_plan
            (plan_name, coverage_details, premium_amount, insurance_company_id)
            VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssdi", $plan_name, $coverage, $premium, $company_id);
    } else {
        /* UPDATE */
        $stmt = $conn->prepare(
            "UPDATE insurance_plan SET
                plan_name=?,
                coverage_details=?,
                premium_amount=?,
                insurance_company_id=?
            WHERE plan_id=?"
        );
        $stmt->bind_param(
            "ssdii",
            $plan_name,
            $coverage,
            $premium,
            $company_id,
            $plan_id
        );
    }

    $stmt->execute();
    header("Location: admin_insurance_plans.php");
    exit();
}

/* ================= DELETE ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $delete_id = (int)$_POST['delete_id'];

    $stmt = $conn->prepare("DELETE FROM insurance_plan WHERE plan_id=?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    header("Location: admin_insurance_plans.php");
    exit();
}

/* ================= EDIT ================= */
$edit = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];

    $stmt = $conn->prepare("SELECT * FROM insurance_plan WHERE plan_id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
}

/* ================= SEARCH ================= */
$search = $_GET['search'] ?? '';

$stmt = $conn->prepare(
    "SELECT ip.*, ic.company_name
     FROM insurance_plan ip
     JOIN insurance_company ic
       ON ip.insurance_company_id = ic.insurance_company_id
     WHERE ip.plan_name LIKE ?
     ORDER BY ip.plan_id DESC"
);

$like = "%$search%";
$stmt->bind_param("s", $like);
$stmt->execute();
$plans = $stmt->get_result();

/* ================= COMPANIES ================= */
$companies = $conn->query("SELECT * FROM insurance_company ORDER BY company_name");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin – Insurance Plans</title>
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
<h2>🛡 Insurance Plans Management</h2>

<!-- ADD / EDIT -->
<div class="card">
<form method="POST">
<h3><?= $edit ? "Update Insurance Plan" : "Add New Insurance Plan" ?></h3>

<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
<input type="hidden" name="plan_id" value="<?= htmlspecialchars($edit['plan_id'] ?? '') ?>">

<input name="plan_name" placeholder="Plan Name" required
       value="<?= htmlspecialchars($edit['plan_name'] ?? '') ?>">

<textarea name="coverage_details" placeholder="Coverage Details" required><?= htmlspecialchars($edit['coverage_details'] ?? '') ?></textarea>

<input type="number" step="0.01" name="premium_amount" placeholder="Premium Amount" required
       value="<?= htmlspecialchars($edit['premium_amount'] ?? '') ?>">

<select name="insurance_company_id" required>
<option value="">Select Insurance Company</option>
<?php
$companies->data_seek(0);
while ($c = $companies->fetch_assoc()):
?>
<option value="<?= $c['insurance_company_id'] ?>"
<?= isset($edit) && $edit['insurance_company_id'] == $c['insurance_company_id'] ? 'selected' : '' ?>>
<?= htmlspecialchars($c['company_name']) ?>
</option>
<?php endwhile; ?>
</select>

<br>
<button name="save"><?= $edit ? "Update Plan" : "Add Plan" ?></button>
</form>
</div>

<!-- SEARCH -->
<div class="card">
<form method="GET">
<input name="search" placeholder="Search by plan name"
       value="<?= htmlspecialchars($search) ?>">
<button>Search</button>
</form>
</div>

<!-- PLANS TABLE -->
<div class="card">
<table>
<tr>
<th>ID</th>
<th>Plan</th>
<th>Coverage</th>
<th>Premium</th>
<th>Company</th>
<th>Actions</th>
</tr>

<?php while ($p = $plans->fetch_assoc()): ?>
<tr>
<td><?= $p['plan_id'] ?></td>
<td><?= htmlspecialchars($p['plan_name']) ?></td>
<td><?= nl2br(htmlspecialchars($p['coverage_details'])) ?></td>
<td><?= htmlspecialchars($p['premium_amount']) ?></td>
<td><?= htmlspecialchars($p['company_name']) ?></td>
<td class="actions">

<a href="?edit=<?= $p['plan_id'] ?>">
<button>Edit</button>
</a>

<form method="POST" style="display:inline" onsubmit="return confirm('Delete this plan?')">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
<input type="hidden" name="delete_id" value="<?= $p['plan_id'] ?>">
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
