<?php
session_start();

//------------------- EMAIL ---------------------------
require '../vendor/autoload.php'; // Include the autoloader
use PhpOffice\PhpSpreadsheet\IOFactory;
$excelFilePath = '../sample.xlsx'; // Replace with the actual path to your Excel file

require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';
require '../vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
//------------------- EMAIL ---------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check the value of the submitted action
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    // Get all the values from the form
    $your_name = isset($_POST['your_name']) ? $_POST['your_name'] : '';
    $your_student_id = isset($_POST['your_student_id']) ? $_POST['your_student_id'] : '';
    $your_school = isset($_POST['your_school']) ? $_POST['your_school'] : '';
    $your_mobile_number = isset($_POST['your_mobile_number']) ? $_POST['your_mobile_number'] : '';
    $Student_mail = isset($_POST['Student_mail']) ? $_POST['Student_mail'] : '';
    $parent_mail = isset($_POST['parent_mail']) ? $_POST['parent_mail'] : '';
    $name = isset($_POST['name']) ? $_POST['name'] : '';    
    $Student_Id = isset($_POST['Student_Id']) ? $_POST['Student_Id'] : '';
    $department = isset($_POST['department']) ? $_POST['department'] : '';
    $Mobile = isset($_POST['Mobile']) ? $_POST['Mobile'] : '';
    $batch = isset($_POST['batch']) ? $_POST['batch'] : '';
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $Reason = isset($_POST['Reason']) ? $_POST['Reason'] : '';

    // Connect to the database (replace with your own database credentials)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "college_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $currentDateTime = date('Y-m-d H:i:s');
    $sql = "INSERT INTO emergency_leaves (proxy_name, proxy_id, proxy_school, proxy_mobile, sick_name, gender, sick_person_sap_id, sick_department, sick_mail, batch, phone, leave_reason, application_date) VALUES ('$your_name', '$your_student_id', '$your_school', '$your_mobile_number', '$name', '$gender', '$Student_Id', '$department', '$Student_mail', '$batch', '$Mobile', '$Reason', '$currentDateTime')";

    if ($conn->query($sql) === TRUE) {
        //----------------- EXCEL -----------------------------
        $recepientEmail = '';

        $spreadsheet = IOFactory::load($excelFilePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();

        // Loop through rows, starting from the second row
        for ($row = 2; $row <= $highestRow; $row++) {
            $cellValue = $worksheet->getCellByColumnAndRow(1, $row)->getValue();

            if ($cellValue === $Student_mail) { // Correct variable name
                $nextColumnValue = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                break;
            }
        }
        if ($nextColumnValue !== null) {
            echo "Match found!<br>";
            echo "Value from the next column: " . $nextColumnValue;
            $recepientEmail = $nextColumnValue;
        } else {
            echo "Match not found!";
        }
        //----------------- EXCEL -----------------------------

        //------------------- EMAIL ---------------------------
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'saicharanmalde@gmail.com'; // SMTP username
            $mail->Password = 'lhthjcnefpkiedeo'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Send email to college administration
            $mail->setFrom('saicharanmalde@gmail.com', 'NMIMS');
            $mail->addAddress($recepientEmail, 'Recipient Name');
    
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Emergency Leave Application';

            $mail->Body = getEmergencyLeaveEmailBody($your_name, $your_student_id, $your_school, $your_mobile_number, $name, $gender, $Student_Id, $department, $Student_mail, $batch, $Mobile, $Reason);
    
            // Send the email
            $mail->send();
            echo "Email sent successfully to college administration.";
            
            // Send email to the student
            $mail->clearAddresses(); // Clear previous recipient addresses
            $mail->addAddress($Student_mail, 'Student Name'); // Set the student's email address
    
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Emergency Leave Application';

            $mail->Body = getStudentEmergencyLeaveEmailBody($your_name, $your_student_id, $your_school, $your_mobile_number, $name, $gender, $Student_Id, $department, $Student_mail, $batch, $Mobile, $Reason);
    
            // Send the email
            $mail->send();
            echo "Email sent successfully to the student.";
        } catch (Exception $e) {
            echo "Email sending failed. Error: {$e->getMessage()}";
        }
    
        // Redirect to a success page
        header("Location: emergency_success.php");
        exit;
        
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    $conn->close();
}

// Function to generate the email body for college administration
function getEmergencyLeaveEmailBody($your_name, $your_student_id, $your_school, $your_mobile_number, $name, $gender, $Student_Id, $department, $Student_mail, $batch, $Mobile, $Reason) {
    return '
    <html>
    <head>
        <title>Emergency Leave Application</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
                background-color: #f9f9f9; /* Light gray background */
            }
            .container {
                max-width: 500px;
                margin: 0 auto;
                padding: 20px;
                background-color: #fff; /* White background */
                border: 1px solid #ccc; /* Add a border for a professional look */
                border-radius: 5px; /* Rounded corners for a softer appearance */
            }
            h1 {
                text-align: center;
                color: red; /* Dark gray text */
                font-size: 30px;
                font-weight: bold;
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
            <h1>Emergency Leave Application</h1>
            <p>Hello,</p>
            <p>NMIMS College Administration,</p>
            <p>This email is to inform you about an emergency Leave Application of your ward.</p>
            
            <h3>Proxy Information</h3>
            <table>
                <tr>
                    <th>Proxy Name:</th>
                    <td>' . $your_name . '</td>
                </tr>
                <tr>
                    <th>Proxy ID:</th>
                    <td>' . $your_student_id . '</td>
                </tr>
                <tr>
                    <th>Proxy Mobile Number:</th>
                    <td>' . $your_mobile_number . '</td>
                </tr>
            </table>
            <p>The above student applied for leave:</p>
            
            <h3>Student Information</h3>
            <table>
                <tr>
                    <th>Name :</th>
                    <td>' . $name . '</td>
                </tr>
                <tr>
                    <th>Gender:</th>
                    <td>' . $gender . '</td>
                </tr>
                <tr>
                    <th>SAP ID:</th>
                    <td>' . $Student_Id . '</td>
                </tr>
                <tr>
                    <th>Department:</th>
                    <td>' . $department . '</td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td>' . $Student_mail . '</td>
                </tr>
                <tr>
                    <th>Batch:</th>
                    <td>' . $batch . '</td>
                </tr>
                <tr>
                    <th>Phone Number:</th>
                    <td>' . $Mobile . '</td>
                </tr>
                <tr>
                    <th>Leave Reason:</th>
                    <td>' . $Reason . '</td>
                </tr>
            </table>
        </div>
    </body>
    </html>
';

}

// Function to generate the email body for the student
function getStudentEmergencyLeaveEmailBody($your_name, $your_student_id, $your_school, $your_mobile_number, $name, $gender, $Student_Id, $department, $Student_mail, $batch, $Mobile, $Reason) {
    return '
    <html>
    <head>
        <title>Emergency Leave Application</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
                background-color: #f9f9f9; /* Light gray background */
            }
            .container {
                max-width: 500px;
                margin: 0 auto;
                padding: 20px;
                background-color: #fff; /* White background */
                border: 1px solid #ccc; /* Add a border for a professional look */
                border-radius: 5px; /* Rounded corners for a softer appearance */
            }
            h1 {
                text-align: center;
                color: red; /* Dark gray text */
                font-size: 30px;
                font-weight: bold;
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
            <h1>Emergency Leave Application</h1>
            <p>Hello,</p>
            <p>Dear Student,</p>
            <p>This email is to confirm that an emergency Leave Application has been submitted on your behalf.</p>

            <h3>Proxy Information</h3>
            <table>
                <tr>
                    <th>Proxy Name:</th>
                    <td>' . $your_name . '</td>
                </tr>
                <tr>
                    <th>Proxy ID:</th>
                    <td>' . $your_student_id . '</td>
                </tr>
                <tr>
                    <th>Proxy Mobile Number:</th>
                    <td>' . $your_mobile_number . '</td>
                </tr>
            </table>
            
            <h3>Your Information</h3>
            <table>
                <tr>
                    <th>Your Name:</th>
                    <td>' . $name . '</td>
                </tr>
                <tr>
                    <th>Gender:</th>
                    <td>' . $gender . '</td>
                </tr>
                <tr>
                    <th>Your SAP ID:</th>
                    <td>' . $Student_Id . '</td>
                </tr>
                <tr>
                    <th>Department:</th>
                    <td>' . $department . '</td>
                </tr>
                <tr>
                    <th>Your Email:</th>
                    <td>' . $Student_mail . '</td>
                </tr>
                <tr>
                    <th>Your Batch:</th>
                    <td>' . $batch . '</td>
                </tr>
                <tr>
                    <th>Your Phone Number:</th>
                    <td>' . $Mobile . '</td>
                </tr>
                <tr>
                    <th>Leave Reason:</th>
                    <td>' . $Reason . '</td>
                </tr>
            </table>
        </div>
    </body>
    </html>
';

}
?>
