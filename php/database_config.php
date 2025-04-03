<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "college_db";
$mysqli = new mysqli($hostname, $username, $password, $database);

// Check the connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
//------------------
try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    // Set PDO to throw exceptions for errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection errors
    die("Connection failed: " . $e->getMessage());
}