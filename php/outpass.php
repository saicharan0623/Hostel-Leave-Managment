<?php
session_start();

// Check if student_id is set in the session
if (!isset($_SESSION['student_id'])) {
    header('Location: student_login_page.php');
    exit();
}

date_default_timezone_set('Asia/Kolkata');
$student_id = $_SESSION['student_id'];  // Now using student_id from session

include 'database_config.php';

// Set default values for filters
$selectedMonth = isset($_POST['selectedMonth']) ? $_POST['selectedMonth'] : '';
$fromDate = isset($_POST['fromDate']) ? $_POST['fromDate'] : '';

try {
    // Get student details (no longer using email, we now use student_id directly)
    $studentQuery = "SELECT * FROM students WHERE student_id = :student_id LIMIT 1";
    $studentStmt = $pdo->prepare($studentQuery);
    $studentStmt->bindParam(':student_id', $student_id);
    $studentStmt->execute();
    $studentDetails = $studentStmt->fetch(PDO::FETCH_ASSOC);

    if (!$studentDetails) {
        // If no student is found
        header("Location: error.php?message=No student data found");
        exit();
    }

    // Build the main query for leave applications
    $query = 'SELECT DISTINCT id, student_id, from_date, to_date, reason, status, rejection_reason, 
              attendance, intime, outime, created_at, updated_at, processed_by, leave_type
              FROM leave_applications 
              WHERE student_id = :student_id';

    // Apply date filters if selected
    if (!empty($selectedMonth)) {
        $query .= " AND DATE_FORMAT(from_date, '%m') = :selectedMonth";
    }
    if (!empty($fromDate)) {
        $query .= " AND from_date = :fromDate";
    }

    $query .= " ORDER BY from_date DESC";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':student_id', $student_id);

    if (!empty($selectedMonth)) {
        $stmt->bindParam(':selectedMonth', $selectedMonth);
    }
    if (!empty($fromDate)) {
        $stmt->bindParam(':fromDate', $fromDate);
    }

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add logic to check for expired passes and remove duplicates
    $currentDate = date('Y-m-d');
    $uniqueResults = [];
    foreach ($results as $row) {
        $key = $row['id'] . '-' . $row['from_date'] . '-' . $row['to_date'];
        if (!isset($uniqueResults[$key])) {
            if ($row['status'] === 'APPROVED' && $currentDate > $row['to_date']) {
                $row['status'] = 'EXPIRED';
            }
            $uniqueResults[$key] = $row;
        }
    }
    $results = array_values($uniqueResults);

} catch (Exception $e) {
    error_log("Database Error: " . $e->getMessage());
    echo "An error occurred while fetching the data. Please try again later.";
    exit();
}
try {
    // Overall statistics query
    $statsQuery = "SELECT 
        COUNT(*) as total_applications,
        SUM(CASE WHEN status = 'APPROVED' THEN 1 ELSE 0 END) as approved_count,
        SUM(CASE WHEN status = 'REJECTED' THEN 1 ELSE 0 END) as rejected_count,
        SUM(CASE WHEN status = 'PENDING' THEN 1 ELSE 0 END) as pending_count,
        AVG(attendance) as avg_attendance,
        MAX(CASE WHEN status = 'APPROVED' THEN attendance ELSE 0 END) as highest_attendance,
        COUNT(DISTINCT DATE_FORMAT(from_date, '%Y-%m')) as months_with_leaves
    FROM leave_applications 
    WHERE student_id = :student_id";

    $statsStmt = $pdo->prepare($statsQuery);
    $statsStmt->bindParam(':student_id', $student_id);
    $statsStmt->execute();
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

    // Current month statistics
    $currentMonthQuery = "SELECT 
        COUNT(*) as month_applications,
        SUM(CASE WHEN status = 'APPROVED' THEN 1 ELSE 0 END) as month_approved
    FROM leave_applications 
    WHERE student_id = :student_id 
    AND MONTH(from_date) = MONTH(CURRENT_DATE())
    AND YEAR(from_date) = YEAR(CURRENT_DATE())";

    $currentMonthStmt = $pdo->prepare($currentMonthQuery);
    $currentMonthStmt->bindParam(':student_id', $student_id);
    $currentMonthStmt->execute();
    $currentMonthStats = $currentMonthStmt->fetch(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log("Statistics Error: " . $e->getMessage());
    $stats = [];
    $currentMonthStats = [];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Outpass Applications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
        .status-pending {
            color: #ffa500;
            font-weight: bold;
        }
        .status-APPROVED {
            color: #008000;
            font-weight: bold;
        }
        .status-rejected {
            color: #ff0000;
            font-weight: bold;
        }
        .tooltip-info {
            cursor: help;
        }
        .reason-cell {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .disabled-link {
            pointer-events: none;
            opacity: 0.6;
            cursor: not-allowed;
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

    /* Styles for the "Get Inpass" button */
    .btn-get-pass {
        background-color: green; /* Set background color to green */
        color: white; /* Set text color */
    }
    .btn-get-pass:hover {
        background-color: darkgreen; /* Darker green on hover */
    }
        .status-EXPIRED {
            color: #808080;
            font-weight: bold;
        }
        .card {
        transition: transform 0.2s ease-in-out;
        border-radius: 10px;
        border: none;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .card-body {
        padding: 1.5rem;
    }
    .card h2 {
        font-size: 2rem;
        font-weight: bold;
        margin: 0.5rem 0;
    }
    .card .fa-2x {
        color: rgba(0, 0, 0, 0.1);
    }
    .small {
        font-size: 0.875rem;
    }
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .rejection-tooltip {
    position: relative;
    display: inline-block;
}

.rejection-reason {
    visibility: hidden;
    background-color: black;
    color: white;
    padding: 5px;
    border-radius: 5px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
}

.rejection-tooltip:hover .rejection-reason {
    visibility: visible;
}
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="logo">
                <img src="../images/back7.png" alt="Logo" class="img-fluid" style="max-height: 60px;">
            </div>
            <div class="ml-auto d-flex">
                <a href="apply_leave.php" class="btn btn-primary mr-2">Apply for Leave</a>
                <a href="student_dashboard.php" class="btn btn-dark">Back</a>
            </div>
        </div>

        <?php if ($studentDetails): ?>
            <div class="student-info mb-4">
                <h3>Welcome, <?php echo htmlspecialchars($studentDetails['student_name']); ?></h3>
                <p><strong>ID:</strong> <?php echo htmlspecialchars($studentDetails['student_id']); ?> | 
                   <strong>School:</strong> <?php echo htmlspecialchars($studentDetails['department']); ?> | 
                   <strong>Year:</strong> <?php echo htmlspecialchars($studentDetails['batch']); ?></p>
            </div>
        <?php endif; ?>

        <!-- Filter Form -->
        <form method="POST" class="mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <label for="fromDate" class="form-label">Filter by Date:</label>
                    <input type="date" id="fromDate" name="fromDate" class="form-control" value="<?php echo $fromDate; ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary mt-4">Apply Filters</button>
                </div>
            </div>
        </form>

        <h2 class="text-center text-danger mb-4">My Leave Applications</h2>
        <div class="table-responsive table-container mb-4">
            <table id="studentTable" class="table table-striped table-bordered">
                <thead class="table-danger">
                    <tr>
                        <th>Application ID</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Reason</th>
                        <th>Out Time</th>
                        <th>In Time</th>
                        <th>Attendance %</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($results)): ?>
                        <tr>
                            <td colspan="9" class="text-center">No outpass applications found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($results as $row): 
                            $isApproved = ($row['status'] === 'APPROVED');
                            $isExpired = ($row['status'] === 'EXPIRED');
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['from_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['to_date']); ?></td>
                                <td class="reason-cell" title="<?php echo htmlspecialchars($row['reason']); ?>">
                                    <?php echo htmlspecialchars($row['reason']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['outime']); ?></td>
                                <td><?php echo htmlspecialchars($row['intime']); ?></td>
                                <td><?php echo htmlspecialchars($row['attendance']); ?>%</td>
                                <td class="status-<?php echo htmlspecialchars($row['status']); ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </td>
                                <td>
   <?php if ($isApproved): ?>
       <a href="outpass_view.php?id=<?php echo urlencode($row['id']); ?>" 
          class="btn btn-primary btn-sm" style="background-color:green;">
           Get Outpass
       </a>
   <?php elseif ($isExpired): ?>
       <button style="background-color:red; color:white;" class="btn btn-secondary btn-sm" disabled>
           Leave Expired
       </button>
   <?php elseif ($row['status'] === 'REJECTED'): ?>
       <div class="rejection-tooltip">
           <button class="btn btn-danger btn-sm" disabled>Rejected</button>
           <span class="rejection-reason"><?php echo htmlspecialchars($row['rejection_reason']); ?></span>
       </div>
   <?php else: ?>
       <button class="btn btn-secondary btn-sm" disabled>Get Outpass</button>
   <?php endif; ?>
</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="row mb-4">
        <h2 class="text-center mb-4">Applications Overview</h2>
                    <div class="col-md-3 mb-3">
                <div class="card bg-white shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-file-alt fa-2x mb-2 text-primary"></i>
                        <h5 class="card-title">Total Applications</h5>
                        <h2 class="card-text text-danger"><?php echo $stats['total_applications']; ?></h2>
                        <p class="small text-muted mb-0">All time submissions</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-white shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                        <h5 class="card-title">Approved</h5>
                        <h2 class="card-text text-success"><?php echo $stats['approved_count']; ?></h2>
                        <p class="small text-muted mb-0">
                            <?php echo round(($stats['approved_count'] / max(1, $stats['total_applications'])) * 100, 1); ?>% approval rate
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-white shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-times-circle fa-2x mb-2 text-danger"></i>
                        <h5 class="card-title">Rejected</h5>
                        <h2 class="card-text text-danger"><?php echo $stats['rejected_count']; ?></h2>
                        <p class="small text-muted mb-0">
                            <?php echo round(($stats['rejected_count'] / max(1, $stats['total_applications'])) * 100, 1); ?>% rejection rate
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-white shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2 text-warning"></i>
                        <h5 class="card-title">Pending</h5>
                        <h2 class="card-text text-warning"><?php echo $stats['pending_count']; ?></h2>
                        <p class="small text-muted mb-0">Awaiting approval</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            
            <div class="col-md-4 mb-3">
                <div class="card bg-white shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line fa-2x mb-2 text-info"></i>
                        <h5 class="card-title">Average Attendance</h5>
                        <h2 class="card-text text-info"><?php echo round($stats['avg_attendance'], 1); ?>%</h2>
                        <p class="small text-muted mb-0">Overall attendance rate</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-white shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-alt fa-2x mb-2 text-primary"></i>
                        <h5 class="card-title">This Month</h5>
                        <h2 class="card-text text-primary"><?php echo $currentMonthStats['month_applications']; ?></h2>
                        <p class="small text-muted mb-0">Applications this month</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-white shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-check fa-2x mb-2 text-success"></i>
                        <h5 class="card-title">Approved This Month</h5>
                        <h2 class="card-text text-success"><?php echo $currentMonthStats['month_approved']; ?></h2>
                        <p class="small text-muted mb-0">Applications approved this month</p>
                    </div>
                </div>
            </div>
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
                "order": [[1, "desc"]], // Sort by from_date descending
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "columnDefs": [
                    {
                        "targets": [3], // Reason column
                        "render": function(data, type, row) {
                            if (type === 'display' && data.length > 30) {
                                return '<span title="' + data + '">' + 
                                       data.substr(0, 30) + '...</span>';
                            }
                            return data;
                        }
                    }
                ]
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
</body>
</html>