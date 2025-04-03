<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include 'database_config.php';

function getOverallStats($pdo) {
    $stats = [];
    
    // Total Students
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM (
        SELECT student_id FROM sptm_students
        UNION ALL SELECT student_id FROM sol_students
        UNION ALL SELECT student_id FROM sbm_students
        UNION ALL SELECT student_id FROM stme_students
    ) as all_students");
    $stats['totalStudents'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Students on Leave
    $stmt = $pdo->query("SELECT COUNT(DISTINCT id) as on_leave FROM leave_applications 
                         WHERE status = 'Approved' AND CURRENT_DATE BETWEEN from_date AND to_date");
    $stats['studentsOnLeave'] = $stmt->fetch(PDO::FETCH_ASSOC)['on_leave'];

    // Boys and Girls on Leave
    $stmt = $pdo->query("SELECT 
                            SUM(CASE WHEN gender = 'Male' THEN 1 ELSE 0 END) as boys_on_leave,
                            SUM(CASE WHEN gender = 'Female' THEN 1 ELSE 0 END) as girls_on_leave
                         FROM leave_applications 
                         WHERE status = 'Approved' AND CURRENT_DATE BETWEEN from_date AND to_date");
    $genderStats = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['boysOnLeave'] = $genderStats['boys_on_leave'];
    $stats['girlsOnLeave'] = $genderStats['girls_on_leave'];

    return $stats;
}

function getLeaveStats($pdo) {
    $stmt = $pdo->query("SELECT 
                            COUNT(*) as totalLeaves,
                            SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approvedLeaves,
                            SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejectedLeaves,
                            SUM(CASE WHEN status = 'PENDING-WITH-RECTOR' THEN 1 ELSE 0 END) as pendingLeaves
                         FROM leave_applications");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getLeavesByDay($pdo) {
    $stmt = $pdo->query("SELECT 
                            DAYNAME(from_date) as day,
                            COUNT(*) as count
                         FROM leave_applications
                         GROUP BY DAYNAME(from_date)
                         ORDER BY FIELD(DAYNAME(from_date), 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getLeaveStatus($pdo) {
    $stmt = $pdo->query("SELECT 
                            status as name,
                            COUNT(*) as value
                         FROM leave_applications
                         GROUP BY status");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSchoolData($pdo) {
    $schools = ['sptm_students', 'sol_students', 'sbm_students', 'stme_students'];
    $schoolData = [];

    foreach ($schools as $school) {
        $stmt = $pdo->query("SELECT COUNT(*) as students FROM $school");
        $totalStudents = $stmt->fetch(PDO::FETCH_ASSOC)['students'];

        $stmt = $pdo->query("SELECT COUNT(DISTINCT la.id) as on_leave 
                             FROM leave_applications la
                             JOIN $school s ON la.id = s.student_id
                             WHERE la.status = 'Approved' AND CURRENT_DATE BETWEEN la.from_date AND la.to_date");
        $onLeave = $stmt->fetch(PDO::FETCH_ASSOC)['on_leave'];

        $schoolData[] = [
            'name' => strtoupper(str_replace('_students', '', $school)),
            'students' => $totalStudents,
            'onLeave' => $onLeave
        ];
    }

    return $schoolData;
}

function getGenderDistribution($pdo) {
    $stmt = $pdo->query("SELECT 
                            gender as name,
                            COUNT(*) as value
                         FROM (
                            SELECT gender FROM sptm_students
                            UNION ALL SELECT gender FROM sol_students
                            UNION ALL SELECT gender FROM sbm_students
                            UNION ALL SELECT gender FROM stme_students
                         ) as all_students
                         GROUP BY gender");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = [
        'overallStats' => getOverallStats($pdo),
        'leaveStats' => getLeaveStats($pdo),
        'leavesByDay' => getLeavesByDay($pdo),
        'leaveStatus' => getLeaveStatus($pdo),
        'schoolData' => getSchoolData($pdo),
        'genderDistribution' => getGenderDistribution($pdo)
    ];

    echo json_encode($data);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}