<?php
session_start(); // This should be at the beginning of the file

require 'database_config.php'; // Ensure this file contains proper DB connection settings

// Check if session email is set
if (!isset($_SESSION['email'])) {
    header("Location: student_login_page.php");
    exit;
}

$email = $_SESSION['email'];
$fromDate = $_POST['from_date'];
$toDate = $_POST['to_date'];

// Get student data from the students table using session email
$stmt = $pdo->prepare("SELECT * FROM students WHERE email = :email");
$stmt->execute(['email' => $email]);
$studentData = $stmt->fetch(PDO::FETCH_ASSOC);

// If student is found, proceed
if ($studentData) {
    $studentId = $studentData['student_id'];

    // Check for overlapping leave applications in the leave_applications table
    $stmt = $pdo->prepare("
        SELECT * FROM leave_applications 
        WHERE student_id = :student_id 
        AND (
            (:from_date BETWEEN from_date AND to_date) OR
            (:to_date BETWEEN from_date AND to_date) OR
            (from_date BETWEEN :from_date AND :to_date) OR
            (to_date BETWEEN :from_date AND :to_date)
        )
    ");
    
    $stmt->execute([
        'student_id' => $studentId,
        'from_date' => $fromDate,
        'to_date' => $toDate
    ]);

    $existingLeave = $stmt->fetch(PDO::FETCH_ASSOC);

    // Prepare response
    $response = [];

    if ($existingLeave) {
        // Correct the condition for rejected/withdrawn status
        if ($existingLeave['status'] === 'REJECTED' || $existingLeave['status'] === 'WITHDRAWN') {
            $response = [
                'overlap' => false, // No overlap, since the previous leave was rejected or withdrawn
                'message' => 'Your previous leave application for these dates was rejected or withdrawn, so you can apply again.'
            ];
        } else {
            // If the leave application has not been rejected or withdrawn, prevent overlapping
            $response = [
                'overlap' => true,
                'message' => 'The selected dates overlap with an existing approved leave application.'
            ];
        }
    } else {
        // No existing leave for the selected dates
        $response = [
            'overlap' => false,
            'message' => 'No overlapping leave found.'
        ];
    }

    echo json_encode($response);
} else {
    // Student not found
    echo json_encode([
        'overlap' => false,
        'message' => 'Student not found.'
    ]);
}
?>
