<?php
session_start();
require '../vendor/autoload.php';
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION["student_id"])) {
    header("Location: student_login_page.php");
    exit();
}

$student_id = $_SESSION["student_id"];

use PhpOffice\PhpSpreadsheet\IOFactory;
include 'database_config.php';

// Fetch student details
$query = "SELECT student_email, student_id, student_name, department, phone, gender FROM students WHERE student_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $studentData = $result->fetch_assoc();
} else {
    echo "No student found with this ID.";
    exit();
}

// Time calculations
$currentDateTime = new DateTime();
$expirationDateTime = clone $currentDateTime;
$expirationDateTime->modify('+1 hour');

$current_time = $currentDateTime->format('h:i A');
$expiration_time = $expirationDateTime->format('h:i A');

if ($expirationDateTime->format('Y-m-d') != $currentDateTime->format('Y-m-d')) {
    $expiration_time .= ' (Next Day)';
}

// Check approved leaves
$check_leaves_query = "SELECT from_date, to_date FROM leave_applications 
                      WHERE student_id = ? 
                      AND status = 'Approved'
                      AND (CURDATE() BETWEEN from_date AND to_date OR CURDATE() > to_date)
                      ORDER BY to_date DESC";

$stmt = $mysqli->prepare($check_leaves_query);
$stmt->bind_param("s", $studentData['student_id']);
$stmt->execute();
$result = $stmt->get_result();

$hasApprovedLeave = false;
$hasExpiredLeave = false;
$currentLeaves = [];
$expiredLeaves = [];
$leaveMessage = "";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        $fromDate = new DateTime($row['from_date']);
        $toDate = new DateTime($row['to_date']);
        
        if ($today <= $toDate) {
            $hasApprovedLeave = true;
            $currentLeaves[] = [
                'from_date' => $row['from_date'],
                'to_date' => $row['to_date']
            ];
        } else {
            $hasExpiredLeave = true;
            $expiredLeaves[] = [
                'from_date' => $row['from_date'],
                'to_date' => $row['to_date']
            ];
        }
    }

    if ($hasApprovedLeave) {
        if (count($currentLeaves) == 1) {
            $fromDate = date("M d, Y", strtotime($currentLeaves[0]['from_date']));
            $toDate = date("M d, Y", strtotime($currentLeaves[0]['to_date']));
            $leaveMessage = "You have an approved leave from $fromDate to $toDate";
        } else {
            $leaveMessage = "You have multiple approved leaves:";
            foreach ($currentLeaves as $leave) {
                $fromDate = date("M d, Y", strtotime($leave['from_date']));
                $toDate = date("M d, Y", strtotime($leave['to_date']));
                $leaveMessage .= "<br>- From $fromDate to $toDate";
            }
        }
    } elseif ($hasExpiredLeave) {
        $leaveMessage = "Your approved leave(s) have expired. You can submit a late entry:";
        foreach ($expiredLeaves as $leave) {
            $fromDate = date("M d, Y", strtotime($leave['from_date']));
            $toDate = date("M d, Y", strtotime($leave['to_date']));
            $leaveMessage .= "<br>- From $fromDate to $toDate";
        }
    }
} else {
    $leaveMessage = "You don't have any current or expired approved leaves.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Return Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
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
            flex-direction: column;
            min-height: 100vh;
            color: Black;
        }

        footer {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
            margin-top: auto;
        }

        table {
            border: 1px solid black !important;
        }

        table th, table td {
            border: 1px solid black !important;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            margin: 20px auto;
            max-width: 100%;
            color: black;
            padding: 20px;
        }

        @media (min-width: 992px) {
            .card {
                width: 60%;
                max-width: 800px;
                padding: 40px;
            }
        }

        @media (max-width: 768px) {
            .card {
                margin-left: 5px;
                margin-right: 5px;
                max-width: 90%;
            }
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeeba;
            color: #856404;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .full-width {
            width: 100%;
        }
    </style>
</head>
<body>
<div class="card my-4 mx-auto bg-light rounded shadow">
<div class="d-flex justify-content-between align-items-center mb-3">
        <div class="logo">
            <img src="../images/back7.png" alt="Logo" class="img-fluid" style="max-height: 60px;">
        </div>
        <a href="#" class="btn btn-dark" onclick="window.history.back(); return false;">Back</a>
    </div>
    
    <!-- Welcome Message -->
    <h2 class="mb-4 text-center">Welcome, <?php echo $studentData['student_name']; ?>!</h2>
    <h4 class="mb-4 text-center">Apply for In-Pass</h4>
    
    <!-- Leave Status Alert -->
    <div id="leaveStatusAlert" class="alert mb-4" role="alert"></div>

    <form method="POST" action="inpass_submission.php" onsubmit="return validateForm()">
        <input type="hidden" name="student_id" value="<?php echo $studentData['student_id']; ?>">
        <input type="hidden" name="student_name" value="<?php echo $studentData['student_name']; ?>">
        <input type="hidden" name="student_email" value="<?php echo $studentData['student_email']; ?>">
        <input type="hidden" name="action" value="Inform Return">

        <!-- Year and Transport Mode (Same Row) -->
        <div class="row g-3 mb-3 justify-content-center">
            <div class="col-md-6">
                <label for="year" class="form-label">Year:</label>
                <select name="year" class="form-control" required>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                    <option value="5">5th Year</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="way_of_transport" class="form-label">Transport Mode:</label>
                <select name="way_of_transport" class="form-control" required>
                    <option value="Car">Car</option>
                    <option value="Public Transport">Public Transport</option>
                    <option value="Fligh">Flight</option>
                </select>
            </div>
        </div>

        <div class="row g-3 mb-3 justify-content-center">
    <div class="col-md-6">
        <label for="in_time" class="form-label">In-Time:</label>
        <input type="time" name="in_time" id="in_time" class="form-control" value="<?php echo date('H:i'); ?>" required disabled>
        <small id="inTimeNote" class="form-text text-muted">Valid for 1 hour from the current time.</small>
    </div>
    <div class="col-md-6">
    <label for="indate" class="form-label">Return Date:</label>
    <?php $currentDate = date('Y-m-d'); ?>
    <input type="date" id="indate" name="indate" value="<?php echo $currentDate; ?>" class="form-control" min="<?php echo $currentDate; ?>" max="<?php echo $currentDate; ?>">
    </div>
</div>

<div class="row g-3 mb-3 justify-content-center">
    <div class="col-md-12">
        <label for="comments" class="form-label">Comments:</label>
        <textarea name="comments" id="comments" class="form-control" rows="4" required placeholder="If Nothing type N/A"></textarea>
    </div>
</div>


    <div id="lateEntryFields" style="display: none;" class="mb-4">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="late_days" class="form-label">Number of Late Days:</label>
                <input type="number" name="late_days" id="late_days" class="form-control" min="1">
                <div class="invalid-feedback">Please enter a valid number of late days.</div>
            </div>
            <div class="col-md-6">
                <label for="is_late_entry" class="form-label">Late Entry:</label>
                <select name="is_late_entry" id="is_late_entry" class="form-control" required disabled>
    <option value="yes" selected>Yes</option>
    <option value="no" disabled>No</option>
</select>

            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <label for="late_reason" class="form-label">Late Entry Reason:</label>
                <textarea name="late_reason" id="late_reason" class="form-control" rows="3"></textarea>
                <div class="invalid-feedback">Please provide a reason for late entry.</div>
            </div>
        </div>
    </div>
<div class="d-flex justify-content-center mt-4">
            <button type="submit" class="btn btn-primary">Submit Return</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var hasExpiredLeave = <?php echo json_encode($hasExpiredLeave); ?>;
    var lateEntryFields = document.getElementById('lateEntryFields');
    var lateDaysInput = document.getElementById('late_days');
    var lateReasonInput = document.getElementById('late_reason');
    var form = document.querySelector('form');
    var indateInput = document.getElementById('indate');
    
    // Function to calculate late days based on expired leave
    function calculateLateDays() {
        var lastExpiredLeave = <?php 
            echo !empty($expiredLeaves) ? 
                json_encode(end($expiredLeaves)['to_date']) : 
                'null'; 
        ?>;
        
        if (lastExpiredLeave) {
            var leaveEndDate = new Date(lastExpiredLeave);
            var selectedDate = new Date(indateInput.value);
            
            leaveEndDate.setHours(0,0,0,0); // Setting to midnight to avoid time difference
            selectedDate.setHours(0,0,0,0); // Setting to midnight to avoid time difference
            
            var diffTime = Math.abs(selectedDate - leaveEndDate); // Difference in time
            var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); // Convert to days
            
            lateDaysInput.value = diffDays; // Set the late days value
            lateDaysInput.readOnly = true; // Prevent manual input
        }
    }

    // Initialize late entry fields if expired leave exists
    if (hasExpiredLeave) {
        lateEntryFields.style.display = 'block';
        lateDaysInput.required = true;
        lateReasonInput.required = true;
        calculateLateDays();
    }

    // Update late days when return date changes
    indateInput.addEventListener('change', calculateLateDays);

    // Form validation on submit
    form.addEventListener('submit', function(event) {
        var isValid = true;

        if (hasExpiredLeave) {
            // Validate late days
            if (!lateDaysInput.value || lateDaysInput.value < 1) {
                lateDaysInput.classList.add('is-invalid');
                isValid = false;
            } else {
                lateDaysInput.classList.remove('is-invalid');
            }

            // Validate late reason
            if (!lateReasonInput.value.trim()) {
                lateReasonInput.classList.add('is-invalid');
                isValid = false;
            } else {
                lateReasonInput.classList.remove('is-invalid');
            }
        }

        // Validate required fields (in_time and indate)
        var inTime = document.querySelector('[name="in_time"]').value;
        var indate = indateInput.value;
        
        if (!inTime || !indate) {
            isValid = false;
            alert('Please fill in all required fields.');
        }

        // AJAX request to validate leave dates
        if (isValid) {
            var formData = new FormData(form);
            formData.append('action', 'check_leave_dates'); // Indicate the action for server-side logic
            
            fetch('check_leave_dates.php', { // AJAX request to server
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.overlap) {
                    alert(data.message); // Display overlap message
                    isValid = false; // Prevent form submission
                } else {
                    form.submit(); // Submit the form if validation is passed
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while checking leave dates.');
            });
        }

        // Prevent form submission if validation fails
        if (!isValid) {
            event.preventDefault();
        }
    });

    // Initialize leave status alert
    var leaveMessage = <?php echo json_encode($leaveMessage); ?>;
    var leaveStatusAlert = document.getElementById('leaveStatusAlert');
    
    if (hasExpiredLeave) {
        leaveStatusAlert.innerHTML = leaveMessage;
        leaveStatusAlert.classList.add('alert-warning');
    } else {
        leaveStatusAlert.innerHTML = leaveMessage;
        leaveStatusAlert.classList.add('alert-success');
    }

    // Prevent manual editing of late days field
    lateDaysInput.addEventListener('input', function(e) {
        if (hasExpiredLeave) {
            calculateLateDays();
        }
    });
});

</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>