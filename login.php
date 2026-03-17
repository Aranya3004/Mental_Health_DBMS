<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = $_POST['user_id'];
    $contact_info = $_POST['contact_info'];

    // Prepare query
    $stmt = $conn->prepare(
        "SELECT * FROM users 
         WHERE user_id = ? AND contact_info = ?"
    );
    $stmt->bind_param('is', $user_id, $contact_info);
    $stmt->execute();
    $result = $stmt->get_result();

    // User found
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Store user session correctly
        $_SESSION['user'] = [
            'user_id'     => $row['user_id'],
            'first_name'  => $row['first_name'],
            'last_name'   => $row['last_name'],
            'contact_info'=> $row['contact_info']
        ];

        header("Location: UsersDashboard.php");
        exit();

    } else {
        $error = "Invalid User ID or Contact Info";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Login</title>

<style>
body {
    font-family: Arial;
    background: #f4f7fb;
    padding: 40px;
}

.card {
    background: white;
    max-width: 350px;
    margin: auto;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

input {
    width: 100%;
    padding: 10px;
    margin-bottom: 12px;
    border-radius: 6px;
    border: 1px solid #d1d5db;
}

button {
    width: 100%;
    padding: 12px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    cursor: pointer;
}

button:hover {
    background: #1e7e34;
}

.error {
    background: #fee2e2;
    color: #991b1b;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
    text-align: center;
}

.register-link {
    text-align: center;
    margin-top: 15px;
}
</style>
</head>

<body>

<div class="card">
<h2>User Login</h2>

<?php if (!empty($error)): ?>
    <div class="error"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <input type="number" name="user_id" placeholder="User ID" required>
    <input type="text" name="contact_info" placeholder="Contact Info" required>
    <button type="submit">Login</button>
</form>

<div class="register-link">
    Don’t have an account? <a href="register.php">Register</a>
</div>
</div>

</body>
</html>
