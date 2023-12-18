<?php
// Database configuration
$hostname = "localhost";
$username = "root";
$password = "";
$database = "college_db";

// Load PhpSpreadsheet library
require '../vendor/autoload.php'; // You need to install PhpSpreadsheet using Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create a new Spreadsheet
$spreadsheet = new Spreadsheet();

// Add a worksheet
$worksheet = $spreadsheet->getActiveSheet();

// Set headers for the Excel file
$headers = ['ID', 'Name', 'School', 'Mobile Number', 'SAP ID', 'From Date', 'To Date', 'Reason', 'Email', 'Status', 'Type'];
$columnIndex = 1;
foreach ($headers as $header) {
    $worksheet->setCellValueByColumnAndRow($columnIndex++, 1, $header);
}

// Establish a database connection
$dbConnection = new mysqli($hostname, $username, $password, $database);

// Check the database connection
if ($dbConnection->connect_error) {
    die("Database connection failed: " . $dbConnection->connect_error);
}

// Example SQL query to fetch data from your 'leave_applications' table
$sql = "SELECT * FROM leave_applications";
$result = $dbConnection->query($sql);

if ($result->num_rows > 0) {
    $rowIndex = 2; // Start from the second row (after headers)
    
    while ($row = $result->fetch_assoc()) {
        // Populate Excel cells with data
        $columnIndex = 1;
        foreach ($row as $value) {
            $worksheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $value);
        }
        
        $rowIndex++;
    }
    
    // Create a writer for XLSX format
    $writer = new Xlsx($spreadsheet);

    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="leave_data.xlsx"');

    // Output the Excel file to the browser
    $writer->save('php://output');
} else {
    echo 'No data found in the database.';
}

// Close the database connection
$dbConnection->close();
?>
