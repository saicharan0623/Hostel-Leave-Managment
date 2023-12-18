<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize variables
$email = '';
$message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    // Perform email validation
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Generate a reset token (for demonstration purposes)
        $resetToken = md5(uniqid());

        // Store the reset token in the session for later use (in a real scenario, you'd store it in a database)
        $_SESSION['reset_token'] = $resetToken;

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "college_db";

        // Create a new MySQLi connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check for connection errors
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Set the character set and collation for the connection
        $conn->set_charset('utf8mb4');

        //CHECK IF THE ABOVE EMAIL IS AVAILABLE IN THE password_reset table
        $currentDateTime = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM password_reset WHERE  email = '$email'";    
    
        $result = $conn->query($sql);
    
        if ($result->num_rows > 0) {
            //EMAIL IS AVAILABLE IN password_reset table update token and send reset password link via email
        

        // Store reset token and expiration time in the database
        $expiryTime = date('Y-m-d H:i:s', strtotime('+1 hour')); // Adjust as needed
        ////$sql = "INSERT INTO password_reset (email, reset_token, token_expires) VALUES ('$email', '$resetToken', '$expiryTime')";
        $sql = "UPDATE password_reset SET reset_token= '$resetToken', token_expires='$expiryTime' WHERE email = '$email'";

        if ($conn->query($sql) === TRUE) {
            // Send email
            require '../vendor/autoload.php'; // Include the Composer autoloader

            $mail = new PHPMailer(true);

            try {
                // Configure mailer settings
                $mail->isSMTP();
                //$mail->Host = 'smtp.example.com';
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'saicharanmalde@gmail.com';
                $mail->Password = 'lhthjcnefpkiedeo';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587; //SMTP port

                // Set sender and recipient
                $mail->setFrom('saicharanmalde@gmai.com', 'Your Name');
                $mail->addAddress($email);

                // Set email content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset';
                //$mail->Body ="Click the link to reset your password: http://localhost/Leave/Leave/php/reset.php?token=$resetToken";
                //$mail->Body ="Click the link to reset your password: http://localhost/Leave/php/student_reset_password.php?token=$resetToken";
                $mail->Body ="Click the link to reset your password: http://localhost/Leave/php/student_reset_password.php?token=$resetToken&email=$email";

                $mail->send();
                $message = "A password reset link has been sent to your email.";
            } catch (Exception $e) {
                $message = "An error occurred while sending the email: " . $mail->ErrorInfo;
            }

        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }else{
        //EMAIL IS NOT AVAILABLE IN password_reset table
        header("Location: student_login.php");
        exit(); 
    }
        

        // Close the database connection
        $conn->close();
    } else {
        $message = "Invalid email format.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body {
            background-image: url("images/back4.jpg");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .logo {
            position: absolute;
            top: 110px;
            left: calc(50% - 125px); /* Center the logo */
            width: 250px;
        }

        .container {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            max-width: 400px;
            background-color: rgba(255, 255, 255, 0.8);
        }

        h1 {
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

        input[type="email"] {
            padding: 10px;
            font-size: 16px;
            width: 90%;
            border: 2px solid red;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center; /* Align text in the email input */
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
    <img class="logo" src="images/back7.png" alt="Logo">
    <div class="container">
        <h1>Student Forgot Password</h1>
        <?php if (!empty($message)) { ?>
            <p><?php echo $message; ?></p>
        <?php } else { ?>
            <form action="student_forgot.php" method="POST">
                <label for="email">Enter your email:</label>
                <input type="email" id="email" name="email" required>
                <input type="submit" value="Reset Password">
            </form>
        <?php } ?>
    </div>
</body>
</html>
