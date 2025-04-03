<?php
session_start();

// Ensure the student_id is available in the session
if (!isset($_SESSION['student_id'])) {
    header('Location: student_login_page.php');
    exit();
}

// Get student_id from the session
$student_id = $_SESSION['student_id'];

// Set the timezone to Asia/Kolkata
date_default_timezone_set('Asia/Kolkata');

// Include the database configuration file
include 'database_config.php';

// Set default values for filters
$selectedMonth = isset($_POST['selectedMonth']) ? $_POST['selectedMonth'] : '';
$inDate = isset($_POST['inDate']) ? $_POST['inDate'] : '';

try {
    // Query to fetch student details using student_id
    $studentQuery = "SELECT si.*, ss.student_name,ss.department, ss.batch 
    FROM student_inform si
    JOIN students ss ON si.student_id = ss.student_id
    WHERE si.student_id = :student_id";
    $studentStmt = $pdo->prepare($studentQuery);
    $studentStmt->bindParam(':student_id', $student_id);
    $studentStmt->execute();
    $studentDetails = $studentStmt->fetch(PDO::FETCH_ASSOC);

    $query = "SELECT si.*, ss.department, ss.batch 
    FROM student_inform si
    JOIN students ss ON si.student_id = ss.student_id
    WHERE si.student_id = :student_id";

    // Apply date filters if selected
    if (!empty($selectedMonth)) {
        $query .= " AND DATE_FORMAT(indate, '%m') = :selectedMonth";
    }
    if (!empty($inDate)) {
        $query .= " AND indate = :inDate";
    }

    $query .= " ORDER BY indate DESC";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':student_id', $student_id);

    if (!empty($selectedMonth)) {
        $stmt->bindParam(':selectedMonth', $selectedMonth);
    }
    if (!empty($inDate)) {
        $stmt->bindParam(':inDate', $inDate);
    }

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Inpass Applications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="icon" href="../images/ico.png" type="image/x-icon">
   <style>
    body {
        background-image: url("../images/back4.jpg");
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
    }
    .btn {
        margin: 5px;
        border: none; /* Remove border for all buttons */
    }
    .btn-primary {
        background-color: rgb(186, 12, 47); /* Primary button background color */
        color: white; /* Text color */
    }
    .btn-primary:hover {
        background-color: rgba(186, 12, 47, 0.8); /* Hover state */
    }
    .btn-primary:disabled {
        background-color: rgb(150, 12, 47); /* Disabled button color */
    }

    .btn-get-pass {
        background-color: green; 
        color: white;
    }
    .btn-get-pass:hover {
        background-color: darkgreen; /* Darker green on hover */
    }

    .table-container {
        padding: 15px; 
        width: 100%;
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 10px;
    }
    .student-info {
        background-color: rgba(255, 255, 255, 0.9);
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
</style>

</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="logo">
        <img src="../images/back7.png" alt="Logo" class="img-fluid" style="max-height: 60px;">
    </div>
    <div class="ml-auto d-flex">
        <a href="apply_inpass.php" class="btn btn-primary mr-2">Apply for Inpass</a>
        <a href="student_dashboard.php" class="btn btn-dark">Back</a>
    </div>
</div>

        <?php if ($studentDetails): ?>
            <div class="student-info">
                <h3>Welcome, <?php echo isset($studentDetails['student_name']) ? htmlspecialchars($studentDetails['student_name']) : 'N/A'; ?></h3>
                <p><strong>ID:</strong> <?php echo isset($studentDetails['student_id']) ? htmlspecialchars($studentDetails['student_id']) : 'N/A'; ?> | 
                   <strong>Department:</strong> <?php echo isset($studentDetails['department']) ? htmlspecialchars($studentDetails['department']) : 'N/A'; ?> | 
                   <strong>Batch:</strong> <?php echo isset($studentDetails['batch']) ? htmlspecialchars($studentDetails['batch']) : 'N/A'; ?></p>
            </div>
        <?php else: ?>
            <p class="text-danger">Student details not found. Please contact the administrator.</p>
        <?php endif; ?>

        <form method="POST" class="mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <label for="selectedMonth" class="form-label">Filter by Month:</label>
                    <select name="selectedMonth" id="selectedMonth" class="form-select">
                        <option value="">All Months</option>
                        <?php
                        $months = [
                            '01' => 'January', '02' => 'February', '03' => 'March',
                            '04' => 'April', '05' => 'May', '06' => 'June',
                            '07' => 'July', '08' => 'August', '09' => 'September',
                            '10' => 'October', '11' => 'November', '12' => 'December'
                        ];
                        foreach ($months as $value => $label) {
                            $selected = ($selectedMonth == $value) ? 'selected' : '';
                            echo "<option value='$value' $selected>$label</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-auto">
                    <label for="inDate" class="form-label">Filter by Date:</label>
                    <input type="date" id="inDate" name="inDate" class="form-control" value="<?php echo $inDate; ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mt-4">Apply Filters</button>
                </div>
            </div>
        </form>

        <h2 class="text-center text-danger mb-4">My Inpass Applications</h2>
        <div class="table-responsive table-container">
            <table id="studentTable" class="table table-striped table-bordered">
                <thead class="table-danger">
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Transport</th>
                        <th>Expiration time</th>
                        <th>Status</th> <!-- New column for status -->
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
    <?php if (empty($results)): ?>
        <tr>
            <td colspan="6" class="text-center">No inpass applications found.</td>
        </tr>
    <?php else: ?>
        <?php
        $currentDateTime = new DateTime();

        foreach ($results as $row): 
            $expirationTime = new DateTime($row['expiration_time']);

            // Determine status considering both date and time
            if ($currentDateTime > $expirationTime) {
                $status = 'Expired';
            } else {
                $status = 'Active';
            }
        ?>
            <tr>
                <td><?php echo htmlspecialchars($row['indate']); ?></td>
                <td><?php echo htmlspecialchars($row['intime']); ?></td>
                <td><?php echo htmlspecialchars($row['way_of_transport']); ?></td>
                <td><?php echo htmlspecialchars($row['expiration_time']); ?></td>
                <td><?php echo htmlspecialchars($status); ?></td>
                <td>
                    <a href="inpass_view.php?id=<?php echo $row['id']; ?>" 
   class="btn btn-get-pass btn-sm <?php echo ($status == 'Expired') ? 'disabled' : ''; ?>">
   Get Inpass</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

</div>
</div>
</body>
</html>

