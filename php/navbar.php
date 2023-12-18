<!-- navbar.php -->
<div class="navbar">
  <a href="../public/index.html">Home</a>
  <a href="about.php">About</a>
  <a href="contact.php">Contact</a>
  <a href="instructions.php">Instructions</a>
  <a href="../php/Files/Hostel_Rules.docx" download="">Hostel Rules</a>
  <a href="emergency_leave.php">Emergency Leaves</a>
  <div class="mobile-menu-button">&#9776;</div> <!-- Hamburger menu icon -->
</div>

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

    .container {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
    }

  .navbar {
    background-color:rgb(186,12,47); /* Change background color */
    display: flex;
    justify-content: right;
    align-items: center;
    padding: 0.1px 20px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
  }

  .navbar a {
    color: #fff;
    text-decoration: none;
    margin: 5px 10px;
    padding: 5px 10px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
  }

  .navbar a:hover {
    background-color: #fff;
    color: #ff0505;
  }

  .mobile-menu-button {
    font-size: 24px;
    color: #fff;
    cursor: pointer;
    display: none;
    align-content: right;
  }

  @media (max-width: 600px) {
    .navbar {
      flex-direction: column;
      text-align: right;
      padding: 10px;
    }

    .navbar a {
      display: block;
      margin: 10px 0;
      padding: 10px;
    }

    .mobile-menu-button {
      display: block;
    }
  }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
      var $navbarLinks = $(".navbar a");
      var $mobileMenuButton = $(".mobile-menu-button");
  
      // Initially hide the mobile menu button
      $mobileMenuButton.hide();
  
      // Function to toggle the mobile menu
      function toggleMobileMenu() {
        $navbarLinks.slideToggle();
      }
  
      // Toggle the mobile menu when the mobile menu button is clicked
      $mobileMenuButton.click(toggleMobileMenu);
  
      // Hide the mobile menu on larger screens
      function hideMobileMenu() {
        if ($(window).width() > 600) {
          $navbarLinks.show();
          $mobileMenuButton.hide();
        } else {
          $navbarLinks.hide();
          $mobileMenuButton.show();
        }
      }
  
      // Call the hideMobileMenu function on page load and window resize
      hideMobileMenu();
      $(window).resize(hideMobileMenu);
    });
  </script>  