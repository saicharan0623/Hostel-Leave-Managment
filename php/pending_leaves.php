<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

include 'database_config.php';

try {
    // Get only pending leaves with student information
    $query = 'SELECT 
                l.id,
                l.student_id,
                l.from_date,
                l.to_date,
                l.reason,
                l.status,
                l.leave_type,
                l.created_at,
                l.updated_at,
                s.student_name,
                s.gender,
                s.department,
                s.batch,
                s.phone
              FROM leave_applications l 
              JOIN students s ON l.student_id = s.student_id 
              WHERE l.status = "PENDING"
              ORDER BY l.updated_at DESC';

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
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
        padding: 15px; /* Adding 10px padding */
        width: 100%; /* Ensuring full width */
    }
    .btn{
        margin:10px ;
    }
    td,th{
        border-color: #000;
       }
    
    .badge-warning {
        background-color: #ffcc00;
        color: white;
        padding: 10px 15px;
        border-radius: 4px;
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

        <h1 class="text-center text-warning">Pending Leave Applications</h1>

        <div class="table-responsive table-container">
        <table id="leaveApplicationsTable" class="table table-striped table-bordered" width="100%">
                <thead class="table-warning">
                    <tr>
                        <th>Sl.No</th>
                        <th>SAP ID</th>
                        <th>Student Name</th>
                        <th>Gender</th>
                        <th>School</th>
                        <th>Year</th>
                        <th>Mobile Number</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Reason</th>
                        <th>Leave Type</th>
                        <th>Applied-On</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
    <?php if (empty($results)): ?>
        <tr>
            <td colspan="13">No records found.</td>
        </tr>
    <?php else: 
        foreach ($results as $row): ?>
            <tr>
                <td><?= $row['id'] ?? 'N/A' ?></td>
                <td><?= $row['student_id'] ?? 'N/A' ?></td>
                <td><?= $row['student_name'] ?? 'N/A' ?></td>
                <td><?= $row['gender'] ?? 'N/A' ?></td>
                <td><?= $row['department'] ?? 'N/A' ?></td>
                <td><?= $row['batch'] ?? 'N/A' ?></td>
                <td><?= $row['phone'] ?? 'N/A' ?></td>
                <td><?= $row['from_date'] ?? 'N/A' ?></td>
                <td><?= $row['to_date'] ?? 'N/A' ?></td>
                <td><?= $row['reason'] ?? 'N/A' ?></td>
                <td><?= $row['leave_type'] ?? 'N/A' ?></td>
                <td><?= $row['created_at'] ?? 'N/A' ?></td>
                <td><?= $row['status'] ?? 'N/A' ?></td>
            </tr>
        <?php endforeach; 
    endif; ?>
</tbody>
            </table>
        </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#leaveApplicationsTable').DataTable({
                "pageLength": 25,
                "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]], 
                "order": [[0, "asc"]],
                "columnDefs": [
                    { "orderable": false, "targets": [11] }
                ]
            });
        });
    </script>

</body>
</html>
