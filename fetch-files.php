<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection file
include 'db.php';

// Retrieve category_id from the query string
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;

// Validate the category_id
if (!is_numeric($category_id)) {
    echo json_encode(['error' => 'Invalid category ID']);
    exit;
}

// Prepare the SQL statement to fetch files based on category_id
$query = "SELECT title, file_path, submission_date FROM Student_Work WHERE category_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
    exit;
}

// Bind the category_id parameter and execute the statement
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the results and store them in an array
$files = [];
while ($row = $result->fetch_assoc()) {
    $files[] = $row;
}

// Output the results as a JSON response
echo json_encode($files);

// Close the statement and connection
$stmt->close();
$conn->close();
?>