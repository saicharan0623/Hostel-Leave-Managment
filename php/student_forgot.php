<?php
session_start();
include 'database_config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = '';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    // Perform email validation
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Generate a reset token (for demonstration purposes)
        $resetToken = md5(uniqid());

        // Store the reset token in the session for later use (in a real scenario, you'd store it in a database)
        $_SESSION['reset_token'] = $resetToken;

        //CHECK IF THE ABOVE EMAIL IS AVAILABLE IN THE password_reset table
        $currentDateTime = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM students WHERE  student_email = '$email'";    
    
        $result = $mysqli->query($sql);
    
        if ($result->num_rows > 0) {
            //EMAIL IS AVAILABLE IN password_reset table update token and send reset password link via email
        

        // Store reset token and expiration time in the database
        $expiryTime = date('Y-m-d H:i:s', strtotime('+1 hour')); // Adjust as needed
        $sql = "UPDATE students SET reset_token= '$resetToken', token_expires='$expiryTime' WHERE student_email = '$email'";

        if ($mysqli->query($sql) === TRUE) {
            // Send email
            require '../vendor/autoload.php';

            $mail = new PHPMailer(true);

            try {
                // Configure mailer settings
                $mail->isSMTP();
                //$mail->Host = 'mail.nmimshyd.in';
                $mail->Host = 'mail.nmimshyd.in';
                $mail->SMTPAuth = true;
                $mail->Username = 'hostelrector@nmimshyd.in';
                $mail->Password = 'Nmims@123$';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465; 
                $mail->setFrom('hostelrector@nmimshyd.in', 'Admin');
                $mail->addAddress($email);

            
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset';
               
                $mail->Body =$mail->Body = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color:rgb(186, 12, 47);
            color: white;
            padding: 10px 0;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            margin: 20px 0;
            font-size: 16px;
            line-height: 1.5;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-top: 20px;
        }
        a.reset-button {
            display: inline-block;
            background-color: rgb(186, 12, 47);
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: bold;
        }
        a.reset-button:hover {
            background-color: #e64a19; /* Darker shade on hover */
        }

        /* Responsive Styles */
        @media (max-width: 600px) {
            .container {
                width: 90%;
                padding: 15px;
            }
            .header h2 {
                font-size: 20px;
            }
            .content {
                font-size: 14px;
            }
            a.reset-button {
                width: 50%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Password Reset Request</h2>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>We received a request to reset your password. You can reset your password by clicking the button below:</p>
            <a class="reset-button" href="https://nmimshyd.in/leave/php/student_reset_password.php?token=' . $resetToken . '&email=' . $email . '">Reset Password</a>
            <p>If you did not request a password reset, please ignore this email.</p>
            <p>Thank you!</p>
        </div>
        <div class="footer">
            <p>Best Regards,<br>NMIMS Hostel Admin</p>
        </div>
    </div>
</body>
</html>';

                $mail->send();
                $message = "A password reset link has been sent to your email.";
                
            } catch (Exception $e) {
                $message = "An error occurred while sending the email: " . $mail->ErrorInfo;
            }

        } else {
            $message = "Error: " . $sql . "<br>" . $mysqli->error;
        }
    }else{
        header("Location: student_login.php");
        exit(); 
    }
        

        // Close the database mysqliection
        $mysqli->close();
    } else {
        $message = "Invalid email format.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url("../images/back4.jpg");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }
        .container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
 <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card" style="width: 100%; max-width: 400px;">
        <img class="card-img-top mx-auto mt-3" src="../images/back7.png" alt="Logo" style="max-width: 100px; height: auto;">
        <div class="card-body">
            <h5 class="card-title text-center">Student Forgot Password</h5>
            <?php if (!empty($message)) { ?>
                <div class="alert alert-info" role="alert">
                    <?php echo $message; ?>
                </div>
                <div class="text-center mt-3">
                    <a href="student_login_page.php" class="btn btn-secondary">Back to Login</a>
                </div>
            <?php } else { ?>
                <form action="student_forgot.php" method="POST">
                    <div class="form-group">
                        <label for="email">Enter your email:</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-danger btn-block mt-3">Reset Password</button>
                </form>
            <?php } ?>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
