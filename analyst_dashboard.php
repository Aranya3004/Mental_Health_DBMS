<?php
session_start();
require 'db.php';

if (!isset($_SESSION['analyst'])) {
    header("Location: analyst_login.php");
    exit();
}

/* Update stress level */
if (isset($_POST['update_stress'])) {
    $log_id = $_POST['log_id'];
    $stress = $_POST['stress_level'];

    $stmt = $conn->prepare(
        "UPDATE daily_logs SET stress_level = ? WHERE log_id = ?"
    );
    $stmt->bind_param("si", $stress, $log_id);
    $stmt->execute();
}

/* Insert recommendation */
if (isset($_POST['add_recommendation'])) {
    $user_id = $_POST['user_id'];
    $notes = $_POST['notes'];
    $today = date('Y-m-d');

    $stmt = $conn->prepare(
        "INSERT INTO recommendations (notes, date, is_completed, user_id)
         VALUES (?, ?, 'No', ?)"
    );
    $stmt->bind_param("ssi", $notes, $today, $user_id);
    $stmt->execute();
}

/* Fetch logs */
$query = "
SELECT 
    dl.log_id,
    dl.log_date,
    dl.mood,
    dl.sleep_hours,
    dl.note,
    dl.stress_level,
    dl.user_id,
    u.first_name,
    u.last_name
FROM daily_logs dl
JOIN users u ON dl.user_id = u.user_id
ORDER BY dl.log_date DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Analyst Dashboard</title>

<style>
:root {
    --primary:#2563eb;
    --secondary:#f1f5f9;
    --dark:#1e293b;
    --border:#e5e7eb;
    --success:#16a34a;
}

body {
    margin:0;
    font-family: "Segoe UI", Tahoma, sans-serif;
    background: linear-gradient(135deg,#eef2ff,#f8fafc);
    padding:30px;
    color:var(--dark);
}

.dashboard {
    max-width:1300px;
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
    font-size:26px;
}

.header p {
    margin:0;
    font-weight:600;
    color:var(--primary);
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
    vertical-align:top;
}

tr:hover {
    background:var(--secondary);
}

select, textarea {
    width:100%;
    padding:8px;
    border-radius:6px;
    border:1px solid #cbd5f5;
    font-size:14px;
    outline:none;
}

textarea {
    resize:none;
}

.actions button {
    width:100%;
    margin-bottom:8px;
    padding:8px;
    border:none;
    border-radius:6px;
    font-weight:600;
    cursor:pointer;
}

.update-btn {
    background:var(--success);
    color:white;
}

.recommend-btn {
    background:var(--primary);
    color:white;
}

.update-btn:hover,
.recommend-btn:hover {
    opacity:0.9;
}

@media (max-width: 1000px) {
    table {
        font-size:13px;
    }
}
</style>
</head>

<body>

<div class="dashboard">

    <div class="header">
        <h2>🧠 Analyst Dashboard</h2>
        <p>Welcome, <?php echo $_SESSION['analyst']['name']; ?></p>
    </div>

    <table>
        <tr>
            <th>Date</th>
            <th>User</th>
            <th>Mood</th>
            <th>Sleep</th>
            <th>Notes</th>
            <th>Stress Level</th>
            <th>Recommendation</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['log_date'] ?></td>
            <td><?= $row['first_name'].' '.$row['last_name'] ?></td>
            <td><?= $row['mood'] ?></td>
            <td><?= $row['sleep_hours'] ?></td>
            <td><?= $row['note'] ?></td>

            <!-- Stress update -->
            <td>
                <form method="POST">
                    <input type="hidden" name="log_id" value="<?= $row['log_id'] ?>">
                    <select name="stress_level">
                        <option value="">-- Select --</option>
                        <option value="Low" <?= $row['stress_level']=="Low"?"selected":"" ?>>Low</option>
                        <option value="Moderate" <?= $row['stress_level']=="Moderate"?"selected":"" ?>>Moderate</option>
                        <option value="High" <?= $row['stress_level']=="High"?"selected":"" ?>>High</option>
                    </select>
            </td>

            <!-- Recommendation input -->
            <td>
                <textarea name="notes" rows="2" placeholder="Enter recommendation..." required></textarea>
                <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
            </td>

            <!-- Actions -->
            <td class="actions">
                <button class="update-btn" type="submit" name="update_stress">
                    Update Stress
                </button>
                <button class="recommend-btn" type="submit" name="add_recommendation">
                    Add Recommendation
                </button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>

    </table>

</div>

</body>
</html>
