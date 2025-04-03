<?php
session_start();
require 'database_config.php';

// Check if user is logged in using student_id
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login_page.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$studentData = null;
$error = null;

try {
    // Query to retrieve student details from the 'students' table
    $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = :student_id");
    $stmt->execute(['student_id' => $student_id]);
    $studentData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$studentData) {
        throw new Exception("Student data not found.");
    }

} catch (Exception $e) {
    error_log("Error in retrieving student data: " . $e->getMessage());
    $error = "An error occurred while retrieving student data. Please try again later.";
}

function getStudentData($key) {
    global $studentData;
    return isset($studentData[$key]) ? htmlspecialchars($studentData[$key]) : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Day Out Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
       <link rel="icon" href="../images/ico.png" type="image/x-icon">

    <style>
        body {
            background-image: url("../images/back4.jpg");
            background-size: cover;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        .container {
            max-width: 100%;
        }
        @media (min-width: 992px) {
            .container {
                max-width: 50%;
            }
        }
        .card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }
        @media (max-width: 767px) {
            body {
                padding: 10px;
            }
            .card {
                margin: 10px;
            }
        }
        .logo {
            max-width: 100px;
            height: auto;
        }
        h1 {
            color: #e10808;
            font-size: 2.5rem;
        }
        h3 {
            color: #000;
            font-size: 1.5rem;
        }
        .form-control, .form-select {
            border-color: #FF0000;
        }
        .btn-primary {
            background-color: #FF0000;
            border-color: #FF0000;
        }
        .btn-primary:hover {
            background-color: #000000;
            border-color: #000000;
        }
        .btn-secondary {
            background-color: #000000;
            border-color: #000000;
        }
        .btn-secondary:hover {
            background-color: #e10808;
            border-color: #e10808;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .form-control[type="date"],
        .form-control[type="time"] {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            padding: 0.375rem 0.75rem;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="header">
                <img src="../images/back7.png" alt="Logo" class="logo">
                <a href="daypass.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php elseif ($studentData): ?>
                <h1 class="text-center mb-4">Welcome, <?php echo getStudentData('student_name'); ?></h1>
                <h3 class="text-center mb-4">Fill the form for Day Outing</h3>
                
                <form method="POST" action="day_out_submisson.php">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="year" class="form-label"><i class="fas fa-calendar-alt"></i> Select Year:</label>
                            <select class="form-select" id="year" name="year" required>
                                <option value="1">Year 1</option>
                                <option value="2">Year 2</option>
                                <option value="3">Year 3</option>
                                <option value="4">Year 4</option>
                                <option value="5">Year 5</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="outdate" class="form-label"><i class="fas fa-calendar-plus"></i> Select Date:</label>
                            <input type="date" class="form-control" id="outdate" name="outdate" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="outime" class="form-label"><i class="fas fa-sign-out-alt"></i> Out Time:</label>
                            <input type="time" class="form-control" id="outime" name="outime" required>
                            <div id="out_time_error" class="text-danger"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="intime" class="form-label"><i class="fas fa-sign-in-alt"></i> In Time:</label>
                            <input type="time" class="form-control" id="intime" name="intime" required>
                            <div id="in_time_error" class="text-danger"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label"><i class="fas fa-comment-alt"></i> Reason:</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <h6>Note: Day Outing timings are from 8:00 AM to 9:00 PM. Day Outing requests will be rejected if the time exceeds these limits.</h6>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Time validation code
    const outTimeInput = document.getElementById('outime');
    const inTimeInput = document.getElementById('intime');
    const outTimeError = document.getElementById('out_time_error');
    const inTimeError = document.getElementById('in_time_error');
    const submitButton = document.querySelector('button[type="submit"]'); // Reference to the submit button

    function checkTime() {
        const outTime = outTimeInput.value;
        const inTime = inTimeInput.value;

        outTimeError.textContent = '';
        inTimeError.textContent = '';

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "check_time.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);

                if (!response.is_valid) {
                    // Show error messages and disable submit button
                    if (response.errors.out_time) {
                        outTimeError.textContent = response.errors.out_time;
                    }
                    if (response.errors.in_time) {
                        inTimeError.textContent = response.errors.in_time;
                    }
                    submitButton.disabled = true; // Disable the submit button
                } else {
                    // No validation errors, enable submit button
                    submitButton.disabled = false;
                }
            }
        };

        xhr.send(`out_time=${encodeURIComponent(outTime)}&in_time=${encodeURIComponent(inTime)}`);
    }

    outTimeInput.addEventListener('change', checkTime);
    inTimeInput.addEventListener('change', checkTime);

    // Date restriction code
    const dateInput = document.getElementById('outdate');
    
    // Get current date and tomorrow's date
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    // Format dates as YYYY-MM-DD
    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    
    // Set min and max dates
    dateInput.min = formatDate(today);
    dateInput.max = formatDate(tomorrow);
    
    // Set default value to today
    dateInput.value = formatDate(today);
    
    // Add event listener to prevent manual entry of other dates
    dateInput.addEventListener('input', function() {
        const selectedDate = new Date(this.value);
        if (selectedDate < today || selectedDate > tomorrow) {
            this.value = formatDate(today);
        }
    });

    // Form submission handler
    document.querySelector('form').addEventListener('submit', function (e) {
        e.preventDefault();
        checkTime(); 

        if (!outTimeError.textContent && !inTimeError.textContent) {
            this.submit(); 
        }
    });
});

</script>
</body>
</html>
