<!-- db.php -->
<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'mentalhealth';


$conn = new mysqli($host, $user, $pass, $db);


if ($conn->connect_error) {
die('Database connection failed: ' . $conn->connect_error);
}
?>