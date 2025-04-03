<?php
session_start();
include 'database_config.php';

// Ensure admin is logged in
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login_page.php");
    exit();
}

// Retrieve data from URL parameters
if (isset($_GET['id']) && isset($_GET['student_id']) && isset($_GET['rejection_reason'])) {
    $id = $_GET['id'];
    $studentId = $_GET['student_id'];
    $rejectionReason = $_GET['rejection_reason'];

    // Retrieve the admin ID from session
    $adminId = $_SESSION['admin_id'];

    try {
        // Update the request status and add rejection reason
        $updateQuery = "UPDATE day_outing_requests SET status = 'REJECTED', rejection_reason = :rejection_reason, processed_by = :admin_id WHERE id = :id AND student_id = :student_id";
        
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':rejection_reason', $rejectionReason, PDO::PARAM_STR);
        $stmt->bindParam(':admin_id', $adminId, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['success_message'] = "Day outing request rejected successfully!";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error rejecting the request: " . $e->getMessage();
    }
} else {
    $_SESSION['error_message'] = "Invalid request parameters!";
}

header("Location: dayout_admin_dashboard.php");  // Redirect back to the page
exit();
?>
