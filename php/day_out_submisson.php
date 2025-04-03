<?php
session_start();
include 'database_config.php';

require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = $_SESSION['email'] ?? ''; 
$student_id = $_SESSION['student_id'] ?? ''; // Ensure student_id is retrieved from session

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reason'])) {
    // Collect form data
    $outdate = $_POST["outdate"] ?? "";
    $intime = $_POST["intime"] ?? "";
    $outime = $_POST["outime"] ?? "";
    $reason = $_POST["reason"] ?? "";
    $status = "PENDING";

    try {
        // Insert data into the database using only student_id and other details
        $stmt = $pdo->prepare("INSERT INTO day_outing_requests 
        (student_id, outdate, intime, outime, reason, status) 
        VALUES 
        (:student_id, :outdate, :intime, :outime, :reason, :status)");

        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':outdate', $outdate);
        $stmt->bindParam(':intime', $intime);
        $stmt->bindParam(':outime', $outime);
        $stmt->bindParam(':reason', $reason);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            // Fetch the parent email and student details from the database using the student_id
            $studentDetails = getStudentDetails($student_id);
            if ($studentDetails && $studentDetails['parent_email']) {
                $emailSent = sendEmail(
                    $studentDetails['parent_email'], 
                    $studentDetails['student_name'], 
                    $studentDetails['batch'], 
                    $student_id, 
                    $outdate, 
                    $intime, 
                    $outime, 
                    $reason
                );
                if ($emailSent) {
                    header("Location: day_out_success.php");
                    exit();
                } else {
                    echo "Email sending failed.";
                }
            } else {
                echo "Parent email or student details not found.";
            }
        } else {
            echo "Failed to submit the request.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Function to get the student details from the database
function getStudentDetails($student_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT student_name, batch, parent_email FROM students WHERE student_id = :student_id");
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to send email using PHPMailer
function sendEmail($recipientEmail, $studentName, $batch, $student_id, $outdate, $intime, $outime, $reason) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Gmail SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'msaicharan013@gmail.com'; // Your Gmail address
        $mail->Password = 'paupedqtnuiwmkpy'; // App password generated from Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use STARTTLS for port 587
        $mail->Port = 587; // Use port 587 for STARTTLS

        $mail->setFrom('msaicharan013@gmail.com', 'NMIMS Hostel');
        $mail->addAddress($recipientEmail, 'Recipient Name');
        $mail->isHTML(true);
        $mail->Subject = 'Day Outing Request Of Your Ward';

        // Generate the email body
        $mail->Body = generateEmailBody($studentName, $batch, $student_id, $outdate, $intime, $outime, $reason);

        return $mail->send();
    } catch (Exception $e) {
        error_log("Email sending failed. Error: {$mail->ErrorInfo}");
        return false;
    }
}

function generateEmailBody($studentName, $batch, $student_id, $outdate, $intime, $outime, $reason) {
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
        <h1>Day Outing Application of Your Ward</h1>
    
        <p>Hello,</p>
        <p>NMIMS College Administration,</p>
        <p>This email is to inform you about a Day Outing Request submitted by your ward.</p>
        
        <h3>Leave Details</h3>
        <table>
            <tr>
                <th>Field</th>
                <th>Value</th>
            </tr>
            <tr>
                <td><strong>Student Name:</strong></td>
                <td>' . htmlspecialchars($studentName) . '</td>
            </tr>
            <tr>
                <td><strong>Batch:</strong></td>
                <td>' . htmlspecialchars($batch) . '</td>
            </tr>
            <tr>
                <td><strong>Student ID:</strong></td>
                <td>' . htmlspecialchars($student_id) . '</td>
            </tr>
            <tr>
                <td><strong>Out Date:</strong></td>
                <td>' . htmlspecialchars($outdate) . '</td>
            </tr>
            <tr> 
                <td><strong>Start Time:</strong></td>
                <td>' . htmlspecialchars($outime) . '</td>
            </tr>
            <tr>
                <td><strong>End Time:</strong></td>
                <td>' . htmlspecialchars($intime) . '</td>
            </tr>
            <tr>
                <td><strong>Reason:</strong></td>
                <td>' . htmlspecialchars($reason) . '</td>
            </tr>
        </table>
    
        <p>Thank you.</p>
    </div>
    </body>
    </html>';
}
?>
