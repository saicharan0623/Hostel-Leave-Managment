<?php
include 'database_config.php'; // Include your database configuration file

$currentDate = date('d_M_Y');
$outDate = $_POST['out_date'];
$school = $_POST['school'];

// Prepare the SQL query to fetch required data
$sql = "SELECT 
            d.id,
            s.department,
            s.student_name,
            s.student_email,
            s.gender,
            s.student_id,
            s.phone,
            s.batch,
            d.outdate,
            d.intime,
            d.outime,
            d.reason,
            d.status,
            d.rejection_reason,
            d.request_date
        FROM day_outing_requests d
        JOIN students s ON d.student_id = s.student_id
        WHERE d.outdate = :outDate";

if ($school !== 'All') {
    $sql .= " AND s.department = :school";
}

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':outDate', $outDate);
if ($school !== 'All') {
    $stmt->bindParam(':school', $school);
}
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if data is available
if (empty($data)) {
    echo "No records found for the selected date and school.";
    exit();
}

// Set HTTP headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="day_outing_students_' . $currentDate . '.csv"');

// Open the output stream
$output = fopen('php://output', 'w');

// Write the column headers
fputcsv($output, ['ID', 'School', 'Student Name', 'Student Email', 'Gender', 'Student ID', 'Mobile', 'Year', 'Outdate', 'Intime', 'Outime', 'Reason', 'Status', 'Rejection Reason', 'Request Date']);

// Write the data rows
foreach ($data as $row) {
    fputcsv($output, $row);
}

// Close the output stream
fclose($output);
exit();
?>
