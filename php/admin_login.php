<?php
session_start();

//----------------- EXCEL -----------------------------
require '../vendor/autoload.php'; // Include the autoloader
use PhpOffice\PhpSpreadsheet\IOFactory;
$excelFilePath = './admin.xlsx'; // Replace with the actual path to your Excel file
//----------------- EXCEL -----------------------------

// Handle admin login authentication
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the submitted email and password
    $email = $_POST["email"];
    $passwordPost = $_POST["password"];

    echo 'ADMIN EMAIL FROM POST : '.$email.'<br>';
    echo 'ADMIN PASSWORD FROM POST : '.$passwordPost.'<br>';

    //----------------- EXCEL -----------------------------
    try {
        $spreadsheet = IOFactory::load($excelFilePath);
        $worksheet = $spreadsheet->getActiveSheet();

        $highestRow = $worksheet->getHighestRow(); // Get the highest row number
        $values = [];

        // Loop through rows, starting from the second row
        for ($row = 2; $row <= $highestRow; $row++) {
            $cellValue = $worksheet->getCellByColumnAndRow(1, $row)->getValue(); // Get value from the first column
            echo 'cellValue :: '.$cellValue.'<br>';
            $values[] = $cellValue;
        }
     
        

        // Check if the submitted email is in the list
        if (in_array($email, $values)) {
             //--------- GETTING POSITION -------------
             $position = '';
             $finalPosition = '';
            echo 'EMAIL FROM POST : '.$email.'<br>';
            for ($row = 2; $row <= $highestRow; $row++) {
                $cellValue = $worksheet->getCellByColumnAndRow(1, $row)->getValue();//email
                echo 'cellValue :: '.$cellValue.'<br>';
                if($cellValue == $email){
                $position = $worksheet->getCellByColumnAndRow(3, $row)->getValue(); // position
                $finalPosition = str_replace(' ', '-', $position);
                echo 'email :: '.$email.' ---- position :: '.$finalPosition.'<br>';
                }
            }
        //--------- GETTING POSITION -------------
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
                    echo 'Admin is NOT allowed to use the Application, as NOT FOUND in password_reset table.' . '<br>';
                    header("Location: admin_choose_password.php");
                    exit();
                }else{
                       foreach ($results as $row){
                        $storedHashedPassword = $row['password'];
                        echo 'ADMIN  THIS IS THE PASSWORD FROM POST : '.$passwordPost.'<br>';
                        echo 'ADMIN  HASHED PASSWORD FROM TABLE : '.$storedHashedPassword.'<br>';
                        if (password_verify(trim($passwordPost), trim($storedHashedPassword))) {
                            // Passwords match
                            echo "ADMIN Login successful!";
                            //header("Location: admin_panel.php");
                            header("Location: admin_panel.php?finalPosition=".$finalPosition);                            
                            exit(); 
                            } else {
                            // Passwords do not match
                            echo "ADMIN  Login failed.";
                            header("Location: admin_login_failed.php");
                            
                            exit(); 
                            }
                    }                  
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
           /*  header("Location: admin_panel.php");
            exit(); */
        } else {
            header("Location: error.php");
            exit();
        }
    } catch (Exception $e) {
        echo 'An error occurred: ' . $e->getMessage();
    }
    //--------------------------EXCEL----------------------------------------
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <!-- Add any necessary CSS styles here -->
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
        .footer {
        background: rgb(99, 102, 106);
        text-align: center;
        padding: 10px 0;
        color: #fff;
        }
    </style>
</head>
<!-- <body>
    <form action="admin_login.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <input type="submit" value="Login">
    </form>
</body> -->
<body>
    <h1>Admin Login</h1>
    <form action="admin_login.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <input type="submit" value="Login">
    </form>
    <a class="forgot-button" href="admin_forgot.php">Forgot Password</a>
    <footer class="footer">
        <div class="container-fluid">
            &copy; MALDE SAICHARAN All rights reserved.
        </div>
     </footer>
</body>
</html>
