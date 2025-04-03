<?php

require_once 'database_config.php';

try {
    // Query to fetch admin details
    $query = "SELECT name, email FROM admins WHERE id = :admin_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        // Handle case where admin details are not found
        die("Admin details not found.");
    }

    // Query to fetch count of pending day outings
    $query = "SELECT COUNT(*) FROM day_outing_requests WHERE status = 'pending'"; // Adjust query according to your DB schema
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $pendingDayOutingsCount = $stmt->fetchColumn();

    // If no pending applications, set it to an empty string
    if ($pendingDayOutingsCount == 0) {
        $pendingDayOutingsCount = '';
    }

} catch (PDOException $e) {
    // Handle SQL errors
    die("Error fetching admin details or pending day outings count: " . $e->getMessage());
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: rgb(210, 35, 42);
            --hover-color: rgba(255, 255, 255, 0.1);
            --text-color: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            overflow-x: hidden;
            background-image: url("../images/back4.jpg");

        }

        .sidebar {
            width: var(--sidebar-width);
            background: var(--primary-color);
            color: var(--text-color);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease-in-out;
            z-index: 1000;
        }

        /* Custom Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: var(--primary-color);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar-header {
            padding: 20px;
            background: #ffffff;
            border-bottom: 1px solid #000000;
        }

        .brand-name {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .profile-section {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .profile-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 10px;
            border: 3px solid #ffffff;
            overflow: hidden;
            background: #ffffff;
        }

        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-info {
            margin-bottom: 10px;
        }

        .profile-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .profile-email {
            font-size: 14px;
            opacity: 0.8;
        }

        .nav-section {
            padding: 10px 0;
        }

        .nav-item {
            padding: 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: var(--hover-color);
            color: #ffffff;
        }

        .nav-link i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }

        .nav-link span {
            flex: 1;
        }

        .nav-item.active .nav-link {
            background: var(--hover-color);
            border-left: 4px solid #ffffff;
        }

        .toggle-sidebar-btn {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            background: var(--primary-color);
            color: #ffffff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            z-index: 1001;
        }

        .content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .toggle-sidebar-btn {
                display: block;
            }

            .content {
                margin-left: 0;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }

        @media (max-width: 576px) {
            .profile-image {
                width: 60px;
                height: 60px;
            }

            .profile-name {
                font-size: 16px;
            }

            .profile-email {
                font-size: 12px;
            }
        }
        .profile-image {
        width: 80px;
        height: 80px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #2a3f54;
        color: #ffffff;
        font-size: 36px;
        font-weight: bold;
        border-radius: 50%;
    }

    .profile-email {
            color: #ffffff;
            }

    .profile-initial {
        text-transform: uppercase;
    }
    .edit-profile-link {
    display: inline-block;
    margin-top: 5px;
    font-size: 14px;
    color:rgb(0, 6, 9);
    text-decoration: none;
}

.edit-profile-link:hover {
    text-decoration: underline;
}
.notification-badge {
    background-color: rgb(0, 0, 0); /* Red background */
    color: #ffffff; /* White text for better contrast */
    border-radius: 50%;
    font-size: 12px;
    padding: 3px 7px;
    position: absolute;
    top: 10px;
    right: 10px;
    display: none; /* Hidden by default, controlled by PHP logic */
}

.nav-item {
    position: relative; /* Make sure the badge positions correctly */
}



    </style>
</head>
<body>

<button class="toggle-sidebar-btn" id="toggleSidebar">
    <i class="fas fa-bars"></i>
</button>

<div class="sidebar-overlay" id="overlay"></div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="brand-name">Admin Panel</div>
    </div>

    <div class="profile-section">
    <div class="profile-image">
        <div class="profile-initial">
            <?php echo htmlspecialchars($admin['name'][0]); ?>
        </div>
    </div>
    <div class="profile-info">
        <div class="profile-name"><?php echo htmlspecialchars($admin['name']); ?></div>
        <div class="profile-email"><?php echo htmlspecialchars($admin['email']); ?></div>
        <div class="edit-profile">
            <a href="admin_edit_profile.php" class="edit-profile-link">Edit Profile</a>
        </div>
    </div>
</div>


<div class="nav-section">
    <div class="nav-item">
        <a href="approved_leaves.php" class="nav-link">
            <i class="fas fa-calendar-check"></i>
            <span>Approved Leaves</span>
        </a>
    </div>
    <div class="nav-item" id="rejected-leaves">
        <a href="rejected_leaves.php" class="nav-link">
            <i class="fas fa-calendar-times"></i>
            <span>Rejected Leaves</span>
        </a>
    </div>
    <div class="nav-item" id="pending-leaves">
        <a href="pending_leaves.php" class="nav-link">
            <i class="fas fa-clock"></i>
            <span>Pending Leaves</span>
        </a>
    </div>

    <div class="nav-item" id="day-outings-approval">
    <a href="dayout_admin_dashboard.php" class="nav-link">
        <i class="fas fa-calendar-day"></i>
        <span>Day Outings</span>
        <span class="notification-badge" id="day-outings-badge" 
              style="display: <?php echo ($pendingDayOutingsCount != '') ? 'inline-block' : 'none'; ?>">
            <?php echo $pendingDayOutingsCount; ?>
        </span>
    </a>
</div>


    <div class="nav-item" id="inform-details">
        <a href="inpass_details.php" class="nav-link">
            <i class="fas fa-user-check"></i>
            <span>In-Students</span>
        </a>
    </div>
    <div class="nav-item" id="late-entry">
        <a href="late_outing_details.php" class="nav-link">
            <i class="fas fa-clock"></i>
            <span>Late Entry</span>
        </a>
    </div>
    <div class="nav-item" id="analytics">
        <a href="data_visualization.php" class="nav-link">
            <i class="fas fa-chart-pie"></i>
            <span>Analytics</span>
        </a>
    </div>
    <div class="nav-item" id="fetch-data">
        <a href="admin_school_data.php" class="nav-link">
            <i class="fas fa-database"></i>
            <span>School Wise Data</span>
        </a>
    </div>
    <div class="nav-item" id="manage-excel">
        <a href="manage_excel.php" class="nav-link">
            <i class="fas fa-file-excel"></i>
            <span>Manage Students Data</span>
        </a>
    </div>
    <div class="nav-item" id="student-data">
        <a href="student_access.php" class="nav-link">
            <i class="fas fa-users"></i>
            <span>Student Access</span>
        </a>
    </div>
    <div class="nav-item" id="school-access">
        <a href="school_access.php" class="nav-link">
            <i class="fas fa-school"></i>
            <span>School Access</span>
        </a>
    </div>
    <div class="nav-item" id="add-admins">
        <a href="admin_add.php" class="nav-link">
            <i class="fas fa-user-shield"></i>
            <span>Manage Admins</span>
        </a>
    </div>
</div>

</div>

<script>
    const toggleSidebarBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    function toggleSidebar() {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    }

    toggleSidebarBtn.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);

    // Handle window resize
    let timeoutId;
    window.addEventListener('resize', () => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            if (window.innerWidth > 992) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        }, 250);
    });
</script>
<script>
    // Get all navigation items
    const navItems = document.querySelectorAll('.nav-item');

    // Get the current URL
    const currentUrl = window.location.pathname.split('/').pop();

    // Loop through all nav items
    navItems.forEach(item => {
        // Get the link href and compare with current URL
        const link = item.querySelector('a');
        const href = link.getAttribute('href');

        // If the href matches the current URL, add 'active' class
        if (href === currentUrl) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
</script>

</body>
</html>