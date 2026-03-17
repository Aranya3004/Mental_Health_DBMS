<?php
require 'insurance_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = $_POST['company_name'];
    $contact_info = $_POST['contact_info'];

    $stmt = $conn->prepare(
        "INSERT INTO insurance_company (company_name, contact_info)
         VALUES (?, ?)"
    );
    $stmt->bind_param("ss", $company_name, $contact_info);

    if ($stmt->execute()) {
        $success = true;
    } else {
        $error = $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Insurance Company Registration</title>

<style>
:root {
    --primary:#2563eb;
    --bg:#f1f5f9;
    --card:#ffffff;
    --text:#1e293b;
    --border:#e5e7eb;
}

* { box-sizing:border-box; }

body {
    margin:0;
    font-family:"Segoe UI", Tahoma, sans-serif;
    background:var(--bg);
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.card {
    background:var(--card);
    padding:35px;
    width:420px;
    border-radius:18px;
    box-shadow:0 25px 50px rgba(0,0,0,.12);
}

h2 {
    margin-top:0;
    text-align:center;
    color:var(--text);
}

input {
    width:100%;
    padding:12px;
    margin-top:15px;
    border-radius:10px;
    border:1px solid var(--border);
    font-size:14px;
}

input:focus {
    border-color:var(--primary);
    outline:none;
}

button {
    margin-top:25px;
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:var(--primary);
    color:white;
    font-weight:600;
    cursor:pointer;
}

button:hover {
    opacity:.9;
}

.success {
    background:#dcfce7;
    color:#166534;
    padding:12px;
    border-radius:10px;
    margin-bottom:15px;
    text-align:center;
}

.error {
    background:#fee2e2;
    color:#991b1b;
    padding:12px;
    border-radius:10px;
    margin-bottom:15px;
    text-align:center;
}

.login-link {
    display:block;
    text-align:center;
    margin-top:20px;
    text-decoration:none;
    color:var(--primary);
    font-weight:600;
}
</style>
</head>

<body>

<div class="card">
    <h2>🏢 Insurance Company Registration</h2>

    <?php if (!empty($success)): ?>
        <div class="success">
            Registration successful!  
            <br><a href="insurance_company_login.php">Login here</a>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="company_name" placeholder="Company Name" required>
        <input type="text" name="contact_info" placeholder="Contact Info" required>

        <button type="submit">Register Company</button>
    </form>

    <a class="login-link" href="insurance_company_login.php">
        Already registered? Login
    </a>
</div>

</body>
</html>
