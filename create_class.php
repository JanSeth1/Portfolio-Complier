<?php
session_start();
include 'db.php'; 

header('Content-Type: application/json');

// Check if the user is logged in and has the role of a teacher
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'teacher') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access.']);
    exit;
}

$user_id = $_SESSION['user_id']; // Assuming the teacher's user ID is stored in the session

// Initialize variables
$className = '';
$classDescription = '';
$classCode = '';
$errors = [];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $className = trim($_POST['class_name']);
    $classDescription = trim($_POST['class_description']);
    $classCode = trim($_POST['class_code']);

    // Validate form data
    if (empty($className)) {
        $errors[] = 'Class name is required.';
    }
    if (empty($classCode)) {
        $errors[] = 'Class code is required.';
    }

    // If no errors, proceed to insert data into the database
    if (empty($errors)) {
        $sql = "INSERT INTO Classes (class_name, teacher_id) VALUES (?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('si', $className, $user_id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
                exit;
            } else {
                $errors[] = 'Error: Could not execute query.';
            }

            $stmt->close();
        } else {
            $errors[] = 'Error: Could not prepare query.';
        }
    }
}

$conn->close();

// Return errors if any
echo json_encode(['success' => false, 'error' => implode(', ', $errors)]);
?>