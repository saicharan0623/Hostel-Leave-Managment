<?php
session_start();

require '../vendor/autoload.php'; // Include the autoloader

use PhpOffice\PhpSpreadsheet\IOFactory;

// Database configuration
$hostname = "localhost";
$username = "root";
$password = "";
$database = "college_db"; // Updated to the correct database name

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
        $studentData = array(
            'id' => $row - 1, // Assuming your IDs start from 1
            'student_name' => $worksheet->getCell('C' . $row)->getValue(),
            'student_id' => $worksheet->getCell('D' . $row)->getValue(),
            'email' => $email,
            'department' => $worksheet->getCell('E' . $row)->getValue(),
            'batch' => $worksheet->getCell('G' . $row)->getValue(),
            'phone' => $worksheet->getCell('F' . $row)->getValue(),
            'gender' => $worksheet->getCell('H' . $row)->getValue(),
        );
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

    // Process other form actions (e.g., submitting "Inform Return" form)
    if ($action === 'Inform Return') {
        // Process the form data
        $returnDate = $_POST['indate'];
        $returnTime = $_POST['intime'];
        $wayOfTransport = $_POST['way_of_transport'];
        $comments = $_POST['comments'];

        // Create a database connection
        $conn = new mysqli($hostname, $username, $password, $database);

        // Check for a successful database connection
        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }

        // Prepare and execute an SQL insert statement
        $sql = "INSERT INTO student_inform (student_name, student_id, email, department, batch, phone, gender, indate, intime, way_of_transport, comments)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssss", $studentData['student_name'], $studentData['student_id'], $studentData['email'], $studentData['department'], $studentData['batch'], $studentData['phone'], $studentData['gender'], $returnDate, $returnTime, $wayOfTransport, $comments);

        if ($stmt->execute()) {
            // Data inserted successfully
            // You can redirect to a success page or display a confirmation message
            header("Location: inform_return_success.php");
            exit();
        } else {
            // Handle the case where data insertion fails
            echo "Error: " . $conn->error;
        }

        // Close the database connection
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Return Form</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
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
            max-width: 600px;
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
            text-align: right;
            color: #000000;
        }

        /* Heading styles */
        h1 {
            font-size: 30px;
            color: #e10808;
            margin-bottom: 20px;
            text-align: center;
        }
        
        h4 {
            font-size:14px;
            margin-top: 10px;
            margin-bottom:10px;
        }
        
        h2 {
            font-size: 30px;
            color: #000;
            margin-bottom: 10px;
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
        input[type="time"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #FF0000;
            border-radius: 5px;
            margin-bottom: 10px;
            font-size: 16px;
        }

        select {
            color: black;
            background-color: white;
        }

        select:focus {
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

        /* Student details styles */
        .student-details {
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .student-details h3 {
            font-size: 20px;
            color: #000000;
            margin-bottom: 10px;
        }

        .student-details p {
            font-size: 16px;
            color: #000000;
            margin-bottom: 5px;
        }

        .input-row {
            display: flex;
            flex-wrap: wrap; /* Allow columns to wrap on small screens */
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .input-column {
            flex: 1;
            margin-right: 10px;
            width: 48%; /* Set a width for columns */
        }

        .input-column:last-child {
            margin-right: 0;
        }

        @media (max-width: 768px) {
            /* Adjust styling for smaller screens */
            .container {
                padding: 10px;
            }
            h1, h2 {
                font-size: 24px;
                margin-bottom: 10px;
            }
            .input-column {
                width: 100%;
                margin-right: 0;
            }
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
    </style>
</head>
<body>
    <div class="container">
        <img src="images/back7.png" alt="Logo" class="logo">
        <h1>Welcome, <?php echo $studentData['student_name']; ?></h1>

        <h2>Inform Return</h2>
        <form method="POST" action="inform_process_submission.php">
            <input type="hidden" name="student_id" value="<?php echo $studentData['id']; ?>">
            <input type="hidden" name="student_name" value="<?php echo $studentData['student_name']; ?>">
            <input type="hidden" name="student_email" value="<?php echo $studentData['email']; ?>">
            <input type="hidden" name="action" value="Inform Return">

            <label for="id" class="input-label">ID:</label>
            <input type="text" value="<?php echo $studentData['student_id']; ?>" name="id" required class="input-field">

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

            <div class="input-row">
                <div class="input-column">
                    <label for="school" class="input-label">School:</label>
                    <input type="text" value="<?php echo $studentData['department']; ?>" name="school" required class="input-field">
                </div>
                <div class="input-column">
                    <label for="year" class "input-label">Year:</label>
                    <select name="year" required class="input-field">
                        <option value="1st year">1st year</option>
                        <option value="2nd year">2nd year</option>
                        <option value="3rd year">3rd year</option>
                        <option value="4th year">4th year</option>
                        <option value="5th year">5th year</option>
                    </select>
                </div>
            </div>

            <label for="mobile" class="input-label">Mobile:</label>
            <input type="number" value="<?php echo $studentData['phone']; ?>" name="mobile" required class="input-field">

            <label for="indate">Indate:</label>
            <input type="date" name="indate" required class="input-field" value="<?php echo date("Y-m-d"); ?>">
            
            <div class="input-row">
                <div class="input-column">
                    <label for="hour">Intime:</label>
                    <select name="hour" id="hour" required class="input-field">
                        <?php
                        for ($h = 1; $h <= 12; $h++) {
                            $hour = str_pad($h, 2, "0", STR_PAD_LEFT); // Ensure two-digit format
                            echo '<option value="' . $hour . '">' . $hour . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="input-column">
                    <label for="minute">Intime:</label>
                    <select name="minute" id="minute" required class="input-field">
                        <?php
                        for ($m = 0; $m < 60; $m += 15) {
                            $minute = str_pad($m, 2, "0", STR_PAD_LEFT); // Ensure two-digit format
                            echo '<option value="' . $minute . '">' . $minute . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="input-column">
                    <label for="ampm">Intime:</label>
                    <select name="ampm" id="ampm" required class="input-field">
                        <option value="AM">AM</option>
                        <option value="PM">PM</option>
                    </select>
                </div>
            </div>

            <label for="way_of_transport">Way of Transport:</label>
            <select name="way_of_transport" required class="input-field">
                <option value="Car">Car</option>
                <option value="Bus">Bus</option>
                <option value="Flight">Flight</option>
                <option value="Public Transport">Public Transport</option>
                <option value="Other">Other</option>
            </select>

            <label for="comments">Comments (if any):</label>
            <textarea name="comments" rows="4" class="input-field"></textarea>

            <input type="submit" value="Inform Return">
            <h4>Note: Please be sure to fill out and submit this form prior to your arrival at the college. 
                Failure to do so may result in delays in your enrollment and orientation process. 
                Thank you for your prompt attention to this matter.
            </h4>
        </form>
        <div class="options">
            <a href="student_dashboard.php">Back</a>
        </div>
    </div>
    <script>
        <script>
function validateTimeSelection() {
    // Get the current time
    var currentTime = new Date();
    
    // Get the selected time
    var selectedHour = parseInt(document.getElementById("hour").value);
    var selectedMinute = parseInt(document.getElementById("minute").value);
    var selectedAMPM = document.getElementById("ampm").value;
    
    // Create a Date object with the selected time
    var selectedTime = new Date();
    selectedTime.setHours(selectedHour + (selectedAMPM === "PM" ? 12 : 0), selectedMinute, 0, 0);
    
    // Calculate the time difference in milliseconds
    var timeDifference = selectedTime - currentTime;
    
    // Convert the time difference to hours
    var hoursDifference = timeDifference / 3600000; // 1 hour = 3600000 milliseconds
    
    if (hoursDifference > 3) {
        alert("You can't select a time more than 3 hours in the future.");
        return false; // Prevent form submission
    }
    return true; // Allow form submission
}
</script>

    </script>
    <?php include 'footer.php'; ?>
</body>
</html>