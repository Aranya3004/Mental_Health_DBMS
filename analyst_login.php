<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $analyst_id = $_POST['analyst_id'];
    $contact = $_POST['contact_info'];

    $stmt = $conn->prepare(
        "SELECT * FROM analyst WHERE analyst_id = ? AND contact_info = ?"
    );
    $stmt->bind_param("is", $analyst_id, $contact);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $_SESSION['analyst'] = $res->fetch_assoc();
        header("Location: analyst_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid credentials');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Analyst Login</title>

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
    <h2>🧠 Analyst Login</h2>

    <form method="POST">
        <input type="number" name="analyst_id" placeholder="Analyst ID" required>
        <input type="text" name="contact_info" placeholder="Contact Info" required>
        <button type="submit">Login</button>
    </form>

    <div class="footer-text">
        Mental Health Monitoring System
    </div>
</div>

</body>
</html>
