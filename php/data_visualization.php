<?php
// Start the session to manage user sessions
session_start();
$finalPosition = '';
if (isset($_GET['finalPosition'])) {
    $finalPosition = $_GET['finalPosition'];
}

// Check if the admin is logged in
if (!isset($_SESSION["email"])) {
    // If the admin is not logged in, redirect to the admin login page
    header("Location: admin_login.php");
    exit();
}

// Database configuration
$hostname = "localhost";
$username = "root";
$password = "";
$database = "college_db";

// Establish a PDO database connection
try {
    $pdo = new PDO("mysql:host=$hostname;dbname=$database;charset=utf8", $username, $password);

    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle database connection errors
    die("Database connection failed: " . $e->getMessage());
}

// Retrieve leave counts for each school from the database
$query = "
    SELECT la.school AS school_name, COUNT(la.id) AS leave_count
    FROM leave_applications la
    GROUP BY la.school
";
$stmt = $pdo->query($query);
$schoolLeaveCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize arrays for school names and leave counts
$schoolNames = [];
$leaveCounts = [];

// Extract school names and leave counts from the result
foreach ($schoolLeaveCounts as $row) {
    $schoolNames[] = $row['school_name'];
    $leaveCounts[] = $row['leave_count'];
}

// Retrieve gender counts from the database
$query = "
    SELECT gender, COUNT(id) AS gender_count
    FROM leave_applications
    GROUP BY gender
";
$stmt = $pdo->query($query);
$genderCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize arrays for gender labels and counts
$genderLabels = [];
$genderData = [];

// Extract gender labels and counts from the result
foreach ($genderCounts as $row) {
    $genderLabels[] = $row['gender'];
    $genderData[] = $row['gender_count'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <!-- Add your CSS styles here -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.css">
</head>
<body>
    <h1>Welcome, <?php echo $finalPosition; ?>!</h1>
    <style>
        body {
            font-family: Arial, sans-serif;
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
            position: absolute;
            top: 10px;
            left: 10px;
            margin-left: 10px;
        }

        .logo img {
            max-height: 90px;
        }

        /* Style for the container div */
        .charts-container {
            display: flex; /* Use flexbox to align side by side */
            justify-content: space-between; /* Space evenly between charts */
            max-width: 1000px; /* Adjust the width as needed */
            margin: 0 auto; /* Center the container horizontally */
        }

        /* Style for individual chart divs */
        .chart {
            flex: 0.5; /* Distribute equal width for both charts */
            max-width: 45%; /* Adjust the max-width as needed */
        }

        .export-button {
            padding: 10px 20px;
            background-color: #000000;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .export-button:hover {
            background-color: #45a049;
        }

        /* Style for chart labels */
        .chart-label {
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
        }
        .footer {
    background-color: rgb(99, 102, 106); /* Background color in RGB */
    color: #fff; /* Text color */
    padding: 10px; /* Adjust padding as needed */
    position: fixed;
    bottom: 0;
    width: 100%;
    text-align: center;
}
    </style>
</head>
<body>
    <!-- Menu -->
    <div class="menu">
        <a href="admin_panel.php?finalPosition=<?= $finalPosition ?>">Admin Panel</a>
        <a href="stme.php?finalPosition=<?= $finalPosition ?>">STME</a>
        <a href="sptm.php?finalPosition=<?= $finalPosition ?>">SPTM</a>
        <a href="sol.php?finalPosition=<?= $finalPosition ?>">SOL</a>
        <a href="sbm.php?finalPosition=<?= $finalPosition ?>">SBM</a>
        <a href="month_wise.php?finalPosition=<?= $finalPosition ?>">Month Wise</a>

    </div>

    <!-- Logo -->
    <div class="logo">
        <img src="images/back7.png" alt="Logo" width="200" height="150">
    </div>

    <!-- Display a pie chart for school-wise leave data -->
    <h1>School Wise Data</h1>
    <div class="charts-container">
        <div class="chart" id="pieChartContainer">
            <canvas id="pieChart"></canvas>
            <div class="chart-label">School Wise</div>
        </div>
        <div class="chart" id="genderChartContainer">
            <canvas id="genderChart"></canvas>
            <div class="chart-label">Gender Wise</div>
        </div>
    </div>

    <!-- Add the present date, day, and year in the right corner of the page -->
    <div class="date" id="current-date"></div>
    <div style="text-align: center; margin-top: 20px;">
        <form action="export_leaves.php" method="POST">
            <button type="submit" name="export" class="export-button">Export Data</button>
        </form>
    </div>

    <!-- Include Chart.js library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>

    <script>
        // JavaScript code to display the current date, day, and year
        var currentDate = new Date();
        var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('current-date').innerHTML = currentDate.toLocaleDateString(undefined, options);

        // Function to update the pie chart with retrieved school data
        function updatePieChart() {
            // Use the PHP variables to generate the pie chart
            const schools = <?= json_encode($schoolNames) ?>;
            const leaveCounts = <?= json_encode($leaveCounts) ?>;

            // Create a pie chart using Chart.js
            var ctx = document.getElementById('pieChart').getContext('2d');
            var pieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: schools, // School names as labels
                    datasets: [{
                        label: 'Schools',
                        data: leaveCounts, // Leave counts as data
                        backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0'], // Replace with your colors
                    }],
                },
                options: {
                    animation: {
                        animateRotate: true, // Enable rotation animation
                        duration: 3000, // Set animation duration (in milliseconds) to slow down rotation
                    },
                },
            });
        }

        // Function to update the gender chart with retrieved gender data
        function updateGenderChart() {
            // Use the PHP variables to generate the gender chart
            const genderLabels = <?= json_encode($genderLabels) ?>;
            const genderData = <?= json_encode($genderData) ?>;

            // Create a pie chart using Chart.js
            var ctx = document.getElementById('genderChart').getContext('2d');
            var genderChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: genderLabels, // Gender labels as labels
                    datasets: [{
                        label: 'Gender',
                        data: genderData, // Gender counts as data
                        backgroundColor: ['#36a2eb', '#ff6384', '#000000'], // Replace with your colors
                    }],
                },
                options: {
                    animation: {
                        animateRotate: true, // Enable rotation animation
                        duration: 3000, // Set animation duration (in milliseconds) to slow down rotation
                    },
                },
            });
        }

        // Call the functions to update the charts
        updatePieChart();
        updateGenderChart();
    </script>
</body>
<footer class="footer">
    <div class="container-fluid text-center">
        <span>&copy; MALDE SAICHARAN All rights reserved.</span>
    </div>
</footer>
</html>
