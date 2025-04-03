<?php
session_start();

// Check if the admin is logged in using admin_id session
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

include('database_config.php');

// Initialize the variable for storing student data and unique school list
$allData = [];
$schools = [];

// Fetch all student data from the 'students' table
try {
    $stmt = $pdo->query("SELECT * FROM students");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $allData[] = $row;

        // Collect unique schools (departments)
        if (!in_array($row['department'], $schools)) {
            $schools[] = $row['department'];
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #2196F3;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        td,th{
        border-color: #000;
       }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <div class="d-flex justify-content-between align-items-center p-3">
        <div class="logo">
            <img src="../images/back7.png" alt="Logo" class="img-fluid" style="max-height: 70px;">
        </div>
        <a href="admin_panel.php" class="btn btn-dark">Back</a>
    </div>

    <main class="row mt-4">
        <div class="col-12">
            <h2 class="text-center mb-4">Manage Student Access</h2>

            <!-- School Filter -->
            <div class="text-center mb-3">
                <label for="schoolFilter" class="form-label">Filter by School:</label>
                <select id="schoolFilter" class="form-select w-25 mx-auto">
                    <option value="">All Schools</option>
                    <?php foreach ($schools as $school): ?>
                        <option value="<?php echo $school; ?>"><?php echo $school; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="table-responsive table-container">
                <table id="studentDataTable" class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>School</th>
                            <th>Batch</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php foreach ($allData as $row): ?>
        <tr data-school="<?php echo $row['department']; ?>">
            <td><?php echo $row['student_id']; ?></td>
            <td><?php echo $row['student_name']; ?></td>
            <td><?php echo $row['department']; ?></td>
            <td><?php echo $row['batch']; ?></td>
            <td><?php echo $row['student_email']; ?></td>
            <td><?php echo $row['phone']; ?></td>
            <td><?php echo $row['gender']; ?></td>
            <td>
                <label class="switch">
                    <input type="checkbox" data-student-id="<?php echo $row['student_id']; ?>"
                        <?php echo $row['access'] == 1 ? 'checked' : ''; ?>>
                    <span class="slider"></span>
                </label>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

                </table>
            </div>
        </div>
    </main>

    <footer class="mt-auto py-3 bg-secondary text-white">
    <div class="container text-center">
      <span>&copy; MALDE SAICHARAN All rights reserved.</span>
    </div>
  </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        const table = $('#studentDataTable').DataTable({
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "order": [[0, "asc"]],
            "columnDefs": [
                { "orderable": false, "targets": [7] } // 'Action' column is not orderable
            ]
        });

        // School Filter
        $('#schoolFilter').on('change', function() {
            var selectedSchool = $(this).val();
            if (selectedSchool === "") {
                table.column(2).search('').draw();  // Reset filter if no school is selected
            } else {
                table.column(2).search(selectedSchool).draw();  // Apply search filter on the school column
            }
        });
    });
</script>


<script>
  document.querySelectorAll('.switch input[type="checkbox"]').forEach(input => {
    input.addEventListener('click', function () {
        const studentId = this.dataset.studentId; // Extract student ID
        const newState = this.checked ? 1 : 0; // Determine new state

        // Send AJAX request to update access state
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_access_students.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                console.log('Access updated successfully');
            } else {
                console.error('Failed to update access');
            }
        };
        xhr.send('student_id=' + studentId + '&new_state=' + newState);
    });
});

</script>

<script>
    document.querySelectorAll('.toggle').forEach(button => {
        button.addEventListener('click', function () {
            const action = this.dataset.action;
            const newState = action === 'on' ? 1 : 0;
            console.log('Toggle clicked. Action:', action);
        });
    });
</script>


</body>
</html>
