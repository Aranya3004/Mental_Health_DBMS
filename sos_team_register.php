<?php
require 'sos_db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $team_name   = trim($_POST['team_name']);
    $location    = trim($_POST['location']);
    $contact_info = trim($_POST['contact_info']);

    if ($team_name && $location && $contact_info) {

        $stmt = $conn->prepare(
            "INSERT INTO emergency_sos_team (team_name, location, contact_info)
             VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $team_name, $location, $contact_info);

        if ($stmt->execute()) {
            $success = "SOS Team Registered Successfully!";
        } else {
            $error = "Registration failed. Please try again.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>SOS Team Registration</title>
<style>
body { font-family:Arial; background:#f4f7fb; padding:30px; }
form {
    background:white;
    padding:25px;
    max-width:400px;
    margin:auto;
    border-radius:8px;
}
input {
    width:100%;
    padding:10px;
    margin-bottom:12px;
}
button {
    width:100%;
    padding:12px;
    background:#dc3545;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
}
button:hover { background:#b02a37; }
.success { color:green; text-align:center; margin-bottom:10px; }
.error { color:red; text-align:center; margin-bottom:10px; }
</style>
</head>

<body>

<h2 style="text-align:center;">🚑 Emergency SOS Team Registration</h2>

<form method="post">

<?php if ($success): ?>
    <p class="success">
        <?= htmlspecialchars($success) ?><br>
        <a href="sos_team_login.php">Login</a>
    </p>
<?php endif; ?>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<input type="text" name="team_name" placeholder="Team Name" required>
<input type="text" name="location" placeholder="Location" required>
<input type="text" name="contact_info" placeholder="Contact Info" required>

<button type="submit">Register</button>

</form>

</body>
</html>
