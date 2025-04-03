<?php
session_start();
include 'database_config.php';

// Ensure admin is logged in
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login_page.php");
    exit();
}

if (isset($_POST['id']) && isset($_POST['student_id'])) {
    $id = $_POST['id'];
    $studentId = $_POST['student_id'];
    $adminId = $_SESSION['admin_id'];  // Retrieve the admin ID from session

    try {
        // Update query to include 'processed_by' (admin ID)
        $updateQuery = "UPDATE day_outing_requests 
                        SET status = 'APPROVED', processed_by = :admin_id 
                        WHERE id = :id AND student_id = :student_id";
        
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
        $stmt->bindParam(':admin_id', $adminId, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['success_message'] = "Day outing request approved successfully!";
        
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error approving the request: " . $e->getMessage();
    }
} else {
    $_SESSION['error_message'] = "Invalid request!";
}
header("Location: dayout_admin_dashboard.php");  // Redirect back to the page
exit();
?>
