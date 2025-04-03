<?php
session_start();

if (!isset($_SESSION['student_id']) || !isset($_GET['id'])) {
    header('Location: student_login_page.php');
    exit();
}

date_default_timezone_set('Asia/Kolkata');

include 'database_config.php';

try {
    // Modified query to join with the students table and fetch student details (using student_name and department)
    $query = 'SELECT dor.*, s.student_name, s.department,s.batch FROM day_outing_requests dor
              JOIN students s ON dor.student_id = s.student_id
              WHERE dor.id = :id AND dor.student_id = :student_id AND dor.status = "Approved"';
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->bindParam(':student_id', $_SESSION['student_id']);
    $stmt->execute();
    $requestDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$requestDetails) {
        header('Location: day_outing_requests.php');
        exit();
    }

    // Fetch the other details from the query result
    $studentName = $requestDetails['student_name'];
    $department = $requestDetails['department'];
    $reason = $requestDetails['reason'];
    $outdate = $requestDetails['outdate'];
    $intime = $requestDetails['intime'];
    $outime = $requestDetails['outime'];

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    echo "An error occurred. Please try again later.";
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Day Outing Pass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../images/ico.png" type="image/x-icon">

    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url("../images/back4.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: 'Arial', sans-serif;
            min-height: 100vh; 
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .pass-card {
            width: 100%; /* Make card responsive */
            max-width: 500px;
            margin: 20px auto; /* Center the card */
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
        }

        .logo {
            height: 50px;
            margin-bottom: 10px;
        }

        .pass-title {
            color: #dc3545;
            font-size: 2rem; /* Increased title font size */
            font-weight: bold;
            margin-bottom: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 1rem; /* Increased font size for info rows */
        }

        .label {
            font-weight: bold;
            color: #495057;
            width: 100px;
            font-size: 1rem; /* Increased label font size */
        }

        .value {
            color: #212529;
            flex: 1;
            text-align: right;
            font-size: 1rem; /* Increased value font size */
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 1rem; /* Increased footer font size */
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .pass-card {
                margin: 15px; /* Add smaller margin on mobile */
                padding: 15px; /* Reduce padding on mobile */
                width: 90%; /* Make card responsive */
            }

            .pass-title {
                font-size: 1.8rem; /* Adjust title font size on mobile */
            }

            .info-row {
                font-size: 0.95rem; /* Slightly larger font size on mobile */
            }
        }

        @media (max-width: 480px) {
            .pass-card {
                margin: 10px; /* Further reduce margin for very small screens */
                padding: 10px; /* Reduce padding */
                width: 85%; /* Make card even more responsive on very small screens */
            }

            .pass-title {
                font-size: 1.6rem; /* Adjust title font size for very small screens */
            }

            .info-row {
                font-size: 0.9rem; /* Font size for very small screens */
            }
        }

        @media print {
            /* Hide content when printing */
            .no-print { display: none; }
            body { background-color: white; }
            .pass-card {
                box-shadow: none;
                margin: 0;
                padding: 10px;
            }
            .value {
                color: #ffffff !important; /* Hide text during print */
            }
        }

        /* Disable right-click menu and text selection */
        body {
            -webkit-user-select: none; /* Disable text selection for webkit browsers */
            -moz-user-select: none; /* Firefox */
            -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* Standard syntax */
        }

        /* Disable screenshot or browser developer tools */
        .pass-card::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.5);
            pointer-events: none;
            display: none;
        }

        /* Show the overlay when screenshot is detected */
        .screenshot-prevention {
            display: block;
        }
    </style>
</head>
<body>
<div class="pass-card screenshot-prevention">
    <div class="header">
        <img src="../images/back7.png" alt="Logo" class="logo">
        <div class="pass-title">DAY OUTING PASS</div>
    </div>

    <div class="info-row">
        <span class="label">Pass ID:</span>
        <span class="value"><?php echo htmlspecialchars($requestDetails['id']); ?></span>
    </div>

    <div class="info-row">
        <span class="label">Name:</span>
        <span class="value"><?php echo htmlspecialchars($requestDetails['student_name']); ?></span>
    </div>

    <div class="info-row">
        <span class="label">Student ID:</span>
        <span class="value"><?php echo htmlspecialchars($requestDetails['student_id']); ?></span>
    </div>

    <div class="info-row">
        <span class="label">Department:</span>
        <span class="value"><?php echo htmlspecialchars($requestDetails['department']); ?></span>
    </div>

    <div class="info-row">
        <span class="label">Reason:</span>
        <span class="value"><?php echo htmlspecialchars($requestDetails['reason']); ?></span>
    </div>

    <div class="info-row">
        <span class="label">Out Date:</span>
        <span class="value"><?php echo date('d-m-Y', strtotime($requestDetails['outdate'])); ?></span>
    </div>

    <div class="info-row">
        <span class="label">Out Time:</span>
        <span class="value"><?php echo htmlspecialchars($requestDetails['outime']); ?></span>
    </div>

    <div class="info-row">
        <span class="label">Return Time:</span>
        <span class="value"><?php echo htmlspecialchars($requestDetails['intime']); ?></span>
    </div>

    <div class="footer">
        <p class="mb-0">Valid only with college ID card • <?php echo date('d-m-Y'); ?></p>
    </div>

    <div class="text-center mt-3 no-print">
        <a href="daypass.php" class="btn btn-secondary btn-sm ms-2">Back</a>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Disable right-click
    document.addEventListener('contextmenu', event => event.preventDefault());

    // Disable text selection
    document.addEventListener('selectstart', event => event.preventDefault());

    // Disable Inspect Element
    document.addEventListener('keydown', (event) => {
        if (event.key === "F12" || (event.ctrlKey && event.shiftKey && event.key === "I")) {
            event.preventDefault();
        }
    });
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
