<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leave Submitted</title>
  <style>
    /* CSS styles here */
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-image: url('../public/images/back4.jpg');
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      min-height: 100vh; /* Ensure the page takes at least the full viewport height */
      position: relative; /* Required for absolute positioning of footer */
    }
    .container {
      max-width: 600px;
      margin: 0 auto;
      padding: 20px;
      border-radius: 10px;
      margin-top: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
      position: relative; /* Ensure container does not overlap the footer */
      z-index: 1; /* Ensure container is above the footer */
    }

    h1 {
      color: #e10808;
    }

    p {
      margin-bottom: 20px;
    }

    input[type="submit"] {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      background-color: #e10808;
      color: #fff;
      transition: background-color 0.3s;
    }

    input[type="submit"]:hover {
      background-color: #ff4444;
    }
    footer {
      background-color: rgb(99, 102, 106);
      color: #fff;
      text-align: center;
      padding: 13px;
      position: absolute;
      bottom: 0; /* Position at the bottom */
      width: 100%; /* Occupy full width */
    }

    footer p {
      margin: 0;
      margin-bottom: 0;
    }
    .logo {
            max-width: 50%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

  </style>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="container">
    <img src="images/back7.png" alt="Logo" class="logo">
    <h1>Leave Application Submitted Successfully</h1>
    <p>Your leave application has been submitted successfully. We will review your request and notify you of the outcome.</p>
    <p>Thank you for applying!</p>
    <!-- Add an audio element for the beep sound -->
    <audio id="beepAudio">
      <source src="../public/music/beep.wav" type="audio/wav">
      Your browser does not support the audio element.
    </audio>
    <form method="POST" action="../php/student_dashboard.php">
      <input type="submit" value="Back">
    </form>
  </div>
  <footer>
    <p>&copy; <?php echo date("Y"); ?> Malde Saicharan. All rights reserved.</p>  </footer>
  <script>
    // Function to play the beep sound
    function playBeepSound() {
      var audio = document.getElementById("beepAudio");
      if (audio) {
        audio.play();
      }
    }
    // Play the beep sound after 5 seconds (5000 milliseconds) when the page loads
    setTimeout(playBeepSound, 500);
  </script>
</body>
</html>
