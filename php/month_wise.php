<?php
// Include necessary files and start the session if needed
session_start();

// Check if the admin is logged in (similar to your admin panel)
if (!isset($_SESSION["email"])) {
    header("Location: admin_login.php");
    exit();
}

// Define the $finalPosition variable if it exists in the session
if (isset($_SESSION["finalPosition"])) {
    $finalPosition = $_SESSION["finalPosition"]; // Assuming you store it in the session
} else {
    // Handle the case where $_SESSION["finalPosition"] is not set
    // You can set a default value or redirect the user as needed
    $finalPosition = "Default Position"; // Set a default value
}

// Include database connection and data retrieval logic
$hostname = "localhost";
$username = "root";
$password = "";
$database = "college_db";

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);

    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve the selected month from the form
    $selectedMonth = isset($_POST['selectedMonth']) ? $_POST['selectedMonth'] : date('m');

    // Query to retrieve leave requests for the selected month from the entire database
    $query = "SELECT * FROM leave_applications WHERE DATE_FORMAT(created_at, '%m') = :selectedMonth
              ORDER BY created_at DESC";

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':selectedMonth', $selectedMonth, PDO::PARAM_STR);
    $stmt->execute();

    // Fetch results as associative array
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the PDO connection
$pdo = null;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <style>
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
        .month-label {
    font-weight: bold;
    font-size: 16px;
    margin-right: 10px;
    color: #333; /* Change the color to your preferred color */
}

/* CSS for the month select element */
.month-select {
    padding: 8px;
    font-size: 14px;
    background-color: #fff;
    border: 2px solid #ccc;
    border-radius: 4px;
    color: #333; /* Change the color to your preferred color */
}

/* CSS for the filter button */
.filter-button {
    padding: 8px 16px;
    background-color: #e60505;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.filter-button:hover {
    background-color: #ff4444;
}
    </style>
</head>
<body>
    <h1>Month Wise Data</h1>
    <!-- Add a button to go back to the school-wise data page -->
    <div class="menu">
        <a href="data_visualization.php">Back to School Wise</a>
    </div>

    <!-- Add a form to select the month to filter by -->
   <!-- Add a form to select the month to filter by -->
<form method="POST">
    <label for="selectedMonth" class="month-label">Select a Month:</label>
    <select name="selectedMonth" id="selectedMonth" class="month-select">
        <option value="01">January</option>
        <option value="02">February</option>
        <option value="03">March</option>
        <option value="04">April</option>
        <option value="05">May</option>
        <option value="06">June</option>
        <option value="07">July</option>
        <option value="08">August</option>
        <option value="09">September</option>
        <option value="10">October</option>
        <option value="11">November</option>
        <option value="12">December</option>
    </select>
    <input type="submit" value="Filter" class="filter-button">
</form>


    <!-- Display the filtered data in a table (filtered by selected month) -->
    <table>
        <!-- Table header -->
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
            </tr>
        </thead>
        <!-- Table data -->
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
          echo '<td class="student-details">' . $row['year'] . '</td>';
          echo '<td class="student-details">' . $row['mobile'] . '</td>';
          echo '<td class="student-details">' . $row['from_date'] . '</td>';
          echo '<td class="student-details">' . $row['to_date'] . '</td>';
          echo '<td class="student-details">' . $row['reason'] . '</td>';
          echo '<td class="student-details">' . $row['academic'] . '</td>';
          echo '<td class="student-details">' . $row['intime'] . '</td>';
          echo '<td class="student-details">' . $row['outime'] . '</td>';
          echo '</td>';
          echo '</tr>';
         }  
      
        ?>
        </tbody>
    </table>
</body>
</html>
