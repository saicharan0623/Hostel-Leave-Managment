<?php
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: admin_login_page.php");
    exit();
}
$email = $_SESSION["email"];
include 'database_config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    if ($newPassword !== $confirmPassword) {
        echo "Passwords do not match.";
        exit();
    }

$hashedPassword = password_hash($newPassword, PASSWORD_ARGON2I);
    
  
    $sql = "INSERT INTO password_reset (email,password) VALUES (:email,:new_password)";
    $stmt = $pdo->prepare($sql);

    $reset_token = '';
    $token_expires = '';   
    
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':new_password', $hashedPassword, PDO::PARAM_STR);
    
    try {
        $stmt->execute();
        header("Location: admin_login_page.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
       <link rel="icon" href="../images/ico.png" type="image/x-icon">

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
            width:100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-10">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="text-center my-3">
                            <!-- Logo inside the card -->
                            <img src="../images/back7.png" alt="Logo" class="img-fluid mb-3" style="max-width: 150px;">
                        </div>
                        <h3 class="card-title text-center mb-3">Reset Your Password</h3>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Enter New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                <small id="passwordHelp" class="form-text text-muted">Use at least 8 characters, including uppercase, lowercase, and numbers.</small>
                                <div id="passwordStrength" class="mt-2"></div>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <div id="passwordMismatch" class="mt-2 text-danger" style="display:none;">Passwords do not match!</div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const passwordStrength = document.getElementById('passwordStrength');
        const passwordMismatch = document.getElementById('passwordMismatch');

        passwordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const strength = getPasswordStrength(password);
            passwordStrength.innerHTML = strength;
            passwordStrength.className = strength === 'Strong' ? 'text-success' : strength === 'Medium' ? 'text-warning' : 'text-danger';
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>