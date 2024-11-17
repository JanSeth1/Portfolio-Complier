<?php
// fetch_available_students.php
include 'db.php'; // Include your database connection

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the class ID from the request
$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;

// Validate the class ID
if ($class_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid class ID.']);
    exit;
}

// Prepare the SQL query to fetch students not in the specified class
$query = "
    SELECT user_id, username FROM Users 
    WHERE role = 'student' 
    AND user_id NOT IN (
        SELECT student_id FROM Students_Classes WHERE class_id = ?
    )
";

$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Database query preparation failed: ' . $conn->error]);
    exit;
}

// Bind the class ID parameter
$stmt->bind_param("i", $class_id);
$stmt->execute();
$result = $stmt->get_result();

// Check for execution errors
if ($result === false) {
    echo json_encode(['success' => false, 'error' => 'Database query execution failed: ' . $stmt->error]);
    exit;
}

// Fetch the students
$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = [
        'user_id' => $row['user_id'],
        'username' => $row['username']
        // No email field since it doesn't exist
    ];
}

// Return the result as JSON
echo json_encode(['success' => true, 'students' => $students]);
?>