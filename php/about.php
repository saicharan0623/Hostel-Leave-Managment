<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us</title>
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="/css/styles.css">
  <style>
    body {
      margin: 0;
      padding: 0;
      background-image: url("images/back4.jpg");
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      font-family: Arial, sans-serif;
      min-height: 100vh; /* Ensure the body takes up at least the full viewport height */
      display: flex;
      flex-direction: column;
    }


    header {
      background-color: #fff;
      width: 100%;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      position: fixed;
      top: 0;
    }

    .container {
      background-color: transparent;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px black;
      animation: fadeIn 1s ease-in-out, moveUp 0.5s ease-in-out;
      max-width: 800px;
      width: 100%;
      margin: 20px auto;
      margin-top: 150px;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    @keyframes moveUp {
      from {
        transform: translateY(20px);
      }
      to {
        transform: translateY(0);
      }
    }

    h1 {
      color: black;
      font-size: 36px;
      margin-bottom: 20px;
    }

    p {
      color: #333;
      font-size: 18px;
      line-height: 1.6;
    }

    footer {
      background-color: #333;
      color: #fff;
      padding: 10px;
      width: 100%;
      text-align: center;
      margin-top: auto;
    }

    @keyframes fadeOut {
      from {
        opacity: 1;
      }
      to {
        opacity: 0;
      }
    }
    /* Font size for screens between 992px and 1200px */
 @media (min-width: 1201px) {
           .container {
            width:100%;
           }

        }

        /* Font size for screens between 992px and 1200px */
        @media (min-width: 992px) and (max-width: 1200px) {
          .container {
            width:90%;
           }
        }

        /* Font size for screens between 768px and 991px */
        @media (min-width: 768px) and (max-width: 991px) {
          .container {
            width:70%;
           }
        }

        /* Font size for screens narrower than 768px */
        @media (max-width: 767px) {
          .container {
            width:80%;
           }
        }
        #logo {
        max-width: 100%;
        height: auto;
        position: absolute;
        top: 50px;
        left: 10px;
        width: 150px;
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

  <div class="container">
    <h1>About Us</h1>
    <p>NMIMS (Narsee Monjee Institute of Management Studies) is a prestigious university known for its excellence in education. Established in 1981, NMIMS has consistently ranked among the top management and engineering institutions in India. With a strong commitment to providing quality education, NMIMS has become a preferred choice for students seeking to excel in various fields.</p>

    <p>Our university offers a wide range of programs, including management, engineering, arts, science, and more. We take pride in our world-class faculty, state-of-the-art facilities, and a vibrant campus life that fosters holistic development among students.</p>

    <p>At NMIMS, we are dedicated to nurturing talent, promoting innovation, and producing leaders who can make a positive impact on society. Explore our website to learn more about the programs we offer and the opportunities that await you at NMIMS.</p>
  </div>
  <footer class="gradient">
    <div class="container-fluid text-center">
    <span> &copy; MALDE SAICHARAN All rights reserved.</span>
    </div>
</footer>
</body>
</html>
