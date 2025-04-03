<?php
session_start();
include 'database_config.php';
require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$excelFilePath = '../Excel/students_list.xlsx';
$errorMsg = "";

function normalizeInput($input) {
    return trim($input ?? '');
}

function logUserActivity($pdo, $student_id, $message, $action) {
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown IP';
    $device_info = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown Device';
    $current_time = date("Y-m-d H:i:s");

    $stmt = $pdo->prepare("INSERT INTO user_logs (student_id, login_time, message, ip_address, device_info) 
                           VALUES (:student_id, :login_time, :message, :ip_address, :device_info)");
    $stmt->execute([
        'student_id' => $student_id,
        'login_time' => ($action === 'login') ? $current_time : null,
        'message' => $message,
        'ip_address' => $ip_address,
        'device_info' => $device_info,
    ]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentID = normalizeInput($_POST['student_id']);
    $passwordPost = $_POST["password"];

    try {
        if (!file_exists($excelFilePath)) {
            throw new Exception("Excel file not found: $excelFilePath");
        }

        $spreadsheet = IOFactory::load($excelFilePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();

        $studentFound = false;
        $studentData = [];

        // Check the Excel file for the student ID
        for ($row = 2; $row <= $highestRow; $row++) {
            $excelStudentID = normalizeInput($worksheet->getCellByColumnAndRow(4, $row)->getValue());

            if ($excelStudentID === $studentID) {
                $studentFound = true;
                $studentData = [
                    'student_id' => $excelStudentID,
                    'email' => $worksheet->getCellByColumnAndRow(1, $row)->getValue(),
                    'parent_mail' => $worksheet->getCellByColumnAndRow(2, $row)->getValue(),
                    'student_name' => $worksheet->getCellByColumnAndRow(3, $row)->getValue(),
                    'department' => $worksheet->getCellByColumnAndRow(5, $row)->getValue(),
                    'phone' => $worksheet->getCellByColumnAndRow(6, $row)->getValue(),
                    'batch' => $worksheet->getCellByColumnAndRow(7, $row)->getValue(),
                    'gender' => $worksheet->getCellByColumnAndRow(8, $row)->getValue(),
                ];
                break;
            }
        }

        if ($studentFound) {
            $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = :student_id");
            $stmt->execute(['student_id' => $studentData['student_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $accessAllowed = false;
            if ($result) {
                $accessAllowed = ($result['access'] == 1);
                $storedPassword = $result['password'];
            } else {
                // Insert the student into the `students` table if not already present
                $stmt = $pdo->prepare(
                    "INSERT INTO students (student_email, parent_email, student_name, student_id, department, phone, batch, gender, access, password) 
                    VALUES (:email, :parent_mail, :student_name, :student_id, :department, :phone, :batch, :gender, 1, NULL)"
                );
                $stmt->execute([ 
                    'email' => $studentData['email'],
                    'parent_mail' => $studentData['parent_mail'],
                    'student_name' => $studentData['student_name'],
                    'student_id' => $studentData['student_id'],
                    'department' => $studentData['department'],
                    'phone' => $studentData['phone'],
                    'batch' => $studentData['batch'],
                    'gender' => $studentData['gender'],
                ]);
                $accessAllowed = true;
                $storedPassword = null;
            }

            if ($accessAllowed) {
                $_SESSION['student_id'] = $studentData['student_id'];
                $_SESSION['time'] = time();
            
                if (!$storedPassword) {
                    // Log successful login attempt (no password set)
                    logUserActivity($pdo, $studentData['student_id'], 'Redirecting to choose password', 'Redirecting to choose password');
                    header("Location: student_choose_password.php");
                    exit();
                } else {
                    if (password_verify(trim($passwordPost), trim($storedPassword))) {
                        logUserActivity($pdo, $studentData['student_id'], 'Login successful', 'login');
                        header("Location: student_dashboard.php");
                        exit();
                    } else {
                        $errorMsg = "Incorrect Password";
                        logUserActivity($pdo, $studentID, 'failed', 'Incorrect password');
                    }
                }
            } else {
                // Handle different access denied cases based on access level
                if ($result['access'] == 1) {
                    $errorMsg = "Access Denied By Admin";
                    // Log access denied by Admin
                    logUserActivity($pdo, $studentData['student_id'], 'failed', 'Access denied by admin');
                } elseif ($result['access'] == 2) {
                    $errorMsg = "Access Denied For School";
                    // Log access denied by School
                    logUserActivity($pdo, $studentData['student_id'], 'failed', 'Access denied for school');
                }
            }
        } else {
            $errorMsg = "Student not found in Excel file";
            logUserActivity($pdo, $studentID, 'failed', 'Student ID not found in Excel file');
        }

    } catch (Exception $e) {
        $errorMsg = 'An error occurred: ' . $e->getMessage();
        logUserActivity($pdo, $studentID, 'error', $errorMsg);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="icon" href="../images/ico.png" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-image: url("../images/back4.jpg");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            justify-content: center;
        }
        .form-container {
            max-width: 400px;
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.85);
            border-radius: 10px;
        }
        footer {
            background-color: rgb(99, 102, 106);
            color: #fff;
            padding: 10px;
            text-align: center;
            margin-top: auto;
        }
        .container {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card-body {
            padding: 10px;
        }
        img {
            max-height: 60px;
        }
        marquee {
            font-size: 15px;
        }
        .btn-red {
            background-color: #dc3545 !important;
            color: #fff;
            width:100%;
            color: #ffffff !important;
        }
        .btn-red:hover {
            background-color: #bb2d3b;
            color: #fff;
        }
    </style>
</head>
<body>
<header>
    <?php include 'navbar.php'; ?>
</header>
<div class="container">
    <div class="form-container">
        <div class="card-body text-center">
            <img src="../images/back7.png" alt="Logo" class="img-fluid mb-3">
            <h1 class="card-title text-danger">Welcome Student</h1>
            <form action="" method="post" class="mt-3">
                <div class="mb-3">
                    <input type="text" name="student_id" placeholder="Student ID" class="form-control" required maxlength="11" />
                </div>

                <div class="mb-3">
                    <input type="password" name="password" placeholder="Password" class="form-control" required />
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-red">Login</button>
                </div>
                <div class="mb-3">
                    <a href="student_forgot.php" class="text-primary">Forgot Password?</a>
                </div>
                <p class="mt-2">NOTE: <b>Enter random password for first Login<b></p>
                <marquee class="text-danger">If you are logging in for the first time, enter a random password</marquee>
            </form>

            <?php if (!empty($errorMsg)): ?>
                <div class="alert alert-danger" role="alert"><?= $errorMsg ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer>
    <span>2024 &copy; MALDE SAICHARAN - STME All rights reserved.</span>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
