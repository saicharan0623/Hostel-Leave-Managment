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
    if ($action === 'late Return') {
        // Process the form data
        $comments = $_POST['comments'];
        $submissionTime = $_POST['submission_time'];
        // Create a database connection
        $conn = new mysqli($hostname, $username, $password, $database);

        // Check for a successful database connection
        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }

        // Prepare and execute an SQL insert statement
        $sql = "INSERT INTO late_outing (student_name, student_id, email, department, batch, phone, gender,submission_time, comments)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssss", $studentData['student_name'], $studentData['student_id'], $studentData['email'], $studentData['department'], $studentData['batch'], $studentData['phone'], $studentData['gender'],$submissionTime, $comments);

        if ($stmt->execute()) {
            // Data inserted successfully
            // You can redirect to a success page or display a confirmation message
            header("Location: late_outing_success.php");
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
    <title>Late Entry Form</title>
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
            font-size: 14px;
            margin-top: 10px;
            margin-bottom: 10px;
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
            flex-wrap: wrap;
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

        /* Adjustments for mobile responsiveness */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            h1, h2 {
                font-size: 24px;
                margin-bottom: 10px;
            }

            input[type="text"],
            input[type="number"],
            input[type="date"],
            input[type="time"],
            select,
            textarea {
                font-size: 14px;
            }
        }
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            h1, h2 {
                font-size: 24px;
                margin-bottom: 10px;
            }

            input[type="text"],
            input[type="number"],
            input[type="date"],
            input[type="time"],
            select,
            textarea {
                font-size: 14px;
            }
        }

        /* Use Flexbox to arrange input fields in two columns per row for all screen sizes */
        .input-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .input-column {
            flex: 1;
            margin-right: 10px;
            width: calc(50% - 5px);
        }

        .input-column:last-child {
            margin-right: 0;
        }
    </style>
</head>
<body>
<div class="container">
    <img src="images/back7.png" alt="Logo" class="logo">
    <h1>Welcome, <?php echo $studentData['student_name']; ?></h1>

    <h2>Late Entry Form</h2>
    <form method="POST" action="late_outing_submission.php">
        <input type="hidden" name="student_id" value="<?php echo $studentData['id']; ?>">
        <input type="hidden" name="student_name" value="<?php echo $studentData['student_name']; ?>">
        <input type="hidden" name="student_email" value="<?php echo $studentData['email']; ?>">
        <input type="hidden" name="action" value="Inform Return">
        <div class="input-row">
            <div class="input-column">
                <label for="id" class="input-label">ID:</label>
                <input type="text" value="<?php echo $studentData['student_id']; ?>" name="id" required class="input-field">
            </div>
            <div class="input-column">
                <label for="name" class="input-label">Name:</label>
                <input type="text" value="<?php echo $studentData['student_name']; ?>" name="name" required class="input-field">
            </div>
        </div>
        <div class="input-row">
            <div class="input-column">
                <label for="gender">Gender:</label>
                <input type="text" value="<?php echo $studentData['gender']; ?>" name="gender" required class="input-field">
            </div>
            <div class="input-column">
                <label for="school" class="input-label">School:</label>
                <input type="text" value="<?php echo $studentData['department']; ?>" name="school" required class="input-field">
            </div>
        </div>
        <div class="input-row">
            <div class="input-column">
                <label for="year" class="input-label">Select Year:</label>
                <select name="year" required class="input-field">
                    <option value="1st year">1st year</option>
                    <option value="2nd year">2nd year</option>
                    <option value="3rd year">3rd year</option>
                    <option value="4th year">4th year</option>
                    <option value="5th year">5th year</option>
                </select>
            </div>
            <div class="input-column">
                <label for="mobile" class="input-label">Mobile:</label>
                <input type="number" value="<?php echo $studentData['phone']; ?>" name="mobile" required class="input-field">
            </div>
        </div>
        <div class="input-row">
            <div class="input-column">
                <label for="comments">Reason For late:</label>
                <textarea name="comments" rows="4" required class="input-field" style="height: auto;"></textarea>
            </div>
        </div>
        <input type="hidden" name="submission_time" id="submission_time" value="submission_time">
        <input type="submit" value="Inform Return">
        <h4>Note: Fill out this form once you enter the campus after Outing Times.</h4>
    </form>
    <div class="options">
        <a href="student_dashboard.php">Back</a>
    </div>
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
<?php include 'footer.php'; ?>
</body>
</html>
