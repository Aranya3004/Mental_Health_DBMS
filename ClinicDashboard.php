<?php
session_start();
require 'clinic_db.php'; // use clinic db connection

if (!isset($_SESSION['clinic_id'])) {
    header("Location: clinic_login.php");
    exit();
}

$clinic_id = $_SESSION['clinic_id'];

// Fetch all pending sessions for this clinic
$stmt = $conn->prepare("
    SELECT 
        s.session_id, s.session_time, s.session_type, s.status,
        u.first_name AS user_first, u.last_name AS user_last,
        c.first_name AS counsellor_first, c.last_name AS counsellor_last
    FROM sessions s
    JOIN users u ON s.user_id = u.user_id
    JOIN counsellors c ON s.counsellor_id = c.counsellor_id
    WHERE c.clinic_id = ? AND s.status='pending'
");
$stmt->bind_param("i", $clinic_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Clinic Dashboard</title>

<style>
:root {
    --primary:#2563eb;
    --success:#16a34a;
    --danger:#dc2626;
    --dark:#1e293b;
    --border:#e5e7eb;
    --bg:#f8fafc;
}

body {
    margin:0;
    font-family:"Segoe UI", Tahoma, sans-serif;
    background: linear-gradient(135deg,#eef2ff,#f8fafc);
    padding:30px;
    color:var(--dark);
}

.dashboard {
    max-width:1200px;
    margin:auto;
    background:white;
    border-radius:14px;
    box-shadow:0 25px 50px rgba(0,0,0,0.1);
    padding:25px;
}

.header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.header h2 {
    margin:0;
}

table {
    width:100%;
    border-collapse:collapse;
    font-size:14px;
}

th {
    background:var(--primary);
    color:white;
    padding:12px;
    text-align:left;
}

td {
    padding:12px;
    border-bottom:1px solid var(--border);
}

tr:hover {
    background:#f1f5f9;
}

.actions a {
    padding:6px 10px;
    border-radius:6px;
    text-decoration:none;
    font-weight:600;
    font-size:13px;
}

.accept {
    background:var(--success);
    color:white;
}

.decline {
    background:var(--danger);
    color:white;
}

.accept:hover,
.decline:hover {
    opacity:0.9;
}

.empty {
    text-align:center;
    padding:20px;
    color:#64748b;
    font-weight:600;
}
</style>
</head>

<body>

<div class="dashboard">

    <div class="header">
        <h2>🏥 Pending Session Requests</h2>
        <strong><?= $_SESSION['clinic_name'] ?></strong>
    </div>

    <table>
        <tr>
            <th>Session ID</th>
            <th>User</th>
            <th>Counsellor</th>
            <th>Session Time</th>
            <th>Session Type</th>
            <th>Action</th>
        </tr>

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['session_id'] ?></td>
                <td><?= $row['user_first'] . ' ' . $row['user_last'] ?></td>
                <td><?= $row['counsellor_first'] . ' ' . $row['counsellor_last'] ?></td>
                <td><?= $row['session_time'] ?></td>
                <td><?= $row['session_type'] ?></td>
                <td class="actions">
                    <a class="accept" href="accept_session.php?session_id=<?= $row['session_id'] ?>">
                        Accept
                    </a>
                    <a class="decline" href="decline_session.php?session_id=<?= $row['session_id'] ?>">
                        Decline
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="empty">
                    No pending session requests
                </td>
            </tr>
        <?php endif; ?>
    </table>

</div>

</body>
</html>
