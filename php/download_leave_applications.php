<?php
// Assuming you have a connection to your database
include('database_config.php');

// Fetch distinct departments from the students table
$query = "SELECT DISTINCT department FROM students";
$result = mysqli_query($mysqli, $query);

$departments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $departments[] = $row['department'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Leave Data</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function exportLeaveData() {
            // Get the input values
            const fromDate = document.getElementById('fromDate').value;
            const toDate = document.getElementById('toDate').value;
            const department = document.getElementById('department').value;

            // Create a form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'export_leaves.php';

            const fromInput = document.createElement('input');
            fromInput.type = 'hidden';
            fromInput.name = 'from_date';
            fromInput.value = fromDate;

            const toInput = document.createElement('input');
            toInput.type = 'hidden';
            toInput.name = 'to_date';
            toInput.value = toDate;

            const departmentInput = document.createElement('input');
            departmentInput.type = 'hidden';
            departmentInput.name = 'department';
            departmentInput.value = department;

            form.appendChild(fromInput);
            form.appendChild(toInput);
            form.appendChild(departmentInput);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</head>
<style>
    body {
        margin: 0;
        padding: 0;
        background-image: url("../images/back4.jpg");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        font-family: Arial, sans-serif;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
    }

    .container {
        max-width: 400px;
        width: 100%;
    }

    .card {
        width: 100%;
    }

    .btn {
        background-color: rgb(210, 35, 42);
        border: none;
    }

    /* Back button positioning */
    .back-button {
        position: absolute;
        top: 20px;
        right: 20px;
        padding: 10px 20px;
        background-color: #6c757d;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    }

    .back-button:hover {
        background-color: #5a6268;
    }
</style>
<body>
    <!-- Back Button -->
    <a href="admin_panel.php" class="back-button">Back</a>

    <div class="container">
        <h1 class="text-center mb-4">Export Leave Data</h1>
        <div class="card">
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label for="fromDate" class="form-label">From Date:</label>
                        <input type="date" class="form-control" id="fromDate" name="fromDate">
                    </div>
                    <div class="mb-3">
                        <label for="toDate" class="form-label">To Date:</label>
                        <input type="date" class="form-control" id="toDate" name="toDate">
                    </div>
                    <div class="mb-3">
                        <label for="department" class="form-label">Department:</label>
                        <select class="form-select" id="department" name="department" required>
                            <option value="" disabled selected>Select a Department</option>
                            <option value="All">All</option> <!-- Added "All" option -->
                            <!-- Fetch departments dynamically -->
                            <?php foreach ($departments as $department): ?>
                                <option value="<?php echo htmlspecialchars($department); ?>"><?php echo htmlspecialchars($department); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="exportLeaveData()">Export</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
