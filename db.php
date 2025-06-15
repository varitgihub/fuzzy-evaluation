<?php
$servername = "localhost";
$username = "root"; // change if your database uses a different username
$password = ""; // change if your database uses a different password
$dbname = "student_performance";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>