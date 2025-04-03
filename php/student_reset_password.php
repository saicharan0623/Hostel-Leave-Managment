<?php
session_start();
include 'database_config.php';

$newPassword = '';
$newPasswordConfirm = '';
$resetValid = false; // Initialize resetValid to false

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_password'])) {
    $newPassword = $_POST["new_password"];
    $newPasswordConfirm = $_POST["new_password_confirm"]; // Get the confirmation password

    // Check if both passwords match
    if ($newPassword !== $newPasswordConfirm) {
        $passwordError = "Passwords do not match. Please try again.";
    }
}

// Check if the reset token is valid and not expired
if (isset($_GET['token']) && isset($_GET['email'])) {
    $resetToken = $_GET['token'];
    $email = $_GET['email'];

    $currentDateTime = date('Y-m-d H:i:s');
    
    // Query to check if the reset token is valid and has not expired
    $sql = "SELECT * FROM student WHERE reset_token = '$resetToken' AND reset_token_expires > '$currentDateTime' AND email = '$email'";    

    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        $resetValid = true;

        // Proceed to update password only if it is valid and passwords match
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_password']) && $newPassword === $newPasswordConfirm) {
            $hashedPassword = password_hash($newPassword, PASSWORD_ARGON2I);
            
            // Update the password in the student table
            $sqlUpd = "UPDATE student SET password = '$hashedPassword', reset_token = NULL, reset_token_expires = NULL WHERE email = '$email'";    

            $resultUpd = $mysqli->query($sqlUpd);
            if ($resultUpd) {
                $resetMessage = "Password reset successful! You can now login with your new password.";
            } else {
                $passwordError = "Error updating password. Please try again.";
            }
        }
    } else {
        $resetValid = false;
    }

    $mysqli->close();
} else {
    $resetValid = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-image: url("../images/back4.jpg");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="card" style="width: 400px;">
            <div class="card-body text-center">
                <?php if ($resetValid) { ?>
                    <h5 class="card-title">Reset Your Password</h5>
                    <?php if (isset($resetMessage)) { ?>
                        <p class="card-text text-success"><?php echo $resetMessage; ?></p>
                        <a href="student_login_page.php" class="btn btn-primary">Back to Login</a>
                    <?php } else { ?>
                        <form method="POST">
                            <div class="form-group">
                                <label for="email">Email: <?php echo htmlspecialchars($email); ?></label>
                            </div>
                            <div class="form-group">
                                <label for="new_password">New Password:</label>
                                <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Enter your new password" required>
                            </div>
                            <div class="form-group">
                                <label for="new_password_confirm">Confirm New Password:</label>
                                <input type="password" id="new_password_confirm" name="new_password_confirm" class="form-control" placeholder="Confirm your new password" required>
                            </div>
                            <?php if (isset($passwordError)) { ?>
                                <p class="text-danger"><?php echo $passwordError; ?></p>
                            <?php } ?>
                            <input type="submit" class="btn btn-dark" value="Reset Password">
                        </form>
                    <?php } ?>
                <?php } else { ?>
                    <p class="text-danger">Invalid or expired reset token. Please request a new password reset.</p>
                <?php } ?>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
