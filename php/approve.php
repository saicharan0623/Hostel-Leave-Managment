<?php
 session_start();
 //------------------- EMAIL ---------------------------
 require '../vendor/autoload.php'; // Include the autoloader
 require '../vendor/phpmailer/phpmailer/src/SMTP.php';
 use PHPMailer\PHPMailer\PHPMailer;
 use PHPMailer\PHPMailer\Exception;
 $mail = new PHPMailer(true);
 //------------------- EMAIL ---------------------------
// Get the email provided during login from the session
$email = $_SESSION['email'];//student
echo $email . '<br>'; 
if (!isset($_SESSION["email"])) {
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $name = $_GET['name'];
    $mobile = $_GET['mobile'];
    $from_date = $_GET['from_date'];
    $to_date = $_GET['to_date'];
    $reason = $_GET['reason'];
    $email_student = $_GET['email_student'];
    $finalPosition = $_GET['finalPosition'];
    $created_at = $_GET['created_at'];
    $id = $_GET['id'];
    
    echo  'FINAL POSITION : '.$finalPosition.'<br>';
    
    // Replace with your database connection details
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "college_db";
    
    try {
        $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Update the status to "Approved"
        //$updateQuery = "UPDATE leave_applications SET status = 'APPROVED' WHERE id = :id";
        $updateQuery = '';
        if($finalPosition == 'Rector'){
            $updateQuery = "UPDATE leave_applications SET status = 'APPROVED' WHERE id = :id AND created_at = :created_at";
        }else{
            $updateQuery = "UPDATE leave_applications SET status = 'PENDING-WITH-RECTOR' WHERE id = :id AND created_at = :created_at";
        }
        
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->execute();

         //------------------- EMAIL ---------------------------
         if($finalPosition == 'Rector'){
         $dateTime = new DateTime($fromDate);
         $formattedFromDate = $dateTime->format('F, j, Y');
 
         $dateTime = new DateTime($toDate);
         $formattedToDate = $dateTime->format('F, j, Y');
         try {
             // Server settings
             $mail->isSMTP();
             //$mail->Host       = 'smtp.example.com'; // SMTP Server    
             $mail->Host       = 'smtp.gmail.com'; // Gmail SMTP      server
             $mail->SMTPAuth   = true;
             $mail->Username   = 'saicharanmalde@gmail.com'; // SMTP username
             $mail->Password   = 'lhthjcnefpkiedeo'; // SMTP password
             $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encryption (tls or ssl)
             $mail->Port       = 587; // SMTP port
         
             echo '======= recepietEmail =========='  . "<br>";
             echo $email . "<br>";
             echo '======= recepietEmail =========='  . "<br>";
 
             
             $mail->setFrom('saicharannmalde@gmail.com', 'NMIMS');
             $mail->addAddress($email_student, 'Recipient Name');

                    
             // Content
             $mail->isHTML(true);
             $mail->Subject = 'Leave Approved';
             //$mail->Body    = 'This is a test email sent from PHPMailer.';
 
             $mail->Body = '
            <html>
             <head>
                 <title>Leave Approval</title>
                 <style>
                     body {
                         font-family: Arial, sans-serif;
                         margin: 0;
                         padding: 20px;
                         background-color: #f9f9f9; /* Light gray background */
                     }
                     .container {
                         max-width: 600px;
                         margin: 0 auto;
                         padding: 20px;
                         background-color: #fff; /* White background */
                         border: 1px solid #ccc; /* Add a border for a professional look */
                         border-radius: 5px; /* Rounded corners for a softer appearance */
                     }
                     h1 {
                         text-align: center;
                         color: red; /* Dark gray text */
                     }
                     table {
                         width: 100%;
                         border-collapse: collapse;
                     }
                     th, td {
                         padding: 10px;
                         border: 1px solid #ccc; /* Add borders to the table cells */
                     }
                     th {
                         background-color: #f2f2f2; /* Light gray background for table headers */
                         text-align: left;
                     }
                     p {
                         color: #555; /* Slightly darker text color */
                     }
                     strong {
                         font-weight: bold;
                     }
                 </style>
             </head>
             <body>
                 <div class="container">
                     <h1>OUT PASS</h1>
                     <p>Hello,</p>
                     <p>NIMIMS College Administration,</p>
                     <p>This email is to inform you that your leave application has been <strong>Approved</strong>.</p>
                     
                     <h3>Student Information</h3>
                     <table>
                         <tr>
                             <th>Student Name:</th>
                             <td>' . $name . '</td>
                         </tr>
                         <tr>
                             <th>Student ID:</th>
                             <td>' . $id . '</td>
                         </tr>
                         <tr>
                             <th>Mobile No:</th>
                             <td>' . $mobile . '</td>
                         </tr>
                     </table>
                     
                     <h3>Leave Details</h3>
                     <table>
                         <tr>
                             <th>Leave Start Date:</th>
                             <td>' . $from_date . '</td>
                         </tr>
                         <tr>
                             <th>Leave End Date:</th>
                             <td>' . $to_date . '</td>
                         </tr>
                         <tr>
                             <th>Reason:</th>
                             <td>' . $reason . '</td>
                         </tr>
                     </table>
                     <p>Thank you.</p>
                 </div>
             </body>
             </html>
             
             ';
             //check
             $mail->send();
             echo "Email sent successfully.";
         } catch (Exception $e) {
             echo "Email sending failed. Error: {$mail->ErrorInfo}";
         }
        }
         //------------------- EMAIL ---------------------------
        
           //header("Location: admin_panel.php"); // Redirect back to the main page
           header("Location: admin_panel.php?finalPosition=".$finalPosition);                            
           exit(); 
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
