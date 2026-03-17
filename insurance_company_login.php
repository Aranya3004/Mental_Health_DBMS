<?php
require 'insurance_db.php';
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $insurance_company_id = $_POST['insurance_company_id'];
    $contact_info = $_POST['contact_info'];

    $stmt = $conn->prepare(
        "SELECT * FROM insurance_company
         WHERE insurance_company_id = ? AND contact_info = ?"
    );
    $stmt->bind_param("is", $insurance_company_id, $contact_info);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $_SESSION['insurance_company_id'] = $row['insurance_company_id'];
        header("Location: InsuranceCompanyDashboard.php");
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
<title>Insurance Company Login</title>

<style>
:root {
    --primary:#2563eb;
    --bg:#f1f5f9;
    --card:#ffffff;
    --text:#1e293b;
    --muted:#64748b;
    --border:#e5e7eb;
    --error:#991b1b;
}

* { box-sizing:border-box; }

body {
    margin:0;
    height:100vh;
    font-family:"Segoe UI", Tahoma, sans-serif;
    background: linear-gradient(135deg,#eef2ff,#f8fafc);
    display:flex;
    align-items:center;
    justify-content:center;
}

/* CARD */
.login-card {
    width:380px;
    background:var(--card);
    padding:30px;
    border-radius:16px;
    box-shadow:0 25px 50px rgba(0,0,0,.15);
}

.login-card h2 {
    text-align:center;
    margin-bottom:20px;
}

/* ERROR */
.error {
    background:#fee2e2;
    color:var(--error);
    padding:10px;
    border-radius:8px;
    text-align:center;
    font-weight:600;
    margin-bottom:15px;
}

/* FORM */
input {
    width:100%;
    padding:12px;
    margin-bottom:15px;
    border-radius:8px;
    border:1px solid var(--border);
    font-size:15px;
    outline:none;
}

input:focus {
    border-color:var(--primary);
}

/* BUTTON */
button {
    width:100%;
    padding:14px;
    background:var(--primary);
    border:none;
    border-radius:10px;
    color:white;
    font-size:16px;
    font-weight:700;
    cursor:pointer;
}

button:hover {
    opacity:.9;
}

.footer {
    margin-top:15px;
    text-align:center;
    font-size:13px;
    color:var(--muted);
}
</style>
</head>

<body>

<div class="login-card">
    <h2>🏥 Insurance Company Login</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="number" name="insurance_company_id" placeholder="Company ID" required>
        <input type="text" name="contact_info" placeholder="Contact Info" required>
        <button type="submit">Login</button>
    </form>

    <div class="footer">
        Mental Health Support System
    </div>
</div>

</body>
</html>
