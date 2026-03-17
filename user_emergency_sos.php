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

$success = false;

/* Handle SOS submission */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $severity = $_POST['severity_level'] ?? '';
    $message  = trim($_POST['message'] ?? '');

    if (!empty($severity)) {
        $stmt = $conn->prepare(
            "INSERT INTO crisis_alert (user_id, severity_level, message)
             VALUES (?, ?, ?)"
        );
        $stmt->bind_param("iss", $user_id, $severity, $message);
        $stmt->execute();

        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Emergency SOS</title>

<style>
:root {
    --danger:#dc3545;
    --danger-dark:#b02a37;
    --primary:#2563eb;
    --bg:#f4f7fb;
    --card:#ffffff;
    --text:#1f2937;
}

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

.card {
    background:var(--card);
    padding:30px;
    border-radius:14px;
    max-width:500px;
    margin:auto;
    box-shadow:0 12px 30px rgba(0,0,0,.1);
}

h2 {
    margin-top:0;
    color:var(--danger);
    text-align:center;
}

label {
    font-weight:bold;
    display:block;
    margin-top:15px;
}

select, textarea {
    width:100%;
    padding:10px;
    margin-top:6px;
    border-radius:6px;
    border:1px solid #d1d5db;
    font-size:14px;
}

textarea {
    min-height:100px;
    resize:vertical;
}

button {
    background:var(--danger);
    color:white;
    padding:14px;
    width:100%;
    border:none;
    border-radius:8px;
    font-size:16px;
    cursor:pointer;
    margin-top:20px;
    font-weight:bold;
}

button:hover {
    background:var(--danger-dark);
}

.success {
    background:#dcfce7;
    color:#166534;
    padding:12px;
    border-radius:6px;
    margin-bottom:15px;
    text-align:center;
    font-weight:bold;
}

.warning {
    background:#fef3c7;
    color:#92400e;
    padding:10px;
    border-radius:6px;
    margin-bottom:15px;
    font-size:14px;
}
</style>
</head>

<body>

<a href="UsersDashboard.php" class="back-btn">← Back to Dashboard</a>

<div class="card">

<h2>🚨 Emergency SOS</h2>

<div class="warning">
If you are in immediate danger, please contact local emergency services as well.
</div>

<?php if ($success): ?>
    <div class="success">Your emergency alert has been sent successfully.</div>
<?php endif; ?>

<form method="post">

    <label>Severity Level</label>
    <select name="severity_level" required>
        <option value="">-- Select Severity --</option>
        <option value="Low">Low</option>
        <option value="Medium">Medium</option>
        <option value="High">High</option>
        <option value="Critical">Critical</option>
    </select>

    <label>Message (optional)</label>
    <textarea name="message" placeholder="Describe your situation (optional)"></textarea>

    <button type="submit">SEND SOS ALERT</button>
</form>

</div>

</body>
</html>
