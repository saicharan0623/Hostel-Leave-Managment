<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title>Contact Details</title>
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
      margin: 15px auto;
      margin-top: 3cm;
      margin-bottom: -45px;

    }

    .container:first-child {
      margin-top: 150px; /* Margin for the first container */
    }

    .container:nth-child(7) {
  margin-bottom: 25px;
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

    h2 {
      color: red;
      font-size: 36px;
      margin-bottom: 20px;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      
    }

    th, td {
      padding: 13px;
      text-align: left;
      border-bottom: 2px solid #ddd;
    }

    footer {
      background-color: #333;
      color: #fff;
      padding: 10px;
      width: 100%;
      text-align: center;
      margin-top: auto;
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
    @media (max-width: 768px) {

  .container {
    padding: 20px; /* Add padding to create space on the left and right */
  }

  h2 {
    font-size: 24px;
  }

  th, td {
    padding: 8px;
  }
}
    
  </style>
</head>
<body>
  <header>
    <?php include 'navbar.php'; ?>
    <img id="logo" src="../public/images/back7.png" alt="Logo">

  </header>

  <div class="container">
    <section class="contact-details">
      <h2>Contact Details</h2>
      <table>
      <tr>
        <th>Name</th>
        <th>Position</th>
        <th>Contact Number</th>
      </tr>
      <tr>
        <td>Chanakya.ND</td>
        <td>Rector</td>
        <td>8463917030</td>
      </tr>
      <tr>
        <td>Sreesha.J</td>
        <td>Warden Girls Hostel</td>
        <td>8977615117</td>
      </tr>
      <tr>
        <td>Ajay Kumar.J</td>
        <td>Warden Boys Hostel</td>
        <td>9642076986</td>
      </tr>
      <tr>
        <td>Hidayat Ali</td>
        <td>Warden Boys Hostel</td>
        <td>8919253966</td>
      </tr>
      <tr>
        <td>Bijan Mitra</td>
        <td>AO</td>
        <td>7980692214</td>
      </tr>
      <tr>
        <td>B.RajuKumar</td>
        <td>SO</td>
        <td>9492504377</td>
      </tr>
    </table>
    </section>
  </div>

  <div class="container">
    <section class="ambulance-services">
    <h2>Ambulance Services 24x7</h2>
    <table>
      <tr>
        <th>Name</th>
        <th>Position</th>
        <th>Contact Number</th>
      </tr>
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
        <td>Shiva</td>
        <td>Ambulance Driver</td>
        <td>9063176808</td>
      </tr>
      <tr>
        <td>Anjaneyulu</td>
        <td>Ambulance Driver</td>
        <td>8790656478</td>
      </tr>
    </table>
  </div>

  <div class="container">
    <section class="hospitals">
      <h2>Hospitals</h2>
      <table>
        <tr>
          <th>Name</th>
          <th>Address</th>
          <th>Contact Number</th>
        </tr>
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
        <!-- Add more hospital details here -->
      </table>
    </section>
  </div>

  <div class="container">
    <section class="police">
      <h2>Police</h2>
      <table>
        <tr>
          <th>Name</th>
          <th>Contact Number</th>
        </tr>
        <tr>
          <td>Jadcherla Local Police</td>
          <td>8712659314</td>
        </tr>
        <!-- Add more police details here -->
      </table>
    </section>
  </div>

  <div class="container">
    <section class="drivers">
      <h2>Drivers</h2>
      <table>
        <tr>
          <th>Name</th>
          <th>Contact Number</th>
        </tr>
        <tr>
          <td>Naveen (Driver)</td>
          <td>9949638361</td>
        </tr>
        <tr>
          <td>Mallesh</td>
          <td>9542308367</td>
        </tr>
        <tr>
          <td>B Lata</td>
          <td>7036258367</td>
        </tr>
        <!-- Add more driver details here -->
      </table>
    </section>
  </div>

  <div class="container">
    <section class="local-transport-support">
      <h2>Local Transport Support</h2>
      <table>
        <tr>
          <th>Name</th>
          <th>Contact Number</th>
        </tr>
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
        <tr>
          <td>Sri Venkateshwara Travels (Paid basis)</td>
          <td>9848658747</td>
        </tr>
        <!-- Add more local transport support details here -->
      </table>
    </section>
  </div>
  <footer class="gradient">
  <div class="container-fluid text-center">
    <span> &copy; MALDE SAICHARAN All rights reserved.</span>
  </div>
</footer>
</html>
