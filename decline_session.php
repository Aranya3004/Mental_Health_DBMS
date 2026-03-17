<?php
session_start();
require 'clinic_db.php';

if (!isset($_SESSION['clinic_id'])) {
    header("Location: clinic_login.php");
    exit();
}

$session_id = isset($_GET['session_id']) ? (int)$_GET['session_id'] : 0;

if ($session_id > 0) {
    $stmt = $conn->prepare("UPDATE sessions SET status='declined' WHERE session_id=?");
    $stmt->bind_param('i', $session_id);
    if (!$stmt->execute()) {
        die("Error: " . $stmt->error);
    }
}

header("Location: ClinicDashboard.php");
exit();
?>
