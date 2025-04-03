<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Outing Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function exportLeaveData() {
            // Get the input values
            const outDate = document.getElementById('outDate').value;
            const school = document.getElementById('school').value;

            // Create a form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'dayout_exporting.php';

            const outDateInput = document.createElement('input');
            outDateInput.type = 'hidden';
            outDateInput.name = 'out_date';
            outDateInput.value = outDate;

            const schoolInput = document.createElement('input');
            schoolInput.type = 'hidden';
            schoolInput.name = 'school';
            schoolInput.value = school;

            form.appendChild(outDateInput);
            form.appendChild(schoolInput);
            document.body.appendChild(form);
            form.submit();
        }

        function goBack() {
            window.history.back(); // Go back to the previous page
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
      flex-direction: column;
    }

    .container {
     max-width:400px;
    }
</style>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Export Outing Data</h1>
        <div class="card">
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label for="outDate" class="form-label">Out Date:</label>
                        <input type="date" class="form-control" id="outDate" name="outDate" required>
                    </div>
                    <div class="mb-3">
    <label for="school" class="form-label">School:</label>
    <select class="form-select" id="school" name="school" required>
        <!-- Options will be dynamically loaded here -->
    </select>
</div>

                    <button type="button" class="btn btn-primary" onclick="exportLeaveData()">Export</button>
                </form>
                <!-- Back button -->
                <button type="button" href="dayout_admin_dashboard.php" class="btn btn-secondary mt-3">Back</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const schoolSelect = document.getElementById('school');

        fetch('get_schools.php')
            .then(response => response.text())
            .then(data => {
                schoolSelect.innerHTML = data;
            })
            .catch(error => console.error('Error fetching school data:', error));
    });
</script>
</body>
</html>
