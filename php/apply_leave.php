<?php
session_start();
require 'database_config.php';

// Check if student_id is set in the session
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login_page.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$studentData = null;

// Query the students table using student_id
$stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = :student_id");
$stmt->execute(['student_id' => $student_id]);
$studentData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$studentData) {
    header("Location: error.php?message=No student data found");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply Leave</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
        }
        h2 {
            color: #e10808;
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
        .custom-radio .form-check-input:checked {
            background-color: #FF0000;
            border-color: #000000;
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
        .leave-type-label {
            font-weight: bold;
            margin-bottom: 10px;
            color: #e10808;
        }
        .custom-radio .form-check-input {
            width: 1.5em;
            height: 1.5em;
            margin-top: 0.25em;
            vertical-align: top;
            background-color: #fff;
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            border: 1px solid rgba(0,0,0,.25);
            appearance: none;
        }
        .custom-radio .form-check-input:checked {
            background-color: #e10808;
            border-color: #e10808;
        }
        .custom-radio .form-check-label {
            padding-left: 0.5em;
        }
/* Basic container styles */
.flatpickr {
    font-family: 'Arial', sans-serif; /* Change font family */
    border-radius: 8px; /* Rounded corners */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Subtle shadow */
}

/* Header styles */
.flatpickr-header {
    background-color: white; /* White background for header */
    color: #333; /* Default text color */
    border-top-left-radius: 8px; /* Rounded top left corner */
    border-top-right-radius: 8px; /* Rounded top right corner */
}

/* Current month styles */
.flatpickr-current-month {
    font-weight: bold; /* Make current month bold */
}

/* Day styles */
.flatpickr-day {
    border-radius: 6px; /* Rounded corners for days */
    transition: border-color 0.3s ease; /* Smooth transition */
    color: #333; /* Default day text color */
    background-color: transparent; /* Ensure no background color */
}

/* Hover effect for days */
.flatpickr-day:hover {
    border: 2px solid red; /* Red border on hover */
}

/* Selected date styles */
.flatpickr-day.selected-date {
    border: 2px solid green !important; /* Green border for selected date */
    background-color: #fff !important; /* Remove any background color */
}

/* Today’s date styles */
.flatpickr-day.today {
    border: 2px solid red; /* Red border around today's date */
    color: green; /* Today's date text color */
}

/* Disabled dates */
.flatpickr-day.disabled {
    color: #ccc; /* Gray text for disabled */
}

/* Weekend styles */
.flatpickr-day.weekend {
    color: #333; /* Default text color for weekend days */
}


.flatpickr-prev-month, .flatpickr-next-month {
    color: #FF0000; 
    font-weight: bold; 
}

.flatpickr-monthDropdown-months,
.flatpickr-yearDropdown {
    background-color: white;
    color: #333; 
    border: 1px solid red; 
    border-radius: 4px; 
}


.flatpickr-input {
    border: 1px solid red; 
    border-radius: 4px; 
    padding: 10px; 
}

.flatpickr-clear {
    color: #333; 
    transition: color 0.3s ease; 
}

.flatpickr-clear:hover {
    color: red;
}

.flatpickr-confirm {
    background-color: white;
    color: #333;
    border: 2px solid red; 
    border-radius: 4px;
    padding: 5px 10px; 
}

.flatpickr-confirm:hover {
    border-color: green; /* Hover border for confirm button */
}

@media (max-width: 768px) {
    .flatpickr {
        font-size: 14px; /* Smaller font on mobile */
    }

    .flatpickr-day {
        padding: 5px; /* Smaller padding for days */
    }
}

    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="header">
                <img src="../images/back7.png" alt="Logo" class="logo">
                <a href="student_dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
            <h1 class="text-center mb-4">Welcome, <?php echo $studentData['student_name']; ?></h1>
            <h3 class="text-center mb-3"><i class="fas fa-clipboard-list"></i> Apply for Leave</h3>

            <form method="POST" action="leave_submission.php" onsubmit="return ValidateForm();">

                <!-- Hidden Fields -->
                <div class="row mb-3" style="display: none;">
                    <div class="col-md-6">
                        <label for="id" class="form-label"><i class="fas fa-id-card"></i> ID:</label>
                        <input type="text" class="form-control" id="id" name="student_id" value="<?php echo $studentData['student_id']; ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="name" class="form-label"><i class="fas fa-user"></i> Name:</label>
                        <input type="text" class="form-control" id="name" name="student_name" value="<?php echo $studentData['student_name']; ?>" readonly>
                    </div>
                </div>

                <div class="row mb-3" style="display: none;">
                    <div class="col-md-6">
                        <label for="gender" class="form-label"><i class="fas fa-venus-mars"></i> Gender:</label>
                        <input type="text" class="form-control" id="gender" name="gender" value="<?php echo $studentData['gender']; ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="school" class="form-label"><i class="fas fa-school"></i> School:</label>
                        <input type="text" class="form-control" id="school" name="school" value="<?php echo $studentData['department']; ?>" readonly>
                    </div>
                </div>

                <div class="mb-3" style="display: none;">
                    <label for="mobile" class="form-label"><i class="fas fa-mobile-alt"></i> Mobile:</label>
                    <input type="number" class="form-control" id="mobile" name="mobile" value="<?php echo $studentData['phone']; ?>" readonly>
                </div>

                <!-- Leave Type Dropdown -->
                <div class="mb-3">
                    <label for="leave_type" class="form-label"><i class="fas fa-clipboard-list"></i> Leave Type:</label>
                    <select class="form-select" id="leave_type" name="leave_type" required>
                        <option value="Academic">Academic - Hours</option>
                        <option value="Non-Academic">Non-Academic - Hours</option>
                    </select>
                </div>

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
                        <label for="attendance" class="form-label"><i class="fas fa-check-double"></i> Attendance:</label>
                        <input type="text" class="form-control" id="attendance" name="attendance" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="from_date" class="form-label"><i class="fas fa-calendar-plus"></i> From Date:</label>
                        <input type="text" class="form-control" id="from_date" name="from_date" placeholder="Select From Date" required>
                        <div id="from_date_error" class="text-danger"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="to_date" class="form-label"><i class="fas fa-calendar-minus"></i> To Date:</label>
                        <input type="date" class="form-control" id="to_date" name="to_date" placeholder="Select To Date" required>
                        <div id="to_date_error" class="text-danger"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="outime" class="form-label"><i class="fas fa-sign-out-alt"></i> Out Time:</label>
                        <input type="time" class="form-control" id="outime" name="outime" required>
                    </div>
                    <div class="col-md-6">
                        <label for="intime" class="form-label"><i class="fas fa-sign-in-alt"></i> In Time:</label>
                        <input type="time" class="form-control" id="intime" name="intime" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="roomno" class="form-label"><i class="fas fa-door-open"></i> Room:</label>
                    <input type="text" class="form-control" id="roomno" name="roomno" required>
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label"><i class="fas fa-comment-alt"></i> Reason:</label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const fromDateInput = document.getElementById('from_date');
        const toDateInput = document.getElementById('to_date');
        const fromDateError = document.getElementById('from_date_error');
        const toDateError = document.getElementById('to_date_error');

        // Initialize Flatpickr for "From Date"
        const fromDatePicker = flatpickr(fromDateInput, {
            dateFormat: "Y-m-d",
            minDate: "today",
            maxDate: new Date().fp_incr(30), // 30 days from today
            onChange: function (selectedDates, dateStr) {
                // Set minDate for "To Date" as the day after "From Date"
                toDatePicker.set('minDate', new Date(selectedDates[0]).fp_incr(1));
                highlightSelectedDateInToPicker(dateStr); // Highlight selected "From Date" in "To Date" picker
                validateDates(); // Validate dates
            }
        });

        // Initialize Flatpickr for "To Date"
        const toDatePicker = flatpickr(toDateInput, {
            dateFormat: "Y-m-d",
            minDate: "today",
            maxDate: new Date().fp_incr(30),
            onChange: function () {
                validateDates(); // Validate dates
            }
        });

        // Validate that "To Date" is after "From Date"
        function validateDates() {
            const fromDate = new Date(fromDateInput.value);
            const toDate = new Date(toDateInput.value);

            if (toDate && fromDate && toDate <= fromDate) {
                fromDateError.textContent = 'The "To Date" must be after the "From Date".';
                toDateError.textContent = 'The "To Date" must be after the "From Date".';
                toDateInput.value = ''; // Clear invalid "To Date"
            } else {
                fromDateError.textContent = '';
                toDateError.textContent = '';
            }

            // Check for overlapping leave dates via AJAX
            if (fromDate && toDate) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "check_leave_dates.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.overlap) {
                            fromDateError.textContent = response.message;
                            toDateError.textContent = response.message;
                        } else {
                            fromDateError.textContent = '';
                            toDateError.textContent = '';
                        }
                    }
                };
                const params = `from_date=${encodeURIComponent(fromDateInput.value)}&to_date=${encodeURIComponent(toDateInput.value)}`;
                xhr.send(params);
            }
        }

        // Function to highlight the selected "From Date" in the "To Date" calendar
        function highlightSelectedDateInToPicker(dateStr) {
            const selectedDate = new Date(dateStr);
            toDatePicker.set('onDayCreate', function (dObj, dStr, fp, dayElem) {
                const day = parseInt(dayElem.textContent);
                const dayDate = new Date(fp.currentYear, fp.currentMonth, day);

                // Apply custom style if the day matches the selected "From Date"
                if (selectedDate.getTime() === dayDate.getTime()) {
                    dayElem.style.backgroundColor = "#FF6F6F"; // Red background for the selected date
                    dayElem.style.color = "white"; // Ensure the text color is white
                }
            });
        }
    });

    // Script for formatting time input into 12-hour AM/PM format
    document.addEventListener("DOMContentLoaded", function () {
        const outTimeInput = document.getElementById("outTime");
        const inTimeInput = document.getElementById("inTime");

        outTimeInput.addEventListener("change", formatTimeHandler);
        inTimeInput.addEventListener("change", formatTimeHandler);

        function formatTimeHandler(event) {
            event.target.value = formatTime(event.target.value);
        }

        function formatTime(time) {
            const parts = time.split(":");
            if (parts.length === 2) {
                let hours = parseInt(parts[0]);
                let amOrPm = hours >= 12 ? "PM" : "AM";
                hours = hours % 12 || 12;
                return `${hours.toString().padStart(2, "0")}:${parts[1]} ${amOrPm}`;
            }
            return time;
        }
    });
</script>

</body>
</html>