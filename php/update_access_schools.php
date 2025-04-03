<?php
include('database_config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get department, batch, and new access state from POST data
    $department = $_POST['department'];
    $batch = $_POST['batch'];
    $new_state = $_POST['new_state'];

    // Update the access field for the students in the given department and batch
    try {
        $stmt = $pdo->prepare("UPDATE students SET access = :access WHERE department = :department AND batch = :batch");
        $stmt->bindParam(':access', $new_state, PDO::PARAM_INT);
        $stmt->bindParam(':department', $department, PDO::PARAM_STR);
        $stmt->bindParam(':batch', $batch, PDO::PARAM_STR);
        $stmt->execute();
        echo "Access updated successfully!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
