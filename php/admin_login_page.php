<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
  <link rel="icon" href="../images/ico.png" type="image/x-icon">
  <style>
    body {
      margin: 0;
      padding: 0;
      background-image: url("../images/back4.jpg");
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      font-family: 'Arial', sans-serif;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    footer {
      background-color: #333;
      color: #fff;
      padding: 15px;
      text-align: center;
      margin-top: 20px;
    }
    header {
      background-color: #fff;
      width: 100%;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      position: fixed;
      top: 0;
      z-index: 1000;
    }
  </style>
</head>
<body class="bg-light">
<header>
    <?php include 'navbar.php'; ?>
  </header>
  
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card" style="width: 25rem;">
      <div class="card-body text-center">
        <img src="../images/back7.png" alt="Logo" class="img-fluid mb-3" style="max-height: 70px;">
        <h1 class="card-title text-danger">Welcome Admin</h1>
        <span class="text-muted" id="current-date"></span>
        <form action="admin_login.php" method="post" class="mt-4">
          <div class="mb-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
          </div>
          <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
          </div>
          <button type="submit" class="btn btn-dark">Login</button>
          <a href="admin_forgot.php" class="btn btn-link">Forgot Password</a>
        </form>
        <p class="mt-3">NOTE: Please enter your credentials</p>
        <div class="contact-section mt-3">
          <p>Contact: 080-51512115</p>
          <p>Email: Nmimshyderabad@nmims.edu</p>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="../js/bootstrap.bundle.min.js"></script>
  <script>
    // Display current date
    const currentDate = new Date();
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('current-date').textContent = currentDate.toLocaleDateString(undefined, options);
  </script>
</body>
<footer class="text-center py-3">
    <span>2024 &copy; MALDE SAICHARAN - STME All rights reserved.</span>
</footer>
</html>