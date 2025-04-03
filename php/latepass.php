<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header('Location: student_login_page.php');
    exit();
}
$student_id = $_SESSION['student_id'];

date_default_timezone_set('Asia/Kolkata'); 
$current_time = new DateTime();
include 'database_config.php';

// Set default values for filters
$selectedMonth = isset($_POST['selectedMonth']) ? $_POST['selectedMonth'] : '';
$outDate = isset($_POST['outDate']) ? $_POST['outDate'] : '';

try {
$studentQuery = "SELECT * FROM students WHERE student_id = :student_id LIMIT 1";
$studentStmt = $pdo->prepare($studentQuery);
$studentStmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
$studentStmt->execute();
$studentDetails = $studentStmt->fetch(PDO::FETCH_ASSOC);


    // Query for late entry requests with filters
    $query = "SELECT s.student_name, s.batch, s.department, s.gender,s.phone,l.*
              FROM students s
              INNER JOIN late_outing l ON s.student_id = l.student_id
              WHERE l.student_id = :student_id";

    // Apply date filters if selected
    if (!empty($selectedMonth)) {
        $query .= " AND DATE_FORMAT(l.submission_time, '%m') = :selectedMonth";
    }
    if (!empty($outDate)) {
        $query .= " AND DATE(l.submission_time) = :outDate";
    }

    $query .= " ORDER BY l.submission_time DESC";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    
    if (!empty($selectedMonth)) {
        $stmt->bindParam(':selectedMonth', $selectedMonth, PDO::PARAM_STR);
    }
    if (!empty($outDate)) {
        $stmt->bindParam(':outDate', $outDate, PDO::PARAM_STR);
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
    <title>My Late Entry Requests</title>
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
    .btn-dark {
        background-color: black;
    }

    .btn:hover {
        background-color: rgba(186, 12, 47, 0.8); /* Change background color on hover */
        color: white; /* Ensure text color remains visible on hover */
    }

    .btn:disabled {
        background-color: rgb(150, 12, 47); /* Change background color for disabled buttons */
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
    .btn-sm{background-color:green;}
</style>

</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="logo">
                <img src="../images/back7.png" alt="Logo" class="img-fluid" style="max-height: 60px;">
            </div>
            <div class="ml-auto d-flex">
                <a href="late_outing.php" class="btn btn-primary mr-2">Apply for Late Entry</a>
                <a href="student_dashboard.php" class="btn btn-dark">Back</a>
            </div>
        </div>

        <?php if ($studentDetails): ?>
            <div class="student-info">
                <h3>Welcome, <?php echo htmlspecialchars($studentDetails['student_name']); ?></h3>
                <p><strong>ID:</strong> <?php echo htmlspecialchars($studentDetails['student_id']); ?> | 
                   <strong>Department:</strong> <?php echo htmlspecialchars($studentDetails['department']); ?> |
                   <strong>Batch:</strong> <?php echo htmlspecialchars($studentDetails['batch']); ?> 
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

        <h2 class="text-center text-danger mb-4">My Late Entry Requests</h2>
        <div class="table-responsive table-container">
          <table id="studentTable" class="table table-striped table-bordered">
    <thead class="table-danger">
        <tr>
            <th>Submission Time</th>
            <th>Name</th>
            <th>ID</th>
            <th>Department</th>
            <th>Batch</th>
            <th>Phone</th>
            <th>Gender</th>
            <th>Comments</th>
            <th>Action</th>
        </tr>
    </thead>
   <tbody>
    <?php if (empty($results)): ?>
        <tr>
            <td colspan="9" class="text-center">No late entry requests found.</td>
        </tr>
    <?php else: ?>
        <?php foreach ($results as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['submission_time']); ?></td>
                <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                <td><?php echo htmlspecialchars($row['department']); ?></td>
                <td><?php echo htmlspecialchars($row['batch']); ?></td>
                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                <td><?php echo htmlspecialchars($row['comments']); ?></td>
                <td>
                    <?php
                   
                    $submission_time = new DateTime($row['submission_time']);
                    $expiry_time = clone $submission_time;
                    $expiry_time->modify('+5 minutes');

                    
                    if ($current_time > $expiry_time): ?>
                        <button class="btn btn-secondary btn-sm" disabled>Expired</button>
                    <?php else: ?>
                        <a href="latepass_view.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-primary btn-sm">Get Pass</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</tbody>
</table>

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
                "order": [[0, "desc"]], // Sort by submission time descending
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
            });
        });
    </script>
</body>
</html>
