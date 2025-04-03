<?php
include 'database_config.php';

function getDistinctDepartments($pdo) {
    $stmt = $pdo->query("SELECT DISTINCT department FROM students");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getNumberOfStudentsOnLeave($pdo, $department) {
    $currentDate = date("Y-m-d");

    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS count 
        FROM leave_applications la
        JOIN students s ON la.student_id = s.student_id
        WHERE la.status = 'Approved' 
        AND :currentDate BETWEEN la.from_date AND la.to_date
        AND s.department = :department
    ");
    $stmt->bindParam(':currentDate', $currentDate);
    $stmt->bindParam(':department', $department);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

function getTotalStudents($pdo, $department) {
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total_count FROM students WHERE department = :department");
    $stmt->bindParam(':department', $department);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_count'];
}

function getGenders($pdo, $department) {
    $stmt = $pdo->prepare("
        SELECT 
            SUM(CASE WHEN s.gender = 'Male' THEN 1 ELSE 0 END) AS total_boys,
            SUM(CASE WHEN s.gender = 'Female' THEN 1 ELSE 0 END) AS total_girls
        FROM students s
        WHERE s.department = :department
    ");
    $stmt->bindParam(':department', $department);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("
        SELECT 
            SUM(CASE WHEN s.gender = 'Male' THEN 1 ELSE 0 END) AS boys_on_leave,
            SUM(CASE WHEN s.gender = 'Female' THEN 1 ELSE 0 END) AS girls_on_leave
        FROM leave_applications la
        JOIN students s ON la.student_id = s.student_id
        WHERE la.status = 'Approved'
        AND :currentDate BETWEEN la.from_date AND la.to_date
        AND s.department = :department
    ");
    $currentDate = date("Y-m-d");
    $stmt->bindParam(':currentDate', $currentDate);
    $stmt->bindParam(':department', $department);
    $stmt->execute();
    $leaveResult = $stmt->fetch(PDO::FETCH_ASSOC);

    $result['total_boys_on_leave'] = $leaveResult['boys_on_leave'];
    $result['total_girls_on_leave'] = $leaveResult['girls_on_leave'];

    return $result;
}

$departments = getDistinctDepartments($pdo);
$selected_department = isset($_GET['department']) ? $_GET['department'] : (isset($departments[0]) ? $departments[0] : null);

$department_data = array();
if ($selected_department) {
    $department_data[$selected_department]['total_students'] = getTotalStudents($pdo, $selected_department);
    $department_data[$selected_department]['number_of_students_on_leave'] = getNumberOfStudentsOnLeave($pdo, $selected_department);
    $department_data[$selected_department]['genders'] = getGenders($pdo, $selected_department);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Leave Data</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
       body {
            background-image: url('../images/back4.jpg');
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .container {
            margin-top: 50px;
        }

        .card-header {
            background-color: rgb(210, 35, 42);
            color: white;
        }

        .card-body {
            font-size: 16px;
        }

        .btn-custom {
            background-color: rgb(210, 35, 42);
            color: white;
        }

        .btn-custom:hover {
            background-color: rgb(210, 35, 42);
        }

        .form-control {
            background-color: #f8f9fa;
            color: black;
        }

        .navbar-nav .nav-link {
            color: white;
        }

        .navbar-nav .nav-link:hover {
            color: #f8f9fa;
        }

        .back-btn-container {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .text-maroon {
            color: rgb(210, 35, 42);
        }

        .form-group label {
            font-weight: bold;
        }

        .scrollable-form {
            max-height: 400px;
            overflow-y: auto;
        }

        .icon-size {
            font-size: 40px;
            margin-right: 10px;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .table-custom th {
            background-color: rgb(210, 35, 42);
            color: white;
        }

        .table-custom td {
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center text-maroon mb-4">Student Leave Data</h2>

    <!-- Department Selection Form -->
    <form method="GET" action="" class="mb-4">
        <div class="form-group">
            <label for="department">Select Department:</label>
            <select name="department" id="department" class="form-control" onchange="this.form.submit()">
                <option value="">Select Department</option>
                <?php
                foreach ($departments as $department) {
                    $selected = $selected_department == $department ? 'selected' : '';
                    echo "<option value='$department' $selected>$department</option>";
                }
                ?>
            </select>
        </div>
    </form>

    <!-- Back Button -->
    <a href="admin_panel.php" class="btn btn-custom mb-3 back-btn-container">
        <span class="material-icons">arrow_back</span> Back
    </a>

    <?php if (isset($department_data[$selected_department])): ?>
        <!-- Department Data -->
        <div class="card">
            <div class="card-header">
                <h4>Department: <?= htmlspecialchars($selected_department) ?></h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-12 col-md-6">
                        <div class="d-flex align-items-center">
                            <span class="material-icons icon-size">group</span>
                            <h5>Total Students: <?= $department_data[$selected_department]['total_students'] ?></h5>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex align-items-center">
                            <span class="material-icons icon-size">event_busy</span>
                            <h5>Students on Leave: <?= $department_data[$selected_department]['number_of_students_on_leave'] ?></h5>
                        </div>
                    </div>
                </div>

                <h6 class="mb-2">Gender Distribution:</h6>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Gender</th>
                                <th>Total</th>
                                <th>On Leave</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Boys</td>
                                <td><?= $department_data[$selected_department]['genders']['total_boys'] ?></td>
                                <td><?= $department_data[$selected_department]['genders']['total_boys_on_leave'] ?></td>
                            </tr>
                            <tr>
                                <td>Girls</td>
                                <td><?= $department_data[$selected_department]['genders']['total_girls'] ?></td>
                                <td><?= $department_data[$selected_department]['genders']['total_girls_on_leave'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-4">
            <strong>Info:</strong> Please select a department to view the data.
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
