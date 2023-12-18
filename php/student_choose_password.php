<?php
session_start();
$email = $_SESSION['email'];
// Replace these values with your actual database configuration
$hostname = "localhost";
$username = "root";
$password = "";
$database = "college_db";

// .......database connection code .........

// Establish the PDO connection
try {
    $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve the new passwords from the form
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Check if passwords match
    if ($newPassword !== $confirmPassword) {
        echo "Passwords do not match.";
        exit();
    }

    //CONVERT PASSWORD TO ARGON2 AND INSERT
    // Hash the password securely using Argon2
    $hashedPassword = password_hash($newPassword, PASSWORD_ARGON2I);

    // Prepare the SQL INSERT statement
    //$sql = "INSERT INTO password_reset (email,password, reset_token, token_expires) VALUES (:email,:new_password,:reset_token,:token_expires)";
    $sql = "INSERT INTO password_reset (email,password) VALUES (:email,:new_password)";
    $stmt = $pdo->prepare($sql);

    $reset_token = '';
    $token_expires = '';   
    
    // Bind parameters and execute the statement
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    //$stmt->bindParam(':new_password', $newPassword, PDO::PARAM_STR);
    $stmt->bindParam(':new_password', $hashedPassword, PDO::PARAM_STR);
  /*   $stmt->bindParam(':reset_token', $reset_token, PDO::PARAM_STR);
    $stmt->bindParam(':token_expires', $token_expires, PDO::PARAM_STR); */
    
    try {
        $stmt->execute();
        echo "Password reset successful!";
        /* header("Location: student_login.php");
        exit(); */
        header("Location: ../public/student_login.html");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url("images/back4.jpg");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
          display: flex;
           flex-direction: column;
          justify-content: center;
          align-items: center;
         height: 100vh;
         text-align: center;
         }

       .logo {
            margin-bottom: 20px;
            text-align: center; /* Center the logo horizontally */
        }

        .logo img {
            width: 200px; /* Set the width of the logo */
            height: auto; /* Let the height adjust proportionally */
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            margin-bottom: 20px;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            font-size: 16px;
            margin-bottom: 10px;
        }

        input[type="password"],
        input[type="text"] {
            padding: 10px;
            font-size: 16px;
            width: 95%;
            border: 2px solid red;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #000;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #ff0000;
        }
    </style>
</head>
<body>
    <div class="container">
    <div class="logo">
            <img src="images/back7.png" alt="Logo">
            </div>
        <form method="POST">
            <label for="new_password">Please enter a new password of your choice</label>
            <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
            
            <label for="confirm_password">Re-enter your new password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter new password" required>

            <input type="submit" value="Submit">
            <?php include 'footer.php'; ?>
        </form>
    </div>

    <script>
        function showPassword() {
            var passwordInput = document.getElementById("new_password");
            var confirmPasswordInput = document.getElementById("confirm_password");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                confirmPasswordInput.type = "text";
            } else {
                passwordInput.type = "password";
                confirmPasswordInput.type = "password";
            }
        }
    </script>
</body>
</html>
