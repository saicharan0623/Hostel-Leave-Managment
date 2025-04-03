<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Define the path to store the Excel file
    $uploadDir = '../Excel/';
    $fileName = 'students_list.xlsx';
    $filePath = $uploadDir . $fileName;

    // Check if a file was uploaded
    if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] === UPLOAD_ERR_OK) {
        // Validate the file type
        $fileType = $_FILES['excel_file']['type'];
        $allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];

        if (in_array($fileType, $allowedTypes)) {
            // Move the uploaded file to the specified path
            if (move_uploaded_file($_FILES['excel_file']['tmp_name'], $filePath)) {
                echo "<script>alert('File uploaded successfully!'); window.location.href = 'manage_excel.php';</script>";
            } else {
                echo "<script>alert('Failed to upload file.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Invalid file type. Only Excel files are allowed.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('No file was selected or an error occurred during upload.'); window.history.back();</script>";
    }
}
?>
