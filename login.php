<?php
session_start();

$valid_username = "kavya"; // Replace with actual admin username
$valid_password = "1234"; // Replace with actual admin password

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate username and password
    if ($username === $valid_username && $password === $valid_password) {
        // Authentication successful
        $_SESSION['admin_logged_in'] = true;
        echo json_encode(array('success' => true));
    } else {
        // Authentication failed
        echo json_encode(array('success' => false, 'message' => 'Invalid username or password'));
    }
} else {
    // Redirect to login page if accessed directly
    header("Location: index.php");
    exit();
}
?>