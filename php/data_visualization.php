<?php
require_once 'database_config.php';
session_start();

class EnhancedDashboardAnalytics {
    private $mysqli;
    
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }
    
    public function getOverallStats() {
        $stats = [];
        
        $queries = [
            'total_students' => "SELECT COUNT(*) as count FROM students WHERE access = 1",
            'total_male' => "SELECT COUNT(*) as count FROM students WHERE gender = 'Male' AND access = 1",
            'total_female' => "SELECT COUNT(*) as count FROM students WHERE gender = 'Female' AND access = 1",
            'today_leaves' => "SELECT COUNT(*) as count FROM leave_applications 
                             WHERE DATE(from_date) <= CURDATE() 
                             AND DATE(to_date) >= CURDATE()",
            'total_leaves' => "SELECT COUNT(*) as count FROM leave_applications",
            'male_on_leave' => "SELECT COUNT(DISTINCT l.student_id) as count 
                               FROM leave_applications l 
                               JOIN students s ON l.student_id = s.student_id 
                               WHERE s.gender = 'Male' 
                               AND DATE(l.from_date) <= CURDATE() 
                               AND DATE(l.to_date) >= CURDATE()",
            'female_on_leave' => "SELECT COUNT(DISTINCT l.student_id) as count 
                                 FROM leave_applications l 
                                 JOIN students s ON l.student_id = s.student_id 
                                 WHERE s.gender = 'Female' 
                                 AND DATE(l.from_date) <= CURDATE() 
                                 AND DATE(l.to_date) >= CURDATE()",
            'total_dayoutings' => "SELECT COUNT(*) as count FROM day_outing_requests",
            'today_day_outings' => "SELECT COUNT(*) as count FROM day_outing_requests 
                                   WHERE DATE(outdate) = CURDATE()",
            'total_late_outings' => "SELECT COUNT(*) as count FROM late_outing",
            'pending_requests' => "SELECT 
                                  (SELECT COUNT(*) FROM leave_applications WHERE status = 'PENDING') +
                                  (SELECT COUNT(*) FROM day_outing_requests WHERE status = 'PENDING') +
                                  (SELECT COUNT(*) FROM late_outing WHERE status = 'PENDING') as count",
            'approved_leaves' => "SELECT COUNT(*) as count FROM leave_applications WHERE status = 'APPROVED'",
            'rejected_leaves' => "SELECT COUNT(*) as count FROM leave_applications WHERE status = 'REJECTED'",
            'pending_leaves' => "SELECT COUNT(*) as count FROM leave_applications WHERE status = 'PENDING'",
            'students_in_hostel' => "SELECT COUNT(*) as count FROM students 
                                   WHERE student_id NOT IN (
                                       SELECT DISTINCT student_id FROM leave_applications 
                                       WHERE DATE(from_date) <= CURDATE() 
                                       AND DATE(to_date) >= CURDATE()
                                   ) AND access = 1",
            'total_user_logs' => "SELECT COUNT(*) as count FROM user_logs",
            'active_users_today' => "SELECT COUNT(DISTINCT student_id) as count FROM user_logs 
                                   WHERE DATE(login_time) = CURDATE()",
            'male_in_hostel' => "SELECT COUNT(*) as count FROM students 
                                WHERE gender = 'Male' 
                                AND student_id NOT IN (
                                    SELECT DISTINCT student_id FROM leave_applications 
                                    WHERE DATE(from_date) <= CURDATE() 
                                    AND DATE(to_date) >= CURDATE()
                                ) AND access = 1",
            'female_in_hostel' => "SELECT COUNT(*) as count FROM students 
                                 WHERE gender = 'Female' 
                                 AND student_id NOT IN (
                                     SELECT DISTINCT student_id FROM leave_applications 
                                     WHERE DATE(from_date) <= CURDATE() 
                                     AND DATE(to_date) >= CURDATE()
                                 ) AND access = 1"
        ];
        
        foreach ($queries as $key => $query) {
            try {
                $result = $this->mysqli->query($query);
                if ($result) {
                    $stats[$key] = $result->fetch_assoc()['count'];
                } else {
                    $stats[$key] = 0;
                }
            } catch (Exception $e) {
                $stats[$key] = 0;
                error_log("Error in query $key: " . $e->getMessage());
            }
        }
        
        return $stats;
    }
    
    public function getWeeklyStats() {
        $query = "SELECT 
                    DATE(from_date) as date,
                    COUNT(*) as leave_count,
                    COUNT(CASE WHEN status = 'APPROVED' THEN 1 END) as approved_count,
                    COUNT(CASE WHEN status = 'PENDING' THEN 1 END) as pending_count,
                    COUNT(CASE WHEN status = 'REJECTED' THEN 1 END) as rejected_count
                 FROM leave_applications
                 WHERE from_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                 GROUP BY DATE(from_date)
                 ORDER BY date";
        
        try {
            $result = $this->mysqli->query($query);
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        } catch (Exception $e) {
            error_log("Error in getWeeklyStats: " . $e->getMessage());
            return [];
        }
    }
    
    public function getDepartmentStats() {
        $query = "SELECT 
                    s.department,
                    COUNT(DISTINCT s.student_id) as total_students,
                    COUNT(DISTINCT CASE WHEN s.gender = 'Male' THEN s.student_id END) as male_students,
                    COUNT(DISTINCT CASE WHEN s.gender = 'Female' THEN s.student_id END) as female_students,
                    COUNT(DISTINCT CASE WHEN l.id IS NOT NULL AND 
                          DATE(l.from_date) <= CURDATE() AND 
                          DATE(l.to_date) >= CURDATE() 
                          THEN l.student_id END) as students_on_leave,
                    COUNT(DISTINCT CASE WHEN do.id IS NOT NULL AND 
                          DATE(do.outdate) = CURDATE() 
                          THEN do.student_id END) as students_on_outing
                 FROM students s
                 LEFT JOIN leave_applications l ON s.student_id = l.student_id
                 LEFT JOIN day_outing_requests do ON s.student_id = do.student_id
                 WHERE s.access = 1
                 GROUP BY s.department";
        
        try {
            $result = $this->mysqli->query($query);
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        } catch (Exception $e) {
            error_log("Error in getDepartmentStats: " . $e->getMessage());
            return [];
        }
    }
    
    public function getLeaveTypeStats() {
        $query = "SELECT 
                    leave_type,
                    COUNT(*) as count,
                    COUNT(CASE WHEN status = 'APPROVED' THEN 1 END) as approved,
                    COUNT(CASE WHEN status = 'PENDING' THEN 1 END) as pending,
                    COUNT(CASE WHEN status = 'REJECTED' THEN 1 END) as rejected
                 FROM leave_applications
                 WHERE MONTH(from_date) = MONTH(CURDATE())
                 GROUP BY leave_type";
        
        try {
            $result = $this->mysqli->query($query);
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        } catch (Exception $e) {
            error_log("Error in getLeaveTypeStats: " . $e->getMessage());
            return [];
        }
    }
    
    public function getOutingStats() {
        $query = "SELECT
                    'Day Outings' as type,
                    COUNT(*) as total,
                    COUNT(CASE WHEN status = 'APPROVED' THEN 1 END) as approved,
                    COUNT(CASE WHEN status = 'PENDING' THEN 1 END) as pending
                 FROM day_outing_requests
                 WHERE MONTH(outdate) = MONTH(CURDATE())
                 UNION ALL
                 SELECT
                    'Late Outings' as type,
                    COUNT(*) as total,
                    COUNT(CASE WHEN status = 'APPROVED' THEN 1 END) as approved,
                    COUNT(CASE WHEN status = 'PENDING' THEN 1 END) as pending
                 FROM late_outing
                 WHERE MONTH(submission_time) = MONTH(CURDATE())";
        
        try {
            $result = $this->mysqli->query($query);
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        } catch (Exception $e) {
            error_log("Error in getOutingStats: " . $e->getMessage());
            return [];
        }
    }
}

// Initialize dashboard
try {
    $dashboard = new EnhancedDashboardAnalytics($mysqli);
    $overallStats = $dashboard->getOverallStats();
    $weeklyStats = $dashboard->getWeeklyStats();
    $departmentStats = $dashboard->getDepartmentStats();
    $leaveTypeStats = $dashboard->getLeaveTypeStats();
    $outingStats = $dashboard->getOutingStats();
} catch (Exception $e) {
    error_log("Dashboard initialization error: " . $e->getMessage());
    die("Error initializing dashboard. Please check the error logs.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Analytics Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-image: url('../images/back4.jpg');
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .min-h-screen {
            min-height: 100vh;
            padding: 1.5rem;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 0 1rem;
        }

        .header-title {
            font-size: 1.875rem;
            font-weight: bold;
            color: rgb(17, 24, 39);
        }

        .header-subtitle {
            color: rgb(107, 114, 128);
            margin-top: 0.25rem;
        }

        .download-btn {
            background-color: rgb(37, 99, 235);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .download-btn:hover {
            background-color: rgb(29, 78, 216);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            padding: 1rem;
        }

        .stat-card {
            border-radius: 0.75rem;
            padding: 1.5rem;
            color: white;
            position: relative;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .icon-container {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.75rem;
            border-radius: 9999px;
        }

        .stat-title {
            font-size: 0.875rem;
            opacity: 0.75;
        }

        .stat-value {
            font-size: 1.875rem;
            font-weight: bold;
            margin-top: 0.5rem;
        }

        .stat-details {
            margin-top: 1rem;
            display: flex;
            justify-content: space-between;
            font-size: 0.875rem;
        }

        .stat-footer {
            margin-top: 0.5rem;
            font-size: 0.75rem;
            opacity: 0.75;
        }

        /* Card color variations */
        .blue-card {
            background: linear-gradient(135deg, rgb(59, 130, 246) 0%, rgb(37, 99, 235) 100%);
        }

        .green-card {
            background: linear-gradient(135deg, rgb(34, 197, 94) 0%, rgb(21, 128, 61) 100%);
        }

        .purple-card {
            background: linear-gradient(135deg, rgb(168, 85, 247) 0%, rgb(126, 34, 206) 100%);
        }

        .pink-card {
            background: linear-gradient(135deg, rgb(236, 72, 153) 0%, rgb(190, 24, 93) 100%);
        }

        .yellow-card {
            background: linear-gradient(135deg, rgb(234, 179, 8) 0%, rgb(161, 98, 7) 100%);
        }

        .red-card {
            background: linear-gradient(135deg, rgb(239, 68, 68) 0%, rgb(185, 28, 28) 100%);
        }

        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen p-6">
        <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8 px-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Student Analytics Dashboard</h1>
    </div>
    <div class="flex space-x-2"> <!-- Reduced space-x-4 to space-x-2 -->
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center transition duration-300">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download Report
        </button>
        <button onclick="location.href='admin_panel.php'" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg flex items-center transition duration-300">
    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    Back
</button>

    </div>
</div>
            <div class="stats-grid">
              <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm opacity-75">Total Students</p>
                            <p class="text-3xl font-bold"><?php echo $overallStats['total_students']; ?></p>
                        </div>
                        <div class="bg-white bg-opacity-20 p-3 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-between text-sm">
                        <span>Male: <?php echo $overallStats['total_male']; ?></span>
                        <span>Female: <?php echo $overallStats['total_female']; ?></span>
                    </div>
                    <div class="mt-2 text-xs opacity-75">
                        Active Today: <?php echo $overallStats['active_users_today']; ?>
                    </div>
                </div>

                <div class="stat-card bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm opacity-75">Students in Hostel</p>
                            <p class="text-3xl font-bold"><?php echo $overallStats['students_in_hostel']; ?></p>
                        </div>
                        <div class="bg-white bg-opacity-20 p-3 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-between text-sm">
                        <span>Boys: <?php echo $overallStats['male_in_hostel']; ?></span>
                        <span>Girls: <?php echo $overallStats['female_in_hostel']; ?></span>
                    </div>
                    <div class="mt-2 text-xs opacity-75">
                        Occupancy: <?php echo round(($overallStats['students_in_hostel'] / $overallStats['total_students']) * 100, 1); ?>%
                    </div>
                </div>

                <!-- Total Students Card -->
                <div class="stat-card blue-card">
                    <div class="card-header">
                        <div>
                            <p class="stat-title">Total Students</p>
                            <p class="stat-value">2</p>
                        </div>
                        <div class="icon-container">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-details">
                        <span>Male: 2</span>
                        <span>Female: 0</span>
                    </div>
                    <div class="stat-footer">
                        Active Today: 0
                    </div>
                </div>

                <!-- Day Outing Card -->
                <div class="stat-card bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm opacity-75">Day Outings</p>
                            <p class="text-3xl font-bold"><?php echo $overallStats['total_dayoutings']; ?></p>
                        </div>
                        <div class="bg-white bg-opacity-20 p-3 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
                        <div>Today: <?php echo $overallStats['today_day_outings']; ?></div>
                        <div>Pending: <?php echo $outingStats[0]['pending'] ?? 0; ?></div>
                    </div>
                </div>

                <!-- Late Outing Card -->
                <div class="stat-card bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl p-6 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm opacity-75">Late Outings</p>
                            <p class="text-3xl font-bold"><?php echo $overallStats['total_late_outings']; ?></p>
                        </div>
                        <div class="bg-white bg-opacity-20 p-3 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
                        <div>Approved: <?php echo $outingStats[1]['approved'] ?? 0; ?></div>
                        <div>Pending: <?php echo $outingStats[1]['pending'] ?? 0; ?></div>
                    </div>
                </div>

                <!-- User Activity Card -->
                <div class="stat-card bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-6 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm opacity-75">User Activity</p>
                            <p class="text-3xl font-bold"><?php echo $overallStats['total_user_logs']; ?></p>
                        </div>
                        <div class="bg-white bg-opacity-20 p-3 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm">
                        Active Today: <?php echo $overallStats['active_users_today']; ?>
                    </div>
                    <div class="mt-2 text-xs opacity-75">
                        Activity Rate: <?php echo round(($overallStats['active_users_today'] / $overallStats['total_students']) * 100, 1); ?>%
                    </div>
                </div>

                <!-- Leave Status Card -->
                <div class="stat-card bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-6 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm opacity-75">Leave Status</p>
                            <p class="text-3xl font-bold"><?php echo $overallStats['total_leaves']; ?></p>
                        </div>
                        <div class="bg-white bg-opacity-20 p-3 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-2 text-sm">
                        <div>
                            <span class="block">Approved</span>
                            <span class="font-bold"><?php echo $overallStats['approved_leaves']; ?></span>
                        </div>
                        <div>
                            <span class="block">Pending</span>
                            <span class="font-bold"><?php echo $overallStats['pending_leaves']; ?></span>
                        </div>
                        <div>
                            <span class="block">Rejected</span>
                            <span class="font-bold"><?php echo $overallStats['rejected_leaves']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-4">
                <!-- Weekly Trends Chart -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-4">Weekly Leave Trends</h2>
                    <div class="chart-container">
                        <canvas id="weeklyTrendsChart"></canvas>
                    </div>
                </div>

                <!-- Department Distribution Chart -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-4">Department Distribution</h2>
                    <div class="chart-container">
                        <canvas id="departmentChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Additional Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-4">
                <!-- Leave Type Distribution -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-4">Leave Type Distribution</h2>
                    <div class="chart-container">
                        <canvas id="leaveTypeChart"></canvas>
                    </div>
                </div>

                <!-- Outing Statistics -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-4">Outing Statistics</h2>
                    <div class="chart-container">
                        <canvas id="outingStatsChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold mb-4">Department Wise Analysis</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Students</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Male Students</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Female Students</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">On Leave</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">On Outing</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($departmentStats as $dept): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo $dept['department']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $dept['total_students']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $dept['male_students']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $dept['female_students']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $dept['students_on_leave']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $dept['students_on_outing']; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Weekly Trends Chart
        const weeklyTrendsChart = new Chart(document.getElementById('weeklyTrendsChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_map(function($item) {
                    return date('D, M d', strtotime($item['date']));
                }, $weeklyStats)); ?>,
                datasets: [{
                    label: 'Total Leaves',
                    data: <?php echo json_encode(array_column($weeklyStats, 'leave_count')); ?>,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Approved',
                    data: <?php echo json_encode(array_column($weeklyStats, 'approved_count')); ?>,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Department Chart
        const departmentChart = new Chart(document.getElementById('departmentChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($departmentStats, 'department')); ?>,
                datasets: [{
                    label: 'Total Students',
                    data: <?php echo json_encode(array_column($departmentStats, 'total_students')); ?>,
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }, {
                    label: 'Students on Leave',
                    data: <?php echo json_encode(array_column($departmentStats, 'students_on_leave')); ?>,
                    backgroundColor: 'rgba(139, 92, 246, 0.5)',
                    borderColor: 'rgb(139, 92, 246)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Leave Type Chart
        const leaveTypeChart = new Chart(document.getElementById('leaveTypeChart'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($leaveTypeStats, 'leave_type')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($leaveTypeStats, 'count')); ?>,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.5)',
                        'rgba(139, 92, 246, 0.5)',
                        'rgba(34, 197, 94, 0.5)',
                        'rgba(244, 63, 94, 0.5)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });

        // Outing Stats Chart
        const outingStatsChart = new Chart(document.getElementById('outingStatsChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($outingStats, 'type')); ?>,
                datasets: [{
                    label: 'Total',
                    data: <?php echo json_encode(array_column($outingStats, 'total')); ?>,
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }, {
                    label: 'Approved',
                    data: <?php echo json_encode(array_column($outingStats, 'approved')); ?>,
                    backgroundColor: 'rgba(34, 197, 94, 0.5)',
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
    </script>
</body>
</html>