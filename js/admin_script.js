// Initialize DataTables
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('.datatable').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search..."
            }
        });
    }

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Sidebar Toggle
    $('#sidebarToggle').on('click', function() {
        $('.wrapper').toggleClass('sidebar-collapsed');
    });

    // Mobile Sidebar Toggle
    $('#mobileSidebarToggle').on('click', function() {
        $('.wrapper').toggleClass('sidebar-active');
    });
});

// Leave Management Functions
function approveLeave(leaveId) {
    Swal.fire({
        title: 'Confirm Approval',
        text: 'Are you sure you want to approve this leave?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, approve it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Send AJAX request to approve leave
            $.post('actions/approve_leave.php', {
                leave_id: leaveId
            }, function(response) {
                if (response.success) {
                    Swal.fire(
                        'Approved!',
                        'The leave has been approved.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire(
                        'Error!',
                        response.message,
                        'error'
                    );
                }
            }, 'json');
        }
    });
}

function rejectLeave(leaveId) {
    $('#rejectionModal').modal('show');
    $('#rejectionForm').on('submit', function(e) {
        e.preventDefault();
        const reason = $('#rejectionReason').val();
        
        $.post('actions/reject_leave.php', {
            leave_id: leaveId,
            reason: reason
        }, function(response) {
            if (response.success) {
                $('#rejectionModal').modal('hide');
                Swal.fire(
                    'Rejected!',
                    'The leave has been rejected.',
                    'success'
                ).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire(
                    'Error!',
                    response.message,
                    'error'
                );
            }
        }, 'json');
    });
}

// View Leave Details
function viewDetails(leaveId) {
    $.get('actions/get_leave_details.php', {
        leave_id: leaveId
    }, function(response) {
        if (response.success) {
            $('#leaveDetailsContent').html(response.html);
            $('#leaveDetailsModal').modal('show');
        } else {
            Swal.fire(
                'Error!',
                'Could not fetch leave details.',
                'error'
            );
        }
    }, 'json');
}

// Refresh DataTable
function refreshTable() {
    $('.datatable').DataTable().ajax.reload();
}

// Handle Dark Mode Toggle
$('#darkModeToggle').on('click', function() {
    $('body').toggleClass('dark-theme');
    localStorage.setItem('darkMode', $('body').hasClass('dark-theme'));
});

// Check for saved dark mode preference
if (localStorage.getItem('darkMode') === 'true') {
    $('body').addClass('dark-theme');
}