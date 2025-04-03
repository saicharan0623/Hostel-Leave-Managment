<?php
// Include database configuration file
require 'database_config.php'; 

// Function to connect to the database
function getDbConnection() {
    // Replace with your actual database credentials
    $dbHost = 'localhost';
    $dbName = 'college_db'; // Replace with your actual database name
    $dbUsername = 'nmimsleave';  // Replace with your actual username
    $dbPassword = 'Nmims@123$';  // Replace with your actual password

    try {
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Function to search for the email and return table names where it exists
function searchEmail($email) {
    $pdo = getDbConnection();

    // Get all tables in the database
    $tablesQuery = $pdo->query("SHOW TABLES");
    $tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);
    
    $tablesWithEmail = [];

    // Loop through each table and search for the email
    foreach ($tables as $table) {
        $searchQuery = "SELECT * FROM $table WHERE email = :email";
        $stmt = $pdo->prepare($searchQuery);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $tablesWithEmail[] = $table;  // Store table name if email is found
        }
    }

    return $tablesWithEmail;
}

// Function to delete student email from all tables (deletes the entire row)
function deleteEmail($email) {
    $pdo = getDbConnection();

    // Get all tables in the database
    $tablesQuery = $pdo->query("SHOW TABLES");
    $tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);

    // Loop through each table and delete the entire row wherever the email is found
    foreach ($tables as $table) {
        $deleteQuery = "DELETE FROM $table WHERE email = :email";
        $stmt = $pdo->prepare($deleteQuery);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    }

    echo "<div class='alert alert-success'>All rows containing the email '$email' have been deleted from all tables.</div>";
}

// Function to update student email wherever it is found in all tables
function updateEmail($oldEmail, $newEmail) {
    $pdo = getDbConnection();

    // Get all tables in the database
    $tablesQuery = $pdo->query("SHOW TABLES");
    $tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);

    // Loop through each table and update the email
    foreach ($tables as $table) {
        $updateQuery = "UPDATE $table SET email = :newEmail WHERE email = :oldEmail";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':newEmail', $newEmail);
        $stmt->bindParam(':oldEmail', $oldEmail);
        $stmt->execute();
    }

    echo "<div class='alert alert-success'>Email $oldEmail has been updated to $newEmail in all tables.</div>";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $email = $_POST['email'] ?? null;
        $newEmail = $_POST['new_email'] ?? null;

        if ($_POST['action'] === 'search' && $email) {
            $searchResults = searchEmail($email);
        } elseif ($_POST['action'] === 'delete' && $email) {
            deleteEmail($email);
        } elseif ($_POST['action'] === 'update' && $email && $newEmail) {
            updateEmail($email, $newEmail);
        } else {
            echo "<div class='alert alert-danger'>Please provide a valid email.</div>";
        }
    }
}
?>

<!-- HTML form for searching, deleting, and updating emails -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
            background-image: url("../images/back4.jpg");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="logo">
                <img src="../images/back7.png" alt="Logo" class="img-fluid" style="max-height: 60px;">
            </div>
            <a href="#" class="btn btn-secondary" onclick="window.history.back(); return false;">Back</a>
        </div>
        
        <h2 class="mb-4">Email Management System</h2>
        
        <form method="POST" action="" class="mb-4">
            <div class="mb-3">
                <label for="email" class="form-label">Enter Student Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="new_email" class="form-label">New Email (for update):</label>
                <input type="email" id="new_email" name="new_email" class="form-control">
            </div>

            <button type="submit" name="action" value="search" class="btn btn-info">Search Email</button>
            <button type="submit" name="action" value="update" class="btn btn-warning">Update Email</button>
            <button type="submit" name="action" value="delete" class="btn btn-danger">Delete Email</button>
        </form>

        <!-- Display Search Results -->
        <?php if (isset($searchResults)): ?>
            <h3>Search Results for Email: <?php echo htmlspecialchars($email); ?></h3>
            <?php if (!empty($searchResults)): ?>
                <ul class="list-group">
                    <?php foreach ($searchResults as $table): ?>
                        <li class="list-group-item">Found in Table: <strong><?php echo htmlspecialchars($table); ?></strong></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="alert alert-info">No records found for this email in any table.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
