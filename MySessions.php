<?php
session_start();
require "db.php";

// Block access if user not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['user_id'];

// Fetch sessions
$stmt = $conn->prepare(
    "SELECT 
        s.session_time,
        s.session_type,
        s.feedback,
        s.medicine,
        s.dosage,
        s.status,
        c.first_name AS c_first,
        c.last_name AS c_last
     FROM sessions s
     LEFT JOIN counsellors c 
        ON s.counsellor_id = c.counsellor_id
     WHERE s.user_id = ?
     ORDER BY s.session_time DESC"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>My Sessions</title>
<style>
body { font-family: Arial; background: #f0f2f5; padding: 20px; }
h2 { text-align: center; }

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 25px;
    background: white;
}

th, td {
    padding: 12px;
    border: 1px solid #ddd;
}

th {
    background: #4CAF50;
    color: white;
}

tr:nth-child(even) {
    background: #f7f7f7;
}

.back-btn {
    display: inline-block;
    padding: 10px 20px;
    background: #2e7dff;
    color: white;
    border-radius: 5px;
    text-decoration: none;
    margin-bottom: 10px;
}

.back-btn:hover { background: #1b52b5; }

.status {
    padding: 5px 10px;
    border-radius: 5px;
    font-weight: bold;
    color: white;
    font-size: 13px;
}

.Pending { background: #f59e0b; }
.Completed { background: #16a34a; }
.Cancelled { background: #dc2626; }
</style>
</head>

<body>

<a href="UsersDashboard.php" class="back-btn">← Back to Dashboard</a>
<a href="BookSession.php" class="back-btn">Book Session</a>

<h2>📅 My Therapy Sessions</h2>

<table>
<tr>
    <th>Date & Time</th>
    <th>Type</th>
    <th>Counsellor</th>
    <th>Feedback</th>
    <th>Medicine</th>
    <th>Dosage</th>
    <th>Status</th>
</tr>

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['session_time']) ?></td>
            <td><?= htmlspecialchars($row['session_type']) ?></td>
            <td>
                <?= $row['c_first'] 
                    ? htmlspecialchars($row['c_first'] . ' ' . $row['c_last']) 
                    : '—' ?>
            </td>
            <td><?= htmlspecialchars($row['feedback'] ?? '—') ?></td>
            <td><?= htmlspecialchars($row['medicine'] ?? '—') ?></td>
            <td><?= htmlspecialchars($row['dosage'] ?? '—') ?></td>
            <td>
                <span class="status <?= htmlspecialchars($row['status']) ?>">
                    <?= htmlspecialchars($row['status']) ?>
                </span>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="7" style="text-align:center;">No sessions booked yet.</td>
    </tr>
<?php endif; ?>

</table>

</body>
</html>
