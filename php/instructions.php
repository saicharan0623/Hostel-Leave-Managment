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
      min-height: 100vh;
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
      margin-top: 130px;
      margin-bottom: 20px;
    }

    h1 {
      -webkit-text-fill-color: red;
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
      text-align: left; /* Left-align text */
      margin-left: 20px; /* Add a left margin for better readability */
    }

    footer {
      background-color: rgb(99,102,106);
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
    .gradient {
     background: rgb(99,102,106)
    }
    /* Apply styles to tables */
table {
    border-collapse: collapse;
    width: 100%;
    margin: 10px 0;
}

/* Add border and spacing between table cells */
table, th, td {
    border: 1.5px solid #000;
}

/* Style table header cells */
th {
    background-color: #f2f2f2;
    color: #333;
}

/* Style table data cells */
td {
    padding: 8px;
    text-align: left;
}

/* Alternate row background color for better readability */
tr:nth-child(even) {
    background-color: #f2f2f2;
}

/* Add hover effect for better interaction */
tr:hover {
    background-color: #ddd;
}
li{
  text-align: left;
  
}

  </style>
</head>
<body>
  <header>
    <?php include 'navbar.php'; ?>
    <img id="logo" src="../public/images/back7.png" alt="Logo">
  </header>

  <div class="container">
    <h1>Hostel Instructions</h1>

    <section>
      <h2>Outing Timings</h2>
      <table>
        <tr>
          <th>Day</th>
          <th>Morning Outing</th>
          <th>Evening Outing</th>
        </tr>
        <tr>
          <td>Monday - Saturday</td>
          <td>6:00 AM - 8:00 AM</td>
          <td>4:00 PM - 8:00 PM</td>
        </tr>
        <tr>
          <td>Sundays and Holidays</td>
          <td>8:00 AM - 8:00 PM</td>
          <td>8:00 AM - 8:00 PM</td>
        </tr>
      </table>
    </section>

    <section>
      <h2>Alcohol and Cigarettes</h2>
      <li>Alcohol and cigarette consumption is strictly prohibited in the hostel premises.</li>
    </section>

    <section>
      <h2>Outing Instructions</h2>
      <ul>
        <li>When returning from an outing, you are required to show your return pass.</li>
        <li>If you return late after an outing, you must fill out a late entry form and obtain a late return pass.</li>
      </ul>
    </section>

    <section>
      <h2>General Hostel Instructions</h2>
      <ul>
        <li>Please ensure that you follow all hostel rules and regulations diligently.</li>
        <li>Quiet hours in the hostel are from 10:00 PM to 6:00 AM. Please maintain silence during these hours.</li>
        <li>Guests are not allowed inside the hostel rooms without prior permission from the hostel authorities.</li>
        <li>Your safety and security are our top priorities. Always inform the hostel warden or security personnel about your whereabouts.</li>
        <li>Do not leave the hostel premises after 10:00 PM without informing the hostel authorities.</li>
        <li>If you have any concerns or face any issues, please report them immediately to the hostel warden or the university administration.</li>
      </ul>
    </section>

    <p>For any additional information or inquiries, you can contact the hostel office.</p>
    <p>Our dedicated staff is ready to assist you with any questions or concerns you may have. Feel free to get in touch with us through the provided contact details.</p>
  </div>

  <footer>
    <span> &copy; MALDE SAICHARAN All rights reserved.</span>
  </footer>
</body>
</html>
