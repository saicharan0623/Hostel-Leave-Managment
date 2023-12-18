<script>
    setTimeout(function () {
        var successMessageElement = document.getElementById('successMessage');
        if (successMessageElement) {
            successMessageElement.style.display = 'none';
        }
    }, 3000); // 5000 milliseconds = 5 seconds
</script>
<?php
session_start();

// Check if a success message is set in the session
if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    // Display the success message
    echo '<div style="font-size:25px;color:green;font-weight:bold;text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);" id="successMessage">' . $successMessage . '</div>';
    // Clear the success message from the session
    unset($_SESSION['success_message']);
}
require '../vendor/autoload.php'; // Include the autoloader
use PhpOffice\PhpSpreadsheet\IOFactory;
// Path to your Excel file
$excelFilePath = '../sample.xlsx';
$email = 'saicharan.m033@nmims.edu.in';


// Load the Excel file
$spreadsheet = IOFactory::load($excelFilePath);
$worksheet = $spreadsheet->getActiveSheet();
$highestRow = $worksheet->getHighestRow();

// Define column letters
/* $columnLetters = [
    'A' => 'student_mail',
    'B' => 'parent_mail',
    'C' => 'name',
    'D' => 'student_id',
    'E' => 'department',
    'F' => 'mobile_number',
    'G' => 'batch',
    'H' => 'gender',
]; */

// Find the row that matches the provided email
$studentData = null;
for ($row = 2; $row <= $highestRow; $row++) {
    if ($worksheet->getCell('A' . $row)) {
        $studentData = [
            'id' => $row - 1,
            // Assuming your IDs start from 1
            'student_name' => $worksheet->getCell('C' . $row)->getValue(),
            'student_id' => $worksheet->getCell('D' . $row)->getValue(),
            'email' => $email,
            'department' => $worksheet->getCell('E' . $row)->getValue(),
            'batch' => $worksheet->getCell('G' . $row)->getValue(),
            'phone' => $worksheet->getCell('F' . $row)->getValue(),
            'gender' => $worksheet->getCell('H' . $row)->getValue(),
            // Add more student data as needed
        ];
        break;
    }
}
// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check the value of the submitted action
    $action = isset($_POST['action']) ? $_POST['action'] : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Leave Request</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <title>About Us</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/css/styles.css">
    <!-- <link rel="stylesheet" href="../css/styles1.css"> -->
    <link rel="stylesheet" href="table.css">
</head>
<style>
     body {
        margin: 0;
        padding: 0;
        background-image: url("images/back4.jpg");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        font-family: Arial, sans-serif;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }


    .content{
        max-width: 1000px;
        /* max-width: 100%; */ /* Adjust the maximum width as per your preference */

        margin-top: 75px;     
        width: 80%;      
        border: 2px solid #ccc;
        padding: 20px;
        box-sizing: border-box;
        border-radius: 10px;
        text-align: center;
        /* border:1px solid red; */
    }
    div {
       /*  border:1px solid blue; */
    }
    
    h1 {
        font-size: 2rem;
        color:rgb(186,12,47);
        margin-top: 50px;
        margin-bottom: 10px;
        text-align: center;
        width: 100%;
    }

    h2 {
        font-size: 1rem;
        color:rgb(186,12,47);
        margin-bottom: 20px;
        text-align: center;
        width: 100%;
    }

    label {
        font-size: 1rem;
        display: block;
        font-weight: bold;
        margin-bottom: 10px;
    }

    input[type="text"],
    input[type="number"],
    input[type="date"],
    select,
    textarea {
        width: 100%;
        padding: 10px;
        border: 2px solid #FF0000;
        border-radius: 5px;
        margin-bottom: 10px;
        font-size: 1rem;
        text-align: center;
    }

    input[type="submit"] {
        background-color:rgb(186,12,47);
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 1rem;
        width:100%;
        margin-top: 10px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    input[type="submit"]:hover {
        background-color: #000000;
    }

    #result {
        width: 100%;
        text-align: center;
        padding: 20px;
        border: 2px solid #ccc;
        border-radius: 10px;
        margin-top: 20px;
    }

    #result h2 {
        color: #e10808;
        font-size: 24px;
    }

    table {
        border-collapse: collapse;
        width: 80%;
        margin-top: 20px;
        font-size: 0.8rem;
    }

    th {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: left;
    }
    td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: left;
        color:navy;
    }
    #logo {
        max-width: 100%;
        height: auto;
        position: absolute;
        top: 50px;
        left: 10px;
        width: 150px;
    }
    footer {
      background-color: #333;
      color: #fff;
      padding: 10px;
      width: 100%;
      text-align: center;
      margin-top: auto;
    }
    #logo {
        max-width: 100%;
        height: auto;
        position: absolute;
        top: 50px;
        left: 10px;
        width: 150px;
    }
    .gradient {
     background: rgb(99,102,106)
    }
</style>
</head>
<body>
  <header>
    <?php include 'navbar.php'; ?>
    <img id="logo" src="../public/images/back7.png" alt="Logo">
    </header>
    <h1>Emergency Leave Applications</h1>
<div class="container content">
  <div class="row">
    <div class="col-xs-12 col-md-6">
      <h2>Your Details</h2>
      <form id="form1" action="emergency_submit.php" method="POST">
        <div class="form-group">
          <label for="your_name">Your Name:</label>
          <input type="text" id="your_name" name="your_name" class="form-control">
        </div>
        <div class="form-group">
          <label for="your_student_id">Your Student ID:</label>
          <input type="text" id="your_student_id" name="your_student_id" class="form-control">
        </div>
        <div class="form-group">
          <label for="your_school">Your School:</label>
          <select name="year" required class="input-field">
          <option value="stme">STME</option>
          <option value="stme">SPTM</option>
          <option value="stme">SOL</option>
          <option value="stme">SBM</option>
          </select>
        </div>
        <div class="form-group">
          <label for="your_mobile_number">Your Mobile Number:</label>
          <input type="text" id="your_mobile_number" name="your_mobile_number" class="form-control">
        </div>
        <form action="emergency_submit.php" method="POST">
         <input type="submit" value="Submit">
      </form>
    </div>
    <div class="col-xs-12 col-md-6">
      <h2>Sick Person's Details</h2>
      <form  onsubmit="retrieveDetails(); return false;">
        <div class="form-group">
          <label for="sick_sap_id">Enter Sick Person's SAP ID:</label>
          <input type="text" id="sick_sap_id" name="sick_sap_id" required class="form-control">
        </div>
        <div class="form-group">
          <label for="sick_reason">Sick Person Reason:</label>
          <input type="text" id="sick_reason" name="sick_reason" required class="form-control">
        </div>
        <input type="submit" value="Retrieve Details" class="btn btn-primary">
      </form>
      <div id="sick_person_details" style="display: none;">
        <h2>Sick Person's Details</h2>
        <table class="table table-bordered">
          <tr>
            <th>Student Mail</th>
            <th>Parent Mail</th>
            <th>Name</th>
            <th>Student_ID</th>
            <th>Department</th>
            <th>Mobile Number</th>
            <th>Batch</th>
            <th>Gender</th>
          </tr>
        </table>
      </div>
    </div>
  </div>
  <div id="result">
        <!-- Results will be displayed here -->
    </div>
    
    </form>
    <!-- <form action="emergency_submit.php" method="POST">
    <input type="submit" value="Submit">
    </form> -->
</div>
   
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
        <script>
        function displayData(data,reason) {
            console.log('displayData reason : '+reason);

            const dataContainer = document.getElementById("result");
            dataContainer.innerHTML = "";
            dataContainer.style.display = "block";

            if (data.length === 0) {
                dataContainer.innerHTML = "No matching data found.";
            } else {
                /*   data.forEach(function (row) {
                      const rowDiv = document.createElement("div");
                      rowDiv.textContent = `Student Mail: ${row.Student_mail}, Parent Mail: ${row.parent_mail}, Name: ${row.name}, Student ID: ${row['student Id']}, Department: ${row.department}, Mobile Number: ${row['mobile number']}, Batch: ${row.batch}, Gender: ${row.gender}`;
                      dataContainer.appendChild(rowDiv);
                  }); */
                //-------------------- TABLE DISPLAY ----------------------------
               // Create a div element with the Bootstrap "table-responsive" class
        const tableContainer = document.createElement("div");
        tableContainer.classList.add("table-responsive");

        // Create a table element with Bootstrap styling
        const table = document.createElement("table");
        table.classList.add("table", "table-bordered");

        // Create a table header row
        const headerRow = document.createElement("tr");
        for (var key in data[0]) {
            const headerCell = document.createElement("th");
            if(key === 'Student_mail'){key = 'Student Mail'}
            if(key === 'parent_mail'){key = 'Parent Mail'}
            if(key === 'name'){key = 'Name'}
            if(key === 'student Id'){key = 'Student_Id'}      
            if(key === 'department'){key = 'Department'}
            if(key === 'mobile number'){key = 'Mobile'}
            if(key === 'batch'){key = 'Batch'}
            if(key === 'gender'){key = 'Gender'}
            headerCell.textContent = key;
            headerRow.appendChild(headerCell);
        }
        table.appendChild(headerRow);
         //---------------------------------
        const form = document.getElementById("form1");        
        //---------------------------------       
        // Populate the table with data
        data.forEach(function (row) {
            const dataRow = document.createElement("tr");
            for (var key in row) {
                const cell = document.createElement("td");
                cell.textContent = row[key];
                dataRow.appendChild(cell);
                 //----------------------------
                 // Create a hidden input field for each cell data
                 const hiddenInput = document.createElement("input");
                hiddenInput.type = "hidden";      
                if(key === 'student Id'){     
                hiddenInput.value = row[key];
                key = 'Student_Id';  
                hiddenInput.name = key; // Use the column name as the input name               
                }else  if(key === 'mobile number'){                        
                hiddenInput.value = row[key];
                key = 'Mobile';                     
                hiddenInput.name = key; // Use the column name as the input name 
                //----------------------------hiddenInput.value = row[key];
                const hiddenInput1 = document.createElement("input");
                key = 'Reason';      
                hiddenInput1.type = "hidden";                  
                hiddenInput1.name = key; // Use the column name as the input name               
                hiddenInput1.value = reason;
                form.appendChild(hiddenInput1);
                }
                else{
                hiddenInput.name = key; // Use the column name as the input name
                hiddenInput.value = row[key];
                }
               
                form.appendChild(hiddenInput);
                //----------------------------
            }
            table.appendChild(dataRow);
            console.log(dataRow);
             //-----------------------
            ////document.body.appendChild(form);
            //-----------------------
        });

        // Append the table to the table container
        tableContainer.appendChild(table);

        // Clear the container and append the table container
        dataContainer.innerHTML = "";
        dataContainer.appendChild(tableContainer);
    
                //-------------------- TABLE DISPLAY ----------------------------
            }
        }


        function retrieveDetails() {
            var sapId = document.getElementById("sick_sap_id").value;
            var reason = document.getElementById("sick_reason").value;
            console.log('sapId :: ' + sapId);
            console.log('reason :: ' + reason);
            // Make an AJAX request to your PHP script to retrieve data
            const xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log(xhr.responseText);
                    const responseData = JSON.parse(xhr.responseText);
                    console.log(responseData);
                    displayData(responseData,reason);
                }
            };
            xhr.open("GET", `retrieve_data.php?sapId=${sapId}`, true);
            xhr.send();
        }
    </script>
</body>

</html>