<div class="table-responsive">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- Logo Section -->
        <!-- <div>
            <img src="../images/back7.png" alt="Logo" height="40">
        </div> -->

        <!-- Search Bar -->
        <div class="input-group w-auto">
            <input type="text" class="form-control" id="searchInput" placeholder="Search...">
        </div>

        <!-- Department Filter -->
        <div>
            <select class="form-select" id="departmentFilter" onchange="filterByDepartment()">
                <option value="">Filter by Department</option>
                <?php 
                // Get unique departments
                $departments = array_unique(array_column($pendingApplications, 'department'));
                foreach ($departments as $department) {
                    echo "<option value='" . htmlspecialchars($department) . "'>" . htmlspecialchars($department) . "</option>";
                }
                ?>
            </select>
        </div>

        <!-- Leave Type Filter -->
        <div>
            <select class="form-select" id="leaveTypeFilter" onchange="filterByLeaveType()">
                <option value="">Filter by Leave Type</option>
                <option value="Academic">Academic</option>
                <option value="Non-Academic">Non-Academic</option>
            </select>
        </div>

        <!-- Per Page Options -->
        <div>
            <select class="form-select" id="perPageSelect" onchange="changePerPage()">
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="all">All</option>
            </select>
        </div>
        
        <div>
    <form action="download_leave_applications.php" method="post" style="display:inline;">
        <button type="submit" class="btn btn-primary" style="background-color:rgb(210, 35, 42); border:none;">
            <i class="fas fa-download"></i> Leaves
        </button>
    </form>
</div>


    </div>

    <table id="lateApplicationsTable" class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Student Name</th>
                <th>Department</th>
                <th>Leave Type</th>
                <th>Duration</th>
                <th>Dates</th>
                <th>Parents Contact</th>
                <th>Purpose</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="applicationsBody">
            <?php 
            if (!empty($pendingApplications)):
                foreach ($pendingApplications as $application):
                    $from_date = new DateTime($application['from_date']);
                    $to_date = new DateTime($application['to_date']);
                    $duration = $from_date->diff($to_date)->days + 1;
            ?>
            <tr data-department="<?php echo htmlspecialchars($application['department']); ?>" data-leave-type="<?php echo htmlspecialchars($application['leave_type']); ?>" data-application-id="<?php echo $application['id']; ?>">
                <td><?php echo str_pad($application['id'], 1, '0', STR_PAD_LEFT); ?></td>
                <td>
                    <div>
                        <div class="fw-semibold"><?php echo htmlspecialchars($application['student_name']); ?></div>
                        <small class="text-muted"><?php echo htmlspecialchars($application['student_id']); ?></small>
                    </div>
                </td>                
                <td>
                    <div class="d-flex flex-column">
                        <span><?php echo htmlspecialchars($application['department']); ?></span>
                        <small class="text-muted"><?php echo htmlspecialchars($application['batch']); ?> Year</small>
                    </div>
                </td>
                <td><?php echo htmlspecialchars($application['leave_type']); ?></td>
                <td><?php echo $duration; ?> days</td>
                <td>
                    From: <?php echo date('d M Y', strtotime($application['from_date'])); ?><br>
                    To: <?php echo date('d M Y', strtotime($application['to_date'])); ?>
                </td>
                <td><?php echo htmlspecialchars($application['phone']); ?></td>
                <td><?php echo htmlspecialchars($application['reason']); ?></td>
                <td>
    <?php 
    $statusColors = [
        'PENDING' => 'danger', // red for pending
        'APPROVED' => '', // no color for approved
        'REJECTED' => '' // no color for rejected
    ];
    $statusColor = $statusColors[$application['status']] ?? '';
    ?>
    <span class="badge <?php echo $statusColor ? 'bg-' . $statusColor : ''; ?>">
        <?php echo htmlspecialchars($application['status']); ?>
    </span>
    <?php if($application['status'] === 'PENDING'): ?>
        <small class="d-block text-muted mt-1">
            <?php echo timeAgo($application['created_at']); ?>
        </small>
    <?php endif; ?>
</td>
<td>

                    <form action="approve.php" method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $application['id']; ?>">
                        <input type="hidden" name="created_at" value="<?php echo $application['created_at']; ?>">
                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                    </form>
                    <form action="reject.php" method="post" style="display:inline;" onsubmit="return askRejectReason();">
                        <input type="hidden" name="id" value="<?php echo $application['id']; ?>">
                        <input type="hidden" name="created_at" value="<?php echo $application['created_at']; ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                    </form>
                </td>
            </tr>
            <?php 
                endforeach;
            else: 
            ?>
            <tr>
                <td colspan="10" class="text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <h6>No pending applications found</h6>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    let searchText = this.value.toLowerCase();
    let rows = document.querySelectorAll('#applicationsBody tr');
    
    rows.forEach(row => {
        let studentName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        let department = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        
        if (studentName.includes(searchText) || department.includes(searchText)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Department Filter Function
function filterByDepartment() {
    let filterValue = document.getElementById('departmentFilter').value.toLowerCase();
    let rows = document.querySelectorAll('#applicationsBody tr');
    
    rows.forEach(row => {
        let department = row.getAttribute('data-department').toLowerCase();
        
        if (filterValue === '' || department.includes(filterValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Leave Type Filter Function
function filterByLeaveType() {
    let filterValue = document.getElementById('leaveTypeFilter').value;
    let rows = document.querySelectorAll('#applicationsBody tr');
    
    rows.forEach(row => {
        let leaveType = row.getAttribute('data-leave-type').toLowerCase();
        
        if (filterValue === '' || leaveType.includes(filterValue.toLowerCase())) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Change items per page
function changePerPage() {
    let perPage = document.getElementById('perPageSelect').value;
    let rows = document.querySelectorAll('#applicationsBody tr');
    
    if (perPage === 'all') {
        rows.forEach(row => row.style.display = ''); // Show all rows
    } else {
        let visibleRows = Array.from(rows).slice(0, perPage);
        rows.forEach(row => row.style.display = 'none'); // Hide all rows first
        visibleRows.forEach(row => row.style.display = ''); // Show selected number of rows
    }
}

function askRejectReason() {
    var reason = prompt("Please provide the reason for rejection:");
    if (reason != null && reason.trim() !== "") {
        // Append the rejection reason to the URL
        window.location.href = "reject.php?id=<?php echo $application['id']; ?>&created_at=<?php echo urlencode($application['created_at']); ?>&reject_reason=" + encodeURIComponent(reason);
        return false; // prevent default behavior (redirection)
    } else {
        alert("Rejection reason is required!");
        return false; // prevent the action from happening if no reason is provided
    }
}
<?php
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);  // Converts the datetime string into a Unix timestamp
    $diff = time() - $timestamp;  // Calculates the difference between the current time and the given timestamp
    
    if ($diff < 60) {  // If the difference is less than 1 minute
        return 'Just now';
    } elseif ($diff < 3600) {  // If the difference is less than 1 hour
        $minutes = floor($diff / 60);  // Convert the difference into minutes
        return $minutes . ' min ago';
    } elseif ($diff < 86400) {  // If the difference is less than 1 day
        $hours = floor($diff / 3600);  // Convert the difference into hours
        return $hours . ' hour ago';
    } elseif ($diff < 2592000) {  // If the difference is less than 30 days
        $days = floor($diff / 86400);  // Convert the difference into days
        return $days . ' day ago';
    } else {  // If the difference is greater than 30 days, return the date in 'd M Y' format
        return date('d M Y', $timestamp);  // Format the date as 'day month year'
    }
}
?>
</script>
