<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name   = $_POST['first_name'];
    $last_name    = $_POST['last_name'];
    $contact_info = $_POST['contact_info'];
    $age          = $_POST['age'];
    $preferences  = $_POST['preferences'];
    $area         = $_POST['area'];
    $city         = $_POST['city'];
    $country      = $_POST['country'];

    $stmt = $conn->prepare(
        "INSERT INTO users 
        (first_name, last_name, contact_info, age, preferences, area, city, country)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        'sssissss',
        $first_name,
        $last_name,
        $contact_info,
        $age,
        $preferences,
        $area,
        $city,
        $country
    );

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
<title>User Registration</title>

<style>
* {
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body {
    background:#f4f7fb;
    padding:40px;
}

/* CARD */
.card {
    background:white;
    max-width:420px;
    margin:auto;
    padding:25px;
    border-radius:12px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
}

h2 {
    text-align:center;
    margin-bottom:20px;
}

/* FORM */
input, textarea {
    width:100%;
    padding:10px;
    margin-bottom:12px;
    border-radius:6px;
    border:1px solid #d1d5db;
    font-size:14px;
}

textarea {
    resize:none;
}

/* BUTTON */
button {
    width:100%;
    padding:12px;
    background:#2563eb;
    color:white;
    border:none;
    border-radius:8px;
    font-size:15px;
    cursor:pointer;
}

button:hover {
    background:#1e40af;
}

/* MESSAGES */
.success {
    background:#dcfce7;
    color:#166534;
    padding:10px;
    border-radius:6px;
    margin-bottom:15px;
    text-align:center;
}

.error {
    background:#fee2e2;
    color:#991b1b;
    padding:10px;
    border-radius:6px;
    margin-bottom:15px;
    text-align:center;
}

.login-link {
    text-align:center;
    margin-top:15px;
}

.login-link a {
    color:#2563eb;
    text-decoration:none;
    font-weight:bold;
}
</style>
</head>

<body>

<div class="card">
<h2>Create Account</h2>

<?php if (!empty($success)): ?>
    <div class="success">
        Registration successful!  
        <br><a href="login.php">Login here</a>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="error">
        Error: <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<form method="POST">
    <input type="text" name="first_name" placeholder="First Name" required>
    <input type="text" name="last_name" placeholder="Last Name" required>
    <input type="text" name="contact_info" placeholder="Contact Info" required>
    <input type="number" name="age" placeholder="Age" required>

    <textarea name="preferences" placeholder="Preferences (optional)"></textarea>

    <input type="text" name="area" placeholder="Area">
    <input type="text" name="city" placeholder="City">
    <input type="text" name="country" placeholder="Country">

    <button type="submit">Register</button>
</form>

<div class="login-link">
    Already have an account? <a href="login.php">Login</a>
</div>
</div>

</body>
</html>
