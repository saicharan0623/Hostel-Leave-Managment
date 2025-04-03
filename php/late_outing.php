<?php
session_start();

// Check if student ID is set in the session
if (!isset($_SESSION["student_id"])) {
    header("Location: student_login_page.php");
    exit();
}

// Get the student ID from the session
$student_id = $_SESSION["student_id"];

require '../vendor/autoload.php';
include 'database_config.php';

// Get the current time
$currentTime = new DateTime("now", new DateTimeZone('Asia/Kolkata')); // 'Asia/Kolkata' timezone
$startTime = new DateTime("12:00", new DateTimeZone('Asia/Kolkata')); // 7:45 PM
$endTime = new DateTime("06:00", new DateTimeZone('Asia/Kolkata'));   // 6:00 AM next day

// Allow access only between 7:45 PM and 6:00 AM
if ($currentTime < $startTime && $currentTime > $endTime) {
    echo "<div class='container'>
                <h1>You can access this page only between 7:45 PM and 6:00 AM.</h1>
          </div>";
    echo "<style>
            * {
                margin: 0;
                padding: 0;
            }
            body, html {
                height: 100%;
                font-family: Arial, sans-serif;
                background: url('../images/back4.jpg') no-repeat center center fixed;
                background-size: cover; /* Makes sure the image covers the entire page */
            }
            h1 {
                font-size: 40px;
            }
            .container {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                width:600px;
                margin:20px auto;
            }
          
          </style>";
    exit();
}

// Fetch student details from the students table
$sql = "SELECT student_id,student_name, student_email, department, batch, phone, gender FROM students WHERE student_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $studentData = $result->fetch_assoc();
} else {
    echo "No student data found for the given ID.";
    exit();
}

$stmt->close();
$mysqli->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Late Entry Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../images/ico.png" type="image/x-icon">

    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url("../images/back4.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: Black;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.6); 
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
            width: 90%;
            max-width: 500px;
            color: black;
        }

        .card input.form-control, 
        .card select.form-select, 
        .card textarea.form-control {
            border: 2px solid red; 
        }

        .card input.form-control:focus,
        .card select.form-select:focus,
        .card textarea.form-control:focus {
            border-color: darkred;
            outline: none;
        }

        h2 {
            text-align: center;
            color: red;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Welcome, <?php echo $studentData['student_name']; ?></h2>

    <h3 class="text-center mb-4">Fill the Late Entry Form</h3>
    
    <form method="POST" action="late_outing_submission.php">
        <input type="hidden" name="student_id" value="<?php echo $studentData['student_id']; ?>">
        <input type="hidden" name="student_name" value="<?php echo $studentData['student_name']; ?>">
        <input type="hidden" name="student_email" value="<?php echo $studentData['student_email']; ?>">
        <input type="hidden" name="action" value="Inform Return">

        <div class="mb-3">
            <label for="year" class="form-label">Select Year:</label>
            <select name="year" class="form-select" required>
                <option value="1st year">1st year</option>
                <option value="2nd year">2nd year</option>
                <option value="3rd year">3rd year</option>
                <option value="4th year">4th year</option>
                <option value="5th year">5th year</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="comments" class="form-label">Reason For Late:</label>
            <textarea name="comments" class="form-control" rows="4" required></textarea>
        </div>

        <input type="hidden" name="submission_time" id="submission_time" value="">

        <div class="d-grid">
            <input type="submit" class="btn btn-danger" value="Inform Return">
        </div>

        <h5 class="text-center mt-3">Note: Fill out this form if you are entering the campus after Daily Outing Times.</h5>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelector("form").addEventListener("submit", function () {
            const currentDateTime = new Date();
            const formattedDateTime = `${currentDateTime.toISOString().slice(0, 10)} ${currentDateTime.toLocaleTimeString()}`;
            document.querySelector("#submission_time").value = formattedDateTime;
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
