<?php
// Start the session to manage user sessions
session_start();

// Include the autoloader to load the PhpSpreadsheet library
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Path to your Excel file
//$excelFilePath = '../admin.xlsx';
$excelFilePath = 'admin.xlsx';

// Get the email provided during login from the session
$email = $_SESSION['email'];
$finalPosition = '';
if (isset($_GET['finalPosition'])) {
  $finalPosition = $_GET['finalPosition'];
}

// Load the Excel file
try {
    $spreadsheet = IOFactory::load($excelFilePath);
    $worksheet = $spreadsheet->getActiveSheet();
    $highestRow = $worksheet->getHighestRow();
} catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
    die("Error loading Excel file: " . $e->getMessage());
}

// Check if the admin is logged in
if (!isset($_SESSION["email"])) {
    // If the admin is not logged in, redirect to the admin login page
    header("Location: admin_login.php");
    exit();
}

// If the admin is logged in, display the admin panel

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$hostname = "localhost";
$username = "root";
$password = "";
$database = "college_db";

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query to retrieve leave requests
    //$query = "SELECT * FROM leave_applications order by name asc";
    $query = '';
    if($finalPosition == 'Rector'){
      //$query = "SELECT * FROM leave_applications WHERE status = 'PENDING-WITH-RECTOR' order by name asc";
      $query = "SELECT * FROM leave_applications WHERE status = 'PENDING-WITH-RECTOR' order by created_at desc";
    }else{
      //$query = "SELECT * FROM leave_applications WHERE status = 'PENDING-WITH-ADMIN' order by name asc";
      $query = "SELECT * FROM leave_applications WHERE status = 'PENDING-WITH-ADMIN' order by created_at desc";
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    // Fetch results as associative array
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the PDO connection
$pdo = null;
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <!-- Add your CSS styles here -->
</head>
<body>
    <!-- <h1>Welcome, Admin!</h1> -->
    <h1>Welcome, <?php echo $finalPosition; ?>!</h1>
    
    <style>
      @media (min-width: 768px) {
  td.student-details,
  td.sap-id,
  td.leave-type,
  td.intime,
  td.outtime,
  td.image,
  td.approve,
  td.reject {
    display: table-cell;
  }

  /* Adjust font size for larger screens */
  th, td {
    font-size: 14px;
  }
}

/* Styles for smaller screens (tablets and smartphones) */
@media (max-width: 767px) {
  td.student-details,
  td.sap-id,
  td.leave-type,
  td.intime,
  td.outtime,
  td.image,
  td.approve,
  td.reject {
    display: none;
  }

  /* Display a button to show/hide additional information */
  td.more-info-button {
    display: table-cell;
  }

  /* Adjust font size for smaller screens */
  th, td {
    font-size: 12px;
  }
}

/* Additional styles for even smaller screens (smartphones) */
@media (max-width: 480px) {
  /* Further reduce font size */
  th, td {
    font-size: 10px;
  }
}
   body {
      margin: 0;
      padding: 0;
      background-image: url("images/back4.jpg");
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      font-family: Arial, sans-serif;
      min-height: 200vh; /* Ensure the body takes up at least the full viewport height */
      display: flex;
      flex-direction: column;
    }
    .container {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
    }
    h1 {
      text-align: center;
      color: #e10808;
      font-size: 36px;
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background-color: #fff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
      animation: fade-in 1s ease-in-out;
    }

    th, td {
      border: 2px solid #000000;
      padding: 10px;
      text-align: center;
    }

    th {
      background-color: #ff4444;
      color: #fff;
    }

    tr:nth-child(even) {
      background-color: #fff;
    }

    tr:nth-child(odd) {
      background-color: #f1f1f1;
    }

    td.student-details {
      background-color: #fff;
      color: #000;
    }

    td.sap-id {
      background-color: #fff;
      color: #000;
    }

    td.status-approve {
      background-color: #ffffff; /* Green */
      color: #000000;
    }

    td.status-reject {
      background-color: #ffffff; /* Red */
      color: #000000;
    }

    td a {
      display: block;
      padding: 10px;
      color: #000000;
      text-decoration: none;
      transition: background-color 0.3s ease, color 0.3s ease;
      height: 100%;
      width: 100%;
      box-sizing: border-box;
    }

    td.status-approve a:hover {
      background-color: #47ff04;
      color: #000000;
      background-size: auto;
    }

    td.status-reject a:hover {
      background-color: #ff0000;
      color: #000000;
      background-size: auto;
    }

    @keyframes fade-in {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

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

    .date {
      position: absolute;
      top: 10px;
      right: 10px;
      font-size: 18px;
    }

    
     .logo {
      position:absolute;
      top: 10px;
      left: 10px;
      margin-left: 10px;
    }

    .logo img {
      max-height: 90px;
    }
    /* .imgx{
    height:70px;
    width:70px;
    background-size: cover; 
    } */
/* Media queries for different device widths */
@media (min-width: 320px) {
  /* Styles for small smartphones */
  .box {
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
  
  .box {
    width: 50%;
    display: inline-block;
  }
}

@media (min-width: 992px) {
  /* Styles for laptops and desktops */
  .box {
    width: 30%;
  }
}

@media (min-width: 1200px) {
  /* Styles for large desktop screens */
  .container {
    width: 60%;
  }
}

  /* =================== IMAGE RELATED ======================= */
  .imgx {
    width: 50px;
    height: 50px;
    background-size: cover; 
    object-fit:
    position: relative; /* Needed for positioning the modal */
    cursor: pointer;    

  }
  .imgxx {
    width: 0px;
    height: 0px;
  }

  .enlarge-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    justify-content: center;
    align-items: center;
  }

  .enlarge-image {
    max-width: 90%;
    max-height: 90%;
  }
  .footer {
    background: rgb(99, 102, 106);
    text-align: center;
    padding: 10px 0;
    color: #fff;
  }
  </style>
</head>
<body>
  <!-- <h1>Welcome, Admin</h1> -->

  <body>
  <!-- Menu -->
  <div class="menu">
    <a href="approved_leaves.php?finalPosition=<?= $finalPosition ?>">Approved Leaves</a>
    <a href="rejected_leaves.php?finalPosition=<?= $finalPosition ?>">Rejected Leaves</a>
    <a href="pending_leaves.php?finalPosition=<?= $finalPosition ?>">Pending Leaves</a>
    <a href="data_visualization.php?finalPosition=<?= $finalPosition ?>">School Wise Leaves</a>
  </div>

  <!-- Logo -->
  <div class="logo">
    <img src="images/back7.png" alt="Logo" width="200" height="150">
  </div>

  <!-- =========== IMAGE RELATED ============== -->
  <div class="enlarge-modal">
    <img class="enlarge-image" src="" alt="Enlarged Image" />
  </div>
  <!-- =========== IMAGE RELATED ============== -->

  <!-- Display a table of leave applications for the admin to manage -->
  <h1>Leave Applications</h1>
  <table>
  <thead>
    <tr>
        <th>Sl.No</th>
        <th>SAP ID</th>
        <th>Student Name</th>
        <th>Gender</th>
        <th>School</th>
        <th>Year</th>
        <th>Mobile Number</th>
        <th>From Date</th>
        <th>To Date</th>
        <th>Reason</th>
        <th>Status</th>
        <th>Leave Type</th>
        <?php if ($finalPosition === 'Program Chair' || $finalPosition === 'Rector') : ?>
            <th>In-Time</th>
            <th>Out-Time</th>
        <?php endif; ?>
        <th>Image</th>
        <th>Approve</th>
        <th>Reject</th>
    </tr>
</thead>
    <tbody>
        <?php
        $serialNumber = 1;
        foreach ($results as $row) {
          echo '<tr>';
          echo '<td>' . $serialNumber++ . '</td>';
          echo '<td class="sap-id">' . $row['id'] . '</td>';
          echo '<td class="student-details">' . $row['name'] . '</td>';
          echo '<td class="student-details">' . $row['gender'] . '</td>';
          echo '<td class="student-details">' . $row['school'] . '</td>';
          echo '<td class="student-details">' . $row['academic'] . '</td>';
          echo '<td class="student-details">' . $row['mobile'] . '</td>';
          echo '<td class="student-details">' . $row['from_date'] . '</td>';
          echo '<td class="student-details">' . $row['to_date'] . '</td>';
          echo '<td class="student-details">' . $row['reason'] . '</td>';
          echo '<td class="status-' . strtolower($row['status']) . '">' . $row['status'] . '</td>';
          echo '<td class="student-details">' . $row['academic'] . '</td>';
          if ($finalPosition === 'Program Chair' || $finalPosition === 'Rector') {
              echo '<td class="student-details">' . $row['intime'] . '</td>';
              echo '<td class="student-details">' . $row['outime'] . '</td>';
          }
          echo '<td class="imgx enlarge-link" style="background-image: url(\'' . $row['imageUrl'] . '\');">';
          echo '<img class="imgxx" src="' . $row['imageUrl'] . '" alt="." />';
          echo '</td>';
          echo '<td class="status-approve"><a href="approve.php?id=' . $row['id'] . '&created_at=' . $row['created_at'] . '&finalPosition=' . $finalPosition . '&email_student=' . $row['email'] . '&mobile=' . $row['mobile'] . '&name=' . $row['name'] . '&from_date=' . $row['from_date'] . '&to_date=' . $row['to_date'] . '&reason=' . $row['reason'] . '">Approve</a></td>';
          echo '<td class="status-reject"><a href="reject.php?id=' . $row['id'] . '&created_at=' . $row['created_at'] . '&finalPosition=' . $finalPosition . '&email_student=' . $row['email'] . '&mobile=' . $row['mobile'] . '&name=' . $row['name'] . '&from_date=' . $row['from_date'] . '&to_date=' . $row['to_date'] . '&reason=' . $row['reason'] . '">Reject</a></td>';
          echo '</tr>';
      }
      
        ?>
    </tbody>
  </table>

  <!-- Add the present date, day, and year in the right corner of the page -->
  <div class="date" id="current-date"></div>

  <!-- Add any additional content or functionality for the admin panel -->

  <script>
    // JavaScript code to display the current date, day, and year
    var currentDate = new Date();
    var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('current-date').innerHTML = currentDate.toLocaleDateString(undefined, options);
   //==========================================================

   //================== IMAGE RELATED =====================================
    const enlargeLinks = document.querySelectorAll('.enlarge-link');
    const modal = document.querySelector('.enlarge-modal');
    const enlargeImage = modal.querySelector('.enlarge-image');

    enlargeLinks.forEach(link => {
    link.addEventListener('click', function (event) {
      event.preventDefault();
      const imageUrl = this.querySelector('img').getAttribute('src');
      enlargeImage.setAttribute('src', imageUrl);
      modal.style.display = 'flex';
    });
    });

    modal.addEventListener('click', function () {
    modal.style.display = 'none';
    });
    //================== IMAGE RELATED =====================================

  </script>
</body>
</html>
