<?php
session_start();
include 'database_config.php';

if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];
    
    function logUserLogout($pdo, $student_id) {
        $logoutTime = date('Y-m-d H:i:s'); // Get current time
        $stmt = $pdo->prepare("UPDATE user_logs SET logout_time = ?, message = 'Logged Out' WHERE student_id = ? AND logout_time IS NULL ORDER BY login_time DESC LIMIT 1");
        $stmt->execute([$logoutTime, $student_id]);
    }

    logUserLogout($pdo, $student_id);

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"], $params["secure"], $params["httponly"]
        );
    }

    session_destroy();

    header("Location: student_login_page.php");
    exit();
} else {
    header("Location: student_login_page.php");
    exit();
}
?>
