<?php
session_start();

if (isset($_GET['id'])) {
    // Extract parameters
    $id = $_GET['id'];
    $reject_reason = $_GET['reject_reason'];  // Reason for rejection

    require 'database_config.php';

    try {
        // Update the database to reject the leave application with the rejection reason
        $updateQuery = "UPDATE leave_applications 
                        SET status = 'REJECTED', 
                            rejection_reason = :reject_reason
                        WHERE id = :id";

        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':reject_reason', $reject_reason);  // Insert the rejection reason
        $stmt->execute();

        // Redirect to the admin panel with success
        header("Location: admin_panel.php");
        exit();

    } catch (PDOException $e) {
        // If an error occurs, log it and redirect with an error message
        error_log("Error in reject.php: " . $e->getMessage());
        header("Location: admin_panel.php?error=database");
        exit();
    }
} else {
    // If no ID is provided, redirect with error
    header("Location: admin_panel.php?error=noid");
    exit();
}
?>
