<!-- BookSessionHandler.php -->
<?php
session_start();
require 'db.php';


if (!isset($_SESSION['user'])) {
header('Location: login.php');
exit();
}


$user_id = $_SESSION['user']['user_id'];


$counsellor_id = $_POST['counsellor_id'] ?? 0;
$session_type = $_POST['session_type'] ?? '';
$session_time = $_POST['session_time'] ?? '';
$feedback = $_POST['feedback'] ?? '';


// Ensure counsellor exists
$stmt_check = $conn->prepare("SELECT counsellor_id FROM counsellors WHERE counsellor_id=?");
$stmt_check->bind_param('i', $counsellor_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
if ($result_check->num_rows === 0) {
die('Invalid counsellor selected');
}


// Insert session request with progress='Pending'
$stmt = $conn->prepare("INSERT INTO sessions (session_time, session_type, feedback, counsellor_id, user_id, progress) VALUES (?, ?, ?, ?, ?, 'Pending')");
$stmt->bind_param('sssii', $session_time, $session_type, $feedback, $counsellor_id, $user_id);


if ($stmt->execute()) {
echo "<script>alert('Session request sent! Wait for clinic approval.'); window.location='MySessions.php';</script>";
} else {
echo 'Error: ' . $stmt->error;
}
?>