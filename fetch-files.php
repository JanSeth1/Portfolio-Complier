<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

// Retrieve class_id, category_id, and user_id from the query string
$class_id = isset($_GET['class_id']) ? $_GET['class_id'] : null;
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

// Validate the inputs
if (!is_numeric($class_id) || !is_numeric($category_id) || !is_numeric($user_id)) {
    echo json_encode(['error' => 'Invalid class, category, or user ID']);
    exit;
}

// Prepare the SQL statement to fetch files based on class_id, category_id, and user_id
$query = "SELECT title, file_path, submission_date, status FROM Student_Work WHERE class_id = ? AND category_id = ? AND student_id = ?";

$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
    exit;
}

// Bind the parameters and execute the statement
$stmt->bind_param("iii", $class_id, $category_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the results and store them in an array
$files = [];
while ($row = $result->fetch_assoc()) {
    $files[] = $row;
}


// Output the results as a JSON response
header('Content-Type: application/json');
echo json_encode($files);

// Close the statement and connection
$stmt->close();
$conn->close();
?>