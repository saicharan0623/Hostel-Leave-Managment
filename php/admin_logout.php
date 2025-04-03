<?php
session_start();
include 'database_config.php'; // Assuming this includes database connection setup

// Check if admin is logged in
if (isset($_SESSION['ema'])) {
    $admin_id = $_SESSION['admin_id']; // Get admin_id from session

    // Function to log admin logout
    function logAdminLogout($pdo, $admin_id) {
        try {
            $logoutTime = date('Y-m-d H:i:s'); // Get current time
            // Make sure to fetch the correct record for logout
            $stmt = $pdo->prepare("UPDATE admin_logs 
                                   SET logout_time = ?, status = 'Logged Out' 
                                   WHERE id = ? AND logout_time IS NULL 
                                   ORDER BY login_in_time DESC LIMIT 1");
            $stmt->execute([$logoutTime, $admin_id]);
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log("Error logging out admin: " . $e->getMessage());
        }
    }

    // Log admin logout
    logAdminLogout($pdo, $admin_id);

    // Unset the session variables related to the admin
    unset($_SESSION['admin_id']); // Unset the admin_id session variable

    // Optionally clear all session variables
    // $_SESSION = array();

    // If it's desired to kill the session, also delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finally, destroy the session
    session_destroy();

    // Redirect to the admin login page
    header("Location: admin_login_page.php");
    exit();
} else {
    // If not logged in, redirect to the admin login page
    header("Location: admin_login_page.php");
    exit();
}
?>
