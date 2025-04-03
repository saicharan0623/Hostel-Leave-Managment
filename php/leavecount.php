<?php
include 'database_config.php';
$sql = "SELECT school, COUNT(*) AS leave_count FROM schools GROUP BY school";
$result = mysqli_query($mysqli, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($mysqli));
}

$data = array();

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// Close the database mysqliection
mysqli_close($mysqli);

// Return data as JSON response
header('Content-Type: application/json');
echo json_encode($data);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Leave Counts by School</title>
    <!-- Add your CSS styles here -->
</head>
<body>
    <h1>Leave Counts by School</h1>

    <?php if (empty($data)): ?>
        <p>No leave counts available.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>School</th>
                    <th>Leave Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $leaveCount): ?>
                    <tr>
                        <td><?= $leaveCount['school'] ?></td>
                        <td><?= $leaveCount['leave_count'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <?php include 'footer.php'; ?>
</body>
</html>
