<?php
// Database connection parameters
$servername = "localhost";
$username = "u8gr0sjr9p4p4";
$password = "9yxuqyo3mt85";
$dbname = "dbhqoo91k063hk";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
