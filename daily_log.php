<?php
session_start();
require 'db.php';

/* Block access if not logged in */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userSession = $_SESSION['user'];
$user_id = $userSession['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mood = $_POST['mood'];
    $sleep_hours = $_POST['sleep_hours'];
    $note = $_POST['note'];

    $stmt = $conn->prepare(
        "INSERT INTO daily_logs 
         (log_date, mood, sleep_hours, note, user_id)
         VALUES (CURDATE(), ?, ?, ?, ?)"
    );

    $stmt->bind_param("sisi", $mood, $sleep_hours, $note, $user_id);
    $stmt->execute();

    $success = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Daily Log</title>

<style>
:root {
    --primary:#2563eb;
    --bg:#f1f5f9;
    --card:#ffffff;
    --text:#1e293b;
    --muted:#64748b;
    --border:#e5e7eb;
    --success:#16a34a;
}

* { box-sizing:border-box; }

body {
    margin:0;
    font-family:"Segoe UI", Tahoma, sans-serif;
    background:linear-gradient(135deg,#eef2ff,#f8fafc);
    min-height:100vh;
}

/* BACK BUTTON */
.top-bar {
    max-width:500px;
    margin:30px auto 0;
    padding:0 15px;
}

.back-btn {
    display:inline-flex;
    align-items:center;
    gap:8px;
    text-decoration:none;
    color:white;
    background:var(--primary);
    padding:10px 18px;
    border-radius:8px;
    font-weight:600;
}

.back-btn:hover {
    opacity:.9;
}

/* CARD */
.card {
    background:var(--card);
    max-width:500px;
    margin:20px auto;
    padding:30px;
    border-radius:18px;
    box-shadow:0 25px 50px rgba(0,0,0,.15);
}

.card h2 {
    margin-top:0;
    margin-bottom:20px;
    text-align:center;
}

/* SUCCESS */
.success {
    background:#dcfce7;
    color:var(--success);
    padding:12px;
    border-radius:10px;
    font-weight:600;
    text-align:center;
    margin-bottom:15px;
}

/* FORM */
label {
    display:block;
    margin-top:15px;
    font-weight:600;
    color:var(--muted);
}

input, select, textarea {
    width:100%;
    margin-top:6px;
    padding:12px;
    border-radius:10px;
    border:1px solid var(--border);
    font-size:14px;
    outline:none;
}

input:focus, select:focus, textarea:focus {
    border-color:var(--primary);
}

textarea {
    resize:vertical;
    min-height:90px;
}

/* BUTTON */
button {
    margin-top:22px;
    width:100%;
    padding:14px;
    background:var(--primary);
    border:none;
    border-radius:12px;
    color:white;
    font-size:16px;
    font-weight:700;
    cursor:pointer;
}

button:hover {
    opacity:.9;
}

/* FOOTER */
.footer {
    text-align:center;
    margin-top:15px;
    font-size:13px;
    color:var(--muted);
}
</style>
</head>

<body>

<div class="top-bar">
    <a href="UsersDashboard.php" class="back-btn">← Back to Dashboard</a>
</div>

<div class="card">
    <h2>📝 Daily Mood Log</h2>

    <?php if (!empty($success)): ?>
        <div class="success">Daily log saved successfully!</div>
    <?php endif; ?>

    <form method="post">
        <label>Mood</label>
        <select name="mood" required>
            <option value="">-- Select Mood --</option>
            <option value="Happy">Happy</option>
            <option value="Neutral">Neutral</option>
            <option value="Sad">Sad</option>
            <option value="Anxious">Anxious</option>
            <option value="Angry">Angry</option>
        </select>

        <label>Sleep Hours</label>
        <input type="number" name="sleep_hours" min="0" max="24" required>

        <label>Notes (optional)</label>
        <textarea name="note" placeholder="How was your day?"></textarea>

        <button type="submit">Save Log</button>
    </form>

    <div class="footer">
        Mental Health Support System
    </div>
</div>

</body>
</html>
