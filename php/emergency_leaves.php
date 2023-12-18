<?php
// Start the session to manage user sessions
session_start();
$finalPosition = '';
if (isset($_GET['finalPosition'])) {
  $finalPosition = $_GET['finalPosition'];
}

// If the admin is logged in, display the admin panel

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
    $query = 'SELECT * FROM emergency_leaves order by application_date asc';
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    // Fetch results as associative array
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the PDO connection
$pdo = null;
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <!-- Add your CSS styles here -->
</head>
<body>
    <!-- <h1>Welcome, Admin!</h1> -->
    <h1>Welcome<?php echo $finalPosition; ?>!</h1>
    <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f1f1f1;
      background-image: url("images/back4.jpg");
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
    }

    h1 {
      text-align: center;
      color: #e10808;
      font-size: 36px;
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background-color: #fff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
      animation: fade-in 1s ease-in-out;
    }

    th, td {
      border: 2px solid #000000;
      padding: 10px;
      text-align: center;
    }

    th {
      background-color: #ff4444;
      color: #fff;
    }

    tr:nth-child(even) {
      background-color: #fff;
    }

    tr:nth-child(odd) {
      background-color: #f1f1f1;
    }

    td.student-details {
      background-color: #fff;
      color: #000;
    }

    td.sap-id {
      background-color: #fff;
      color: #000;
    }

    td.status-approve {
      background-color: #ffffff; /* Green */
      color: #000000;
    }

    td.status-reject {
      background-color: #ffffff; /* Red */
      color: #000000;
    }

    td a {
      display: block;
      padding: 10px;
      color: #000000;
      text-decoration: none;
      transition: background-color 0.3s ease, color 0.3s ease;
      height: 100%;
      width: 100%;
      box-sizing: border-box;
    }

    td.status-approve a:hover {
      background-color: #47ff04;
      color: #000000;
      background-size: auto;
    }

    td.status-reject a:hover {
      background-color: #ff0000;
      color: #000000;
      background-size: auto;
    }

    @keyframes fade-in {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .menu {
      text-align: center;
      margin-bottom: 20px;
    }

    .menu a {
      display: inline-block;
      padding: 10px 20px;
      background-color: #000000;
      color: #fff;
      text-decoration: none;
      margin-right: 10px;
    }

    .menu a:hover {
      background-color: #e60505;
    }

    .date {
      position: absolute;
      top: 10px;
      right: 10px;
      font-size: 18px;
    }

    
     .logo {
      position:absolute;
      top: 10px;
      left: 10px;
      margin-left: 10px;
    }

    .logo img {
      max-height: 90px;
    }
    /* =================== IMAGE RELATED ======================= */
  .imgx {
    width: 50px;
    height: 50px;
    background-size: cover; 
    object-fit:
    position: relative; /* Needed for positioning the modal */
    cursor: pointer;    

  }
  .imgxx {
    width: 0px;
    height: 0px;
  }

  .enlarge-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    justify-content: center;
    align-items: center;
  }

  .enlarge-image {
    max-width: 90%;
    max-height: 90%;
  }
  .footer {
    background-color: rgb(99, 102, 106); /* Background color in RGB */
    color: #fff; /* Text color */
    padding: 10px; /* Adjust padding as needed */
    position: fixed;
    bottom: 0;
    width: 100%;
    text-align: center;
}

  /* =================== IMAGE RELATED ======================= */
  </style>
</head>
<body>

  <div class="menu">
  <a href="admin_panel.php?finalPosition=<?= $finalPosition ?>">Admin Panel</a>
  </div>

  <!-- Logo -->
  <div class="logo">
    <img src="images/back7.png" alt="Logo" width="200" height="150">
  </div>

   <!-- =========== IMAGE RELATED ============== -->
   <div class="enlarge-modal">
  <img class="enlarge-image" src="" alt="Enlarged Image" />
</div>
<!-- =========== IMAGE RELATED ============== -->

  <!-- Display a table of leave applications for the admin to manage -->
  <h1>Emergency Leave Applications</h1>
  <table>
    <thead>
      <tr>
        <th>Apply Date</th>     
        <th>Proxy Name</th>
        <th>Proxy Id</th> 
        <th>Proxy School</th>       
        <th>Proxy Contact</th>
        <th>sick name</th>
        <th>Sick Student Gender</th>
        <th>Sick Student SAP ID</th>
        <th>Sick Student Department</th>
        <th>Sick Student Email</th>
        <th>Sick Student Batch</th>
        <th>Sick mobile</th>
        <th>Sick Reason</th> 
      </tr>
    </thead>
    <tbody>    
    <?php if (empty($results)): ?>
    <tr>
        <td colspan="10">No records found.</td>
    </tr>
<?php else: ?>
    <?php foreach ($results as $row): ?>
        <tr>
            <td class="student-details"><?= $row['application_date'] ?></td>
            <td class="student-details"><?= $row['proxy_name'] ?></td>
            <td class="student-details"><?= $row['proxy_id'] ?></td>
            <td class="student-details"><?= $row['proxy_school'] ?></td>
            <td class="student-details"><?= $row['proxy_mobile'] ?></td>
            <td class="student-details"><?= $row['sick_name'] ?></td>
            <td class="student-details"><?= $row['gender'] ?></td>
            <td class="student-details"><?= $row['sick_person_sap_id'] ?></td>
            <td class="student-details"><?= $row['sick_department'] ?></td>
            <td class="student-details"><?= $row['sick_mail'] ?></td>
            <td class="student-details"><?= $row['batch'] ?></td>
            <td class="student-details"><?= $row['phone'] ?></td>
            <td class="student-details"><?= $row['leave_reason'] ?></td>

            
        </tr>
    <?php endforeach; ?>
<?php endif; ?>

    </table>
    </tbody>
  </table>

  <!-- Add the present date, day, and year in the right corner of the page -->
  <div class="date" id="current-date"></div>

  <!-- Add any additional content or functionality for the admin panel -->

  <script>
    // JavaScript code to display the current date, day, and year
    var currentDate = new Date();
    var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('current-date').innerHTML = currentDate.toLocaleDateString(undefined, options);
    //================== IMAGE RELATED =====================================
    const enlargeLinks = document.querySelectorAll('.enlarge-link');
  const modal = document.querySelector('.enlarge-modal');
  const enlargeImage = modal.querySelector('.enlarge-image');

  enlargeLinks.forEach(link => {
    link.addEventListener('click', function (event) {
      event.preventDefault();
      const imageUrl = this.querySelector('img').getAttribute('src');
      enlargeImage.setAttribute('src', imageUrl);
      modal.style.display = 'flex';
    });
  });

  modal.addEventListener('click', function () {
    modal.style.display = 'none';
  });
//================== IMAGE RELATED =====================================
  </script>
</body>
</html>
