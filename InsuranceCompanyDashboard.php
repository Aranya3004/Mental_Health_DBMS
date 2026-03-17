<?php
session_start();
require 'insurance_db.php';

if (!isset($_SESSION['insurance_company_id'])) {
    header("Location: insurance_company_login.php");
    exit();
}

$company_id = $_SESSION['insurance_company_id'];

/* ADD new plan */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_plan'])) {
    $stmt = $conn->prepare(
        "INSERT INTO insurance_plan (plan_name, coverage_details, premium_amount, insurance_company_id)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "ssdi",
        $_POST['plan_name'],
        $_POST['coverage_details'],
        $_POST['premium_amount'],
        $company_id
    );
    $stmt->execute();
}

/* UPDATE plan */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_plan'])) {
    $stmt = $conn->prepare(
        "UPDATE insurance_plan
         SET plan_name = ?, coverage_details = ?, premium_amount = ?
         WHERE plan_id = ? AND insurance_company_id = ?"
    );
    $stmt->bind_param(
        "ssdii",
        $_POST['plan_name'],
        $_POST['coverage_details'],
        $_POST['premium_amount'],
        $_POST['plan_id'],
        $company_id
    );
    $stmt->execute();
}

/* DELETE plan */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_plan'])) {
    $stmt = $conn->prepare(
        "DELETE FROM insurance_plan WHERE plan_id = ? AND insurance_company_id = ?"
    );
    $stmt->bind_param("ii", $_POST['plan_id'], $company_id);
    $stmt->execute();
}

/* FETCH plans */
$stmt = $conn->prepare(
    "SELECT * FROM insurance_plan WHERE insurance_company_id = ?"
);
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Insurance Company Dashboard</title>

<style>
:root {
    --primary:#2563eb;
    --danger:#dc2626;
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

/* HEADER */
header {
    background:#1e293b;
    color:white;
    padding:18px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

header h2 { margin:0; }

header a {
    color:white;
    text-decoration:none;
    font-weight:600;
}

/* CONTAINER */
.container {
    max-width:1200px;
    margin:30px auto;
    padding:0 20px;
}

/* CARD */
.card {
    background:var(--card);
    padding:25px;
    border-radius:16px;
    box-shadow:0 20px 40px rgba(0,0,0,.1);
    margin-bottom:30px;
}

.card h3 {
    margin-top:0;
}

/* FORM */
input, textarea {
    width:100%;
    padding:12px;
    border-radius:10px;
    border:1px solid var(--border);
    font-size:14px;
    margin-top:10px;
}

textarea { resize:vertical; min-height:90px; }

input:focus, textarea:focus {
    border-color:var(--primary);
    outline:none;
}

/* BUTTONS */
.btn {
    padding:10px 18px;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-weight:600;
}

.btn-primary {
    background:var(--primary);
    color:white;
}

.btn-secondary {
    background:#6b7280;
    color:white;
}

.btn-danger {
    background:var(--danger);
    color:white;
}

.btn:hover { opacity:.9; }

/* TABLE */
.table-card {
    overflow:hidden;
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

.save-btn {
    display:none;
}
</style>
</head>

<body>

<header>
    <h2>🏥 Insurance Plans Dashboard</h2>
    <a href="logout.php">Logout</a>
</header>

<div class="container">

<!-- ADD PLAN -->
<div class="card">
    <h3>Add New Insurance Plan</h3>

    <form method="post">
        <input type="text" name="plan_name" placeholder="Plan Name" required>
        <textarea name="coverage_details" placeholder="Coverage Details" required></textarea>
        <input type="number" step="0.01" name="premium_amount" placeholder="Premium Amount" required>
        <br><br>
        <button class="btn btn-primary" name="add_plan">Add Plan</button>
    </form>
</div>

<!-- EXISTING PLANS -->
<div class="card table-card">
    <h3>My Existing Plans</h3>
    <button class="btn btn-secondary" onclick="enableEdit()">Enable Editing</button>

    <form method="post">
    <table>
        <tr>
            <th>ID</th>
            <th>Plan Name</th>
            <th>Coverage</th>
            <th>Premium</th>
            <th>Update</th>
            <th>Delete</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['plan_id'] ?></td>

            <td>
                <input type="text" name="plan_name"
                       value="<?= htmlspecialchars($row['plan_name']) ?>" readonly>
            </td>

            <td>
                <textarea name="coverage_details" readonly><?= htmlspecialchars($row['coverage_details']) ?></textarea>
            </td>

            <td>
                <input type="number" step="0.01" name="premium_amount"
                       value="<?= $row['premium_amount'] ?>" readonly>
            </td>

            <td>
                <input type="hidden" name="plan_id" value="<?= $row['plan_id'] ?>">
                <button class="btn btn-primary save-btn" name="update_plan">Save</button>
            </td>

            <td>
                <button class="btn btn-danger" name="delete_plan"
                        onclick="return confirm('Delete this plan?')">
                    Delete
                </button>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    </form>
</div>

</div>

<script>
function enableEdit() {
    document.querySelectorAll('input, textarea').forEach(el => {
        el.removeAttribute('readonly');
    });
    document.querySelectorAll('.save-btn').forEach(btn => {
        btn.style.display = 'inline-block';
    });
}
</script>

</body>
</html>
