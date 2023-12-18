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
  if ($finalPosition == 'Rector') {
    //$query = "SELECT * FROM leave_applications WHERE status = 'PENDING-WITH-RECTOR' order by name asc";
    $query = "SELECT * FROM leave_applications WHERE status = 'PENDING-WITH-RECTOR' order by created_at desc";
  } else {
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
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  
<style>
   @media screen and (max-width: 320px) {
    .menu {
      background-color: #000000;
      text-align: center;
      margin-bottom: 20px;
      margin-right: 5px;
      border-right: 1px solid #fff;
      padding: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .menu a {
      display: inline-block;
      color: #fff;
      text-decoration: none;
      margin-right: 10px;
      text-align: center;
      font-size: 12px;
    }

    .menu:hover {
      background-color: #e60505;
    }
    /* =============== SEARCH REALTED ================== */
    /* Style for the search input */
    .search-container input[type="text"] {
      flex-grow: 1;
      padding: 5px;/* THIS */
      max-width: 100%;
      border: none;
      outline: none;
      transition: background-color 0.3s ease-in-out;
    }

    /* Hover effect for the search input */
    .search-container input[type="text"]:hover {
      background-color: #f9f9f9;
      /* Change the background color on hover */
    }
  /* Your CSS styles for mobile devices here */
  .search-container button[type="submit"] {
      background-color: #000000;
      color: #fff;
      border: none;
      cursor: pointer;
      padding: 8px 10px;
      transition: background-color 0.3s ease-in-out;
    }

    /* Hover effect for the search button */
    .search-container button[type="submit"]:hover {
      background-color: #333333;
      /* Change the background color on hover */
    }

    /* Style for the search icon */
    .search-container button[type="submit"] i {
      font-size: 10px;
      color: red;
    }
  .search-text {
      padding: 3px;/* THIS */
      color: #333;
      /* Text color */
      font-weight: bold;      
    }
  .search-container {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
      background-color: #fff;
      border: 2px solid #000;
      border-radius: 5px;      
      overflow: hidden;
      max-width: 100%;/* THIS */
      font-size: 11px;/* THIS */
      transition: border-color 0.3s ease-in-out;
      position: relative;
      /* margin-left: 50px; */
    }
    /* =============== SEARCH REALTED ================== */
  span{
    font-size: 13px;
  }
  h1 {
        font-size: 22px;
      }
}
    @media screen and (min-width: 320px) and (max-width: 480px) {
      
      .menu {
      background-color: #000000;
      text-align: center;
      margin-bottom: 20px;
      margin-right: 5px;
      border-right: 1px solid #fff;
      padding: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .menu a {
      display: inline-block;
      color: #fff;
      text-decoration: none;
      margin-right: 10px;
      text-align: center;
      font-size: 15px;
    }

    .menu:hover {
      background-color: #e60505;
    }
      /* =============== SEARCH REALTED ================== */
    /* Style for the search input */
    .search-container input[type="text"] {
      flex-grow: 1;
      padding: 5px;/* THIS */
      max-width: 100%;
      border: none;
      outline: none;
      transition: background-color 0.3s ease-in-out;
    }

    /* Hover effect for the search input */
    .search-container input[type="text"]:hover {
      background-color: #f9f9f9;
      /* Change the background color on hover */
    }
  /* Your CSS styles for mobile devices here */
  .search-container button[type="submit"] {
      background-color: #000000;
      color: #fff;
      border: none;
      cursor: pointer;
      padding: 8px 10px;
      transition: background-color 0.3s ease-in-out;
    }

    /* Hover effect for the search button */
    .search-container button[type="submit"]:hover {
      background-color: #333333;
      /* Change the background color on hover */
    }

    /* Style for the search icon */
    .search-container button[type="submit"] i {
      font-size: 10px;
      color: red;
    }
  .search-text {
      padding: 3px;/* THIS */
      color: #333;
      /* Text color */
      font-weight: bold;      
    }
  .search-container {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
      background-color: #fff;
      border: 2px solid #000;
      border-radius: 5px;      
      overflow: hidden;
      max-width: 100%;/* THIS */
      font-size: 11px;/* THIS */
      transition: border-color 0.3s ease-in-out;
      position: relative;
      /* margin-left: 50px; */
    }
    /* =============== SEARCH REALTED ================== */
  /* Your CSS styles for mobile devices here */
  span{
    font-size: 13px;
  }
  h1{
    font-size: 25px!important;
      }
}

/* iPads and Tablets (481px - 768px) */
@media screen and (min-width: 481px) and (max-width: 768px) {

  .menu {
      background-color: #000000;
      text-align: center;
      margin-bottom: 20px;
      margin-right: 5px;
      border-right: 1px solid #fff;
      padding: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .menu a {
      display: inline-block;
      color: #fff;
      text-decoration: none;
      margin-right: 10px;
      text-align: center;
      font-size: 15px;
    }

    .menu:hover {
      background-color: #e60505;
    }
   /* =============== SEARCH REALTED ================== */
    /* Style for the search input */
    .search-container input[type="text"] {
      flex-grow: 1;
      padding: 8px;/* THIS */
      max-width: 100%;
      border: none;
      outline: none;
      transition: background-color 0.3s ease-in-out;
    }

    /* Hover effect for the search input */
    .search-container input[type="text"]:hover {
      background-color: #f9f9f9;
      /* Change the background color on hover */
    }
  /* Your CSS styles for mobile devices here */
  .search-container button[type="submit"] {
      background-color: #000000;
      color: #fff;
      border: none;
      cursor: pointer;
      padding: 8px 10px;
      transition: background-color 0.3s ease-in-out;
    }

    /* Hover effect for the search button */
    .search-container button[type="submit"]:hover {
      background-color: #333333;
      /* Change the background color on hover */
    }

    /* Style for the search icon */
    .search-container button[type="submit"] i {
      font-size: 15px;
      color: red;
    }
  .search-text {
      padding: 8px;/* THIS */
      color: #333;
      /* Text color */
      font-weight: bold;      
    }
  .search-container {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
      background-color: #fff;
      border: 2px solid #000;
      border-radius: 5px;      
      overflow: hidden;
      max-width: 65%;/* THIS */
      font-size: 11px;/* THIS */
      transition: border-color 0.3s ease-in-out;
      position: relative;
      /* margin-left: 50px; */
    }
    /* =============== SEARCH REALTED ================== */
  /* Your CSS styles for iPads and Tablets here */
  h1{
    font-size: 27px;
      }
}

/* Small screens and laptops (769px - 1024px) */
@media screen and (min-width: 769px) and (max-width: 1024px) {
  .menu {
      background-color: #000000;
      text-align: center;
      margin-bottom: 20px;
      margin-right: 5px;
      border-right: 1px solid #fff;
      padding: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .menu a {
      display: inline-block;
      color: #fff;
      text-decoration: none;
      margin-right: 10px;
      text-align: center;
      font-size: 16px;
    }

    .menu:hover {
      background-color: #e60505;
    }
   /* =============== SEARCH REALTED ================== */
    /* Style for the search input */
    .search-container input[type="text"] {
      flex-grow: 1;
      padding: 8px;/* THIS */
      max-width: 100%;
      border: none;
      outline: none;
      transition: background-color 0.3s ease-in-out;
    }

    /* Hover effect for the search input */
    .search-container input[type="text"]:hover {
      background-color: #f9f9f9;
      /* Change the background color on hover */
    }
  /* Your CSS styles for mobile devices here */
  .search-container button[type="submit"] {
      background-color: #000000;
      color: #fff;
      border: none;
      cursor: pointer;
      padding: 8px 10px;
      transition: background-color 0.3s ease-in-out;
    }

    /* Hover effect for the search button */
    .search-container button[type="submit"]:hover {
      background-color: #333333;
      /* Change the background color on hover */
    }

    /* Style for the search icon */
    .search-container button[type="submit"] i {
      font-size: 15px;
      color: red;
    }
  .search-text {
      padding: 8px;/* THIS */
      color: #333;
      /* Text color */
      font-weight: bold;      
    }
  .search-container {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
      background-color: #fff;
      border: 2px solid #000;
      border-radius: 5px;      
      overflow: hidden;
      max-width: 55%;/* THIS */
      font-size: 15px;/* THIS */
      transition: border-color 0.3s ease-in-out;
      position: relative;
      /* margin-left: 50px; */
    }
    /* =============== SEARCH REALTED ================== */
  /* Your CSS styles for small screens and laptops here */
  h1{
    font-size: 30px;
      }
}

/* Desktops and large screens (1025px - 1200px) */
@media screen and (min-width: 1025px) and (max-width: 1200px) {
  .menu {
      background-color: #000000;
      text-align: center;
      margin-bottom: 20px;
      margin-right: 5px;
      border-right: 1px solid #fff;
      padding: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .menu a {
      display: inline-block;
      color: #fff;
      text-decoration: none;
      margin-right: 10px;
      text-align: center;
      font-size: 16px;
    }

    .menu:hover {
      background-color: #e60505;
    }
   /* =============== SEARCH REALTED ================== */
    /* Style for the search input */
    .search-container input[type="text"] {
      flex-grow: 1;
      padding: 8px;/* THIS */
      max-width: 100%;
      border: none;
      outline: none;
      transition: background-color 0.3s ease-in-out;
    }

    /* Hover effect for the search input */
    .search-container input[type="text"]:hover {
      background-color: #f9f9f9;
      /* Change the background color on hover */
    }
  /* Your CSS styles for mobile devices here */
  .search-container button[type="submit"] {
      background-color: #000000;
      color: #fff;
      border: none;
      cursor: pointer;
      padding: 8px 10px;
      transition: background-color 0.3s ease-in-out;
    }

    /* Hover effect for the search button */
    .search-container button[type="submit"]:hover {
      background-color: #333333;
      /* Change the background color on hover */
    }

    /* Style for the search icon */
    .search-container button[type="submit"] i {
      font-size: 15px;
      color: red;
    }
  .search-text {
      padding: 8px;/* THIS */
      color: #333;
      /* Text color */
      font-weight: bold;      
    }
  .search-container {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
      background-color: #fff;
      border: 2px solid #000;
      border-radius: 5px;      
      overflow: hidden;
      max-width: 40%;/* THIS */
      font-size: 15px;/* THIS */
      transition: border-color 0.3s ease-in-out;
      position: relative;
      /* margin-left: 50px; */
    }
    /* =============== SEARCH REALTED ================== */
  /* Your CSS styles for desktops and large screens here */
  h1{
    font-size: 30px;
      }
}

/* Extra large screens and TV (1201px and more) */
@media screen and (min-width: 1201px) {
  .menu {
      background-color: #000000;
      text-align: center;
      margin-bottom: 20px;
      margin-right: 5px;
      border-right: 1px solid #fff;
      padding: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .menu a {
      display: inline-block;
      color: #fff;
      text-decoration: none;
      margin-right: 10px;
      text-align: center;
      font-size: 16px;
    }

    .menu:hover {
      background-color: #e60505;
    }
   /* =============== SEARCH REALTED ================== */
    /* Style for the search input */
    .search-container input[type="text"] {
      flex-grow: 1;
      padding: 8px;/* THIS */
      max-width: 100%;
      border: none;
      outline: none;
      transition: background-color 0.3s ease-in-out;
    }

    /* Hover effect for the search input */
    .search-container input[type="text"]:hover {
      background-color: #f9f9f9;
      /* Change the background color on hover */
    }
  /* Your CSS styles for mobile devices here */
  .search-container button[type="submit"] {
      background-color: #000000;
      color: #fff;
      border: none;
      cursor: pointer;
      padding: 8px 10px;
      transition: background-color 0.3s ease-in-out;
    }

    /* Hover effect for the search button */
    .search-container button[type="submit"]:hover {
      background-color: #333333;
      /* Change the background color on hover */
    }

    /* Style for the search icon */
    .search-container button[type="submit"] i {
      font-size: 15px;
      color: red;
    }
  .search-text {
      padding: 8px;/* THIS */
      color: #333;
      /* Text color */
      font-weight: bold;      
    }
  .search-container {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
      background-color: #fff;
      border: 2px solid #000;
      border-radius: 5px;      
      overflow: hidden;
      max-width: 40%;/* THIS */
      font-size: 15px;/* THIS */
      transition: border-color 0.3s ease-in-out;
      position: relative;
      /* margin-left: 50px; */
    }
    /* =============== SEARCH REALTED ================== */
  /* Your CSS styles for extra large screens and TV here */
  h1{
    font-size: 32px;
      }
}
    </style>

</head>

<body>
  <div class="container" style="margin-top: -60px;">
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-12 text-center text-lg-left" 
    style="margin-top: -20px;">
      <img src="images/back7.png" alt="Logo" width="200" height="100">
    </div>   
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-12 text-center text-lg-right" style="margin-top: -20px;">
    <div style="margin-top: 20px;" class="date" id="current-date"></div>
    </div>
  </div>
</div>
  <!-- <h1>Welcome, Admin</h1> -->
  <h1>Welcome
    <?php echo $finalPosition; ?>!
  </h1>

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
      th,
      td {
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
      th,
      td {
        font-size: 12px;
      }

    }

    /* Additional styles for even smaller screens (smartphones) */
    @media (max-width: 480px) {

      /* Further reduce font size */
      th,
      td {
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
      min-height: 200vh;
      /* Ensure the body takes up at least the full viewport height */
      display: flex;
      flex-direction: column;
    }

    .container1 {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
    }

    h1 {
      text-align: center;
      color: #e10808;
      /* font-size: 36px; */
      margin-bottom: 30px;
      position: relative;
    }

    /* Base table styles */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background-color: #fff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
      animation: fade-in 1s ease-in-out;
    }

    th,
    td {
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

    td.student-details,
    td.sap-id {
      background-color: #fff;
      color: #000;
    }

    td.status-approve {
      background-color: #ffffff;
      /* Green */
      color: #000000;
    }

    td.status-reject {
      background-color: #ffffff;
      /* Red */
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

    /* Responsive styles for smaller screens (tablets and smartphones) */
    @media (max-width: 767px) {
      table {
        font-size: 12px;
        /* Reduce font size for smaller screens */
      }

      th,
      td {
        padding: 8px;
        /* Adjust cell padding for smaller screens */
      }

      td {
        display: block;
        width: 50%;
      }

      td:before {
        content: attr(data-label);
        /* Use "data-label" attribute content as label for each cell */
        font-weight: bold;
        text-transform: uppercase;
      }
    }

    div1 {
      border: 1px solid red;
    }

    .date {
      top: 10px;
      right: 10px;
      font-size: 18px;
    }
   
    .logo img {
      max-height: 90px;
    }

    /* Media queries for different device widths */
    @media (min-width: 320px) {

      /* Styles for small smartphones */
      .table {
        width: 50%;
      }
    }

    @media (min-width: 480px) {

      /* Styles for larger smartphones */
      .container1 {
        width: 70%;
      }

      .table {
        width: 50%;
      }
    }

    @media (min-width: 768px) {

      /* Styles for tablets */
      .container1 {
        width: 70%;
      }

      .table {
        width: 70%;
      }
    }

    @media (min-width: 992px) {

      /* Styles for laptops and desktops */
      .table {
        width: 95%;
      }
    }

    @media (min-width: 1200px) {

      /* Styles for large desktop screens */
      .container1 {
        width: 60%;
      }

      .table {
        width: 100%;
      }
    }

    .footer {
      background-color: rgb(99, 102, 106);
      /* Background color in RGB */
      color: #fff;
      /* Text color */
      padding: 10px;
      /* Adjust padding as needed */
      position: fixed;
      bottom: 0;
      width: 100%;
      text-align: center;
    }

    /* Clear the body's margin to avoid overlap with the footer */
    body {
      margin: 0;
      padding-bottom: 60px;
      /* Adjust this value to match the footer's height */
    }

    /* =================== IMAGE RELATED ======================= */
    .imgx {
      width: 50px;
      height: 50px;
      background-size: cover;
      object-fit:
        position: relative;
      /* Needed for positioning the modal */
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

       /* Hover effect for the search container */
    .search-container:hover {
      border-color: #ff0000;
      /* Change the border color on hover */
    }

   

  </style>
  </head>

  <body>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <body style="margin-top: 80px">


      <!-- Menu -->
      <!-- <div class="menu">
    <a href="approved_leaves.php?finalPosition=<?= $finalPosition ?>">Approved Leaves</a>
    <a href="rejected_leaves.php?finalPosition=<?= $finalPosition ?>">Rejected Leaves</a>
    <a href="pending_leaves.php?finalPosition=<?= $finalPosition ?>">Pending Leaves</a>
    <?php
    if ($finalPosition === 'Rector') {
      echo '<a href="data_visualization.php?finalPosition=' . $finalPosition . '">School Wise Leaves</a>';
      echo '<a href="emergency_leaves.php?finalPosition=' . $finalPosition . '">Emergency Leaves</a>';
      echo '<a href="inform_details.php?finalPosition=' . $finalPosition . '">In Student Forms</a>';
      echo '<a href="late_outing_details.php?finalPosition=' . $finalPosition . '">Late Entry</a>';

    }
    ?>
  </div> -->


      <div class="container">
        <?php
        if ($finalPosition !== 'Rector') {
          echo '<div class="row justify-content-between">';

          echo '<div class="col-12 col-sm menu">';
          echo '<a href="approved_leaves.php?finalPosition=<?= $finalPosition ?>">Approved Leaves</a>';
          echo '</div>';
          echo '<div class="col-12 col-sm menu">';
          echo '<a href="rejected_leaves.php?finalPosition=<?= $finalPosition ?>">Rejected Leaves</a>';
          echo '</div>';
          echo '<div class="col-12 col-sm menu">';
          echo '<a href="pending_leaves.php?finalPosition=<?= $finalPosition ?>">Pending Leaves</a>';
          echo '</div>';

          echo '</div>';
        }
        if ($finalPosition === 'Rector') {
          echo '<div class="row justify-content-between">';

          echo '<div class="col-12 col-sm menu">';
          echo '<a href="approved_leaves.php?finalPosition=<?= $finalPosition ?>">Approved Leaves</a>';
          echo '</div>';
          echo '<div class="col-12 col-sm menu">';
          echo '<a href="rejected_leaves.php?finalPosition=<?= $finalPosition ?>">Rejected Leaves</a>';
          echo '</div>';
          echo '<div class="col-12 col-sm menu">';
          echo '<a href="pending_leaves.php?finalPosition=<?= $finalPosition ?>">Pending Leaves</a>';
          echo '</div>';
          
          echo '<div class="col-12 col-sm menu">';
          echo '<a href="data_visualization.php?finalPosition=' . $finalPosition . '">School Wise Leaves</a>';
          echo '</div>';
          echo '<div class="col-12 col-sm menu">';
          echo '<a href="emergency_leaves.php?finalPosition=' . $finalPosition . '">Emergency Leaves</a>';
          echo '</div>';
          echo '<div class="col-12 col-sm menu">';
          echo '<a href="inform_details.php?finalPosition=' . $finalPosition . '">In Student Forms</a>';
          echo '</div>';
          echo '<div class="col-12 col-sm menu">';
          echo '<a href="late_outing_details.php?finalPosition=' . $finalPosition . '">Late Entry</a>';
          echo '</div>';
          echo '</div>';
          
        }
        ?>
      </div>

      <!-- <form method="POST" action="admin_panel.php">
        <div class="search-container">
          <span class="search-text">Search</span>
          <input type="text" name="search" placeholder="Search by Student Name or SAP ID">
          <button type="submit"><i class="fa fa-search"></i></button>
        </div>
      </form> -->
      
      <form method="POST" action="admin_panel.php">
      <div class="container">
      <div class="row">
      <div class="col-12">
        <div class="search-container">         
          <span class="search-text">Search</span>
          <input type="text" name="search" placeholder="Search by Student Name or SAP ID">
          <button type="submit"><i class="fa fa-search"></i></button>
        </div>
        </div>
        </div>
      </div>
      </form>
     



      <!-- =========== IMAGE RELATED ============== -->
      <!-- <div class="enlarge-modal">
    <img class="enlarge-image" src="" alt="Enlarged Image" />
  </div> -->
      <!-- =========== IMAGE RELATED ============== -->

      <!-- Display a table of leave applications for the admin to manage -->
      <h1>Leave Applications</h1>
      <div class="table-responsive">

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
              <th>Leave Type</th>
              <th>In-Time</th>
              <th>Out-Time</th>
              <th>Image</th>
              <th>Attendance</th>
              <th>Approve</th>
              <th>Reject</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $serialNumber = 1;
            foreach ($results as $row) {
              // Check if finalPosition matches the school of the leave application
              if (
                ($finalPosition === 'STME' && $row['school'] !== 'STME') ||
                ($finalPosition === 'SPTM' && $row['school'] !== 'SPTM') ||
                ($finalPosition === 'SOL' && $row['school'] !== 'SOL') ||
                ($finalPosition === 'SBM' && $row['school'] !== 'SBM')
              ) {
                // Skip this row if the final position does not match the school
                continue;
              }
              echo '<tr>';
              echo '<td>' . $serialNumber++ . '</td>';
              echo '<td class="sap-id">' . $row['id'] . '</td>';
              echo '<td class="student-details">' . $row['name'] . '</td>';
              echo '<td class="student-details">' . $row['gender'] . '</td>';
              echo '<td class="student-details">' . $row['school'] . '</td>';
              echo '<td class="student-details">' . $row['year'] . '</td>';
              echo '<td class="student-details">' . $row['mobile'] . '</td>';
              echo '<td class="student-details">' . $row['from_date'] . '</td>';
              echo '<td class="student-details">' . $row['to_date'] . '</td>';
              echo '<td class="student-details">' . $row['reason'] . '</td>';
              echo '<td class="student-details">' . $row['academic'] . '</td>';
              echo '<td class="student-details">' . $row['intime'] . '</td>';
              echo '<td class="student-details">' . $row['outime'] . '</td>';
              echo '<td class="imgx enlarge-link" style="background-image: url(\'' . $row['imageUrl'] . '\');">';
              echo '<img class="imgxx" src="' . $row['imageUrl'] . '" alt="." />';
              echo '</td>';
              echo '<td class="student-details">'. $row['attendance'] . '</td>';
              echo '<td class="status-approve"><a href="approve.php?id=' . $row['id'] . '&created_at=' . $row['created_at'] . '&finalPosition=' . $finalPosition . '&email_student=' . $row['email'] . '&mobile=' . $row['mobile'] . '&name=' . $row['name'] . '&from_date=' . $row['from_date'] . '&to_date=' . $row['to_date'] . '&reason=' . $row['reason'] . '&attendance=' . $row['attendance'].'">Approve</a></td>';
              echo '<td class="status-reject">
          <a href="javascript:void(0);" onclick="showRejectionReasonForm(' . $row['id'] . ', \'' . $row['created_at'] . '\', \'' . $finalPosition . '\', \'' . $row['email'] . '\', \'' . $row['mobile'] . '\', \'' . $row['name'] . '\', \'' . $row['from_date'] . '\', \'' . $row['to_date'] . '\', \'' . $row['reason'] . '\', \'' . $row['attendance'] . '\')">Reject</a>
          </td>';
              echo '</tr>';
            }

            ?>
          </tbody>
        </table>
      </div>

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

        //======================Reject Reason ==================================
        function showRejectionReasonForm(id, created_at, finalPosition, email, mobile, name, from_date, to_date, reason) {
          var reject_reason = prompt("Enter the reason for rejection:");
          if (reject_reason !== null) {
            window.location.href = 'reject.php?id=' + id + '&created_at=' + created_at + '&finalPosition=' + encodeURIComponent(finalPosition) + '&email_student=' + encodeURIComponent(email) + '&mobile=' + encodeURIComponent(mobile) + '&name=' + encodeURIComponent(name) + '&from_date=' + encodeURIComponent(from_date) + '&to_date=' + encodeURIComponent(to_date) + '&reason=' + encodeURIComponent(reason) + '&reject_reason=' + encodeURIComponent(reject_reason);
          }
          //======================Reject Reason ==================================

        }
      </script>
      <script src="../js/bootstrap.min.js"></script>
    </body>
    <footer class="footer">
      <div class="container-fluid text-center">
        <span>&copy; MALDE SAICHARAN All rights reserved.</span>
      </div>
    </footer>

</html>