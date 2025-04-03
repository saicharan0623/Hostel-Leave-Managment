<?php
session_start();

require '../vendor/autoload.php'; // Include the autoloader
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';
include 'database_config.php';

// Get the student ID from the session
$student_id = $_SESSION['student_id'];

// Fetch student details from the database
$studentData = null;

$sql = "SELECT student_name, department, batch, phone, gender, parent_email FROM students WHERE student_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $studentData = $result->fetch_assoc();
} else {
    echo "Error: Student data not found.";
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check the value of the submitted action
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // Process the form for "Inform Return"
    if ($action === 'Inform Return') {
        // Get form inputs
        $comments = $_POST['comments'];
        $submissionTime = $_POST['submission_time'];

        // Prepare and execute an SQL insert statement
        $sql = "INSERT INTO late_outing (student_id, submission_time, comments)
                VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sss", $student_id, $submissionTime, $comments);

        if ($stmt->execute()) {
            // Send email to the parent
            sendReturnNotificationEmail($studentData, $submissionTime, $comments);

            header("Location: late_outing_success.php");
            exit();
        } else {
            echo "Error: " . $mysqli->error;
        }

        $stmt->close();
        $mysqli->close();
    }
}

// Function to send an email to notify about the return to the parent
function sendReturnNotificationEmail($studentData, $submissionTime, $comments) {
    $mailToParent = new PHPMailer(true);

    try {
        // Configuration for Gmail SMTP
        $mailToParent->isSMTP();
        $mailToParent->Host = 'smtp.gmail.com';
        $mailToParent->SMTPAuth = true;
        $mailToParent->Username = 'saicharanmalde@gmail.com'; // Your Gmail address
        $mailToParent->Password = 'dsfhazqfsnprjwbs'; // Your Gmail app password
        $mailToParent->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mailToParent->Port = 587;

        $mailToParent->setFrom('saicharanmalde@gmail.com', 'NMIMS');
        $mailToParent->addAddress($studentData['parent_mail'], 'Parent Name'); // Parent's email
        $mailToParent->isHTML(true);
        $mailToParent->Subject = 'Late Return Notification of Your Ward';
        $mailToParent->Body = getParentEmailBody($studentData, $submissionTime, $comments);

        $mailToParent->send(); // Send the email to the parent
    } catch (Exception $e) {
        echo "Email sending failed. Error: {$e->getMessage()}";
    }
}

function getParentEmailBody($studentData, $submissionTime, $comments) {
    // Create the email body for the parent
    return '
    <html>
    <head>
        <title>Return Notification</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
                background-color: #f9f9f9;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                background-color: #fff;
                border: 1px solid #ccc;
                border-radius: 5px;
            }
            h1 {
                text-align: center;
                color: red;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                padding: 10px;
                border: 1px solid #ccc;
            }
            th {
                background-color: #f2f2f2;
                text-align: left;
            }
            p {
                color: #555;
            }
            strong {
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Late Return Notification</h1>
            <h3>Dear Parent/Guardian of ' . $studentData['student_name'] . ',</h3>
            <p>We have received a late return notification from your ward. Here are the details:</p>
                    
            <h3>Student Information</h3>
            <table>
                <tr>
                    <th>Student Name:</th>
                    <td>' . $studentData['student_name'] . '</td>
                </tr>
                <tr>
                    <th>Student Department:</th>
                    <td>' . $studentData['department'] . '</td>
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
                    <th>Late Entry Time</th>
                    <td>' . $submissionTime . '</td>
                </tr>
                <tr>
                    <th>Reason</th>
                    <td>' . $comments . '</td>
                </tr>
            </table>
            <p>Please ensure that your ward adheres to the return schedule to avoid further inconveniences.</p>
        </div>
    </body>
    </html>
    ';
}
?>
