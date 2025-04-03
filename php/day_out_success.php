<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Submitted</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
       <link rel="icon" href="../images/ico.png" type="image/x-icon">

    <style>
        body {
            background-image: url('../images/back4.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            min-height: 100vh; /* Ensure the page takes at least the full viewport height */
            display: flex;
            flex-direction: column; /* Arrange children in a column */
            justify-content: center;
            align-items: center;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            padding: 20px;
            margin:20px auto;
            max-width: 60%; /* Set max width for larger screens */
            flex-grow: 1; /* Allow the container to grow and fill space */
            display: flex; /* Enable flex layout for vertical alignment */
            flex-direction: column; /* Arrange contents in a column */
            justify-content: center; /* Center the contents vertically */
            align-items: center; /* Center the contents horizontally */
        }

        h1 {
            color: #e10808;
        }

        footer {
            background-color: rgb(99, 102, 106);
            color: #ffffff;
            text-align: center;
            padding: 10px 0;
            width: 100%; /* Full width */
            margin-top: auto; /* Push footer to the bottom */
        }

        .logo {
            max-width: 50%;
            height: auto;
            display: block;
            margin: 0 auto 20px; 
        }

        @media (max-width: 767px) {
            .container {
                max-width: 80%;
                margin: 40px; 
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="../images/back7.png" alt="Logo" class="logo">
        <h1>Day Out Form Submitted Successfully</h1>
        <p>Your day out form has been submitted successfully. We will review your request and notify you of the outcome.</p>
        <p>Thank you for applying!</p>
        <audio id="beepAudio">
            <source src="../music/beep.wav" type="audio/wav">
            Your browser does not support the audio element.
        </audio>
        <form method="POST" action="daypass.php">
            <button type="submit" class="btn btn-danger">Back</button>
        </form>
    </div>
    <script>
        function playBeepSound() {
            var audio = document.getElementById("beepAudio");
            if (audio) {
                audio.play();
            }
        }
        // Play the beep sound after a short delay when the page loads
        setTimeout(playBeepSound, 500);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <footer>
    <div class="container-fluid text-center">
      <span>2024 &copy; MALDE SAICHARAN STME All rights reserved.</span>
    </div>
  </footer>
</body>
</html>
