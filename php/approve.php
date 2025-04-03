<?php
session_start();
require '../vendor/autoload.php';

// Check if the form was submitted via POST
if (isset($_POST['id'])) {
    // Extract parameters
    $id = $_POST['id'];
    $created_at = $_POST['created_at'];

    try {
        include 'database_config.php';

        // Prepare the update query to approve the leave application
        $updateQuery = "UPDATE leave_applications 
                        SET status = 'APPROVED' 
                        WHERE id = :id AND created_at = :created_at";

        // Execute the query
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->execute();

        // Redirect to the admin panel after successful update
        header("Location: admin_panel.php");
        exit();

    } catch (Exception $e) {
        // Display the error on the screen for debugging
        echo "Error in approve.php: " . $e->getMessage();
        exit();
    }
} else {
    // If no ID is provided, display an error message
    echo "Error: No ID parameter provided.";
    exit();
}
?>
