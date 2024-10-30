<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

// Get the category from the query parameters
$category = $_GET['category'] ?? '';

// Check if the category is valid
if (!in_array($category, ['quizzes', 'homework', 'projects'])) {
    echo json_encode([]);
    exit;
}

// Database connection (replace with your own connection details)
$mysqli = new mysqli("localhost", "username", "password", "database");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Prepare a statement to fetch the uploads
$stmt = $mysqli->prepare("SELECT filename FROM uploads WHERE category = ?");
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();

// Initialize an array to hold the uploads
$uploads = [];
while ($row = $result->fetch_assoc()) {
    $uploads[] = $row; // Store the filenames in an array
}

$stmt->close();
$mysqli->close();

// Return the uploads as JSON
echo json_encode($uploads);

