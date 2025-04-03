<?php
// Assuming you have a connection to your database
include('database_config.php');

// Handle form submission for adding admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    // Password hash
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert new admin into the database
    $query = "INSERT INTO admins (name, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param("sssss", $name, $email, $password_hash, $role, $status);

        $message = '';
        if ($stmt->execute()) {
            $message = 'Admin added successfully!';
            header("Location: admin_add.php"); 
            exit(); // Ensure no further code execution
        } else {
            $message = 'Error: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        $message = 'Error preparing statement: ' . $mysqli->error;
    }
}

// Handle admin deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $query = "DELETE FROM admins WHERE id = ?";
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param("i", $delete_id);
        $message = '';
        $status = 'error';
        if ($stmt->execute()) {
            $message = 'Admin deleted successfully!';
            $status = 'success';
            // Redirect to clear URL
            header("Location: admin_add.php"); 
            exit(); // Ensure no further code execution
        } else {
            $message = 'Error: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = 'Error preparing statement: ' . $mysqli->error;
    }
}

// Handle admin edit
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    // Fetch admin details for editing
    $query = "SELECT * FROM admins WHERE id = ?";
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_admin'])) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $role = $_POST['role'];
            $status = $_POST['status'];  

            // Update admin details including status
            $query = "UPDATE admins SET name = ?, email = ?, role = ?, status = ? WHERE id = ?";
            if ($stmt = $mysqli->prepare($query)) {
                $stmt->bind_param("ssssi", $name, $email, $role, $status, $edit_id);

                $message = '';
                if ($stmt->execute()) {
                    $message = 'Admin updated successfully!';
                    // Redirect to remove the query parameters
                    header("Location: admin_add.php"); 
                    exit(); // Ensure no further code execution
                } else {
                    $message = 'Error: ' . $stmt->error;
                }

                $stmt->close();
            } else {
                $message = 'Error preparing statement: ' . $mysqli->error;
            }
        }

        $stmt->close();
    } else {
        $message = 'Error preparing statement: ' . $mysqli->error;
    }
}

// Fetch all admins from the database
$query = "SELECT * FROM admins";
$result = mysqli_query($mysqli, $query);
$admins = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background-image: url("../images/back4.jpg");
            background-size: cover;
            background-repeat: no-repeat;
            margin:0px;
            padding: 10px;
        }
        .dashboard-container {
            padding: 30px;
        }
        .card-header {
            background-color: #f8f9fa;
            font-size: 1.25rem;
            font-weight: 500;
        }
        table {
            margin-top: 20px;
        }
        .btn-danger {
            background-color: rgb(210, 35, 42);
            border: none;
        }
        .btn-back {
            position: absolute;
            right: 20px;
            top: 20px;
        }
    </style>
</head>
<body>
    <div class="container dashboard-container">
        <h1 class="text-center mb-4">Manage Admins</h1>
        
        <!-- Back Button -->
        <a href="admin_panel.php" class="btn btn-secondary btn-back">Back</a>

        <!-- Add Admin Form -->
        <div class="card">
            <div class="card-header">Add New Admin</div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name:</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role:</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="RECTOR">RECTOR</option>
                            <option value="WARDEN">WARDEN</option>
                            <option value="STAFF">STAFF</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status:</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="ACTIVE">Active</option>
                            <option value="INACTIVE">Inactive</option>
                            <option value="SUSPENDED">Suspended</option>
                        </select>
                    </div>

                    <button type="submit" name="add_admin" class="btn btn-primary">Add Admin</button>
                </form>
            </div>
        </div>

       <!-- Admin Edit Form -->
<?php if (isset($admin)): ?>
    <div class="card mt-5">
        <div class="card-header">Edit Admin</div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="name" class="form-label">Name:</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $admin['name']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $admin['email']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role:</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="RECTOR" <?php echo ($admin['role'] == 'RECTOR') ? 'selected' : ''; ?>>RECTOR</option>
                        <option value="WARDEN" <?php echo ($admin['role'] == 'WARDEN') ? 'selected' : ''; ?>>WARDEN</option>
                        <option value="STAFF" <?php echo ($admin['role'] == 'STAFF') ? 'selected' : ''; ?>>STAFF</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status:</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="ACTIVE" <?php echo ($admin['status'] == 'ACTIVE') ? 'selected' : ''; ?>>Active</option>
                        <option value="INACTIVE" <?php echo ($admin['status'] == 'INACTIVE') ? 'selected' : ''; ?>>Inactive</option>
                        <option value="SUSPENDED" <?php echo ($admin['status'] == 'SUSPENDED') ? 'selected' : ''; ?>>Suspended</option>
                    </select>
                </div>
                <button type="submit" name="edit_admin" class="btn btn-warning">Update Admin</button>
            </form>
        </div>
    </div>
<?php endif; ?>
 <table class="table table-bordered table-striped mt-5">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td><?php echo $admin['name']; ?></td>
                        <td><?php echo $admin['email']; ?></td>
                        <td><?php echo $admin['role']; ?></td>
                        <td><?php echo $admin['status']; ?></td>
                        <td>
                            <a href="?edit_id=<?php echo $admin['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="?delete_id=<?php echo $admin['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
