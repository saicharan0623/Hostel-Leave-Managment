<?php
session_start();
include 'database_config.php';
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ensure the user is logged in by checking the session
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id_from_session = $_SESSION['student_id']; 
$email = $_SESSION['email'] ?? ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reason'])) {
    // Collect form data
    $school = $_POST["school"] ?? "";
    $year = $_POST["year"] ?? "";
    $student_id = $_POST["student_id"] ?? "";
    $name = $_POST["student_name"] ?? "";
    $roomno = $_POST["roomno"] ?? "";
    $mobile = $_POST["mobile"] ?? "";
    $fromDate = $_POST["from_date"] ?? "";
    $toDate = $_POST["to_date"] ?? "";
    $reason = $_POST["reason"] ?? "";
    $attendance = $_POST["attendance"] ?? "";
    $intime = $_POST["intime"] ?? "";
    $outime = $_POST["outime"] ?? "";
    $leave_type = $_POST["leave_type"] ?? ""; // New field for leave type
    $status = "PENDING"; // Default status

    // Check if the session student_id matches the submitted student_id
    if ($student_id_from_session != $student_id) {
        echo "Error: You cannot submit a leave request for another student.";
        exit();
    }

    try {
        // Insert the leave application into the database
        $stmt = $pdo->prepare("INSERT INTO leave_applications (student_id, from_date, to_date, reason, status, attendance, intime, outime, leave_type, created_at)
                               VALUES (:student_id, :from_date, :to_date, :reason, :status, :attendance, :intime, :outime, :leave_type, NOW())");

        // Bind parameters
        $stmt->bindParam(":student_id", $student_id);
        $stmt->bindParam(":from_date", $fromDate);
        $stmt->bindParam(":to_date", $toDate);
        $stmt->bindParam(":reason", $reason);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":attendance", $attendance);
        $stmt->bindParam(":intime", $intime);
        $stmt->bindParam(":outime", $outime);
        $stmt->bindParam(":leave_type", $leave_type);

        // Get recipient email using the student SAP ID
        $recipientEmail = getRecipientEmail($student_id);

        // Send email
        if ($recipientEmail && sendEmail($recipientEmail, $name, $year, $school, $student_id, $fromDate, $toDate, $reason, $attendance)) {
            $stmt->execute();
            header("Location: leave_success.php");
            exit();
        } else {
            echo "Email sending failed.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Function to get the parent's email using the student ID
function getRecipientEmail($studentId) {
    global $pdo;
    try {
        // Query the database to get the parent's email based on the student SAP ID
        $stmt = $pdo->prepare("SELECT parent_email FROM students WHERE student_id = :student_id");
        $stmt->bindParam(":student_id", $studentId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['parent_email'] ?? null;
    } catch (PDOException $e) {
        error_log("Error fetching recipient email: " . $e->getMessage());
        return null;
    }
}

// Function to send the email
function sendEmail($recipientEmail, $name, $year, $school, $student_id, $fromDate, $toDate, $reason, $attendance) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'saicharanmalde@gmail.com'; // Your Gmail address
        $mail->Password = 'dsfhazqfsnprjwbs'; // Your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('saicharanmalde@gmail.com', 'NMIMS');
        $mail->addAddress($recipientEmail, 'Recipient Name');
        $mail->isHTML(true);
        $mail->Subject = 'NMIMS Leave Application';

        // Generate the email body content
        $mail->Body = generateEmailBody($name, $year, $school, $student_id, $fromDate, $toDate, $reason, $attendance);

        // Attempt to send the email
        if (!$mail->send()) {
            throw new Exception('Mail could not be sent. PHPMailer Error: ' . $mail->ErrorInfo);
        }

        return true; 
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        echo 'Error: ' . $e->getMessage(); // Show error message to the user
        return false; // Return false if email sending failed
    }
}

// Function to generate the email body
function generateEmailBody($name, $year, $school, $student_id, $fromDate, $toDate, $reason, $attendance) {
    $formattedFromDate = $fromDate ? (new DateTime($fromDate))->format('Y-m-d') : 'N/A';
    $formattedToDate = $toDate ? (new DateTime($toDate))->format('Y-m-d') : 'N/A';

    return '
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        <h1>Leave Application of Your Ward</h1>
    
        <p>Hello,</p>
        <p>NMIMS College Administration,</p>
        <p>This email is to inform you about a leave application submitted by your ward.</p>
        
        <h3>Student Information</h3>
        <table>
            <tr>
                <th>Field</th>
                <th>Value</th>
            </tr>
            <tr>
                <td><strong>Student Name:</strong></td>
                <td>' . htmlspecialchars($name) . '</td>
            </tr>
            <tr>
                <td><strong>Year Studying:</strong></td>
                <td>' . htmlspecialchars($year) . '</td>
            </tr>
            <tr>
                <td><strong>Student Department:</strong></td>
                <td>' . htmlspecialchars($school) . '</td>
            </tr>
            <tr>
                <td><strong>Student ID:</strong></td>
                <td>' . htmlspecialchars($student_id) . '</td>
            </tr>
        </table>
        
        <h3>Leave Details</h3>
        <table>
            <tr>
                <th>Field</th>
                <th>Value</th>
            </tr>
            <tr>
                <td><strong>Leave Start Date:</strong></td>
                <td>' . htmlspecialchars($formattedFromDate) . '</td>
            </tr>
            <tr>
                <td><strong>Leave End Date:</strong></td>
                <td>' . htmlspecialchars($formattedToDate) . '</td>
            </tr>
            <tr>
                <td><strong>Reason:</strong></td>
                <td>' . htmlspecialchars($reason) . '</td>
            </tr>
            <tr>
                <td><strong>Attendance:</strong></td>
                <td>' . htmlspecialchars($attendance) . '</td>
            </tr>
        </table>
    
        <p>Thank you.</p>
    </div>
    </body>
    </html>';
}
?>
