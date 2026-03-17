<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $contact = $_POST['contact_info'];

    $stmt = $conn->prepare(
        "INSERT INTO analyst (name, role, contact_info) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("sss", $name, $role, $contact);
    $stmt->execute();

    echo "<script>
            alert('Analyst registered successfully');
            window.location.href='analyst_login.php';
          </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Analyst Registration</title>

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

.register-card {
    width:420px;
    background:white;
    border-radius:14px;
    box-shadow:0 25px 50px rgba(0,0,0,0.15);
    padding:30px;
}

.register-card h2 {
    margin:0 0 20px;
    text-align:center;
    color:var(--dark);
}

.register-card input {
    width:100%;
    padding:12px;
    margin-bottom:15px;
    border-radius:8px;
    border:1px solid var(--border);
    font-size:15px;
    outline:none;
}

.register-card input:focus {
    border-color:var(--primary);
}

.register-card button {
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

.register-card button:hover {
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

<div class="register-card">
    <h2>🧠 Analyst Registration</h2>

    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="text" name="role" placeholder="Role (e.g. Clinical Analyst)" required>
        <input type="text" name="contact_info" placeholder="Contact Info" required>
        <button type="submit">Register</button>
    </form>

    <div class="footer-text">
        Already registered? <a href="analyst_login.php">Login</a>
    </div>
</div>

</body>
</html>
