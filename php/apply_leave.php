<?php
session_start();

//----------------- EXCEL -----------------------------
require '../vendor/autoload.php'; // Include the autoloader
use PhpOffice\PhpSpreadsheet\IOFactory;
$excelFilePath = '../sample.xlsx'; // Replace with the actual path to your Excel file
//----------------- EXCEL -----------------------------

//------------------- EMAIL ---------------------------
require '../vendor/autoload.php'; // Include the autoloader
require '../vendor/phpmailer/phpmailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$mail = new PHPMailer(true);
//------------------- EMAIL ---------------------------

$email = $_SESSION['email']; // Student
echo $email . '<br>';
echo $_POST["from_date"] . '<br>';
echo $_POST["year"] . '<br>';
echo $_POST["to_date"] . '<br>';
echo $_POST["reason"] . '<br>';
echo $_POST['imageData']. '<br>';
echo $_POST['intime'] .'<br>';
echo $_POST['outime'] .'<br>';
echo $_POST['attendance'] .'<br>';
// Initialize imageUrl
$imageUrl = '';
//$imageData = '';

// Check if imageData is set
if (isset($_POST['imageData'])) {
    $imageData = $_POST['imageData'];
    $imageName = 'image_' . time() . '.jpg'; // Generate a unique image name

    echo $imageName . '<br>';


    // Save the image to the folder
    $imagePath = 'photos/' . $imageName;

    if (!is_dir('photos')) {
        mkdir('photos', 0777, true); // Create the photos folder recursively
    }

    if (file_put_contents($imagePath, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData)))) {
        echo 'Image saved successfully.';
        $imageUrl = 'http://localhost/leave/php/' . $imagePath; // Update with your actual URL
        $_SESSION['imageUrl'] = $imageUrl;
        $_SESSION['imageData'] = $imageData;
    } else {
        echo 'Image save failed: ' . error_get_last()['message'];
    }
}

//----------------- EXCEL -----------------------------
// Handle leave application submission by students

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reason']) && isset($_SESSION['imageUrl'])) {
    // Retrieve the submitted form data
    $school = isset($_POST["school"]) ? $_POST["school"] : "";
    $year = isset($_POST["year"]) ? $_POST["year"] : "";
    $name = isset($_POST["name"]) ? $_POST["name"] : "";
    $id = isset($_POST["id"]) ? $_POST["id"] : "";
    $mobile = isset($_POST["mobile"]) ? $_POST["mobile"] : "";
    $fromDate = isset($_POST["from_date"]) ? $_POST["from_date"] : "";
    $toDate = isset($_POST["to_date"]) ? $_POST["to_date"] : "";
    $reason = isset($_POST["reason"]) ? $_POST["reason"] : "";
    $academic = isset($_POST["academic"]) ? $_POST["academic"] : "";
    $gender = isset($_POST["gender"]) ? $_POST["gender"] : "";
    $intime = isset($_POST["intime"]) ? $_POST["intime"] : "";
    $outime = isset($_POST["outime"]) ? $_POST["outime"] : "";
    $attendance = isset($_POST["attendance"]) ? $_POST["attendance"] :"" ;
    

    // Establish a database connection (replace with your own credentials)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "college_db";

    if ($academic === 'Academic') {
        $status = "PENDING-WITH-ADMIN";
    } else {
        $status = "PENDING-WITH-RECTOR";
    };

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO leave_applications (school, year, name, id, mobile, status, email, from_date, to_date, reason, imageUrl, academic, gender, intime, outime,attendance) VALUES (:school, :year, :name, :id, :mobile, :status, :email, :from_date, :to_date, :reason, :imageUrl, :academic, :gender, :intime, :outime, :attendance)");

        // Bind the parameters with the form data
        $stmt->bindParam(":school", $school);
        $stmt->bindParam(":year", $year);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":mobile", $mobile);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":from_date", $fromDate);
        $stmt->bindParam(":to_date", $toDate);
        $stmt->bindParam(":reason", $reason);
        $stmt->bindParam(":imageUrl", $_SESSION['imageUrl']);
        $stmt->bindParam(":academic", $academic);
        $stmt->bindParam(":gender",$gender);
        $stmt->bindParam(":intime",$intime);
        $stmt->bindParam(":outime",$outime);
        $stmt->bindParam(":attendance", $attendance);

        // Execute the statement
        $stmt->execute();

        //----------------- EXCEL -----------------------------
        $recepientEmail = '';

        $spreadsheet = IOFactory::load($excelFilePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();

        // Loop through rows, starting from the second row
        for ($row = 2; $row <= $highestRow; $row++) {
            $cellValue = $worksheet->getCellByColumnAndRow(1, $row)->getValue();

            if ($cellValue === $email) {
                $nextColumnValue = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                break;
            }
        }
        if ($nextColumnValue !== null) {
            echo "Match found!<br>";
            echo "Value from the next column: " . $nextColumnValue;
            $recepientEmail = $nextColumnValue;
        } else {
            echo "Match not found!";
        }
        //----------------- EXCEL -----------------------------

        //------------------- EMAIL ---------------------------
        $dateTime = new DateTime($fromDate);
        $formattedFromDate = $dateTime->format('F, j, Y');

        $dateTime = new DateTime($toDate);
        $formattedToDate = $dateTime->format('F, j, Y');
        //---------------------------
        $imageUrl = $_SESSION['imageUrl'];
        $pathParts = pathinfo($imageUrl);
        $imageFilename = $pathParts['filename'] . '.jpg'; // This will give you "image_1698221063"

        //---------------------------
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
            echo $recepientEmail . "<br>";
            echo '======= recepietEmail =========='  . "<br>";

            // Recipients
            ////$mail->setFrom('saicharanmalde@gmail.com', 'Sender Name');
            //$mail->addAddress('saicharanmalde@gmail.com', 'Recipient Name');
            //$mail->addAddress('venkatdada369@gmail.com', 'Recipient Name');
            //$mail->addAddress('narresh.gudimetlaa@gmail.com', 'Recipient Name');

            ////$mail->setFrom('saicharannmalde@gmail.com', 'Sender Name');
            $mail->addAddress($recepientEmail, 'Recipient Name');
        
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'NMIMS Email';            
            $imageData = $_SESSION['imageData'];
            $mail->AddEmbeddedImage("photos/".$imageFilename, "my-image", $imageFilename);
            $mail->Body = '
                <html>
                <head>
                    <title>Leave Application</title>
                </head>
                <body>
                    <p>Hello,</p>
                    <p>NIMIMS College Administration,</p>
                    <p>This email is to inform you about a leave application submitted by your ward.</p>
                    
                    <h3>Student Information</h3>
                    <p><strong>Student Name:</strong> ' . $name . '</p>
                    <p><strong>Year Studying:</strong> ' . $year . '</p>
                    <p><strong>Student ID:</strong> ' . $id . '</p>        
                    <h3>Leave Details</h3>
                    <p><strong>Leave Start Date:</strong> ' . $formattedFromDate . '</p>
                    <p><strong>Leave End Date:</strong> ' . $formattedToDate . '</p>
                    <p><strong>Reason:</strong> ' . $reason . '</p>
                    <p><strong>Attendance:</strong> '.$attendance .'</p>
                    <p><strong>Captured Image:</strong></p>                    
                    <img src="cid:my-image" alt="Captured Image" width="200">

                    <p>Thank you.</p>
                </body>
                </html>
            ';

            // Send the email
            $mail->send();
            echo "Email sent successfully.";

            //---------------------- CHECK AND REMOVE UNUSED PHOTOS --------------------------
            $dbHost = 'localhost';
            $dbUsername = 'root';
            $dbPassword = '';
            $dbName = 'college_db';

            $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Step 1: Retrieve the List of Images from the Table
            $query = "SELECT imageUrl FROM leave_applications";
            $result = $conn->query($query);

            $databaseImages = array();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $databaseImages[] = basename($row['imageUrl']);
                }
            }

            // Step 2: Scan the Folder
            $folderPath = 'photos';
            $folderImages = scandir($folderPath);

            // Step 3: Compare and Delete
            foreach ($folderImages as $image) {
                if ($image != '.' && $image != '..' && in_array($image, $databaseImages) === false) {
                    $imagePath = $folderPath . '/' . $image;
                    if (unlink($imagePath)) {
                        echo "Deleted: $imagePath<br>";
                    } else {
                        echo "Failed to delete: $imagePath<br>";
                    }
                }
            }

            $conn->close();
            //---------------------- CHECK AND REMOVE UNUSED PHOTOS --------------------------

        } catch (Exception $e) {
            echo "Email sending failed. Error: {$mail->ErrorInfo}";
        }
        //------------------- EMAIL ---------------------------

        // Redirect the student to a success page or back to the leave application form with a success message
        header("Location: leave_success.php");
        exit();
    } catch (PDOException $e) {
        // Handle any database errors
        echo "Error: " . $e->getMessage();
    }
}

// ... (Rest of your code)
?>
