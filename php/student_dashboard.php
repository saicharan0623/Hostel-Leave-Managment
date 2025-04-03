<?php
session_start();
include 'database_config.php';

// Initialize error variable
$error = '';

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login_page.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$studentData = null;
$leaveDays = 0;

try {
    $query = "SELECT * FROM students WHERE student_id = :student_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['student_id' => $student_id]);
    $studentData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$studentData) {
        throw new Exception("Student data not found for ID: $student_id");
    }

    // Fetch leave count from leave applications
    $leaveQuery = "SELECT SUM(DATEDIFF(to_date, from_date)) as leaveDays 
                   FROM leave_applications 
                   WHERE student_id = :student_id AND status = 'APPROVED' AND leave_type = 'Academic'";
    
    $leaveStmt = $pdo->prepare($leaveQuery);
    $leaveStmt->execute(['student_id' => $student_id]);
    $leaveRow = $leaveStmt->fetch(PDO::FETCH_ASSOC);

    if ($leaveRow) {
        $leaveDays = intval($leaveRow['leaveDays']) ?: 0;
    }

    $updateQuery = "UPDATE students SET leave_count = :leaveCount WHERE student_id = :student_id";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute([
        'leaveCount' => $leaveDays,
        'student_id' => $student_id
    ]);

} catch (Exception $e) {
    error_log("Error in student dashboard: " . $e->getMessage());
    $error = "An error occurred: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
  <title>Student Dashboard</title>
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
    footer {
      background-color: rgb(99, 102, 106);
      color: #fff;
      padding: 10px;
      text-align: center;
      margin-top: auto;
    }
    .table-danger {
      background-color: #ba0c2f;
    }
    .error-message {
      background-color: rgba(220, 53, 69, 0.9);
      color: white;
      padding: 15px;
      margin: 10px 0;
      border-radius: 5px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body class="bg-light">
  <div class="container py-3">
    <div class="row">
      <div class="col-12 d-flex justify-content-between align-items-center">
        <div class="logo">
          <img src="../images/back7.png" alt="Logo" class="img-fluid" style="max-height: 70px;">
        </div>
      </div>
    </div>

    <?php if ($error): ?>
    <div class="row mt-3">
      <div class="col-12">
        <div class="error-message">
          <?php echo htmlspecialchars($error); ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($studentData): ?>
    <div class="row mt-3">
      <div class="col-12 text-center">
        <h1 class="display-6 text-danger fw-bold">Welcome, <?php echo htmlspecialchars($studentData['student_name']); ?></h1>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-12">
        <table class="table table-bordered table-hover">
          <thead class="table-danger text-white">
            <tr>
              <th colspan="2">Student Details</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>Name:</strong></td>
              <td><?php echo htmlspecialchars($studentData['student_name']); ?></td>
            </tr>
            <tr>
              <td><strong>Student ID:</strong></td>
              <td><?php echo htmlspecialchars($studentData['student_id']); ?></td>
            </tr>
            <tr>
              <td><strong>Email:</strong></td>
              <td><?php echo htmlspecialchars($studentData['student_email']); ?></td>
            </tr>
            <tr>
              <td><strong>Department:</strong></td>
              <td><?php echo htmlspecialchars($studentData['department']); ?></td>
            </tr>
            <tr>
              <td><strong>Batch:</strong></td>
              <td><?php echo htmlspecialchars($studentData['batch']); ?></td>
            </tr>
            <tr>
              <td><strong>Phone:</strong></td>
              <td><?php echo htmlspecialchars($studentData['phone']); ?></td>
            </tr>
            <tr>
              <td><strong>Gender:</strong></td>
              <td><?php echo htmlspecialchars($studentData['gender']); ?></td>
            </tr>
            <tr>
              <td><strong>Leave Counts:</strong></td>
              <td><?php echo $leaveDays; ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="row text-center">
      <div class="col-12 mt-4">
        <div class="d-flex flex-column flex-md-row justify-content-center">
          <a href="outpass.php" class="btn btn-dark mb-2 mb-md-0 me-md-2">Leave Form</a>
          <a href="inpass.php" class="btn btn-dark mb-2 mb-md-0 me-md-2">Return Form</a>
          <a href="daypass.php" class="btn btn-dark mb-2 mb-md-0 me-md-2">Day Outing Form</a>
          <a href="latepass.php" class="btn btn-dark mb-2 mb-md-0 me-md-2">Late Entry Form</a>
          <a href="logout.php" class="btn btn-dark">Logout</a>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <footer>
    <span>2024 &copy; MALDE SAICHARAN - STME All rights reserved.</span>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>