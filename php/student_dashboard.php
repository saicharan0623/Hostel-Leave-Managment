<?php
session_start();
/* if (isset($_SESSION['imageUrl']) && !empty($_SESSION['imageUrl'])){
  $_SESSION['imageUrl'] = '';
} */


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

$hostname = "localhost";
$username = "root";
$password = "";
$database = "college_db";
$leaveDays = 0;

$mysqli = new mysqli($hostname, $username, $password, $database);

if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

$email = $_SESSION['email'];

// Query the database for approved leave applications and calculate the total leave days
//$query = "SELECT SUM(DATEDIFF(to_date, from_date)) as leaveDays FROM leave_applications WHERE email = '$email' AND status = 'APPROVED'";

//FOR TESTING
$query = "SELECT SUM(DATEDIFF(to_date, from_date)) as leaveDays FROM leave_applications WHERE email = '$email' AND status ='APPROVED'";

$result = $mysqli->query($query);
if ($result) {
    $row = $result->fetch_assoc();
    //$leaveDays = $row['leaveDays'];
    $leaveDays = intval($row['leaveDays']);
}

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
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <title>Student Dashboard</title>
    <style>
      /* ============================== MEDIA QUERIES =================================== */
      /* Font size for screens between 992px and 1200px */
      @media (min-width: 1201px) {
        .container {
          font-size: 18px;
        }
        .body {
          font-size: 26px;
          font-weight: bold;
        }
      }

      /* Font size for screens between 992px and 1200px */
      @media (min-width: 992px) and (max-width: 1200px) {
        .container {
          font-size: 15px;
        }
        .body {
          font-size: 24px;
        }
      }

      /* Font size for screens between 768px and 991px */
      @media (min-width: 768px) and (max-width: 991px) {
  .container {
    font-size: 17px;
  }
  .body {
    font-size: 18px;
  }
}


      /* Font size for screens narrower than 768px */
      @media (max-width: 767px) {
        .container {
          font-size: 12px;
        }
      
      }
      body {
      background-image: url("images/back4.jpg");
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
      margin: 0;
      padding:0;
      min-height:100vh;
      font-family: Arial, sans-serif;
      display:flex;
      flex-direction: column;
    }

      .container {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height:scroll;
      background-color: rgba(255, 255, 255, 0);
      animation: fade-in 1s ease-in-out;
      position: relative;
      padding: 20px;
      width:90%;
      margin: 0 auto;
    }
    a {
    text-decoration: none;
  }
    .menu {
        display: flex;
        align-items: center;
      }

      .menu a {
        display: inline-block;
        margin-top: 10px;
        margin-left: 10px;
        padding: 10px 23px;
        font-size: 20px;
        text-decoration: none;
        background-color: #000000;
        color: #fff;
        border-radius: 5px;
        transition: background-color 0.3s ease-in-out;
      }

      .menu a:hover {
        background-color: #f20707;
      }
      
      table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
      }

      th,
      td {
        padding: 10px;
        border: 1px solid #000000;
        text-align: left;
      }

      th {
        background-color: #e10808;
        color: #fff;
      }

      #apply-leave {
        text-align: center;
        margin-top: 30px;
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

	    h1 {
      font-size: 40px;
      color: #e10808;
      margin-bottom: 50px;
      margin-top: 80px;
      text-align: center;
    }
    .options {
     text-align: center;
     margin-top: 20px;
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
      margin-bottom: 50px;
    }

    .options a:hover {
      background-color: #e10808;
       color: #FFFFFF;
    }


    #student-info {
      padding: 15px;
      color: #e10808; /* White text color */
      border-radius: 10px;
      margin-top: 20px;
    }

    #student-info h2 {
      font-size: 20px;
      margin-bottom: 10px;
    }

     /* Position the logo in the top-left corner */
     .logo {
      position: absolute;
      top: -12px;
      left: -12px;
      margin: 10px;
    }

    .logo img {
      max-height: 70px;
    }

    /* Position the date in the top-right corner */
    .date {
      position: absolute;
      top: 10px;
      right: 10px;
      color: #000000; /* White text color */
      margin-top: 10px;
    }
	 label {
      display: block;
      font-weight: bold;
      margin-bottom: 10px;
    }
  
 /* Font size for screens between 992px and 1200px */
 @media (min-width: 1201px) {
            h1 {
                font-size: 26px;
                color: rgb(255, 0, 0);
            }

            .date {
                font-size: 24px;
                color: rgb(0, 0, 0);
                margin-bottom: 20px;
            }
            .logo img {
            max-width: 150px;
            }
            table{
              width: 80%;
            }

        }

        /* Font size for screens between 992px and 1200px */
        @media (min-width: 992px) and (max-width: 1200px) {
            h1 {
                font-size: 24px;
                color: rgb(255, 0, 0);
            }

            .date {
                font-size: 22px;
                color: rgb(0, 0, 0);
                margin-bottom: 20px;
            }
            .logo img {
            max-width: 150px;
            }
            table{
              width: 76%;
            }
        }

        /* Font size for screens between 768px and 991px */
        @media (min-width: 768px) and (max-width: 991px) {
            h1 {
                font-size: 22px;
                color: rgb(255, 0, 0);
            }

            .date {
                font-size: 20px;
                color: rgb(0, 0, 0);
                margin-bottom: 20px;
            }
            .logo img {
            max-width: 70px;
            }
            table{
              width:50%;
            }
        }

        /* Font size for screens narrower than 768px */
        @media (max-width: 767px) {
            h1 {
                font-size: 20px;
                color: rgb(255, 0, 0);
            }

            .date {
                font-size: 16px;
                color: rgb(0, 0, 0);
                margin-bottom: 20px;
            }
            .logo img {
            max-width: 120px;
            }
            table{
              width: 100%;
            }
        }
    
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
  <link rel="stylesheet" type="text/css" href="../css/animate.css" />
</head>
<body>
  <div class="container">
    <div class="logo">
    <img src="images/back7.png" alt="Logo" width="200px" height="155px">
      </div>
    <span class="date">
      <script>
        var currentDate = new Date();
        var day = currentDate.getDate();
        var monthIndex = currentDate.getMonth();
        var year = currentDate.getFullYear();
        var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        document.write(monthNames[monthIndex] + " " + day + ", " + year);
      </script>
    </span>
    <h1>Welcome, <?php echo $studentData['student_name']; ?></h1>
    <table>
    <thead>
          <tr>
            <th colspan="2">Student Details</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td><strong>Name:</strong></td>
          <td><?php echo $studentData['student_name']; ?></td>
        </tr>
        <tr>
          <td><strong>Student ID:</strong></td>
          <td><?php echo $studentData['student_id']; ?></td>
        </tr>
        <tr>
          <td><strong>Email:</strong></td>
          <td><?php echo $studentData['email']; ?></td>
        </tr>
        <tr>
          <td><strong>Department:</strong></td>
          <td><?php echo $studentData['department']; ?></td>
        </tr>
        <tr>
          <td><strong>Batch:</strong></td>
          <td><?php echo $studentData['batch']; ?></td>
        </tr>
        <tr>
          <td><strong>Phone:</strong></td>
          <td><?php echo $studentData['phone']; ?></td>
        </tr>
        <tr>
          <td><strong>Gender:</strong></td>
          <td><?php echo $studentData['gender']; ?></td>
        </tr>
        <tr>
         <td><strong>Leave Counts:</strong></td>
         <td><?php echo $leaveDays; ?></td>
        </tr>

      </tbody>  
      </table>

      <div class="options">
      <a href="leavebutton.php">Apply leave</a>
      <a href="inform.php">Return Form</a>
      <a href="statusHistory.php">Leave Status</a>
      <a href="late_outing.php">Late Entry</a>
      <a href="../public/student_login.html">Logout</a>
    </div>
    </div>
  </div>
  <?php include 'footer.php'; ?>
</body>
</html>
