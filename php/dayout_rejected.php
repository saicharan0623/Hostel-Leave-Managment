<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login_page.php");
    exit();
}

include 'database_config.php';

$adminId = $_SESSION['admin_id']; // Assuming you store admin's id in session

try {
    // Query to join day_outing_requests with students table based on student_id
    $query = 'SELECT dor.*, s.student_name, s.student_email, s.gender, s.department, s.batch, s.phone
              FROM day_outing_requests dor
              JOIN students s ON dor.student_id = s.student_id
              WHERE dor.status = "REJECTED" AND dor.processed_by = :adminId';

    $stmt = $pdo->prepare($query);
    // Bind parameters
    $stmt->bindParam(':adminId', $adminId, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$pdo = null;
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
            padding: 15px;
            width: 100%;
        }
        .btn {
            margin: 10px;
        }
        td, th {
            border-color: #000;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="logo">
            <img src="../images/back7.png" alt="Logo" class="img-fluid" style="max-height: 60px;">
        </div>
        <a href="#" class="btn btn-secondary" onclick="window.history.back(); return false;">Back</a>
    </div>

    <h1 class="text-center text-danger">Rejected Day Outings</h1>

    <div class="table-responsive table-container">
        <table id="leaveApplicationsTable" class="table table-striped table-bordered" width="100%">
            <thead class="table-danger">
                <tr>
                    <th>Sl.No</th>
                    <th>SAP ID</th>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>School</th>
                    <th>Batch</th>
                    <th>Mobile Number</th>
                    <th>Out Date</th>
                    <th>Out time</th>
                    <th>In time</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($results)): ?>
                    <tr>
                        <td colspan="13">No records found.</td>
                    </tr>
                <?php else: 
                    $serialNumber = 1;
                    foreach ($results as $row): ?>
                        <tr>
                            <td><?= $serialNumber++ ?></td>
                            <td><?= $row['student_id'] ?? 'N/A' ?></td>
                            <td><?= $row['student_name'] ?? 'N/A' ?></td>
                            <td><?= $row['student_email'] ?? 'N/A' ?></td>
                            <td><?= $row['gender'] ?? 'N/A' ?></td>
                            <td><?= $row['school'] ?? 'N/A' ?></td>
                            <td><?= $row['batch'] ?? 'N/A' ?></td>
                            <td><?= $row['phone'] ?? 'N/A' ?></td>
                            <td><?= $row['outdate'] ?? 'N/A' ?></td>
                            <td><?= $row['outime'] ?? 'N/A' ?></td>
                            <td><?= $row['intime'] ?? 'N/A' ?></td>
                            <td><?= $row['reason'] ?? 'N/A' ?></td>
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
                "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
            });
        });
    </script>
</body>
</html>
