<?php
// Connect to the database (replace with your own database credentials)
$servername = "localhost";
$username = "saicharan";
$password = "6304856382";
$dbname = "Leave23";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert the student data into the users table
$student_name = "John Doe";
$student_id = "123456";
$email = "john.doe@example.com";
$department = "Computer Science";
$batch = "2023";
$phone = "+1234567890";
$role = "student";

$sql = "INSERT INTO users (email, password, name, role) VALUES ('$email', 'your_password_hash', '$student_name', '$role')";

if ($conn->query($sql) === TRUE) {
    $student_id = $conn->insert_id;

    // Insert more student details into the student_details table
    $sql = "INSERT INTO student_details (student_id, student_name, department, batch, phone) VALUES ('$student_id', '$student_name', '$department', '$batch', '$phone')";

    if ($conn->query($sql) === TRUE) {
        echo "Student data inserted successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
