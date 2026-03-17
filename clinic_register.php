<?php
require 'clinic_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clinic_name = $_POST['clinic_name'];
    $address = $_POST['address'];
    $contact_info = $_POST['contact_info'];
    $operating_hours = $_POST['operating_hours'];

    $stmt = $conn->prepare(
        "INSERT INTO clinic (clinic_name, address, contact_info, operating_hours)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param('ssss', $clinic_name, $address, $contact_info, $operating_hours);

    if ($stmt->execute()) {
        echo "<script>
                alert('Clinic registration successful!');
                window.location.href='clinic_login.php';
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Clinic Registration</title>

<style>
:root {
    --primary:#2563eb;
    --dark:#1e293b;
    --border:#e5e7eb;
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

.register-card {
    width:460px;
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
    padding:13px;
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
    <h2>🏥 Clinic Registration</h2>

    <form method="POST">
        <input type="text" name="clinic_name" placeholder="Clinic Name" required>
        <input type="text" name="address" placeholder="Address" required>
        <input type="text" name="contact_info" placeholder="Contact Info" required>
        <input type="text" name="operating_hours" placeholder="Operating Hours" required>

        <button type="submit">Register Clinic</button>
    </form>

    <div class="footer-text">
        Already registered?
        <a href="clinic_login.php">Login here</a>
    </div>
</div>

</body>
</html>
