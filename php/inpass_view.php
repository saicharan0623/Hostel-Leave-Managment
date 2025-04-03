<?php
session_start();

if (!isset($_SESSION['student_id']) || !isset($_GET['id'])) {
    header('Location: student_login_page.php');
    exit();
}

date_default_timezone_set('Asia/Kolkata');

include 'database_config.php';

try {
    // Modified query to join student_inform and students tables using student_id from session
    $query = 'SELECT si.*, s.student_name, s.department, s.batch, s.student_id 
              FROM student_inform si
              JOIN students s ON si.student_id = s.student_id
              WHERE si.id = :id AND si.student_id = :student_id';
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->bindParam(':student_id', $_SESSION['student_id']);
    $stmt->execute();
    $inpass = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$inpass) {
        header('Location: student_dashboard.php');
        exit();
    }

    // Calculate expiration time
    $intime = DateTime::createFromFormat('H:i:s', $inpass['intime']);
    $expiration_time = $intime->modify('+1 hour');

    // Determine status
    $currentDateTime = new DateTime();
    $status = ($currentDateTime > $expiration_time) ? 'Expired' : 'Active';

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
    <title>Student Inpass</title>
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

.inpass-card {
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

.inpass-title {
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

.status {
    text-align: center;
    margin-top: 15px;
    padding: 8px;
    border-radius: 4px;
    font-weight: bold;
    font-size: 1rem; /* Increased font size for status */
}

.status-Active {
    background-color: #d4edda;
    color: #155724;
}

.status-Expired {
    background-color: #f8d7da;
    color: #721c24;
}

.footer {
    margin-top: 15px;
    text-align: center;
    font-size: 1rem; /* Increased footer font size */
    color: #6c757d;
    border-top: 1px solid #dee2e6;
    padding-top: 10px;
}

@media (max-width: 768px) {
    .inpass-card {
        margin: 20px; /* Add smaller margin on mobile */
        padding: 15px; /* Reduce padding on mobile */
        width: 80%; /* Make card responsive */
    }
}

@media (max-width: 480px) {
    .inpass-card {
        margin: 10px; /* Further reduce margin for very small screens */
        padding: 10px; /* Reduce padding */
        width: 85%; /* Make card even more responsive on very small screens */
    }
}

@media print {
    .no-print { display: none; }
    body { background-color: white; }
    .inpass-card {
        box-shadow: none;
        margin: 0;
        padding: 10px;
    }
}

    </style>
</head>
<body>
    <div class="inpass-card">
        <div class="header">
            <img src="../images/back7.png" alt="Logo" class="logo">
            <div class="inpass-title">STUDENT INPASS</div>
        </div>

        <div class="info-row">
            <span class="label">Pass ID:</span>
            <span class="value"><?php echo htmlspecialchars($inpass['id']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Name:</span>
            <span class="value"><?php echo htmlspecialchars($inpass['student_name']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">ID:</span>
            <span class="value"><?php echo htmlspecialchars($inpass['student_id']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">School:</span>
            <span class="value"><?php echo htmlspecialchars($inpass['department']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Batch:</span>
            <span class="value"><?php echo htmlspecialchars($inpass['batch']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">In Date:</span>
            <span class="value"><?php echo date('d-m-Y', strtotime($inpass['indate'])); ?></span>
        </div>

        <div class="info-row">
            <span class="label">In Time:</span>
            <span class="value"><?php echo htmlspecialchars($inpass['intime']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Transport:</span>
            <span class="value"><?php echo htmlspecialchars($inpass['way_of_transport']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Expires At:</span>
            <span class="value"><?php echo $expiration_time->format('H:i:s'); ?></span>
        </div>

        <div class="status status-<?php echo $status; ?>">
            Status: <?php echo $status; ?>
        </div>

        <div class="footer">
            <p class="mb-0">Valid only with college ID card • <?php echo date('d-m-Y'); ?></p>
        </div>

        <div class="text-center mt-3 no-print">
            <a href="inpass.php" class="btn btn-secondary btn-sm ms-2">Back</a>
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