<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header('Location: student_login_page.php');
    exit();
}

date_default_timezone_set('Asia/Kolkata');

if (!isset($_GET['id'])) {
    header('Location: outpass.php');
    exit();
}

include 'database_config.php';

try {
    // Fetch the outpass details
    $query = "SELECT 
    la.*, 
    DATE_FORMAT(la.from_date, '%d-%m-%Y') as formatted_from_date, 
    DATE_FORMAT(la.to_date, '%d-%m-%Y') as formatted_to_date, 
    s.student_name, 
    s.phone, 
    s.department, 
    s.batch
FROM 
    leave_applications la
JOIN 
    students s 
ON 
    la.student_id = s.student_id
WHERE 
    la.id = :id 
    AND la.student_id = :student_id 
    AND la.status = 'APPROVED'";

    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->bindParam(':student_id', $_SESSION['student_id'], PDO::PARAM_STR);
    $stmt->execute();
    
    $outpass = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$outpass) {
        header('Location: my_applications.php');
        exit();
    }
} catch (Exception $e) {
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
    <title>Student Outpass</title>
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
        .outpass-card {
            width: 100%;
            max-width: 500px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        @media (max-width: 768px) {
            .outpass-card {
                width: 85%;
                margin: 10px;
                padding: 15px;
            }
        }
        @media (max-width: 480px) {
            .outpass-card {
                width: 85%;
                margin: 10px;
                padding: 10px;
            }
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
        .outpass-title {
            color: #dc3545;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 1rem;
        }
        .label {
            font-weight: bold;
            color: #495057;
            width: 100px;
            font-size: 1rem;
        }
        .value {
            color: #212529;
            flex: 1;
            text-align: right;
            font-size: 1rem;
        }
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 1rem;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
      
        @media print {
            .no-print {
                display: none;
            }
            body {
                background: white;
            }
            .outpass-card {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="outpass-card">
        <div class="header">
            <img src="../images/back7.png" alt="Logo" class="logo">
            <div class="outpass-title">STUDENT OUTPASS</div>
        </div>

        <div class="info-row">
            <span class="label">Outpass ID:</span>
            <span class="value"><?php echo htmlspecialchars($outpass['id']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Name:</span>
            <span class="value"><?php echo htmlspecialchars($outpass['student_name']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Sap Id:</span>
            <span class="value"><?php echo htmlspecialchars($outpass['student_id']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">School:</span>
            <span class="value"><?php echo htmlspecialchars($outpass['department']); ?> - <?php echo htmlspecialchars($outpass['batch']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Approved Out Date:</span>
            <span class="value"><?php echo htmlspecialchars($outpass['formatted_from_date']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Time:</span>
            <span class="value"><?php echo htmlspecialchars($outpass['outime']); ?> - <?php echo htmlspecialchars($outpass['intime']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Reason:</span>
            <span class="value"><?php echo htmlspecialchars($outpass['reason']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Mobile:</span>
            <span class="value"><?php echo htmlspecialchars($outpass['phone']); ?></span>
        </div>

        <div class="footer">
            <p class="mb-0">Valid only with college ID card • <?php echo date('d-m-Y'); ?></p>
        </div>

        <div class="text-center mt-3 no-print">
            <a href="outpass.php" class="btn btn-secondary btn-sm ms-2">Back</a>
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

</body>
</html>
