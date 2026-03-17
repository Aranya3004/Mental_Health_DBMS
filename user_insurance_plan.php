<?php
session_start();
require 'db.php';

$stmt = $conn->prepare(
    "SELECT 
        ip.plan_name,
        ip.coverage_details,
        ip.premium_amount,
        ic.company_name
     FROM insurance_plan ip
     JOIN insurance_company ic 
       ON ip.insurance_company_id = ic.insurance_company_id"
);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Available Insurance Plans</title>

<style>
:root {
    --primary:#2563eb;
    --bg:#f4f7fb;
    --card:#ffffff;
    --text:#1f2937;
    --border:#e5e7eb;
}

* { box-sizing:border-box; }

body {
    margin:0;
    font-family:Arial, sans-serif;
    background:var(--bg);
    padding:30px;
}

.back-btn {
    display:inline-block;
    padding:10px 20px;
    background:var(--primary);
    color:white;
    border-radius:6px;
    text-decoration:none;
    margin-bottom:20px;
}

.back-btn:hover {
    background:#1e40af;
}

h2 {
    margin-top:0;
    margin-bottom:20px;
    color:var(--text);
}

.table-container {
    background:var(--card);
    border-radius:14px;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
    overflow:hidden;
}

table {
    width:100%;
    border-collapse:collapse;
}

th {
    background:var(--primary);
    color:white;
    padding:14px;
    text-align:left;
    font-size:14px;
}

td {
    padding:14px;
    border-bottom:1px solid var(--border);
    vertical-align:top;
}

tr:hover {
    background:#f9fafb;
}

.badge {
    display:inline-block;
    padding:4px 10px;
    border-radius:999px;
    background:#e0e7ff;
    color:#1e40af;
    font-size:12px;
    font-weight:600;
}

.price {
    font-weight:bold;
    color:#065f46;
}

.empty {
    padding:20px;
    text-align:center;
    color:#6b7280;
}
</style>
</head>

<body>

<a href="UsersDashboard.php" class="back-btn">← Back to Dashboard</a>

<h2>🛡 Available Insurance Plans</h2>

<div class="table-container">
<table>
<tr>
    <th>Company</th>
    <th>Plan Name</th>
    <th>Coverage Details</th>
    <th>Premium</th>
</tr>

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td>
            <span class="badge">
                <?= htmlspecialchars($row['company_name']) ?>
            </span>
        </td>
        <td><?= htmlspecialchars($row['plan_name']) ?></td>
        <td><?= nl2br(htmlspecialchars($row['coverage_details'])) ?></td>
        <td class="price">$<?= number_format($row['premium_amount'], 2) ?></td>
    </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="4" class="empty">No insurance plans available.</td>
    </tr>
<?php endif; ?>

</table>
</div>

</body>
</html>
