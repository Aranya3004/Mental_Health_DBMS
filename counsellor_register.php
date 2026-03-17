<?php
require 'counsellor_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $specialization = $_POST['specialization'];
    $contact_info = $_POST['contact_info'];
    $license_number = $_POST['license_number'];
    $schedule = $_POST['schedule'];
    $clinic_id = $_POST['clinic_id'];

    $stmt = $conn->prepare(
        "INSERT INTO counsellors 
        (first_name, last_name, specialization, contact_info, license_number, schedule, clinic_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        'ssssssi',
        $first_name,
        $last_name,
        $specialization,
        $contact_info,
        $license_number,
        $schedule,
        $clinic_id
    );

    if ($stmt->execute()) {
        echo "<p style='color:green;text-align:center;font-weight:bold'>
                Counsellor registration successful!
                <br><a href='counsellor_login.php'>Login</a>
              </p>";
    } else {
        echo "<p style='color:red;text-align:center'>Error: {$stmt->error}</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Counsellor Registration</title>

<style>
:root {
    --primary:#2563eb;
    --bg:#f1f5f9;
    --card:#ffffff;
    --border:#e5e7eb;
    --text:#1e293b;
}

body {
    margin:0;
    min-height:100vh;
    font-family:"Segoe UI", Tahoma, sans-serif;
    background: linear-gradient(135deg,#eef2ff,#f8fafc);
    display:flex;
    align-items:center;
    justify-content:center;
}

.card {
    width:500px;
    background:var(--card);
    padding:30px;
    border-radius:16px;
    box-shadow:0 25px 50px rgba(0,0,0,0.15);
}

.card h2 {
    text-align:center;
    color:var(--text);
    margin-bottom:25px;
}

.form-grid {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:15px;
}

.card input {
    padding:12px;
    border-radius:8px;
    border:1px solid var(--border);
    font-size:14px;
    outline:none;
    width:100%;
}

.card input:focus {
    border-color:var(--primary);
}

.full {
    grid-column:1 / -1;
}

.card button {
    margin-top:20px;
    width:100%;
    padding:14px;
    background:var(--primary);
    border:none;
    border-radius:10px;
    color:white;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
}

.card button:hover {
    opacity:0.9;
}

.footer {
    text-align:center;
    margin-top:15px;
    font-size:13px;
    color:#64748b;
}
</style>
</head>

<body>

<div class="card">
    <h2>🧑‍⚕️ Counsellor Registration</h2>

    <form method="POST">
        <div class="form-grid">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>

            <input type="text" name="specialization" placeholder="Specialization" required>
            <input type="text" name="contact_info" placeholder="Contact Info" required>

            <input type="text" name="license_number" placeholder="License Number" required>
            <input type="text" name="schedule" placeholder="Schedule" required>

            <input class="full" type="number" name="clinic_id" placeholder="Clinic ID" required>
        </div>

        <button type="submit">Register Counsellor</button>
    </form>

    <div class="footer">
        Already registered? <a href="counsellor_login.php">Login here</a>
    </div>
</div>

</body>
</html>
