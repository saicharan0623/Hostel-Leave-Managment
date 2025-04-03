<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Details</title>
  <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
     <link rel="icon" href="../images/ico.png" type="image/x-icon">

</head>
<style>
  body {
    margin: 0;
    padding: 0;
    background-image: url("../images/back4.jpg");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    font-family: Arial, sans-serif;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }
  
  footer {
    background-color: #333;
    color: #fff;
    padding: 10px;
    text-align: center;
    margin-top: auto;
  }
  table {
    border: 1px solid black !important;
  }
  table th, table td {
    border: 1px solid black !important;
  }
</style>
<body>
<header>
    <?php include 'navbar.php'; ?>
</header>

<div class="container mt-5 pt-4">
  <div class="my-4">
    <h2 class="text-danger"><i class="fas fa-address-book"></i> Contact Details</h2>
    <table class="table table-bordered table-hover">
      <thead class="thead-dark">
        <tr>
          <th>Name</th>
          <th>Position</th>
          <th>Contact Number</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Chanakya.ND</td>
          <td>Rector</td>
          <td>8463917030</td>
        </tr>
        <tr>
          <td>Savitha.U</td>
          <td>Warden Girls Hostel</td>
          <td>8125204909 / 6281306160</td>
        </tr>
        <tr>
          <td>Ajay Kumar.J</td>
          <td>Warden Boys Hostel</td>
          <td>9642076986</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Ambulance Services Section -->
  <div class="my-4">
    <h2 class="text-danger"><i class="fas fa-ambulance"></i> Ambulance Services 24x7</h2>
    <table class="table table-bordered table-hover">
      <thead class="thead-dark">
        <tr>
          <th>Name</th>
          <th>Position</th>
          <th>Contact Number</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Mallesh</td>
          <td>Nurse Boys Hostel</td>
          <td>9704486383</td>
        </tr>
        <tr>
          <td>Sunitha</td>
          <td>Nurse Girls Hostel</td>
          <td>9963756049</td>
        </tr>
        <tr>
          <td>Anjaneyulu</td>
          <td>Ambulance Driver</td>
          <td>8790656478</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Hospitals Section -->
  <div class="my-4">
    <h2 class="text-danger"><i class="fas fa-hospital"></i> Hospitals</h2>
    <table class="table table-bordered table-hover">
      <thead class="thead-dark">
        <tr>
          <th>Name</th>
          <th>Address</th>
          <th>Contact Number</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Agur Prime Hospital</td>
          <td>12. Municipality, #12-94-12-99, Block, near Netaji chowk, Badepally, Telangana 509301</td>
          <td>7997992977</td>
        </tr>
        <tr>
          <td>Amoggh Hospital</td>
          <td>1-8/5/A/1, opp LIC Office, Badepally road, Jadcherla, Telangana 509301</td>
          <td>9611199877</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Police Section -->
  <div class="my-4">
    <h2 class="text-danger"><i class="fas fa-shield-alt"></i> Police</h2>
    <table class="table table-bordered table-hover">
      <thead class="thead-dark">
        <tr>
          <th>Name</th>
          <th>Contact Number</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Jadcherla Local Police</td>
          <td>8712659314</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Drivers Section -->
  <div class="my-4">
    <h2 class="text-danger"><i class="fas fa-car"></i> Drivers</h2>
    <table class="table table-bordered table-hover">
      <thead class="thead-dark">
        <tr>
          <th>Name</th>
          <th>Contact Number</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Mallesh</td>
          <td>9542308367</td>
        </tr>
        <tr>
          <td>B Lata</td>
          <td>7036258367</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Local Transport Support Section -->
  <div class="my-4">
    <h2 class="text-danger"><i class="fas fa-bus"></i> Local Transport Support</h2>
    <table class="table table-bordered table-hover">
      <thead class="thead-dark">
        <tr>
          <th>Name</th>
          <th>Contact Number</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Farukh (Auto Driver) (Paid basis)</td>
          <td>9666727861</td>
        </tr>
        <tr>
          <td>Yadagiri (Auto Driver) (Paid basis)</td>
          <td>9652536527</td>
        </tr>
        <tr>
          <td>Sudarshan (Auto Driver) (Paid basis)</td>
          <td>9515871127</td>
        </tr>
        <tr>
          <td>Ramu Travels (Cab) (Paid basis)</td>
          <td>9912693357 or 9100188093</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<footer>
  <span>2024 &copy; MALDE SAICHARAN All rights reserved.</span>
</footer>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
