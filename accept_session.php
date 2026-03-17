<?php
session_start();
require 'db.php'; // make sure this connects to the correct database

if (!isset($_SESSION['clinic_id'])) {
    header("Location: clinic_login.php");
    exit();
}

$session_id = $_GET['session_id'] ?? 0;

if ($session_id) {
    $stmt = $conn->prepare("UPDATE sessions SET status='accepted' WHERE session_id=?");
    $stmt->bind_param("i", $session_id);

    if (!$stmt->execute()) {
        die("Database update failed: " . $stmt->error);
    }
}

header("Location: ClinicDashboard.php");
exit();
?>
