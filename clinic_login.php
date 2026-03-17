<?php
require 'clinic_db.php';
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clinic_id = $_POST['clinic_id'];
    $contact_info = $_POST['contact_info'];

    $stmt = $conn->prepare(
        "SELECT * FROM clinic WHERE clinic_id = ? AND contact_info = ?"
    );
    $stmt->bind_param('is', $clinic_id, $contact_info);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $_SESSION['clinic_id'] = $row['clinic_id'];      // required for dashboard
        $_SESSION['clinic_name'] = $row['clinic_name'];  // store clinic name
        header("Location: ClinicDashboard.php");
        exit();
    } else {
        $error = "Invalid credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Clinic Login</title>

<style>
:root {
    --primary:#2563eb;
    --dark:#1e293b;
    --border:#e5e7eb;
}

body {
    margin:0;
    height:100vh;
    font-family:"Segoe UI", Tahoma, sans-serif;
    background: linear-gradient(135deg,#eef2ff,#f8fafc);
    display:flex;
    align-items:center;
    justify-content:center;
}

.login-card {
    width:380px;
    background:white;
    border-radius:14px;
    box-shadow:0 25px 50px rgba(0,0,0,0.15);
    padding:30px;
}

.login-card h2 {
    margin:0 0 20px;
    text-align:center;
    color:var(--dark);
}

.error {
    background:#fee2e2;
    color:#991b1b;
    padding:10px;
    border-radius:8px;
    text-align:center;
    margin-bottom:15px;
    font-weight:600;
}

.login-card input {
    width:100%;
    padding:12px;
    margin-bottom:15px;
    border-radius:8px;
    border:1px solid var(--border);
    font-size:15px;
    outline:none;
}

.login-card input:focus {
    border-color:var(--primary);
}

.login-card button {
    width:100%;
    padding:12px;
    background:var(--primary);
    border:none;
    border-radius:8px;
    color:white;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
}

.login-card button:hover {
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

<div class="login-card">
    <h2>🏥 Clinic Login</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="number" name="clinic_id" placeholder="Clinic ID" required>
        <input type="text" name="contact_info" placeholder="Contact Info" required>
        <button type="submit">Login</button>
    </form>

    <div class="footer-text">
        Healthcare Management System
    </div>
</div>

</body>
</html>
