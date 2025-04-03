<?php
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: student_login_page.php");
    exit();
}

$student_id = $_SESSION["student_id"];
require 'database_config.php';

$stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = :student_id");
$stmt->execute(['student_id' => $student_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$successMessage = '';  // Initialize the success message variable
$emailError = false;   // Initialize email error flag
$dataComplete = false; // Initialize data completeness flag

if ($result) {
    // The student exists, now handle password update
    $studentEmail = $result['student_email'];
    $parentEmail = $result['parent_email'];

    if ($studentEmail === $parentEmail) {
        $emailError = true; // Flag email error if the student and parent emails are the same
    }

    if (!$emailError) {
        $dataComplete = true; // Set dataComplete to true if no errors
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST" && !$emailError) {
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword !== $confirmPassword) {
            $passwordError = "Passwords do not match.";
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_ARGON2I);

            // Update the password
            $sql = "UPDATE students SET password = :new_password WHERE student_id = :student_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_STR);
            $stmt->bindParam(':new_password', $hashedPassword, PDO::PARAM_STR);

            try {
                $stmt->execute();
                $successMessage = "Password reset successful! You can now log in with your new password.";
            } catch (PDOException $e) {
                $passwordError = "Error resetting password: " . $e->getMessage();
            }
        }
    }
} else {
    // The student doesn't exist, handle error or redirect
    $errorMsg = "Student not found in the database!";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $dataComplete ? 'Password Reset' : 'Data Error'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body, html {
            height: 100%;
            background-image: url("../images/back4.jpg");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }
        .container {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            max-width: 600px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 col-sm-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="text-center my-3">
                            <img src="../images/back7.png" alt="Logo" class="img-fluid mb-3" style="max-width: 150px;">
                        </div>
                        <?php if ($dataComplete): ?>
                            <h3 class="card-title text-center mb-3">Reset Your Password</h3>
                            <?php if (!empty($passwordError)): ?>  <!-- Check if there is an actual error -->
                                <div class="alert alert-danger"><?php echo $passwordError; ?></div>
                            <?php endif; ?>
                            <?php if ($successMessage): ?>
                                <div class="alert alert-success"><?php echo $successMessage; ?></div>
                                <div class="text-center mt-3">
                                    <a href="student_login_page.php" class="btn btn-primary">Login Now</a>
                                </div>
                            <?php else: ?>
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">Enter New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        <small id="passwordHelp" class="form-text text-muted">Use at least 8 characters, including uppercase, lowercase, and numbers.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        <div id="passwordMismatch" class="mt-2 text-danger" style="display:none;">Passwords do not match!</div>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">Reset Password</button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <h3 class="card-title text-center mb-3 text-danger">Data Error</h3>
                            
                            <?php if ($emailError): ?>
                                <div class="alert alert-danger">
                                    <h5>Email Error</h5>
                                    <p>The student email and parent email are the same. This is not allowed.</p>
                                    <p>Please contact the administrator to update the email addresses.</p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($missingFields)): ?>
                                <div class="alert alert-danger">
                                    <h5>Missing Information</h5>
                                    <p>The following information is missing or incomplete:</p>
                                    <ul>
                                        <?php foreach ($missingFields as $field): ?>
                                            <li><?php echo $field; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <p>Please ensure that all required fields are filled in your Excel file.</p>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const passwordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const passwordStrength = document.getElementById('passwordStrength');
        const passwordMismatch = document.getElementById('passwordMismatch');

        passwordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const strength = getPasswordStrength(password);
            let strengthText = '';
            let strengthClass = '';

            switch(strength) {
                case 'Strong':
                    strengthText = 'Strong password';
                    strengthClass = 'text-success';
                    break;
                case 'Medium':
                    strengthText = 'Medium strength password';
                    strengthClass = 'text-warning';
                    break;
                default:
                    strengthText = 'Weak password';
                    strengthClass = 'text-danger';
            }
            
            passwordStrength.textContent = strengthText;
            passwordStrength.className = strengthClass;
        });

        confirmPasswordInput.addEventListener('input', function() {
            const confirmPassword = confirmPasswordInput.value;
            const password = passwordInput.value;
            
            if (confirmPassword !== password) {
                passwordMismatch.style.display = 'block';
            } else {
                passwordMismatch.style.display = 'none';
            }
        });

        function getPasswordStrength(password) {
            const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
            const mediumRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/;

            if (strongRegex.test(password)) {
                return 'Strong';
            } else if (mediumRegex.test(password)) {
                return 'Medium';
            } else {
                return 'Weak';
            }
        }
    </script>

</body>
</html>
