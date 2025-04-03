<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hostel Instructions</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
     <link rel="icon" href="../images/ico.png" type="image/x-icon">

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background-image: url("../images/back4.jpg");
      min-height: 100vh;
      font-family: Arial, sans-serif;
      background-size: cover;
    }
    .navbar {
      background-color: rgba(255, 255, 255, 0.8);
    }

    .navbar-brand {
      font-weight: bold;
      font-size: 24px;
      color: #333;
    }

    .card {
      background-color: rgba(255, 255, 255, 0.9);
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    .card h2 {
      font-size: 24px;
      color: rgb(186, 12, 47); /* Updated color */
    }

    .card p,
    .card li {
      color: #555;
      font-size: 16px;
    }

    footer {
      background-color: #343a40;
      color: #fff;
      padding: 10px 0;
      text-align: center;
    }

    footer a {
      color: white;
      margin: 0 10px;
    }

    .text-red {
      color: rgb(186, 12, 47); /* Updated color */
    }
  </style>
</head>

<body>
  <header>
    <?php include 'navbar.php'; ?>
  </header>

  <div class="container mt-5">
    <div id="outing-timings" class="card p-4">
      <h2 class="text-center">Outing Timings</h2>
      <table class="table table-bordered mt-3">
        <thead class="thead-dark">
          <tr>
            <th>Day</th>
            <th>Evening Outing</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Monday - Saturday</td>
            <td>5:00 PM - 7:45 PM</td>
          </tr>
          <tr>
            <td>Sundays and Holidays</td>
            <td>8:00 AM - 8:00 PM</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Alcohol and Cigarettes -->
    <div id="alcohol-rules" class="card p-4">
      <h2 class="text-center">Alcohol & Cigarettes</h2>
      <ul class="list-group mt-3">
        <li class="list-group-item">
          <i class="fas fa-ban text-red"></i> Alcohol and cigarette consumption is strictly prohibited in the hostel premises.
        </li>
      </ul>
    </div>

    <!-- Outing Instructions -->
    <div class="card p-4">
      <h2 class="text-center">Outing Instructions</h2>
      <ul class="list-group mt-3">
        <li class="list-group-item"><i class="fas fa-id-card text-red"></i> Show your return pass when returning from an outing.</li>
        <li class="list-group-item"><i class="fas fa-clock text-red"></i> If late, fill out a late entry form and get a late return pass.</li>
      </ul>
    </div>

    <!-- General Instructions -->
    <div id="general-instructions" class="card p-4">
      <h2 class="text-center">General Hostel Instructions</h2>
      <ul class="list-group mt-3">
        <li class="list-group-item"><i class="fas fa-book text-red"></i> Follow all hostel rules and regulations diligently.</li>
        <li class="list-group-item"><i class="fas fa-bed text-red"></i> Quiet hours: 10:00 PM - 6:00 AM. Maintain silence during these hours.</li>
        <li class="list-group-item"><i class="fas fa-users text-red"></i> Guests are not allowed inside rooms without prior permission.</li>
      </ul>
    </div>
  </div>

  <!-- Footer -->
  <footer class="mt-5">
    <div class="container">
      <p>&copy; 2024 MALDE SAICHARAN. All rights reserved.</p>
    </div>
  </footer>

  <!-- Bootstrap JS and dependencies -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>

</html>
