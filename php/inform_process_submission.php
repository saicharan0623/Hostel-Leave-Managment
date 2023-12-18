<?php
session_start();

require '../vendor/autoload.php'; // Include the autoloader
use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/phpmailer/phpmailer/src/PHPMailer.php'; // Corrected PHPMailer class inclusion
require '../vendor/phpmailer/phpmailer/src/SMTP.php';

// Database configuration
$hostname = "localhost";
$username = "root";
$password = "";
$database = "college_db"; // Updated to the correct database name

// Path to your Excel file
$excelFilePath = '../sample.xlsx';

// Get the email provided during login
$email = $_SESSION['email'];

// Load the Excel file
$spreadsheet = IOFactory::load($excelFilePath);

$worksheet = $spreadsheet->getActiveSheet();
$highestRow = $worksheet->getHighestRow();

// Find the row that matches the provided email
$studentData = null;
for ($row = 2; $row <= $highestRow; $row++) {
    if ($worksheet->getCell('A' . $row)->getValue() == $email) {
        $studentData = array(
            'id' => $row - 1, // Assuming your IDs start from 1
            'parent_mail' => $worksheet->getCell('B' . $row)->getValue(),
            'student_name' => $worksheet->getCell('C' . $row)->getValue(),
            'student_id' => $worksheet->getCell('D' . $row)->getValue(),
            'email' => $email,
            'department' => $worksheet->getCell('E' . $row)->getValue(),
            'batch' => $worksheet->getCell('G' . $row)->getValue(),
            'phone' => $worksheet->getCell('F' . $row)->getValue(),
            'gender' => $worksheet->getCell('H' . $row)->getValue(),
        );
        break;
    }
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check the value of the submitted action
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    $selectedHour = $_POST['hour'];
    $selectedMinute = $_POST['minute'];
    $selectedAmPm = $_POST['ampm'];

    // Process other form actions (e.g., submitting "Inform Return" form)
    if ($action === 'Inform Return') {
        // Process the form data
        $returnDate = $_POST['indate'];
        $returnTime = sprintf('%02d:%s:00', $selectedHour, $selectedMinute, $selectedAmPm);
        $wayOfTransport = $_POST['way_of_transport'];
        $comments = $_POST['comments'];

        // Connect to the database
        $conn = new mysqli($hostname, $username, $password, $database);

        // Check for a successful database connection
        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }

        // Prepare and execute an SQL insert statement
        $sql = "INSERT INTO student_inform (student_name, student_id, email, department, batch, phone, gender, indate, intime, way_of_transport, comments)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssss", $studentData['student_name'], $studentData['student_id'], $studentData['email'], $studentData['department'], $studentData['batch'], $studentData['phone'], $studentData['gender'], $returnDate, $returnTime, $wayOfTransport, $comments);

        if ($stmt->execute()) {
            // Data inserted successfully

            // Send an email to notify about the return
            sendReturnNotificationEmail($studentData, $returnDate, $returnTime, $wayOfTransport, $comments);

            // You can redirect to a success page or display a confirmation message
            header("Location: inform_return_success.php");
            exit();
        } else {
            // Handle the case where data insertion fails
            echo "Error: " . $conn->error;
        }

        // Close the database connection
        $stmt->close();
        $conn->close();
    }
}
// Function to send an email to notify about the return to both student and parent
function sendReturnNotificationEmail($studentData, $returnDate, $returnTime, $wayOfTransport, $comments) {
    $mailToStudent = new PHPMailer(true);
    $mailToParent = new PHPMailer(true);

    try {
        $mailToStudent->isSMTP();
        $mailToStudent->Host = 'smtp.gmail.com'; // Your SMTP server
        $mailToStudent->SMTPAuth = true;
        $mailToStudent->Username = 'saicharanmalde@gmail.com'; // Your SMTP username
        $mailToStudent->Password = 'lhthjcnefpkiedeo'; // Your SMTP password
        $mailToStudent->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mailToStudent->Port = 587;

        $mailToStudent->isHTML(true);
        $mailToStudent->setFrom('saicharanmalde@gmail.com', 'NMIMS');
        $mailToStudent->addAddress($studentData['email'], $studentData['student_name']);
        $mailToStudent->Subject = 'Return Notification to Student';
        $mailToStudent->Body = getStudentEmailBody($studentData, $returnDate, $returnTime, $wayOfTransport, $comments);

        if ($studentData['email'] !== $studentData['parent_mail']) {
            $mailToParent->isSMTP();
            $mailToParent->Host = 'smtp.gmail.com'; // Your SMTP server
            $mailToParent->SMTPAuth = true;
            $mailToParent->Username = 'saicharanmalde@gmail.com'; // Your SMTP username
            $mailToParent->Password = 'lhthjcnefpkiedeo'; // Your SMTP password
            $mailToParent->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mailToParent->Port = 587;

            $mailToParent->isHTML(true);
            $mailToParent->setFrom('saicharanmalde@gmail.com', 'NMIMS');
            $mailToParent->addAddress($studentData['parent_mail'], 'Parent Name');
            $mailToParent->Subject = 'Return Notification to Parent';
            $mailToParent->Body = getParentEmailBody($studentData, $returnDate, $returnTime, $wayOfTransport, $comments);
        }

        $mailToStudent->send();
        echo "Return notification email sent successfully to the student.";

        if ($studentData['email'] !== $studentData['parent_mail']) {
            $mailToParent->send();
            echo "Return notification email sent successfully to the parent.";
        }
    } catch (Exception $e) {
        echo "Email sending failed. Error: {$e->getMessage()}";
    }
}

// Functions to generate email bodies
function getStudentEmailBody($studentData, $returnDate, $returnTime, $wayOfTransport, $comments) {
    // Create the email body for the student
    return '
        <html>
        <head>
            <title>Return Notification</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    background-color: #f9f9f9; /* Light gray background */
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    background-color: #fff; /* White background */
                    border: 1px solid #ccc; /* Add a border for a professional look */
                    border-radius: 5px; /* Rounded corners for a softer appearance */
                }
                h1 {
                    text-align: center;
                    color: red; /* Dark gray text */
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                th, td {
                    padding: 10px;
                    border: 1px solid #ccc; /* Add borders to the table cells */
                }
                th {
                    background-color: #f2f2f2; /* Light gray background for table headers */
                    text-align: left;
                }
                p {
                    color: #555; /* Slightly darker text color */
                }
                strong {
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>IN PASS</h1>
                <p>Hello,</p>
                <p>NIMIMS College Administration,</p>
                <p>This email is to inform you that your leave application has been <strong>Approved</strong>.</p>
                
                <h3>Student Information</h3>
                <table>
                    <tr>
                        <th>Student Name:</th>
                        <td>' . $studentData['student_name'] . '</td>
                    </tr>
                    <tr>
                        <th>Student ID:</th>
                        <td>' . $studentData['student_id'] . '</td>
                    </tr>
                    <tr>
                        <th>Mobile No:</th>
                        <td>' . $studentData['phone'] . '</td>
                    </tr>
                </table>
                
                <h3>Return Details</h3>
                <table>
                    <tr>
                        <th>Return Date:</th>
                        <td>' . $returnDate . '</td>
                    </tr>
                    <tr>
                        <th>Return Time:</th>
                        <td>' . $returnTime . '</td>
                    </tr>
                    <tr>
                        <th>Transportation Method:</th>
                        <td>' . $wayOfTransport . '</td>
                    </tr>
                    <tr>
                        <th>Additional Comments:</th>
                        <td>' . $comments . '</td>
                    </tr>
                </table>
                <p>Wishing you a safe journey and looking forward to your return!</p>
            </div>
        </body>
        </html>
    ';
}



function getParentEmailBody($studentData, $returnDate, $returnTime, $wayOfTransport, $comments) {
    // Create the email body for the parent
    return'
    <html>
        <head>
            <title>Return Notification</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    background-color: #f9f9f9; /* Light gray background */
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    background-color: #fff; /* White background */
                    border: 1px solid #ccc; /* Add a border for a professional look */
                    border-radius: 5px; /* Rounded corners for a softer appearance */
                }
                h1 {
                    text-align: center;
                    color: red; /* Dark gray text */
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                th, td {
                    padding: 10px;
                    border: 1px solid #ccc; /* Add borders to the table cells */
                }
                th {
                    background-color: #f2f2f2; /* Light gray background for table headers */
                    text-align: left;
                }
                p {
                    color: #555; /* Slightly darker text color */
                }
                strong {
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>Return Notification of Your Ward</h1>
                <p>Dear Sir/Madam,</p>
                <p>This email is to inform you about your child\'s upcoming return to college.</p>

                <h3>Student Information</h3>
                <table>
                    <tr>
                        <th>Student Name:</th>
                        <td>' . $studentData['student_name'] . '</td>
                    </tr>
                    <tr>
                        <th>Student ID:</th>
                        <td>' . $studentData['student_id'] . '</td>
                    </tr>
                    <tr>
                        <th>Mobile No:</th>
                        <td>' . $studentData['phone'] . '</td>
                    </tr>
                </table>
                
                <h3>Return Details</h3>
                <table>
                    <tr>
                        <th>Return Date:</th>
                        <td>' . $returnDate . '</td>
                    </tr>
                    <tr>
                        <th>Return Time:</th>
                        <td>' . $returnTime . '</td>
                    </tr>
                    <tr>
                        <th>Transportation Method:</th>
                        <td>' . $wayOfTransport . '</td>
                    </tr>
                    <tr>
                        <th>Additional Comments:</th>
                        <td>' . $comments . '</td>
                    </tr>
                </table>
                <p>Wishing a safe journey for your child, and we look forward to their return!</p></div>
        </body>
        </html>
    ';
}
  
