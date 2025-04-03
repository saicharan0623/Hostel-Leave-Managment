<?php
session_start();
include 'database_config.php';

// Check if admin_id exists in the session
if (!isset($_SESSION['admin_id'])) {
    echo "Access Denied!";
    exit();
}

// Prepare and execute the query to join late_outing and students tables
$query = 'SELECT 
            l.student_id,
            s.student_name,
            s.student_email,
            s.phone,
            s.batch,
            s.gender,
            s.department,
            l.submission_time,
            l.comments
          FROM late_outing l
          JOIN students s ON l.student_id = s.student_id
          ORDER BY l.submission_time ASC';

$stmt = $pdo->prepare($query);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Late Applications</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="icon" href="../images/ico.png" type="image/x-icon">

    <style>
        body {
            background-image: url("../images/back4.jpg");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .table-container {
            padding: 10px;
            width: 100%;
        }
        td, th {
            border-color: #000;
        }
        .btn {
            margin: 10px;
        }
        .filter-container {
            margin-bottom: 20px;
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

    <div class="container mt-4">
        <h2 class="text-center mb-4">Student Late Applications</h2>

        <div class="filter-container">
            <div class="row">
                <div class="col-md-3">
                    <label for="monthFilter" class="form-label">Filter by Month:</label>
                    <select id="monthFilter" class="form-select">
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
                <div class="col-md-3">
                    <label for="date" class="form-label">Filter by Year:</label>
                    <input type="date" id="dateFilter" class="form-control bg-light text-dark">
                </div>
            </div>
        </div>

        <table id="lateApplicationsTable" class="table table-striped table-bordered">
            <thead class="table-danger">
                <tr>
                    <th>Sl.No</th>
                    <th>Student Name</th>
                    <th>Gender</th>
                    <th>Student Id</th>
                    <th>Department</th>
                    <th>Batch</th>
                    <th>Student Email</th>
                    <th>Student Mobile</th>
                    <th>Late Entry Time</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($results)): ?>
                <tr>
                    <td colspan="10" class="text-center">No records found.</td>
                </tr>
                <?php else: 
                    foreach ($results as $index => $row): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $row['student_name'] ?></td>
                    <td><?= $row['gender'] ?></td>
                    <td><?= $row['student_id'] ?></td>
                    <td><?= $row['department'] ?></td>
                    <td><?= $row['batch'] ?></td>
                    <td><?= $row['student_email'] ?></td>
                    <td><?= $row['phone'] ?></td>
                    <td><?= $row['submission_time'] ?></td>
                    <td><?= $row['comments'] ?></td>
                </tr>
                <?php endforeach; 
                endif; ?>
            </tbody>
        </table>
    </div>

    <footer class="footer bg-secondary text-white mt-auto">
        <div class="container-fluid text-center py-2">
            <span>&copy; MALDE SAICHARAN All rights reserved.</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>
    <script>
       $(document).ready(function() {
    var table = $('#lateApplicationsTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
    });

    // Extend search function for filtering by date
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            var selectedMonth = $('#monthFilter').val();
            var selectedDate = $('#dateFilter').val();  // Capture the date value as YYYY-MM-DD
            var rowDate = new Date(data[8]);  // Assuming submission_time is the 9th column (index 8)

            // No filters applied
            if (selectedMonth === "" && selectedDate === "") {
                return true;
            }

            // Convert the selectedDate to Date object if selected
            var selectedYear = selectedDate ? new Date(selectedDate).getFullYear() : null;

            // Apply both month and year filters
            if (selectedMonth !== "" && selectedDate !== "") {
                return (rowDate.getMonth() + 1 === parseInt(selectedMonth) && rowDate.getFullYear() === selectedYear);
            }

            // Apply only the month filter
            if (selectedMonth !== "") {
                return (rowDate.getMonth() + 1 === parseInt(selectedMonth));
            }

            // Apply only the year filter
            if (selectedDate !== "") {
                return (rowDate.getFullYear() === selectedYear);
            }

            return true;
        }
    );

    // Trigger dynamic filtering when month or date changes
    $('#monthFilter, #dateFilter').on('change', function() {
        table.draw();
    });
});
    </script>
</body>
</html>
