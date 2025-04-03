<?php
session_start();
require_once 'database_config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Function to get pending applications for the admin
function getPendingApplications($pdo) {
    $query = "SELECT la.*, s.student_name, s.department, s.gender, s.batch, s.phone, s.student_email 
              FROM leave_applications la
              JOIN students s ON la.student_id = s.student_id
              WHERE la.status = 'PENDING' 
              ORDER BY la.created_at DESC";

    // Execute the query
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get admin statistics
function getAdminStatistics($pdo) {
    $stats = [
        'total_pending' => 0,
        'total_approved' => 0,
        'total_rejected' => 0,
        'today_applications' => 0,
        'total_students' => 0,
        'active_leaves' => 0
    ];
    
    $baseQuery = "SELECT 
        COUNT(CASE WHEN la.status IN ('PENDING', 'PROCESSING') THEN 1 END) as pending,
        COUNT(CASE WHEN la.status = 'APPROVED' THEN 1 END) as approved,
        COUNT(CASE WHEN la.status = 'REJECTED' THEN 1 END) as rejected,
        COUNT(CASE WHEN DATE(la.created_at) = CURDATE() THEN 1 END) as today,
        COUNT(DISTINCT s.student_id) as total_students,
        COUNT(CASE WHEN la.status = 'APPROVED' AND la.to_date >= CURDATE() THEN 1 END) as active_leaves
        FROM leave_applications la
        RIGHT JOIN students s ON la.student_id = s.student_id
        WHERE 1=1";

    // Execute the query
    $stmt = $pdo->prepare($baseQuery);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get data for the dashboard
$pendingApplications = getPendingApplications($pdo);
$statistics = getAdminStatistics($pdo);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
      :root {
    --sidebar-width: 260px;
    --sidebar-collapsed-width: 70px;
    --header-height: 60px;
    --primary-color: #4e73df;
    --secondary-color: #858796;
    --success-color: #1cc88a;
    --info-color: #36b9cc;
    --warning-color: #f6c23e;
    --danger-color: #e74a3b;
    --light-color: #f8f9fc;
    --dark-color: #5a5c69;
}

body{
  background-image: url("../images/back4.jpg");
  background-size: cover;
  background-repeat: no-repeat;
};

/* Layout */
.wrapper {
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: var(--sidebar-width);
    background: #fff;
    border-right: 1px solid rgba(0,0,0,.125);
    position: fixed;
    height: 100vh;
    z-index: 1000;
    transition: all 0.3s ease;
}

.main-content {
    flex: 1;
    margin-left: var(--sidebar-width);
    transition: all 0.3s ease;
}

/* Sidebar Styles */
.sidebar-header {
    height: var(--header-height);
    padding: 0 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(0,0,0,.125);
}

.app-brand {
    display: flex;
    align-items: center;
}

.brand-logo {
    height: 40px;
    width: auto;
    margin-right: 10px;
}

.brand-name {
    font-weight: 600;
    font-size: 1.1rem;
    color: var(--dark-color);
}

.nav-list {
    padding: 1rem 0;
    list-style: none;
    margin: 0;
}

.nav-item {
    margin: 0.25rem 1rem;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: var(--secondary-color);
    border-radius: 0.375rem;
    transition: all 0.2s ease;
}

.nav-link i {
    width: 1.25rem;
    margin-right: 1rem;
    font-size: 1.1rem;
}

.nav-link:hover {
    color: var(--primary-color);
    background: rgba(78,115,223,.1);
}

.nav-item.active .nav-link {
    color: var(--primary-color);
    background: rgba(78,115,223,.1);
    font-weight: 600;
}

/* Responsive Sidebar */
.sidebar-collapsed .sidebar {
    width: var(--sidebar-collapsed-width);
}

.sidebar-collapsed .main-content {
    margin-left: var(--sidebar-collapsed-width);
}

.sidebar-collapsed .brand-name,
.sidebar-collapsed .nav-link span {
    display: none;
}

.sidebar-collapsed .nav-item {
    margin: 0.25rem 0.5rem;
}

.sidebar-collapsed .nav-link i {
    margin-right: 0;
    font-size: 1.25rem;
}

/* Cards and Components */
.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,.15);
    transition: box-shadow 0.3s ease;
}

.card:hover {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,.25);
}

.icon-shape {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-weight: 600;
}

.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
}

/* Dark Theme */
.dark-theme {
    background: #1a1c23;
    color: #e4e6eb;
}

.dark-theme .sidebar,
.dark-theme .card {
    background: #242631;
    border-color: rgba(255,255,255,.1);
}

.dark-theme .nav-link {
    color: #e4e6eb;
}

.dark-theme .nav-link:hover {
    background: rgba(255,255,255,.1);
}

/* Additional Utilities */
.notification-badge {
    position: absolute;
    top: 0;
    right: 0;
    transform: translate(50%, -50%);
}

.notifications-dropdown {
    width: 300px;
    max-height: 400px;
    overflow-y: auto;
}

.notifications-header {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid rgba(0,0,0,.125);
}

.notifications-body {
    padding: 0.5rem 0;
}

/* Responsive Adjustments */
@media (max-width: 991.98px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .main-content {
        margin-left: 0;
    }
    
    .sidebar-active .sidebar {
        transform: translateX(0);
    }
    
    .sidebar-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,.5);
        z-index: 999;
        display: none;
    }
    
    .sidebar-active .sidebar-backdrop {
        display: block;
    }
}
.nav-item.active {
    background-color: #007bff; /* Example color */
    color: white;
}

.nav-item.active .nav-link {
    color: white;
}
    </style>
</head>
<body class="bg-light">
    <div class="wrapper">
        <!-- Sidebar -->
        <?php include 'admin_sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
        <div class="container">
    <div class="row">
        <div class="col-12 text-center mt-5">
        <a href="admin_logout.php" class="btn btn-dark float-end">Logout</a>

            <h2>Leave Applications</h2>
        </div>
    </div>
</div>

            <!-- Page Content -->
            <div id="admin-panel-content" class="container-fluid py-4">
                <?php include 'admin_dashboard.php'; ?>
            </div>
        </div>
    </div>
   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/admin-scripts.js"></script>
</body>
</html>