<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login_page.php");
    exit();
}

// Include database connection
require 'database_config.php'; // Replace with your database connection file

// Fetch admin details
$admin_id = $_SESSION['admin_id'];
$query = "SELECT name, email FROM admins WHERE id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Update query
    $update_query = "UPDATE admins SET name = ?, email = ?";

    // Handle password update if provided
    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $update_query .= ", password_hash = ?";
    }
    $update_query .= " WHERE id = ?";

    $stmt = $mysqli->prepare($update_query);
    if (!empty($password)) {
        $stmt->bind_param("sssi", $name, $email, $password_hash, $admin_id);
    } else {
        $stmt->bind_param("ssi", $name, $email, $admin_id);
    }
    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
    } else {
        $message = "Error updating profile: " . $stmt->error;
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        .form-group {
            margin-bottom: 1rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
        }
        .form-input {
            width: 100%;
            padding: 0.5rem;
            font-size: 1rem;
        }
        .form-button {
            padding: 0.5rem 1rem;
            font-size: 1rem;
            background-color: #2a3f54;
            color: #ffffff;
            border: none;
            cursor: pointer;
        }
        .message {
            margin-top: 1rem;
            color: green;
        }
        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Admin Panel</h1>
    
    <!-- Profile Information -->
    <div class="profile-section">
        <div class="profile-image">
            <div class="profile-initial">
                <?php echo htmlspecialchars($admin['name'][0]); ?>
            </div>
        </div>
        <div class="profile-info">
            <div class="profile-name"><?php echo htmlspecialchars($admin['name']); ?></div>
            <div class="profile-email"><?php echo htmlspecialchars($admin['email']); ?></div>
            <!-- Edit Profile Button -->
            <button class="form-button" id="editProfileBtn">Edit Profile</button>
        </div>
    </div>

    <!-- Modal for Editing Profile -->
    <div id="editProfileModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2>Edit Profile</h2>
            <?php if (isset($message)) { ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php } ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="name">Name:</label>
                    <input class="form-input" type="text" id="name" name="name" value="<?php echo htmlspecialchars($admin['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email:</label>
                    <input class="form-input" type="email" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">New Password (optional):</label>
                    <input class="form-input" type="password" id="password" name="password">
                </div>
                <button class="form-button" type="submit">Update Profile</button>
            </form>
        </div>
    </div>

    <script>
        // Modal functionality
        var modal = document.getElementById("editProfileModal");
        var btn = document.getElementById("editProfileBtn");
        var span = document.getElementById("closeModal");

        // Open the modal
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // Close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Close the modal if clicked outside
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
