<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inform Return</title>
    <style>
        /* CSS styles here */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('../images/back4.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 100vh;
            position: relative;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 10px;
            margin-top: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
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
            bottom: 0;
            /* Position at the bottom */
            width: 100%;
            /* Occupy full width */
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
        <img src="../images/back7.png" alt="Logo" class="logo">
        <h1>Return Form Submitted Successfully</h1>
        <p>Your Return Form is Submitted successfully.<strong>Check your Mail For In Pass</strong></p>
        <p>Thank you for applying!</p>
        <!-- Add an audio element for the beep sound -->
        <audio id="beepAudio">
            <source src="../music/beep.wav" type="audio/wav">
            Your browser does not support the audio element.
        </audio>
            <form method="" action="inpass.php">
            <input type="submit" value="Back">
        </form>
    </div>
    <footer>
        <p>&copy;
            <?php echo date("Y"); ?> Malde Saicharan. All rights reserved.
        </p>
    </footer>
    <script>
        function playBeepSound() {
            var audio = document.getElementById("beepAudio");
            if (audio) {
                audio.play();
            }
        }
        setTimeout(playBeepSound, 500);
    </script>
</body>

</html>