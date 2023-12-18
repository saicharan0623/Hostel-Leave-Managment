<?php
session_start();
if (isset($_SESSION['imageUrl']) && !empty($_SESSION['imageUrl'])){
  $_SESSION['imageUrl'] = 'xx';
}


require '../vendor/autoload.php'; // Include the autoloader

use PhpOffice\PhpSpreadsheet\IOFactory;

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
        $studentData = [
            'id' => $row - 1, // Assuming your IDs start from 1
            'student_name' => $worksheet->getCell('C' . $row)->getValue(),
            'student_id' => $worksheet->getCell('D' . $row)->getValue(),
            'email' => $email,
            'department' => $worksheet->getCell('E' . $row)->getValue(),
            'batch' => $worksheet->getCell('G' . $row)->getValue(),
            'phone' => $worksheet->getCell('F' . $row)->getValue(),
            'gender'=> $worksheet->getcell('H' . $row)->getvalue(),
            // Add more student data as needed
        ];
        break;
    }
}
// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Check the value of the submitted action
  $action = isset($_POST['action']) ? $_POST['action'] : '';

  // If "History/Status" button was clicked, redirect to history.php
  if ($action === 'History/Status') {
      header("Location: history.php");
      exit();
  }

  // Process other form actions (e.g., submitting leave application)
  // ...
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Apply Leave</title>
  <!-- <link rel="stylesheet" type="text/css" href="style.css"> -->
  <style>
  /* Reset some default styles */
  /* Reset some default styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Remove underlines from links */
a {
    text-decoration: none;
}

/* Menu styles */
.menu {
    text-align: center;
    margin-bottom: 20px;
}

.menu a {
    display: inline-block;
    padding: 10px 20px;
    background-color: #000000;
    color: #fff;
    text-decoration: none;
    margin-right: 10px;
}

.menu a:hover {
    background-color: #e60505;
}

/* Body styles */
body {
    margin: 0;
    background-image: url("images/back4.jpg");
    background-size: cover;
    background-repeat: no-repeat;
    font-family: Arial, sans-serif;
}

/* Container styles */
.container {
    max-width: 600px; /* Increase container width */
    margin: 0 auto;
    padding: 20px;
    border-radius: 10px;
    margin-top: 10px;
    position: relative;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}

/* Logo styles */
.logo {
    max-width: 100%;
    height: auto;
    display: block;
    margin: 0 auto;
    }
    
/* Date styles */
.date {
    position: absolute;
    top: 10px;
    right: 10px;
    color: #000000;
}

/* Heading styles */
h1 {
    font-size: 30px;
    color: #e10808;
    margin-bottom: 20px;
    text-align: center;
}

/* Label styles */
label {
    display: block;
    font-weight: bold;
    margin-bottom: 10px;
}

/* Input field styles */
input[type="text"],
input[type="number"],
input[type="date"],
select[name="year"],
input[type="file"],
select[name="gender"],
input[type="time"],
textarea {
    width: 100%;
    padding: 10px;
    border: 2px solid #FF0000;
    border-radius: 5px;
    margin-bottom: 10px;
    font-size: 16px;
}

input[name="reason"],
textarea {
    width: 100%;
    padding: 20px;
    border: 2px solid #FF0000;
    border-radius: 5px;
    margin-bottom: 10px;
    font-size: 16px;
    word-wrap: break-word; /* Text will overflow to the next line */
}


/* Dropdown option styles */
select[name="year"] option {
    color: red;
    background-color: white;
}

/* Focus styles for dropdown */
select[name="year"]:focus {
    outline: none;
    border-color: black;
}

/* Submit button styles */
input[type="submit"] {
    background-color: #FF0000;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 16px;
    margin-top: 10px;
    transition: background-color 0.3s ease, color 0.3s ease;
}

input[type="submit"]:hover {
    background-color: #000000;
}

/* Capture button styles */
.capture {
    background-color: #000000;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    align-items: center;
    position: center;
}

.capture:hover {
    background-color: #FF0000;
}

/* Custom radio button styles */
.custom-radio {
    display: inline-block;
    position: relative;
    padding-left: 30px;
    margin-right: 15px;
    cursor: pointer;
    font-size: 16px;
    user-select: none;
}

.custom-radio input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

.checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 20px;
    width: 20px;
    background-color: #eee;
    border-radius: 50%;
    border: 2px solid #ccc;
}

.custom-radio input:checked ~ .checkmark {
    background-color: #FF0000;
    border-color: #000000;
}

.checkmark:after {
    content: "";
    position: absolute;
    display: none;
}

.custom-radio input:checked ~ .checkmark:after {
    display: block;
}

.custom-radio .checkmark:after {
    left: 6px;
    top: 2px;
    width: 6px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

/* Media queries for responsive design */
@media (min-width: 320px) {
    /* Styles for small smartphones */
    .container {
        width: 100%;
    }
}

@media (min-width: 480px) {
    /* Styles for larger smartphones */
    .container {
        width: 80%;
    }
}

@media (min-width: 768px) {
    /* Styles for tablets */
    .container {
        width: 70%;
    }
}

@media (min-width: 992px) {
    /* Styles for laptops and desktops */
    .container {
        width: 60%;
    }
}

@media (min-width: 1200px) {
    /* Styles for large desktop screens */
    .container {
        width: 60%;
    }
}

/* Attachment button styles */
.attachment-button {
    background-color: black;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    display: inline-block;
    margin-top: 10px;
    text-align: center;
    text-decoration: none;
}

.attachment-button:hover {
    background-color: red;
}

/* Attachment input styles */
.attachment-input {
    display: none;
}

/* Attachment label styles */
.attachment-label {
    background-color: #FF0000;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    display: inline-block;
    margin-top: 10px;
    text-align: center;
    text-decoration: none;
}

.attachment-label:hover {
    background-color: #000000;
}

/* Camera styles */
#camera {
    width: 300px;
    height: 300px;
    object-fit: cover;
    border-radius: 100%;
    display: block;
    margin: 0 auto;
}

/* Preview image styles */
#preview {
    display: none;
    width: 320px;
    height: 240px;
    border: 2px solid #ccc;
    border-radius: 5px;
    margin: 10px auto;
}
.input-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.input-column {
    flex: 1;
    margin-right: 10px;
}

.input-column:last-child {
    margin-right: 0;
}
.apply-button {
        padding: 10px 10px;
        width:100%;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-size: 20px;
        background-color: #000000;
        margin-bottom: 5px;
        color: #fff;
        transition: background-color 0.3s ease-in-out;
      }

      .apply-button:hover {
        background-color: #fa0000;
      }
      .options {
     text-align: center;
     margin-top: 0;
    }
    .options a {
      display: block;
      color: white;
      font-size: 1.0rem;
      margin: 10px 0;
      text-decoration: none;
      display: inline-block;
      padding: 10px 20px;
      background-color: #000000;
      border-radius: 10px;
      margin-bottom: 5px;
    }

    .options a:hover {
      background-color: #e10808;
       color: #FFFFFF;
    }
        h2 {
            font-size:20px;
            color:red;
            margin-bottom:20px;
            margin-top:20px;
            text-align: center;
        }
</style>

</head>
<body>
  <div class="container">
    <img src="images/back7.png" alt="Logo" class="logo">

    <span class="date"><?php echo date("Y-m-d"); ?></span>
    <h1>Welcome, <?php echo $studentData['student_name']; ?></h1>
    <div id="options"> 
      <h1>Take Picture</h1>
    <video id="camera" autoplay></video>    
    <button class="capture" id="capture">Capture</button>
    <img id="preview" src="#" alt="Preview" style="display:none;">
    <script src="./script.js"></script>

      <form method="POST" action="apply_leave.php" onsubmit="return ValidateForm();">
        <input type="hidden" name="student_id" value="<?php echo $studentData['id']; ?>">
        <input type="hidden" name="student_name" value="<?php echo $studentData['student_name']; ?>">
        <input type="hidden" name="student_email" value="<?php echo $studentData['email']; ?>">
        <br>

        <label class="custom-radio">
        <input type="radio" name="academic" value="Academic" required class="input-field">
        <span class="checkmark"></span>
        WeekDays
    </label>
    <label class="custom-radio">
        <input type="radio" name="academic" value="Non-Academic" required class="input-field">
        <span class="checkmark"></span>
        Weekend/Holidays
    </label>
    <br><br>

</label>

<label for="id" class="input-label">ID:</label>
<input type="text" value="<?php echo $studentData['student_id']; ?>" name="id" required class="input-field">

<!-- <form action="#" method="post"> -->
<div class="input-row">
<div class="input-column">
<label for="name" class="input-label">Name:</label>
<input type="text" value="<?php echo $studentData['student_name']; ?>" name="name" required class="input-field">
</div>
<div class="input-column">
<label for="gender">Gender:</label>
<input type="text" value="<?php echo $studentData['gender']; ?>" name="gender" required class="input-field">
</div>
</div>

<!-- <form action="#" method="post"> -->
<div class="input-row">
  <div class="input-column">
    <label for="school" class="input-label">School:</label>
    <input type="text" value="<?php echo $studentData['department']; ?>" name="school" required class="input-field">
  </div>
  <div class="input-column">
    <label for="mobile" class="input-label">Mobile:</label>
    <input type="number" value="<?php echo $studentData['phone']; ?>" name="mobile" required class="input-field">
  </div>
</div>

<h2>Fill the Below Details</h2>

<div class="input-row">
  <div class="input-column">
    <label for="year" class="input-label">Select Year:</label>
    <select name="year" required class="input-field">
      <option value="1">Year 1</option>
      <option value="2">Year 2</option>
      <option value="3">Year 3</option>
      <option value="4">Year 4</option>
      <!-- Add more options as needed -->
    </select>
  </div>
  <div class="input-column">
    <label for="attendance" class="input-label">Attendance:</label>
    <input type="text" id="attendance" name="attendance" required class="input-field">
  </div>
</div>
<!-- <form action="#" method="post"> -->
<div class="input-row">
<div class="input-column">
<label for="from_date" class="input-label">From Date:</label>
<input type="date" name="from_date" required class="input-field">
</div>
<div class="input-column">
<label for="to_date" class="input-label">To Date:</label>
<input type="date" name="to_date" required class="input-field">
</div>
</div>

<!-- <form action="#" method="post"> -->
<div class="input-row">
    <div class="input-column">
        <label for="outime">Out Time:</label>
        <input type="time" id="outime" name="outime" required class="input-field">
    </div>
    <div class="input-column">
        <label for="intime">In Time:</label>
        <input type="time" id="intime" name="intime" required class="input-field">
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const outTimeInput = document.getElementById("outTime");
    const inTimeInput = document.getElementById("inTime");

    outTimeInput.addEventListener("change", function() {
        outTimeInput.value = formatTime(outTimeInput.value);
    });

    inTimeInput.addEventListener("change", function() {
        inTimeInput.value = formatTime(inTimeInput.value);
    });

    function formatTime(time) {
        const parts = time.split(":");
        if (parts.length === 2) {
            let hours = parseInt(parts[0]);
            let amOrPm = "AM";

            if (hours >= 12) {
                amOrPm = "PM";
                if (hours > 12) {
                    hours -= 12;
                }
            }

            const formattedTime = hours.toString().padStart(2, "0") + ":" + parts[1] + " " + amOrPm;

            return formattedTime;
        }
        return time; // Already in HH:MM AM/PM format
    }
});
</script>

<label for="reason" class="input-label">Reason:</label>
<input type="reason" name="reason" required class="input-field">
 
        <!-- <input type="text" value="<?php echo $_SESSION["imageUrl"] ?>" name="imageUrl"> -->
       
        <?php if ($_SESSION["imageUrl"] === 'Please take a picture for submitting.'): ?>
          <p style="font-weight:bold">Please take a picture for submitting.</p>
        
    <?php else: ?>
      <input type="submit" value="Submit">
    <?php endif; ?>
    <div class="options">
    <a href="student_dashboard.php">Back</a>
</div>
    </form>
    </div>
  </div>
  <?php include 'footer.php'; ?>
</body>
</html>
