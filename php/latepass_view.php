<?php
session_start();

if (!isset($_SESSION['student_id']) || !isset($_GET['id'])) {
    header('Location: student_login_page.php');
    exit();
}

date_default_timezone_set('Asia/Kolkata');

include 'database_config.php';

try {
    // SQL query to join 'late_outing' and 'students' tables
    $query = 'SELECT lo.id, lo.submission_time, lo.comments, s.student_id,s.student_name, s.department, s.phone, s.batch 
              FROM late_outing lo
              JOIN students s ON lo.student_id = s.student_id
              WHERE lo.id = :id AND s.student_id = :student_id';
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->bindParam(':student_id', $_SESSION['student_id']);
    $stmt->execute();
    
    $pass = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pass) {
        header('Location: latepass.php');
        exit();
    }

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage()); // Log the error
    echo "Database Error: " . htmlspecialchars($e->getMessage()); // Display the error message
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Late Pass</title>
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
            .no-print { display: none; }
            body { background-color: white; }
            .pass-card {
                box-shadow: none;
                margin: 0;
                padding: 10px;
            }
        }

    </style>
</head>
<body>
    <div class="pass-card">
        <div class="header">
            <img src="../images/back7.png" alt="Logo" class="logo">
            <div class="pass-title">LATE PASS</div>
        </div>

        <div class="info-row">
            <span class="label">Pass ID:</span>
            <span class="value"><?php echo htmlspecialchars($pass['id']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Name:</span>
            <span class="value"><?php echo htmlspecialchars($pass['student_name']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Sap ID:</span>
            <span class="value"><?php echo htmlspecialchars($pass['student_id']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Department:</span>
            <span class="value"><?php echo htmlspecialchars($pass['department']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Batch:</span>
            <span class="value"><?php echo htmlspecialchars($pass['batch']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Phone:</span>
            <span class="value"><?php echo htmlspecialchars($pass['phone']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Entry Date & Time:</span>
            <span class="value"><?php echo date('d-m-Y H:i:s', strtotime($pass['submission_time'])); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Reason:</span>
            <span class="value"><?php echo htmlspecialchars($pass['comments']); ?></span>
        </div>

        <div class="footer">
            <p class="mb-0">Valid only with college ID card • <?php echo date('d-m-Y'); ?></p>
        </div>

        <div class="text-center mt-3 no-print">
            <a href="latepass.php" class="btn btn-secondary btn-sm ms-2">Back</a>
        </div>
    </div>
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
