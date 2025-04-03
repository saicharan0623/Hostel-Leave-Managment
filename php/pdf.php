<?php
// Include the database configuration
include 'database_config.php';

function getNumberOfStudentsOnLeave($pdo, $school) {
    $currentDate = date("Y-m-d");

    $stmt = $pdo->prepare("SELECT COUNT(*) AS count 
                           FROM leave_applications 
                           WHERE status = 'Approved' 
                           AND :currentDate BETWEEN from_date AND to_date
                           AND id IN (SELECT student_id FROM $school)");
    $stmt->bindParam(':currentDate', $currentDate);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

function getTotalStudents($pdo, $school) {
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total_count FROM $school");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_count'];
}

function getGenders($pdo, $school) {
    $stmt = $pdo->prepare("
        SELECT 
            SUM(CASE WHEN gender = 'Male' THEN 1 ELSE 0 END) AS total_boys,
            SUM(CASE WHEN gender = 'Female' THEN 1 ELSE 0 END) AS total_girls
        FROM $school
    ");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("
        SELECT 
            SUM(CASE WHEN gender = 'Male' THEN 1 ELSE 0 END) AS boys_on_leave,
            SUM(CASE WHEN gender = 'Female' THEN 1 ELSE 0 END) AS girls_on_leave
        FROM leave_applications
        WHERE status = 'Approved' 
        AND CURRENT_DATE() BETWEEN from_date AND to_date
        AND id IN (SELECT student_id FROM $school)
    ");
    $stmt->execute();
    $leaveResult = $stmt->fetch(PDO::FETCH_ASSOC);

    $result['total_boys_on_leave'] = $leaveResult['boys_on_leave'];
    $result['total_girls_on_leave'] = $leaveResult['girls_on_leave'];

    return $result;
}

$schools = array(
    'STME' => 'stme_students',
    'SOL' => 'sol_students',
    'SPTM' => 'sptm_students',
    'SBM' => 'sbm_students'
);

$school_data = array();
foreach ($schools as $school_name => $school_table) {
    $school_data[$school_name]['total_students'] = getTotalStudents($pdo, $school_table);
    $school_data[$school_name]['number_of_students_on_leave'] = getNumberOfStudentsOnLeave($pdo, $school_table);
    $school_data[$school_name]['genders'] = getGenders($pdo, $school_table);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Report</title>
    
    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.6/jspdf.plugin.autotable.min.js"></script>
    
    <script>
        function getCurrentDateTime() {
            const now = new Date();
            const date = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            return `${date}`;
        }

        function generateTable() {
            var doc = new jsPDF('p', 'pt', 'letter');
            var y = 20;
            doc.setLineWidth(2);
            doc.setFontSize(12);
            doc.text(450, 35, getCurrentDateTime()); 

            var logo = new Image();
            logo.src = '../images/back7.png'; 
            logo.onload = function() {
                doc.addImage(logo, 'PNG', 20, 20, 100, 0);

                doc.setFontSize(24);
                doc.setTextColor("red");
                doc.text(210, 60, "Hostel Leaves Report");

                var contentWidth = doc.internal.pageSize.width - 40;
                var contentHeight = doc.internal.pageSize.height - 40;
                doc.rect(20, 20, contentWidth, contentHeight);

                var tableData = <?php echo json_encode($school_data); ?>;
                var columns = ["School", "Total Students", "Students on Leave", "Total Boys", "Total Girls", "Boys on Leave", "Girls on Leave"];
                var startY = 100;
                for (var school in tableData) {
                    var schoolData = tableData[school];
                    var rows = [
                        [
                            school,
                            schoolData['total_students'] ? schoolData['total_students'] : 0,
                            schoolData['number_of_students_on_leave'] ? schoolData['number_of_students_on_leave'] : 0,
                            schoolData['genders']['total_boys'] ? schoolData['genders']['total_boys'] : 0,
                            schoolData['genders']['total_girls'] ? schoolData['genders']['total_girls'] : 0,
                            schoolData['genders']['total_boys_on_leave'] ? schoolData['genders']['total_boys_on_leave'] : 0,
                            schoolData['genders']['total_girls_on_leave'] ? schoolData['genders']['total_girls_on_leave'] : 0
                        ]
                    ];
                    doc.autoTable(columns, rows, {
                        startY: startY,
                    });
                    startY = doc.autoTableEndPosY() + 20;
                }

                var totalCountTable = [
                    ["Total Students", <?php echo array_sum(array_column($school_data, 'total_students')); ?>],
                    ["Total Students on Leave", <?php echo array_sum(array_column($school_data, 'number_of_students_on_leave')); ?>],
                    ["Total Boys", <?php echo array_sum(array_column(array_column($school_data, 'genders'), 'total_boys')); ?>],
                    ["Total Girls", <?php echo array_sum(array_column(array_column($school_data, 'genders'), 'total_girls')); ?>],
                    ["Total Boys on Leave", <?php echo array_sum(array_column(array_column($school_data, 'genders'), 'total_boys_on_leave')); ?>],
                    ["Total Girls on Leave", <?php echo array_sum(array_column(array_column($school_data, 'genders'), 'total_girls_on_leave')); ?>]
                ];
                doc.autoTable({
                    head: [],
                    body: totalCountTable,
                    startY: startY + 20,
                });

                doc.save('student_report.pdf');
            };
        }
    </script>
</head>
<body>

<div class="container text-center mt-5">
    <img src="../images/back7.png" alt="Logo" class="img-fluid mb-4 animated pulse infinite">
    <h1 class="text-danger mb-3">Report is Ready to Download</h1>
    <p class="lead">Thank you for your patience!</p>
    
    <button class="btn btn-danger btn-lg mb-3" onclick="generateTable()">
        <i class="fas fa-file-pdf"></i> Generate PDF
    </button>
    
    <form action="fetch_data.php">
        <button type="submit" class="btn btn-secondary btn-lg">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
