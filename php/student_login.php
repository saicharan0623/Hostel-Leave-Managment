<?php
session_start();

//----------------- EXCEL -----------------------------
require '../vendor/autoload.php'; // Include the autoloader
use PhpOffice\PhpSpreadsheet\IOFactory;
$excelFilePath = '../sample.xlsx'; // Replace with the actual path to your Excel file
//----------------- EXCEL -----------------------------

// Handle student login authentication
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the submitted email and password
    $email = $_POST["email"];
    $passwordPost = $_POST["password"];

    echo 'EMAIL FROM POST : '.$email.'<br>';
    echo 'PASSWORD FROM POST : '.$passwordPost.'<br>';
    
    //----------------- EXCEL -----------------------------
    try {
        $spreadsheet = IOFactory::load($excelFilePath);
        $worksheet = $spreadsheet->getActiveSheet();
    
        $highestRow = $worksheet->getHighestRow(); // Get the highest row number
        $values = [];
    
        // Loop through rows, starting from the second row
        for ($row = 2; $row <= $highestRow; $row++) {
            $cellValue = $worksheet->getCellByColumnAndRow(1, $row)->getValue(); // Get value from the first column
            $values[] = $cellValue;
        }    
        // Display the extracted values
        $flg = false;
        foreach ($values as $value) {
            //echo $value . '<br>';
            if($value === $email){                
                $flg = true;
                break;
            }
        }
        if($flg){
            echo 'User is allowed to use the Application.' . '<br>';
            $_SESSION['email'] = $email;
            $_SESSION['time'] = time();            

            //STUDENT IS AVAILABLE IN EXCEL SHEET
            //NOW CHECK IF IN PASSWORD_RESET TABLE
            //IF NOT AVAILABLE...SEND HIM TO CHOOSE PASSWORD
            //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
            $hostname = "localhost";
            $username = "root";
            $password = "";
            $database = "college_db";

            try {
                // Create a new PDO instance
                $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
                
                // Set PDO error mode to exception
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Query to retrieve leave requests
                $query = 'SELECT * FROM password_reset where email="'.$email.'"';
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                
                // Fetch results as associative array
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo $results;
                if (empty($results)) {
                    echo 'User is NOT allowed to use the Application, as NOT FOUND in password_reset table.' . '<br>';
                    header("Location: student_choose_password.php");
                    exit();
                }else{
                       foreach ($results as $row){
                        $storedHashedPassword = $row['password'];
                        echo 'THIS IS THE PASSWORD FROM POST : '.$passwordPost.'<br>';
                        echo 'HASHED PASSWORD FROM TABLE : '.$storedHashedPassword.'<br>';
                        if (password_verify(trim($passwordPost), trim($storedHashedPassword))) {
                            // Passwords match
                            echo "Login successful!";
                            $_SESSION['imageUrl'] = 'Please take a picture for submitting.';
                            header("Location: student_dashboard.php");
                            exit(); 
                            } else {
                            // Passwords do not match
                            echo "Login failed.";
                            header("Location: student_login_failed.php");
                            exit(); 
                            }
                    }                  
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
            /* header("Location: student_dashboard.php");
            exit(); */
        } else {
            echo 'User is NOT allowed to use the Application.' . '<br>';
            header("Location: error.php");
            exit();
        }
    } catch (Exception $e) {
        echo 'An error occurred: ' . $e->getMessage();
    }
    //----------------- EXCEL -----------------------------
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-top: 50px;
        }

        form {
            width: 300px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #333;
            text-decoration: none;
        }

        /* Additional styles for the "Forgot Password" button */
        .forgot-button {
            width: 100%;
            padding: 10px;
            background-color: #ccc;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Student Login</h1>
    <form action="student_login.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <input type="submit" value="Login">
    </form>

    <a class="forgot-button" href="forgot.php">Forgot Password</a>
    <?php include 'footer.php'; ?>
</body>
</html>
