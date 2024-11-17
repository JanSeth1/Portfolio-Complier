<?php
session_start(); // This must be the first line
include 'db.php'; // Include your database connection

$response = ['success' => false, 'submissions' => []];

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) { // Check for 'user_id' instead of 'teacher_id'
    error_log("User not logged in."); // Debugging output
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit; // Stop further execution
}

// User is logged in, proceed to fetch submissions
$teacher_id = $_SESSION['user_id']; // Use the correct session variable
$class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : null;

// Example query to fetch submissions for classes taught by the logged-in teacher
$query = "
    SELECT sw.work_id, u.username, sw.title, sw.description, sw.submission_date
    FROM Student_Work sw
    JOIN Users u ON sw.student_id = u.user_id
    JOIN Classes c ON sw.class_id = c.class_id
    WHERE c.teacher_id = ?
";

// If a class_id is provided, filter by that class
if ($class_id) {
    $query .= " AND sw.class_id = ?";
}

if ($stmt = $conn->prepare($query)) {
    // Bind parameters based on whether class_id is provided
    if ($class_id) {
        $stmt->bind_param("ii", $teacher_id, $class_id);
    } else {
        $stmt->bind_param("i", $teacher_id);
    }
    
    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch results
    while ($row = $result->fetch_assoc()) {
        $response['submissions'][] = $row;
    }

    $response['success'] = true;
} else {
    $response['error'] = 'Database query failed.';
}

// Return the response as JSON
echo json_encode($response);
?>