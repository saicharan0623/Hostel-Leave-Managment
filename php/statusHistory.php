<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["email"])) {
    header("Location: student_login_page.php");
    exit();
}

$email = $_SESSION["email"];

// Include database configuration
require_once 'database_config.php';

try {
    $query = "SELECT * FROM leave_applications WHERE email = :email ORDER BY created_at DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['email' => $email]);
    $results = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error_message = "An error occurred while fetching the data. Please try again later.";
}

// Close the PDO connection
$pdo = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Status/History</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            background-image: url("../images/back4.jpg");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            min-height: 100vh;
        }

        h1 {
            text-align: center;
            color: #e10808;
            font-size: 2.5rem;
            margin-bottom: 20px;
            margin-top: 80px;
        }

        .table {
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        th, td {
            border: 2px solid #000000;
            padding: 10px;
            text-align: center;
            font-size: 12px;
        }

        th {
            background-color: #ff4444;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #fff;
        }

        tr:nth-child(odd) {
            background-color: #f1f1f1;
        }

        .logo {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1000;
        }

        .logo img {
            max-height: 60px;
        }

        .status-approved { color: green; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .status-rejected { color: red; font-weight: bold; }

        @media screen and (max-width: 768px) {
            .logo img {
                max-height: 50px;
            }

            h1 {
                font-size: 2rem;
                margin-top: 70px;
            }

            th, td {
                font-size: 10px;
                padding: 5px;
            }
        }

        @media screen and (max-width: 576px) {
            .logo img {
                max-height: 40px;
            }

            h1 {
                font-size: 1.8rem;
                margin-top: 60px;
            }
        }

        #pass-card-container {
            display: none;
            margin-top: 20px;
        }

        .pass-table {
            border-radius: 8px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .pass-header {
            background-color: #4caf50; /* Green background */
            color: white;
            padding: 10px;
            border-radius: 5px 5px 0 0;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .pass-table td {
            border: none; /* Remove borders from the details table */
            padding: 10px 0; /* Padding for details */
            font-size: 1rem;
        }

        .pass-table tr:nth-child(even) {
            background-color: #f9f9f9; /* Light gray for even rows */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="../images/back7.png" alt="Logo">
        </div>

        <h1>Leave Status/History</h1>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>NO.</th>
                            <th>Student Name</th>
                            <th>School</th>
                            <th>Mobile Number</th>
                            <th>SAP ID</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Reason</th>
                            <th>Applied Date</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($results)): ?>
                            <tr>
                                <td colspan="12">No records found.</td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $rowNumber = 1;
                            foreach ($results as $row): 
                                $status = strtolower($row['status']);
                            ?>
                                <tr>
                                    <td><?php echo $rowNumber++; ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['school']); ?></td>
                                    <td><?php echo htmlspecialchars($row['mobile']); ?></td>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['from_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['to_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['reason']); ?></td>             
                                    <td><?php echo htmlspecialchars($row['created_at']); ?></td> 
                                    <td><?php echo htmlspecialchars($row['academic']); ?></td> 
                                    <td class="status-<?php echo $status; ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </td> 
                                    <td>
                                        <?php if ($row['status'] == 'APPROVED'): ?>
                                            <button class="btn btn-info get-pass-btn" 
                                                data-status="<?php echo htmlspecialchars($row['status']); ?>"
                                                data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                                data-school="<?php echo htmlspecialchars($row['school']); ?>"
                                                data-mobile="<?php echo htmlspecialchars($row['mobile']); ?>"
                                                data-id="<?php echo htmlspecialchars($row['id']); ?>"
                                                data-from="<?php echo htmlspecialchars($row['from_date']); ?>"
                                                data-to="<?php echo htmlspecialchars($row['to_date']); ?>">
                                                Get Pass
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Pass Card Container -->
        <div id="pass-card-container">
        <div class="pass-table">
       <div class="pass-header">
            OUT PASS <?php echo isset($row['status']) ? $row['status'] : 'Status not available'; ?>
        </div>

        <table class="table">
                    <tr>
                        <th>Field</th>
                        <th>Details</th>
                    </tr>
                    <tr>
                        <td>Student Name</td>
                        <td id="student-name"></td>
                    </tr>
                    <tr>
                        <td>School</td>
                        <td id="pass-school"></td>
                    </tr>
                    <tr>
                        <td>Mobile</td>
                        <td id="pass-mobile"></td>
                    </tr>
                    <tr>
                        <td>SAP ID</td>
                        <td id="pass-id"></td>
                    </tr>
                    <tr>
                        <td>From Date</td>
                        <td id="pass-from"></td>
                    </tr>
                    <tr>
                        <td>To Date</td>
                        <td id="pass-to"></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Back Button -->
        <a href="student_dashboard.php" class="btn btn-dark" style="position: fixed; top: 15px; right: 20px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 1-.5.5H3.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 0 1 .708.708L3.707 7.5H14.5A.5.5 0 0 1 15 8z"/>
            </svg> 
            Back
        </a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function () {
            $('.get-pass-btn').on('click', function () {
                $('#student-name').text($(this).data('name'));
                $('#pass-school').text($(this).data('school'));
                $('#pass-mobile').text($(this).data('mobile'));
                $('#pass-id').text($(this).data('id'));
                $('#pass-from').text($(this).data('from'));
                $('#pass-to').text($(this).data('to'));

                $('#pass-card-container').show();
            });
        });
    </script>
</body>
</html>
