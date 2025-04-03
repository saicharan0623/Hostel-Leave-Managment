<?php
session_start();

// Check if session is set for email (or student ID)
if (!isset($_SESSION['student_id'])) {
    header('Location: student_login_page.php');
    exit();
}
date_default_timezone_set('Asia/Kolkata');

$student_id = $_SESSION['student_id'];  // Using student_id from session

include 'database_config.php';

// Set default values for filters
$selectedMonth = isset($_POST['selectedMonth']) ? $_POST['selectedMonth'] : '';
$outDate = isset($_POST['outDate']) ? $_POST['outDate'] : '';

try {
    // Query to get student details based on student_id
    $studentQuery = "SELECT student_id,student_name, department, batch FROM students WHERE student_id = :student_id LIMIT 1";
    $studentStmt = $pdo->prepare($studentQuery);
    $studentStmt->bindParam(':student_id', $student_id);
    $studentStmt->execute();
    $studentDetails = $studentStmt->fetch(PDO::FETCH_ASSOC);

    // Query for outing requests, joining with students table
    $query = 'SELECT d.*, s.student_name, s.department, s.batch
              FROM day_outing_requests d
              JOIN students s ON d.student_id = s.student_id
              WHERE d.student_id = :student_id';

    // Apply date filters if selected
    if (!empty($selectedMonth)) {
        $query .= " AND DATE_FORMAT(d.outdate, '%m') = :selectedMonth";
    }
    if (!empty($outDate)) {
        $query .= " AND d.outdate = :outDate";
    }

    $query .= " ORDER BY d.request_date DESC";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':student_id', $student_id);

    if (!empty($selectedMonth)) {
        $stmt->bindParam(':selectedMonth', $selectedMonth);
    }
    if (!empty($outDate)) {
        $stmt->bindParam(':outDate', $outDate);
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
    <title>My Day Outing Requests</title>
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
        background-color: rgb(186, 12, 47); /* Set button background color for all buttons */
        color: white; /* Set text color for better contrast */
        margin: 5px;
        border: none; /* Remove border */
    }
    .btn-dark
    {
        background-color:black;
    }

    .btn:hover {
        background-color: rgba(186, 12, 47, 0.8); /* Change background color on hover */
        color: white; /* Ensure text color remains visible on hover */
    }

    .btn:disabled {
        background-color: rgb(150, 12, 47); /* Change background color for disabled buttons */
    }

    /* Styles for the "View Pass" button */
    .btn-view-pass {
        background-color: green; /* Set background color to green */
        border: none; /* Remove border */
    }

    .btn-view-pass:hover {
        background-color: darkgreen; /* Change to a darker green on hover */
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
                <a href="apply_day_outing.php" class="btn btn-primary mr-2">Apply for Day Outing</a>
                <a href="student_dashboard.php" class="btn btn-dark">Back</a>
            </div>
        </div>

        <?php if ($studentDetails): ?>
            <div class="student-info">
                <h3>Welcome, <?php echo htmlspecialchars($studentDetails['student_name']); ?></h3>
                <p><strong>ID:</strong> <?php echo htmlspecialchars($studentDetails['student_id']); ?> | 
                   <strong>Year:</strong> <?php echo htmlspecialchars($studentDetails['department']); ?></p>
                </div>
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
                    <label for="outDate" class="form-label">Filter by Date:</label>
                    <input type="date" id="outDate" name="outDate" class="form-control" value="<?php echo $outDate; ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mt-4">Apply Filters</button>
                </div>
            </div>
        </form>

        <h2 class="text-center text-danger mb-4">My Day Outing Requests</h2>
        <div class="table-responsive table-container">
            <table id="studentTable" class="table table-striped table-bordered">
                <thead class="table-danger">
                    <tr>
                        <th>Request Date</th>
                        <th>Out Date</th>
                        <th>Out Time</th>
                        <th>Return Time</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Rejection Reason</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($results)): ?>
                        <tr>
                            <td colspan="8" class="text-center">No day outing requests found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($results as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['request_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['outdate']); ?></td>
                                <td><?php echo htmlspecialchars($row['outime']); ?></td>
                                <td><?php echo htmlspecialchars($row['intime']); ?></td>
                                <td><?php echo htmlspecialchars($row['reason']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td>
    <?php 
    if ($row['status'] == 'REJECTED') {
        echo htmlspecialchars($row['rejectionreason']);
    } else {
        echo 'Na';
    }
    ?>
</td>

                               <td>
    <?php
    $outDateTimestamp = strtotime($row['outdate']);
    $currentDateTimestamp = strtotime(date('Y-m-d'));

    if ($row['status'] == 'APPROVED' && $outDateTimestamp >= $currentDateTimestamp): ?>
        <a href="daypass_view.php?id=<?php echo $row['id']; ?>" class="btn btn-view-pass btn-sm">View Pass</a>
    <?php elseif ($row['status'] == 'APPROVED' && $outDateTimestamp < $currentDateTimestamp): ?>
        <button class="btn btn-secondary btn-sm disabled" style= "background-color:red;"aria-disabled="true">Pass Expired</button>
    <?php elseif ($row['status'] == 'REJECTED'): ?>
        <button class="btn btn-secondary btn-sm disabled" aria-disabled="true">Rejected</button>
    <?php else: ?>
        <span>Processing</span>
    <?php endif; ?>
</td>



                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#studentTable').DataTable({
                "pageLength": 10,
                "order": [[0, "desc"]], // Sort by request date descending
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
            });
        });
    </script>
</body>
</html>