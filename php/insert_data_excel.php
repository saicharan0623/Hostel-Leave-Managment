<?php
require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$excelFilePath = '../Excel/sample.xlsx';  // Path to the Excel file
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Load the Excel file
    $spreadsheet = IOFactory::load($excelFilePath);
    $worksheet = $spreadsheet->getActiveSheet();

    // Get the next available row
    $nextRow = $worksheet->getHighestRow() + 1;

    // Insert new data
    $worksheet->setCellValue('A' . $nextRow, $_POST['student_email']);
    $worksheet->setCellValue('B' . $nextRow, $_POST['parent_email']);
    $worksheet->setCellValue('C' . $nextRow, $_POST['student_name']);
    $worksheet->setCellValue('D' . $nextRow, $_POST['student_sap']);
    $worksheet->setCellValue('E' . $nextRow, $_POST['school']);
    $worksheet->setCellValue('F' . $nextRow, $_POST['contact_number']);
    $worksheet->setCellValue('G' . $nextRow, $_POST['batch']);
    $worksheet->setCellValue('H' . $nextRow, $_POST['gender']);

    // Save the updated file
    $writer = new Xlsx($spreadsheet);
    $writer->save($excelFilePath);

    $message = "New data inserted successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Insert New Student Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('../images/back4.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .container {
            width: 50%;
            margin: auto;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            margin-top: 50px;
        }
        @media (max-width: 768px) {
            .container {
                width: 90%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mb-4">Insert New Student Data</h2>

    <?php if ($message) { ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php } ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="student_email" class="form-label">Student Email ID:</label>
            <input type="email" class="form-control" name="student_email" required>
        </div>

        <div class="mb-3">
            <label for="parent_email" class="form-label">Parent Email ID:</label>
            <input type="email" class="form-control" name="parent_email" required>
        </div>

        <div class="mb-3">
            <label for="student_name" class="form-label">Student Name:</label>
            <input type="text" class="form-control" name="student_name" required>
        </div>

        <div class="mb-3">
            <label for="student_sap" class="form-label">Student SAP ID:</label>
            <input type="text" class="form-control" name="student_sap" required>
        </div>

        <div class="mb-3">
            <label for="school" class="form-label">School:</label>
            <input type="text" class="form-control" name="school" required>
        </div>

        <div class="mb-3">
            <label for="contact_number" class="form-label">Contact Number:</label>
            <input type="text" class="form-control" name="contact_number" required>
        </div>

        <div class="mb-3">
            <label for="batch" class="form-label">Batch:</label>
            <input type="text" class="form-control" name="batch" required>
        </div>

        <div class="mb-3">
            <label for="gender" class="form-label">Gender:</label>
            <input type="text" class="form-control" name="gender" required>
        </div>

        <button type="submit" class="btn btn-primary">Insert Data</button>
    </form>
    <a href="#" class="btn btn-secondary mt-3" onclick="window.history.back(); return false;">Back</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>