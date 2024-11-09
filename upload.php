<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

// Assuming you have stored the student ID in the session
$student_id = $_SESSION['user_id'];

// Fetch classes the student is enrolled in
$query = "SELECT c.class_id, c.class_name FROM Classes c
          JOIN Students_Classes sc ON c.class_id = sc.class_id
          WHERE sc.student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$classes = [];
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_id = $_POST['class_id'];
    $category_id = $_POST['category_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $upload_dir = 'uploads/';

    // Check if the class_id is valid for the student
    $valid_class = false;
    foreach ($classes as $class) {
        if ($class['class_id'] == $class_id) {
            $valid_class = true;
            break;
        }
    }

    if (!$valid_class) {
        $_SESSION['message'] = "Invalid class selection.";
        header("Location: dashboard.php");
        exit;
    }

    // Check if file was uploaded without errors
    if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] == 0) {
        $file_name = uniqid() . '_' . basename($_FILES['fileToUpload']['name']);
        $file_path = $upload_dir . $file_name;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $file_path)) {
            // Prepare the SQL statement
            $query = "INSERT INTO Student_Work (student_id, class_id, category_id, title, description, file_path, submission_date) VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($query);

            if ($stmt) {
                $stmt->bind_param("iiisss", $student_id, $class_id, $category_id, $title, $description, $file_path);
                if ($stmt->execute()) {
                    $_SESSION['message'] = "File uploaded and record saved successfully.";
                } else {
                    $_SESSION['message'] = "Error executing query: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $_SESSION['message'] = "Prepare failed: " . $conn->error;
            }
        } else {
            $_SESSION['message'] = "Failed to move uploaded file.";
        }
    } else {
        $_SESSION['message'] = "Error: " . $_FILES['fileToUpload']['error'];
    }

    header("Location: dashboard.php");
    exit;
}

$conn->close();
?>