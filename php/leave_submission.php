<?php
session_start();
include 'database_config.php';

// Check if the session variable 'student_id' is set to ensure the user is logged in
if (!isset($_SESSION['student_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}

// Retrieve the student_id from the session
$student_id_from_session = $_SESSION['student_id']; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reason'])) {
    // Collect form data
    $school = $_POST["school"] ?? "";
    $year = $_POST["year"] ?? "";
    $student_id = $_POST["student_id"] ?? "";  // Assuming the student_id is included in the form submission
    $name = $_POST["student_name"] ?? "";
    $roomno = $_POST["roomno"] ?? "";
    $mobile = $_POST["mobile"] ?? "";
    $fromDate = $_POST["from_date"] ?? "";
    $toDate = $_POST["to_date"] ?? "";
    $reason = $_POST["reason"] ?? "";
    $attendance = $_POST["attendance"] ?? "";
    $intime = $_POST["intime"] ?? "";
    $outime = $_POST["outime"] ?? "";
    $leave_type = $_POST["leave_type"] ?? ""; // New field for leave type
    $status = "PENDING"; // Default status

    // Check if the session student_id matches the submitted student_id
    if ($student_id_from_session != $student_id) {
        // If the session student_id does not match the submitted student_id, prevent form submission
        echo "Error: You cannot submit a leave request for another student.";
        exit();
    }

    try {
        // Prepare the SQL statement to insert leave application without including 'updated_at', 'rejection_reason', and 'processed_by'
        $stmt = $pdo->prepare("INSERT INTO leave_applications (student_id, from_date, to_date, reason, status, attendance, intime, outime, leave_type, created_at)
                               VALUES (:student_id, :from_date, :to_date, :reason, :status, :attendance, :intime, :outime, :leave_type, NOW())");

        // Bind parameters
        $stmt->bindParam(":student_id", $student_id);
        $stmt->bindParam(":from_date", $fromDate);
        $stmt->bindParam(":to_date", $toDate);
        $stmt->bindParam(":reason", $reason);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":attendance", $attendance);
        $stmt->bindParam(":intime", $intime);
        $stmt->bindParam(":outime", $outime);
        $stmt->bindParam(":leave_type", $leave_type);

        // Execute the query
        $stmt->execute();
        
        // Redirect to a success page after the application is submitted
        header("Location: leave_success.php");
        exit();

    } catch (PDOException $e) {
        // Display any error messages
        echo "Error: " . $e->getMessage();
    }
}
?>
