<?php
require 'db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO admin (username, password) VALUES (?, ?)"
        );

        if ($stmt) {
            $stmt->bind_param("ss", $username, $hashed);

            if ($stmt->execute()) {
                $success = "Admin registered successfully";
            } else {
                $error = "Username already exists";
            }
        } else {
            $error = "Database error";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Register</title>
<style>
body {
    font-family: Arial;
    background: #f4f7fb;
    margin: 0;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
    background: white;
    width: 400px;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    text-align: center;
}

h2 {
    margin-bottom: 20px;
}

input {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 6px;
    border: 1px solid #ccc;
}

button {
    width: 100%;
    padding: 10px;
    margin-top: 15px;
    background: #2b6ef6;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
}

button:hover {
    background: #1f5edc;
}

.success {
    background: #e8f6ec;
    color: #2d8a4f;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
}

.error {
    background: #fdecea;
    color: #c0392b;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
}

a {
    display: inline-block;
    margin-top: 15px;
    color: #2b6ef6;
    text-decoration: none;
    font-weight: 600;
}
</style>
</head>

<body>

<div class="card">

<h2>🛡 Admin Registration</h2>

<?php if ($error): ?>
<div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
<div class="success">
<?= htmlspecialchars($success) ?><br>
<a href="admin_login.php">Go to Login</a>
</div>
<?php endif; ?>

<form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password (min 6 chars)" required>
    <button type="submit">Register</button>
</form>

</div>

</body>
</html>
