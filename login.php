<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "sarthak@123?";
$dbname = "criminal";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user input
$login_id = $_POST['id'];
$user_password = $_POST['password'];

// Prepare SQL statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM Login WHERE id = ? AND password = ?");
$stmt->bind_param("ss", $login_id, $user_password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    // Successful login
    $_SESSION['user'] = $login_id;
    header("Location:menu.html");
    exit();
} else {
    // Failed login
    header("Location:index.php?error=Wrong credentials");
    exit();
}

?>