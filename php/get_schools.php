<?php
include 'database_config.php';
$query = "SELECT DISTINCT department FROM students";
$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    echo '<option value="" disabled selected>Select a school</option>';
    echo '<option value="All">All</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row['department']) . '">' . htmlspecialchars($row['department']) . '</option>';
    }
} else {
    echo '<option value="" disabled>No schools found</option>';
}

$mysqli->close();
?>
