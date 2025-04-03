<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <style>
        
    .navbar {
        background: linear-gradient(90deg, rgba(255, 255, 255, 1) 0%, rgba(128, 0, 0, 1) 100%);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
      padding: 0;

    }
        .gradient {
            background: rgb(99, 102, 106);
        }
        .nav-link {
      color: #ffffff !important;
      transition: color 0.3s, border-color 0.3s; 
      border-bottom: 2px solid transparent;
    }

    .nav-link:hover {
      color: #000000 !important; 
      border-color: #000000;
    }

    @media (max-width: 768px) {
            .container {
                width: 80%;
            }
            .nav-link {
      color: #000000 !important;
      font-weight: bold;
        }
    }

#logo {
    max-width: 120px; 
    height: auto;
    margin: 0; 
    padding: 0; 
    display: block; 
}

    </style>
</head>
<body>
   <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
            <a class="navbar-brand" href="../index.html">
                <img id="logo" src="../images/back7.png" alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="instructions.php">Instructions</a></li>
                    <li class="nav-item"><a class="nav-link" href="./Files/Hostel_Rules.docx" download>Hostel Rules</a></li>
                    <li class="nav-item"><a class="nav-link" href="https://portal.svkm.ac.in/usermgmt/stepsOfHostelBooking">Hostel Application</a></li>
                </ul>
            </div>
            </div>
        </nav>
    </header>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  function toggleChatbot() {
    var chatbotContainer = document.getElementById('chatbotContainer');
    chatbotContainer.style.display = chatbotContainer.style.display === 'none' || chatbotContainer.style.display === '' ? 'block' : 'none';
  }
</script>
</body>
</html>
