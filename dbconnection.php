<?php

session_start();

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "outpass";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

$pdo = new PDO('mysql:host=localhost;dbname=outpass', 'root', '');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>