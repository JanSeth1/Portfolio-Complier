<?php
include 'db.php';

// Parse the JSON data
$data = json_decode(file_get_contents("php://input"), true);
$work_id = $data['work_id'];
$status = $data['status'];

// Validate the inputs
if (!is_numeric($work_id) || !in_array($status, ['submitted', 'reviewed', 'approved', 'rejected'])) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// Prepare the SQL statement
$query = "UPDATE Student_Work SET status = ? WHERE work_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
    exit;
}

// Bind the parameters and execute the statement
$stmt->bind_param("si", $status, $work_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Execution failed: ' . $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
