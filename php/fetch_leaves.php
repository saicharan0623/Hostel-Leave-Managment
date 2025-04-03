<?php
// Database connection
require 'database_config.php';  // Make sure to have your database connection 

if (isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];

    // Query to get leave dates for the student
    $query = "SELECT from_date, to_date FROM leave_applications WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $leave_dates = [];
    
    while ($row = $result->fetch_assoc()) {
        // Store the range of leave dates
        $startDate = new DateTime($row['from_date']);
        $endDate = new DateTime($row['to_date']);
        
        while ($startDate <= $endDate) {
            $leave_dates[] = $startDate->format('Y-m-d');
            $startDate->modify('+1 day');
        }
    }

    // Return the leave dates as JSON
    echo json_encode($leave_dates);
}
?>
