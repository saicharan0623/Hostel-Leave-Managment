<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

    header {
      background-color: #fff;
      width: 100%;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      position: fixed;
      top: 0;
      z-index: 1000;
    }

    .content-container {
      background-color: rgba(255, 255, 255, 0.9);
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
      margin: 110px auto 10px;
      max-width: 80%; 
      width: 90%;
    }

    h1 {
      color: #ba0c2f;
      font-size: 36px;
      margin-bottom: 20px;
      text-align: center;
    }

    p {
      color: #333;
      font-size: 18px;
      line-height: 1.6;
    }

    .icon {
      font-size: 24px;
      color: #ba0c2f;
      margin-right: 10px;
    }

    .img-container {
      display: flex;
      flex-wrap: nowrap;
      overflow: hidden;
      padding: 10px 0;
      position: relative;
      width: 100%;
    }

    .img-wrapper {
      flex: 0 0 auto;
      width: 30%;
      margin-right: 10px;
      cursor: pointer;
    }

    .img-fluid {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 10px;
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.9);
      align-items: center;
      justify-content: center;
      display: flex;
    }

    .modal-content {
      margin: auto;
      display: block;
      max-width: 90%;
      max-height: 80%;
    }

    .close {
      position: absolute;
      top: 15px;
      right: 35px;
      color: #fff;
      font-size: 40px;
      font-weight: bold;
      cursor: pointer;
    }

    @keyframes scroll {
      0% { transform: translateX(0); }
      100% { transform: translateX(-100%); }
    }

    .scrolling {
      display: flex;
      animation: scroll 30s linear infinite; /* Adjust duration for speed */
    }

    footer {
      background-color: #333;
      color: #fff;
      padding: 15px;
      text-align: center;
      margin-top: 20px;
    }

    @media (max-width: 768px) {
      .content-container {
        max-width: 100%;
        padding: 20px;
      }

      .img-wrapper {
        width: 80%;
        margin-right: 10px;
      }
    }
  </style>
</head>
<body>
  <header>
    <?php include 'navbar.php'; ?>
  </header>
  
  <div class="content-container" id="aboutUs">
    <h1>About Us</h1>
    <p><i class="fas fa-university icon"></i> NMIMS (Narsee Monjee Institute of Management Studies) is a prestigious university known for its excellence in education. Established in 1981, NMIMS has consistently ranked among the top management and engineering institutions in India. With a strong commitment to providing quality education, NMIMS has become a preferred choice for students seeking to excel in various fields.</p>
    <p><i class="fas fa-book icon"></i> Our university offers a wide range of programs, including management, engineering, arts, science, and more. We take pride in our world-class faculty, state-of-the-art facilities, and a vibrant campus life that fosters holistic development among students.</p>
    <p><i class="fas fa-users icon"></i> At NMIMS, we are dedicated to nurturing talent, promoting innovation, and producing leaders who can make a positive impact on society. Explore our website to learn more about the programs we offer and the opportunities that await you at NMIMS.</p>
  </div>
  
  <div class="content-container" id="hostelLife">
    <h1>Hostel Life</h1>
    <div class="img-container">
      <div class="scrolling">
        <div class="img-wrapper" onclick="openModal(this)">
          <img src="../images/boys1.jpg" class="img-fluid" alt="Hostel Image 1">
        </div>
        <div class="img-wrapper" onclick="openModal(this)">
          <img src="../images/boys2.jpg" class="img-fluid" alt="Hostel Image 2">
        </div>
        <div class="img-wrapper" onclick="openModal(this)">
          <img src="../images/boys3.jpg" class="img-fluid" alt="Hostel Image 3">
        </div>
        <div class="img-wrapper" onclick="openModal(this)">
          <img src="../images/boys4.jpg" class="img-fluid" alt="Hostel Image 4">
        </div>
        <div class="img-wrapper" onclick="openModal(this)">
          <img src="../images/girls1.jpg" class="img-fluid" alt="Hostel Image 5">
        </div>
        <div class="img-wrapper" onclick="openModal(this)">
          <img src="../images/girls2.jpg" class="img-fluid" alt="Hostel Image 6">
        </div>
        <div class="img-wrapper" onclick="openModal(this)">
          <img src="../images/girls3.jpg" class="img-fluid" alt="Hostel Image 7">
        </div>
        <div class="img-wrapper" onclick="openModal(this)">
          <img src="../images/girls4.jpg" class="img-fluid" alt="Hostel Image 8">
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Structure -->
  <div id="myModal" class="modal">
    <span class="close" onclick="closeModal()">&times;</span>
    <img class="modal-content" id="img01">
  </div>

  <footer>
    <span>2024 &copy; MALDE SAICHARAN All rights reserved.</span>
  </footer>

  <script>
    function openModal(element) {
      const modal = document.getElementById("myModal");
      const img = document.getElementById("img01");
      img.src = element.querySelector("img").src; // Set the modal image source
      modal.style.display = "flex"; // Use flex to center the modal
    }

    function closeModal() {
      const modal = document.getElementById("myModal");
      modal.style.display = "none"; // Hide the modal
    }

    // Close modal when clicking anywhere outside the image
    window.onclick = function(event) {
      const modal = document.getElementById("myModal");
      if (event.target == modal) {
        closeModal();
      }
    }
  </script>
</body>
</html>
