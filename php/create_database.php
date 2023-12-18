<?php
$servername = "localhost";
$username = "root";
$password = "your_password";
$dbname = "college_db";

try {
  $conn = new PDO("mysql:host=$servername", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  // Create the database if it does not exist
  $conn->exec("CREATE DATABASE IF NOT EXISTS $dbname");
  
  // Switch to the 'college_db' database
  $conn->exec("USE $dbname");
  
  // Create the 'users' table if it does not exist
  $conn->exec("CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255),
    role ENUM('student', 'admin') NOT NULL
  )");
  
  // Create the 'leave_applications' table if it does not exist
  $conn->exec("CREATE TABLE IF NOT EXISTS leave_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    school VARCHAR(50) NOT NULL,
    from_date DATE NOT NULL,
    to_date DATE NOT NULL,
    reason TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
  )");
  
  echo "Database and tables created successfully";
} catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}
?>
