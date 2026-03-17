<?php
session_start();
require 'sos_db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $team_id = $_POST['team_id'];
    $contact_info = $_POST['contact_info'];

    $stmt = $conn->prepare(
        "SELECT team_id, team_name, location
         FROM emergency_sos_team
         WHERE team_id = ? AND contact_info = ?"
    );
    $stmt->bind_param("is", $team_id, $contact_info);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Store ONLY what you need
        $_SESSION['sos_team'] = [
            'team_id'   => $row['team_id'],
            'team_name' => $row['team_name'],
            'location'  => $row['location']
        ];

        header("Location: sos_team_dashboard.php");
        exit();
    } else {
        $error = "Invalid Team ID or Contact Info";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>SOS Team Login</title>
<style>
body { font-family:Arial; background:#f4f7fb; padding:30px; }
form { background:white; padding:25px; max-width:350px; margin:auto; border-radius:8px; }
input { width:100%; padding:10px; margin-bottom:10px; }
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
.error { color:red; text-align:center; margin-bottom:10px; }
</style>
</head>

<body>

<h2 style="text-align:center;">🚑 Emergency SOS Team Login</h2>

<form method="post">
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <input type="number" name="team_id" placeholder="Team ID" required>
    <input type="text" name="contact_info" placeholder="Contact Info" required>
    <button type="submit">Login</button>
</form>

</body>
</html>
