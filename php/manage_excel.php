<?php
session_start();

include 'database_config.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Define the path to Excel file
$excelFilePath = "../Excel/students_list.xlsx";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'import_from_excel':
                try {
                    // Load Excel file
                    $spreadsheet = IOFactory::load($excelFilePath);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $rows = $worksheet->toArray();
                    
                    // Remove header row
                    array_shift($rows);
                    
                    $mysqli->begin_transaction();
                    
                    // Clear existing data
                    $mysqli->query("TRUNCATE TABLE students");
                    
                    // Prepare insert statement
                    $stmt = $mysqli->prepare("INSERT INTO students (student_id, student_name, department, student_email, 
                                            parent_email, phone, gender, batch) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    
                    foreach ($rows as $row) {
                        if (!empty($row[0])) { // Only insert if student ID exists
                            $stmt->bind_param("ssssssss", 
                                $row[0], $row[1], $row[2], $row[3], 
                                $row[4], $row[5], $row[6], $row[7]
                            );
                            $stmt->execute();
                        }
                    }
                    
                    $mysqli->commit();
                    $_SESSION['message'] = 'Excel data imported successfully!';
                } catch (Exception $e) {
                    $mysqli->rollback();
                    $_SESSION['error'] = 'Error importing Excel data: ' . $e->getMessage();
                }
                break;

            case 'update':
                try {
                    $mysqli->begin_transaction();
                    
                    $stmt = $mysqli->prepare("UPDATE students SET 
                        student_name = ?, 
                        department = ?,
                        student_email = ?,
                        parent_email = ?,
                        phone = ?,
                        gender = ?,
                        batch = ?
                        WHERE student_id = ?");
                        
                    $stmt->bind_param("ssssssss",
                        $_POST['student_name'],
                        $_POST['department'],
                        $_POST['student_email'],
                        $_POST['parent_email'],
                        $_POST['phone'],
                        $_POST['gender'],
                        $_POST['batch'],
                        $_POST['student_id']
                    );
                    
                    $stmt->execute();
                    
                    if($stmt->affected_rows >= 0) {
                        $mysqli->commit();
                        $_SESSION['message'] = 'Student updated successfully!';
                    } else {
                        throw new Exception("Error updating student");
                    }
                } catch (Exception $e) {
                    $mysqli->rollback();
                    $_SESSION['error'] = 'Error: ' . $e->getMessage();
                }
                break;

            case 'delete':
                try {
                    $mysqli->begin_transaction();
                    
                    $tables = [
                        'user_logs',
                        'student_inform',
                        'leave_applications',
                        'late_outing',
                        'day_outing_requests'
                    ];
                    
                    $student_id = $_POST['student_id'];
                    
                    foreach($tables as $table) {
                        $stmt = $mysqli->prepare("DELETE FROM $table WHERE student_id = ?");
                        $stmt->bind_param("s", $student_id);
                        $stmt->execute();
                    }
                    
                    $stmt = $mysqli->prepare("DELETE FROM students WHERE student_id = ?");
                    $stmt->bind_param("s", $student_id);
                    $stmt->execute();
                    
                    if($stmt->affected_rows > 0) {
                        $mysqli->commit();
                        $_SESSION['message'] = 'Student deleted successfully!';
                    } else {
                        throw new Exception("Student not found");
                    }
                } catch (Exception $e) {
                    $mysqli->rollback();
                    $_SESSION['error'] = 'Error: ' . $e->getMessage();
                }
                break;
        }
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Get student data for edit modal
if (isset($_GET['get_student'])) {
    $stmt = $mysqli->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $_GET['get_student']);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    
    header('Content-Type: application/json');
    echo json_encode($student);
    exit;
}

// Fetch database data
$result = $mysqli->query("SELECT * FROM students ORDER BY student_name");
$dbData = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/datatables@1.10.18/media/css/jquery.dataTables.min.css" rel="stylesheet">
    <style>

body {
    margin: 0;
    padding: 0;
    background-image: url("../images/back4.jpg");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    font-family: 'Arial', sans-serif;
    min-height: 100vh; 
    display: flex;
    justify-content: center;
    align-items: center;
}
        .action-buttons .btn {
            margin-right: 5px;
        }
        .alert {
            margin-top: 20px;
        }
        .data-section {
            margin-bottom: 40px;
        }
        .excel-info {
            margin-bottom: 20px;
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
    <a href="admin_panel.php" class="btn btn-dark float-end">Back</a>

        <h2 class="mb-4">Student Management System</h2>
        
        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
  <!-- Excel Operations -->
  <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Excel Operations</h5>
            </div>
            <div class="card-body row">
                <div class="col-md-6">
                    <h6>Import Excel Data</h6>
                    <form method="POST" action="upload_excel.php" enctype="multipart/form-data" class="mb-3" id="excelUploadForm">
                        <div class="mb-3">
                            <input type="file" class="form-control" name="excel_file" accept=".xlsx,.xls" required>
                        </div>
                        <div class="btn-group">
                            <button type="submit" name="action" value="import_replace" class="btn btn-warning"
                                    onclick="return confirm('This will replace the existing file. Continue?')">
                                Replace Existing File
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <h6>Export Data</h6>
                    <a href="../Excel/students_list.xlsx" class="btn btn-success" download>
                        Download Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- Database Data Section -->
        <div class="data-section">
            <h3>Database Data</h3>
            <div class="card">
                <div class="card-body">
                    <table id="dbTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Batch</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($dbData as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['department']); ?></td>
                                    <td><?php echo htmlspecialchars($student['student_email']); ?></td>
                                    <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($student['batch']); ?></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-primary btn-sm" 
                                                onclick="editStudent('<?php echo $student['student_id']; ?>')">
                                            Edit
                                        </button>
                                        <button class="btn btn-danger btn-sm" 
                                                onclick="deleteStudent('<?php echo $student['student_id']; ?>')">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="student_id" id="edit_student_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="student_name" id="edit_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" name="department" id="edit_department" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Student Email</label>
                            <input type="email" class="form-control" name="student_email" id="edit_student_email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Parent Email</label>
                            <input type="email" class="form-control" name="parent_email" id="edit_parent_email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" name="phone" id="edit_phone" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Gender</label>
                            <select class="form-select" name="gender" id="edit_gender" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Batch</label>
                            <input type="text" class="form-control" name="batch" id="edit_batch" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="student_id" id="delete_student_id">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables@1.10.18/media/js/jquery.dataTables.min.js"></script>
    
    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#studentsTable').DataTable({
                pageLength: 10,
                order: [[1, 'asc']], // Sort by name by default
                language: {
                    search: "Search students:"
                }
            });
        });

        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        
        function editStudent(studentId) {
            // Fetch student data
            fetch(`?get_student=${studentId}`)
                .then(response => response.json())
                .then(student => {
                    document.getElementById('edit_student_id').value = student.student_id;
                    document.getElementById('edit_name').value = student.student_name;
                    document.getElementById('edit_department').value = student.department;
                    document.getElementById('edit_student_email').value = student.student_email;
                    document.getElementById('edit_parent_email').value = student.parent_email;
                    document.getElementById('edit_phone').value = student.phone;
                    document.getElementById('edit_gender').value = student.gender;
                    document.getElementById('edit_batch').value = student.batch;
                    
                    editModal.show();
                })
                .catch(error => {
                    alert('Error loading student data');
                    console.error('Error:', error);
                });
        }
        
        function deleteStudent(studentId) {
            if (confirm('Are you sure you want to delete this student? This action cannot be undone.')) {
                document.getElementById('delete_student_id').value = studentId;
                document.getElementById('deleteForm').submit();
            }
        }

        function submitExcelForm() {
            if (confirm('Uploading a new Excel file will replace all existing student data. Continue?')) {
                document.getElementById('excelUploadForm').submit();
            }
        }
    </script>
    <script>
        $(document).ready(function() {
    // Initialize DataTable
    $('#dbTable').DataTable({
        searching: true,       // Enable search functionality
        lengthChange: true,    // Enable the option to change number of rows displayed
        pageLength: 10,        // Set default rows per page to 10
        lengthMenu: [5, 10, 15, 20],  // Set the options for rows per page
        language: {
            search: "Filter records:",   // Custom search label
            lengthMenu: "Show _MENU_ rows per page" // Custom rows per page label
        }
    });
});

    </script>
</body>
</html>
<?php
// Close the database connection
$mysqli->close();
?>