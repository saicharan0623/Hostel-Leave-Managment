<?php
include('database_config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $new_state = $_POST['new_state'];

    // Update the access field for the student
    try {
        $stmt = $pdo->prepare("UPDATE students SET access = :access WHERE student_id = :student_id");
        $stmt->bindParam(':access', $new_state, PDO::PARAM_INT);
        $stmt->bindParam(':student_id', $student_id, PDO::PARAM_STR);
        $stmt->execute();
        echo "Access updated successfully!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
