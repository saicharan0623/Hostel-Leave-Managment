<?php
include 'database_config.php'; // Include the database configuration file

// Load PhpSpreadsheet library
require '../vendor/autoload.php'; // You need to install PhpSpreadsheet using Composer
$currentDate = date('d M Y');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Get date range and school from POST request
$fromDate = $_POST['from_date'] ?? null;
$toDate = $_POST['to_date'] ?? null;
$school = $_POST['school'] ?? null;

// Create a new Spreadsheet
$spreadsheet = new Spreadsheet();

// Add a worksheet
$worksheet = $spreadsheet->getActiveSheet();

// Set headers for the Excel file
$headers = ['Sl.no', 'School', 'Batch', 'Student Name', 'Student Id', 'from_Date', 'To_Date', 'Reason', 'Attendance', 'Phone', 'Status', 'Rejection_Reason', 'Email', 'Leave_type', 'created_at', 'Updated_at', 'gender', 'In_time', 'Out_time'];
$columnIndex = 1;
foreach ($headers as $header) {
    $worksheet->setCellValueByColumnAndRow($columnIndex++, 1, $header);
}

// Start with a base query to join the leave_applications table with the students table
$sql = "SELECT 
            la.*, 
            s.student_name,
            s.student_id,
            s.phone ,
            s.student_email,
            s.gender, 
            s.batch,
            s.department
        FROM leave_applications la
        JOIN students s ON la.student_id = s.student_id
        WHERE 1=1";

// Add conditions based on date range
if ($fromDate && !$toDate) {
    // Only from date is provided
    $sql .= " AND la.from_date >= ?";
} elseif (!$fromDate && $toDate) {
    // Only to date is provided
    $sql .= " AND la.to_date <= ?";
} elseif ($fromDate && $toDate) {
    // Both dates are provided
    $sql .= " AND la.from_date >= ? AND la.to_date <= ?";
}

// Add condition for school if it's not 'All'
if ($school && $school !== 'All') {
    $sql .= " AND s.department = ?";
}

// Prepare and execute the statement
$stmt = $mysqli->prepare($sql);
$params = [];

if ($fromDate) {
    $params[] = $fromDate;
}

if ($toDate) {
    $params[] = $toDate;
}

if ($school && $school !== 'All') {
    $params[] = $school;
}

// Bind parameters dynamically
if ($params) {
    $types = str_repeat('s', count($params)); // Assuming all are strings
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $rowIndex = 2; // Start from the second row (after headers)
    
    while ($row = $result->fetch_assoc()) {
        // Populate Excel cells with data
        $columnIndex = 1;
        // Set values for each cell with exact matching keys
        $worksheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $rowIndex - 1); // Sl.no
        $worksheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $row['department']);
        $worksheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $row['batch']);
        $worksheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $row['student_name']);
        $worksheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $row['student_id']);
        $worksheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $row['from_date']);
        $worksheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $row['to_date']);
        $worksheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $row['reason']);
        $worksheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $row['attendance']);
        $worksheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $row['phone']);
        $worksheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $row['status']);
        $worksheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $row['rejection_reason']);
        $worksheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $row['student_email']);
        $worksheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $row['leave_type']);
        $worksheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $row['created_at']);
        $worksheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $row['updated_at']);
        $worksheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $row['gender']);
        $worksheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $row['intime']);
        $worksheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $row['outime']);
        
        $rowIndex++;
    }

    // Create a writer for XLSX format
    $writer = new Xlsx($spreadsheet);

    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="leave_Applications_' . $currentDate . '.xlsx"');
    header('Cache-Control: max-age=0');

    // Output the Excel file to the browser
    $writer->save('php://output');
} else {
    // This else block is correctly placed now
    echo 'No data found in the database for the selected criteria.';
}

// Close the database connection
$stmt->close();
$mysqli->close();
?>
