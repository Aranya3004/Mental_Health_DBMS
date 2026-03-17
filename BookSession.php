<?php
session_start();
require "db.php";

// Must be logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$user_id = $user['user_id'];

// Fetch all counsellors for dropdown
$counsellors = $conn->query("SELECT counsellor_id, first_name, last_name, specialization FROM counsellors");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Session</title>

<style>
:root {
    --primary:#2563eb;
    --dark:#1e293b;
    --border:#e5e7eb;
    --bg:#f8fafc;
}

body {
    margin:0;
    min-height:100vh;
    font-family:"Segoe UI", Tahoma, sans-serif;
    background: linear-gradient(135deg,#eef2ff,#f8fafc);
    display:flex;
    align-items:center;
    justify-content:center;
    padding:30px;
}

.booking-card {
    width:450px;
    background:white;
    border-radius:14px;
    box-shadow:0 25px 50px rgba(0,0,0,0.12);
    padding:30px;
}

.booking-card h2 {
    text-align:center;
    margin-bottom:20px;
    color:var(--dark);
}

.booking-card label {
    font-weight:600;
    font-size:14px;
    display:block;
    margin-bottom:6px;
}

.booking-card select,
.booking-card input,
.booking-card textarea {
    width:100%;
    padding:11px;
    margin-bottom:16px;
    border-radius:8px;
    border:1px solid var(--border);
    font-size:14px;
    outline:none;
}

.booking-card select:focus,
.booking-card input:focus,
.booking-card textarea:focus {
    border-color:var(--primary);
}

.booking-card textarea {
    resize:none;
    min-height:90px;
}

.booking-card button {
    width:100%;
    padding:13px;
    background:var(--primary);
    border:none;
    border-radius:8px;
    color:white;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
}

.booking-card button:hover {
    opacity:0.9;
}

.footer-text {
    text-align:center;
    margin-top:15px;
    font-size:13px;
    color:#64748b;
}
</style>
</head>

<body>

<div class="booking-card">
    <h2>📅 Book a Session</h2>

    <form method="POST" action="BookSessionHandler.php">

        <label>Counsellor</label>
        <select name="counsellor_id" required>
            <option value="">Select counsellor</option>
            <?php while ($c = $counsellors->fetch_assoc()): ?>
                <option value="<?= $c['counsellor_id'] ?>">
                    <?= $c['first_name'] . ' ' . $c['last_name'] ?> — <?= $c['specialization'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Session Type</label>
        <select name="session_type" required>
            <option value="General Consultation">General Consultation</option>
            <option value="Therapy">Therapy</option>
            <option value="Medication Follow-up">Medication Follow-up</option>
            <option value="Emergency Check">Emergency Check</option>
        </select>

        <label>Preferred Date & Time</label>
        <input type="datetime-local" name="session_time" required>

        <label>Message / Additional Info</label>
        <textarea name="feedback" placeholder="Describe your concern..."></textarea>

        <button type="submit">Submit Request</button>
    </form>

    <div class="footer-text">
        Mental Health Support System
    </div>
</div>

</body>
</html>
