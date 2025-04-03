<?php
session_start();
require '../vendor/autoload.php';

function logAdminActivity($pdo, $admin_id, $action, $details, $login_in_time = null) {
    $ip_address = $_SERVER['REMOTE_ADDR']; // Capture the IP address
    $created_at = date('Y-m-d H:i:s'); // Current timestamp
    
    // If login_in_time is not provided, use the current time
    $login_in_time = $login_in_time ?? $created_at;

    $query = "INSERT INTO admin_logs (admin_id, action, details, ip_address, created_at, login_in_time) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$admin_id, $action, $details, $ip_address, $created_at, $login_in_time]);
}

// Handle admin login authentication
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the submitted email and password
    $email = $_POST["email"];
    $passwordPost = $_POST["password"];

    include 'database_config.php';

    try {
        // Query to fetch admin details by email
        $query = "SELECT * FROM admins WHERE email = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$email]);

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            // Verify the password
            if (password_verify(trim($passwordPost), trim($admin['password_hash']))) {
                // Set session variables
                $_SESSION['email'] = $admin['email'];
                $_SESSION['role'] = $admin['role'];
                $_SESSION['admin_id'] = $admin['id']; // Store admin ID in session
                $_SESSION['time'] = time();

                // Log successful login with login time
                logAdminActivity($pdo, $admin['id'], 'Login', 'Admin logged in successfully', date('Y-m-d H:i:s'));

                // Redirect to admin panel
                header("Location: admin_panel.php"); // No role passed in URL
                exit();
            } else {
                // Log failed login due to incorrect password
                logAdminActivity($pdo, null, 'Login Failed', 'Incorrect password');
                header("Location: admin_login_failed.php");
                exit();
            }
        } else {
            // Log failed login due to email not found
            logAdminActivity($pdo, null, 'Login Failed', 'Email not found');
            header("Location: error.php");
            exit();
        }
    } catch (PDOException $e) {
        // Log database error
        logAdminActivity($pdo, null, 'Error', 'Database error: ' . $e->getMessage());
        echo "Error: " . $e->getMessage();
    }
}
?>
