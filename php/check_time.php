<?php
// Retrieve the POST data
$out_time = $_POST['out_time'];
$in_time = $_POST['in_time'];

// Define valid time range
$min_out_time = "08:00";  // 6 AM
$max_in_time = "21:00";   // 9 PM

$response = [
    'is_valid' => true, // default to true
    'errors' => []
];

// Check if the out time is less than 6 AM
if ($out_time < $min_out_time) {
    $response['is_valid'] = false;
    $response['errors']['out_time'] = 'Out time should not be earlier than 8:00 AM.';
}

// Check if the in time is greater than 9 PM
if ($in_time > $max_in_time) {
    $response['is_valid'] = false;
    $response['errors']['in_time'] = 'In time should not be later than 9:00 PM.';
}

// Return the JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>
