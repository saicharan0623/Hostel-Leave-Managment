<?php
// Start the session to manage user sessions
session_start();
$finalPosition = '';
if (isset($_GET['finalPosition'])) {
  $finalPosition = $_GET['finalPosition'];
}
// Include the autoloader to load the PhpSpreadsheet library
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Path to your Excel file
//$excelFilePath = '../admin.xlsx';
$excelFilePath = 'admin.xlsx';

// Get the email provided during login from the session
$email = $_SESSION['email'];

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
    $query = 'SELECT * FROM leave_applications where email="'.$email.'" order by created_at desc';
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
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Status/History</title>
  <style>
    /* Common styles for both desktop and mobile */
    /* Common styles for both desktop and mobile */
body {
  font-family: Arial, sans-serif;
  background-color: #f1f1f1;
  background-image: url("images/back4.jpg");
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
}

h1 {
  text-align: center;
  color: #e10808;
  font-size: 2.5rem;
  margin-bottom: 20px; /* Adjusted margin */
}

h2 {
  text-align: center;
  color: #000000;
  font-size: 2.0rem;
  margin-bottom: 20px; /* Adjusted margin */
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

/* Set background colors for even and odd rows within the table */
tr:nth-child(even) {
  background-color: #fff;
}

tr:nth-child(odd) {
  background-color: #f1f1f1;
}

/* Apply responsive styling for table cells with the class "student-details" */
td.student-details {
  background-color: #fff;
  color: #000;
}

td.sap-id {
  background-color: #fff;
  color: #000;
}

/* Apply responsive styling for links within table cells */
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

/* Keyframes animation for fade-in effect */
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

/* Center-align the menu and add responsive padding */
.menu {
  text-align: center;
  margin-bottom: 20px;
  padding: 10px; /* Added padding for mobile devices */
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

/* Add responsive font size for date */
.date {
  position: absolute;
  top: 10px;
  right: 10px;
  font-size: 16px; /* Adjusted font size for mobile devices */
}

/* Apply responsive styling for the logo image */
.logo {
  position: absolute;
  top: 10px; /* Adjusted top position for mobile devices */
  left: 10px; /* Adjusted left position for mobile devices */
  margin-left: 10px;
}

.logo img {
  max-height: 60px; /* Adjusted max height for mobile devices */
}

/* Apply responsive styles for image containers */
.imgx {
  width: 50px;
  height: 50px;
  background-size: cover;
  object-fit: cover;
  position: relative; /* Needed for positioning the modal */
  cursor: pointer;
}

.imgxx {
  width: 0px;
  height: 0px;
}

/* Apply responsive styles for the enlarge modal */
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

/* Apply responsive styles for enlarged images */
.enlarge-image {
  max-width: 90%;
  max-height: 90%;
}

/* Add responsive styling for small screens */
@media screen and (max-width: 600px) {
  h1 {
    font-size: 2rem; /* Decreased font size for smaller screens */
  }

  h2 {
    font-size: 1.5rem; /* Decreased font size for smaller screens */
  }

  .date {
    font-size: 14px; /* Decreased font size for smaller screens */
  }
}

/* Add responsive styling for extra small screens */
@media screen and (max-width: 400px) {
  h1 {
    font-size: 1.8rem; /* Further decreased font size for extra small screens */
  }

  h2 {
    font-size: 1.3rem; /* Further decreased font size for extra small screens */
  }

  .date {
    font-size: 12px; /* Further decreased font size for extra small screens */
  }

  .menu a {
    padding: 8px 16px; /* Adjusted padding for menu links on extra small screens */
  }
}

  </style>
</head>
<body>
  <div class="container">
  <div class="logo">
  <img src="images/back7.png" alt="Logo" class="logo">
  </div>
  </div>


  
  <!-- =========== IMAGE RELATED ============== -->
  <div class="enlarge-modal">
    <img class="enlarge-image" src="" alt="Enlarged Image" />
  </div>
  <!-- =========== IMAGE RELATED ============== -->

  <!-- Display a table of leave applications for the admin to manage -->
  <h1>Leave Status/History</h1>
  <h2><?php echo $email; ?></h2> 
  <table>
    <thead>
      <tr>
        <th>NO.</th>
        <th>Image</th>
        <th>Student Name</th>
        <th>School</th>
        <th>Mobile Number</th>
        <th>SAP ID</th>
        <th>From Date</th>
        <th>To Date</th>
        <th>Reason</th>
        <th>Applied Dt</th>
        <th>Type</th>
        <th>Status</th>       
      </tr>
    </thead>
    <tbody>    
  <?php 
  $rowNumber = 1; // Initialize the row number variable
  if (empty($results)): ?>
    <tr>
      <td colspan="12">No records found.</td> <!-- Updated colspan to match the number of columns -->
    </tr>
  <?php else: ?>
    <?php foreach ($results as $row): ?>
      <tr>
        <td><?= $rowNumber++; ?></td> <!-- Display and increment the row number -->
        <td class="imgx enlarge-link" style="background-image: url('<?= $row['imageUrl'] ?>');">    
          <img class="imgxx" src="<?= $row['imageUrl'] ?>" alt="image" />
        </td>
        <td class="student-details"><?= $row['name'] ?></td>
        <td class="student-details"><?= $row['school'] ?></td>
        <td class="student-details"><?= $row['mobile'] ?></td>
        <td class="student_details"><?= $row['id'] ?></td>
        <td class="student-details"><?= $row['from_date'] ?></td>
        <td class="student-details"><?= $row['to_date'] ?></td>
        <td class="student-details"><?= $row['reason'] ?></td>             
        <td class="student-details"><?= $row['created_at'] ?></td> 
        <td class="student-details"><?= $row['academic'] ?></td> 
        <td class="status-<?= strtolower($row['status']) ?>"><?= $row['status'] ?></td> 
      </tr>
    <?php endforeach; ?>
  <?php endif; ?>
</tbody>

  </table>

  <div class="container">
  <div class="date" id="current-date"></div>
  </div>
  <!-- Add any additional content or functionality for the admin panel -->

  <script>
    // JavaScript code to display the current date, day, and year
    var currentDate = new Date();
  var options = {year: 'numeric', month: 'long', day: 'numeric' };
  var formattedDate = currentDate.toLocaleDateString(undefined, options);
  document.getElementById('current-date').innerHTML = formattedDate;

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
