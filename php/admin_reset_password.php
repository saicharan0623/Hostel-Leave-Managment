<?php
session_start();

/* if (isset($_GET['token'])) {
    $email = $_GET["email"];
} */

$newPassword = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_password'])) {
$newPassword = $_POST["new_password"];
}

// Check if the reset token is valid and not expired
if (isset($_GET['token'])) {
    $resetToken = $_GET['token'];
    $email = $_GET["email"];
 

    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "college_db";
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the reset token exists and is not expired
    $currentDateTime = date('Y-m-d H:i:s');
    $sql = "SELECT * FROM password_reset WHERE reset_token = '$resetToken' AND token_expires > '$currentDateTime' AND email = '$email'";    

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Valid reset token, allow user to reset password
        $resetValid = true;
        //------------------------------------------------------------------------
        //CHECK IF THE ABOVE EMAIL IS AVAILABLE IN THE password_reset table and update the new password with Argon2 hashing
        //if above email is not available, redirect to student login page

        $hashedPassword = password_hash($newPassword, PASSWORD_ARGON2I);

        $sqlUpd = "UPDATE password_reset SET password = '$hashedPassword' WHERE email = '$email'";    

        $resultUpd = $conn->query($sqlUpd);
        //------------------------------------------------------------------------
    } else {
        // Invalid or expired reset token
        $resetValid = false;
    }

    // Close the database connection
    $conn->close();

   /*  header("Location: admin_login.php");
    exit();  */
} else {
    // No reset token provided
    $resetValid = false;
}

// Handle password reset form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_password']) && $resetValid) {
    // Validate and update the new password in your database
    // Implement your password update logic here

    // Display success message or error message
    $resetMessage = "Password reset successful! You can now login with your new password.";
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
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
            text-align: center;
            padding: 20px;
            max-width: 400px;
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

        input[type="password"] {
            padding: 10px;
            font-size: 16px;
            width: 90%;
            border: 2px solid red;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center; /* Align text in the password input */
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
        <?php if ($resetValid) { ?>
            <h2>Admin Reset Your Password</h2>
            <?php if (isset($resetMessage)) { ?>
                <p><?php echo $resetMessage; ?></p>
            <?php } else { ?>
                <form method="POST">
                    <label for="email"><?php echo $email; ?></label>
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Enter your new password" required>
                    <input type="submit" value="Reset Password">
                </form>
            <?php } ?>
        <?php } else { ?>
            <p>Invalid or expired reset token. Please request a new password reset.</p>
        <?php } ?>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
