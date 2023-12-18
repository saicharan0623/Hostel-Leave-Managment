<?php
require '../vendor/autoload.php'; // Include the autoloader

use PhpOffice\PhpSpreadsheet\IOFactory;

$excelFilePath = '../sample.xlsx';

$spreadsheet = IOFactory::load($excelFilePath);
$worksheet = $spreadsheet->getActiveSheet();

$sapIdToFilter = $_GET['sapId']; // Get the SAP ID from the query string

$result = [];

foreach ($worksheet->getRowIterator() as $row) {
    $rowData = [];
    foreach ($row->getCellIterator() as $cell) {
        $rowData[] = $cell->getValue();
    }

    // Assuming the column headers are as specified
    $studentId = $rowData[3]; // Index 3 corresponds to the "student Id" column

    if ($studentId == $sapIdToFilter) {
        $result[] = [
            'Student_mail' => $rowData[0], // Index 0 corresponds to the "student_mail" column
            'parent_mail' => $rowData[1], // Index 1 corresponds to the "parent_mail" column
            'name' => $rowData[2], // Index 2 corresponds to the "name" column
            'student Id' => $rowData[3], // Index 3 corresponds to the "student Id" column
            'department' => $rowData[4], // Index 4 corresponds to the "department" column
            'mobile number' => $rowData[5], // Index 5 corresponds to the "mobile number" column
            'batch' => $rowData[6], // Index 6 corresponds to the "batch" column
            'gender' => $rowData[7], // Index 7 corresponds to the "gender" column
        ];
    }
}

// Send the result as JSON
header('Content-Type: application/json');
echo json_encode($result);
?>
