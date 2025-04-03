<?php
session_start();
include 'database_config.php';

// Ensure admin is logged in
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login_page.php");
    exit();
}

// Retrieve the admin ID from session
$adminId = $_SESSION['admin_id'];

// Query to join day_outing_requests with students table based on student_id and status is 'PENDING'
$query = "SELECT dor.*, s.student_name, s.student_email, s.gender, s.department, s.batch, s.phone
          FROM day_outing_requests dor
          JOIN students s ON dor.student_id = s.student_id
          WHERE dor.status = 'PENDING'";

$stmt = $pdo->prepare($query);
$stmt->execute();
$leaveRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Day Outing Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
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
        <a href="admin_panel.php" class="btn btn-secondary">Back</a>
    </div>

    <h1 class="text-center text-danger">Day Outing Requests</h1>

    <div class="d-flex justify-content-center mb-4">
        <a href="dayout_approved.php" class="btn btn-danger">Approved</a>
        <a href="dayout_rejected.php" class="btn btn-danger">Rejected</a>
        <a href="dayout_dataselect.php" class="btn btn-success">Export Data</a>
    </div>

    <div class="table-responsive table-container">
        <table id="dayouitngsTable" class="table table-striped table-bordered" width="100%">
            <thead class="table-danger">
                <tr>
                    <th>Req.no</th>
                    <th>SAP ID</th>
                    <th>Student Name</th>
                    <th>Mobile</th>
                    <th>Gender</th>
                    <th>School</th>
                    <th>Batch</th>
                    <th>Out Date</th>
                    <th>Out Time</th>
                    <th>In Time</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Approve</th>
                    <th>Reject</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leaveRequests)): ?>
                    <tr><td colspan="14" class="text-center">No Pending Requests Found</td></tr>
                <?php else: ?>
                    <?php 
                    foreach ($leaveRequests as $row): 
                    ?>
                    <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['department']); ?></td>
                        <td><?php echo htmlspecialchars($row['batch']); ?></td>
                        <td><?php echo htmlspecialchars($row['outdate']); ?></td>
                        <td><?php echo htmlspecialchars($row['outime']); ?></td>
                        <td><?php echo htmlspecialchars($row['intime']); ?></td>
                        <td><?php echo htmlspecialchars($row['reason']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <form method="POST" action="dayout_approve.php">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="student_id" value="<?php echo $row['student_id']; ?>">
                                <button type="submit" class="btn btn-success btn-sm" name="approve">Approve</button>
                            </form>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="showRejectionReasonForm('<?php echo $row['id']; ?>', '<?php echo $row['student_id']; ?>', '<?php echo addslashes($row['student_name']); ?>', '<?php echo addslashes($row['student_email']); ?>', '<?php echo addslashes($row['gender']); ?>', '<?php echo addslashes($row['department']); ?>', '<?php echo addslashes($row['phone']); ?>', '<?php echo $row['batch']; ?>', '<?php echo addslashes($row['outdate']); ?>', '<?php echo addslashes($row['intime']); ?>', '<?php echo addslashes($row['outime']); ?>', '<?php echo addslashes($row['request_date']); ?>', '<?php echo addslashes($row['reason']); ?>')">Reject</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
      function showRejectionReasonForm(id, studentId) {
    const rejectionReason = prompt("Please enter the reason for rejection:");
    if (rejectionReason) {
        window.location.href = "dayout_reject.php?" + new URLSearchParams({
            id: id,
            student_id: studentId,
            rejection_reason: rejectionReason
        }).toString();
    }
}


        $(document).ready(function() {
            $('#dayouitngsTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                order: [[0, 'asc']],
                language: {
                    emptyTable: "No Pending Requests Found",
                    search: "Filter records:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });
        });
    </script>
</body>
</html>
