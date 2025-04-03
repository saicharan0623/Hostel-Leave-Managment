<?php
// Start the session to manage user sessions
session_start();
$admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : '';  // Get admin_id from session

include 'database_config.php';

try {
    // Base query with a JOIN operation
    $query = 'SELECT si.*, s.student_name, s.gender, s.student_id, s.department, s.batch, s.student_email, s.phone
              FROM student_inform si
              JOIN students s ON si.student_id = s.student_id
              ORDER BY si.indate ASC';
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    // Fetch results as associative array
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Query to fetch distinct departments (schools)
$schoolQuery = 'SELECT DISTINCT department FROM students';
$schoolStmt = $pdo->prepare($schoolQuery);
$schoolStmt->execute();
$schools = $schoolStmt->fetchAll(PDO::FETCH_ASSOC);


$selectedMonth = isset($_POST['selectedMonth']) ? $_POST['selectedMonth'] : '';
$selectedSchool = isset($_POST['selectedSchool']) ? $_POST['selectedSchool'] : '';
$inDate = isset($_POST['inDate']) ? $_POST['inDate'] : '';

$query = 'SELECT si.*, s.student_name, s.gender, s.student_id, s.department, s.batch, s.student_email, s.phone
          FROM student_inform si
          JOIN students s ON si.student_id = s.student_id';

if (!empty($selectedMonth) || !empty($selectedSchool) || !empty($inDate)) {
    $query .= ' WHERE 1=1'; // Placeholder condition

    if (!empty($selectedMonth)) {
        $query .= " AND DATE_FORMAT(si.indate, '%m') = :selectedMonth";
    }
    if (!empty($selectedSchool)) {
        $query .= " AND s.department = :selectedSchool"; // Filter based on department
    }
    if (!empty($inDate)) {
        $query .= " AND si.indate = :inDate";
    }
}

$query .= " ORDER BY si.indate ASC";

$stmt = $pdo->prepare($query);

if (!empty($selectedMonth)) {
    $stmt->bindValue(':selectedMonth', $selectedMonth, PDO::PARAM_STR);
}
if (!empty($selectedSchool)) {
    $stmt->bindValue(':selectedSchool', $selectedSchool, PDO::PARAM_STR);
}
if (!empty($inDate)) {
    $stmt->bindValue(':inDate', $inDate, PDO::PARAM_STR);
}

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$pdo = null;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Informs</title>
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
            margin: 10px;
        }
        .table-container {
            padding: 15px;
            width: 100%;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="logo">
            <img src="../images/back7.png" alt="Logo" class="img-fluid" style="max-height: 60px;">
        </div>
        <a href="admin_panel.php" class="btn btn-secondary">Back</a>
    </div>

    <form method="POST" class="mb-4">
        <div class="row g-3 align-items-center justify-content-center">
            <div class="col-auto">
                <label for="selectedMonth" class="form-label">Select a Month:</label>
                <select name="selectedMonth" id="selectedMonth" class="form-select">
                    <option value="">All Months</option>
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
            </div>
            <div class="col-auto">
    <label for="selectedSchool" class="form-label">Select a School:</label>
    <select name="selectedSchool" id="selectedSchool" class="form-select">
        <option value="">All Schools</option>
        <?php foreach ($schools as $school): ?>
            <option value="<?= htmlspecialchars($school['department']) ?>"><?= htmlspecialchars($school['department']) ?></option>
        <?php endforeach; ?>
    </select>
</div>

            <div class="col-auto">
                <label for="inDate" class="form-label">Date:</label>
                <input type="date" id="inDate" name="inDate" class="form-control">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-danger mt-4">Filter</button>
            </div>
        </div>
    </form>
    <h2 class="text-center text-danger mb-4">Student In Applications</h2>
    <div class="table-responsive table-container">
        <table id="studentTable" class="table table-striped table-bordered">
            <thead class="table-danger">
                <tr>
                    <th>Sl.No</th>    
                    <th>Student Name</th>
                    <th>Gender</th>
                    <th>Student Id</th> 
                    <th>Student School</th>
                    <th>Batch</th>
                    <th>Student Email</th>       
                    <th>Student Mobile</th>
                    <th>In Date</th>
                    <th>In Time</th>
                    <th>Way of Transport</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($results)): ?>
                    <tr>
                        <td colspan="12" class="text-center">No records found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($results as $index => $row): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($row['student_name']) ?></td>
                            <td><?= htmlspecialchars($row['gender']) ?></td>
                            <td><?= htmlspecialchars($row['student_id']) ?></td>
                            <td><?= htmlspecialchars($row['department']) ?></td>
                            <td><?= htmlspecialchars($row['batch']) ?></td>
                            <td><?= htmlspecialchars($row['student_email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= htmlspecialchars($row['indate']) ?></td>
                            <td><?= htmlspecialchars($row['intime']) ?></td>
                            <td><?= htmlspecialchars($row['way_of_transport']) ?></td>
                            <td><?= htmlspecialchars($row['comments']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#studentTable').DataTable({
                "pageLength": 25,
                "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
            });

            var currentDate = new Date();
            var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('current-date').innerHTML = currentDate.toLocaleDateString(undefined, options);
        });
    </script>
</body>
</html>
