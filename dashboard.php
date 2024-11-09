<?php
session_start();
include 'db.php'; 

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id']; 


// Fetch the username
$query = "SELECT username FROM Users WHERE user_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$username = $user['username'];

// Fetch classes the student is enrolled in
$query = "SELECT c.class_id, c.class_name FROM Classes c
          JOIN Students_Classes sc ON c.class_id = sc.class_id
          WHERE sc.student_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$classes = [];
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}
$stmt->close();

// Fetch the count of each category for the logged-in user
$query = "SELECT c.category_name, COUNT(sw.work_id) AS total
          FROM Categories c
          LEFT JOIN Student_Work sw ON c.category_id = sw.category_id
          WHERE sw.student_id = ?
          GROUP BY c.category_name";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$categoryCounts = [];
while ($row = $result->fetch_assoc()) {
    $categoryCounts[$row['category_name']] = $row['total'];
}
$stmt->close();

// Get the message from the session and clear it
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Student Portfolio</title>
    <link rel="stylesheet" href="dash-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
    <div class="brand">
        <h2>Portfolio Organizer</h2>
    </div>
    <!-- Sidebar Menu with Icons -->
    <ul id="sidebarMenu">
        <li><a href="dashboard.php"><i class="fas fa-home"></i>Dashboard Home</a></li>
        <li><a href="javascript:void(0);" onclick="openUploadModal()"><i class="fas fa-upload"></i>Upload Work</a></li>
        <li><a href="javascript:void(0);" onclick="selectCategory('quizzes')"><i class="fas fa-book"></i>View Quizzes</a></li>
        <li><a href="javascript:void(0);" onclick="selectCategory('homework')"><i class="fas fa-pencil-alt"></i>View Homework</a></li>
        <li><a href="javascript:void(0);" onclick="selectCategory('projects')"><i class="fas fa-project-diagram"></i>View Projects</a></li>
        <li><a href="javascript:void(0);" onclick="openSettingsModal()"><i class="fas fa-cog"></i>Settings</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
    </header>

    <section class="dashboard-overview">
    <h2>Dashboard Overview</h2>
    <div class="summary">
    <div class="card" onclick="selectCategory('homework')">
                <h3>Homework</h3>
                <p>Total: <?php echo $categoryCounts['homework'] ?? 0; ?></p>
            </div>

            <div class="card" onclick="selectCategory('quizzes')">
                <h3>Quizzes</h3>
                <p>Total: <?php echo $categoryCounts['quiz'] ?? 0; ?></p>
            </div>

            <div class="card" onclick="selectCategory('projects')">
                <h3>Projects</h3>
                <p>Total: <?php echo $categoryCounts['project'] ?? 0; ?></p>
            </div>

            <div id="viewUploadsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('viewUploadsModal')">&times;</span>
        <h2 id="modalTitle">Uploaded Files</h2>
        
        <label for="filterClass">Filter by Class:</label>
        <select id="filterClass" onchange="fetchUploads()">
            <option value="">Select a class</option>
            <!-- Options will be added dynamically -->
        </select>

        <div id="uploadsList" class="uploads-overview">
            <!-- Uploads will be dynamically inserted here -->
        </div>
    </div>
</div>

    </div>
</section>

    <section class="quick-actions">
        <h2>Quick Actions</h2>
        <ul>
            <li><button onclick="openUploadModal()">Upload New Work</button></li>
            <li><a href="view_portfolio.php">View All Submissions</a></li>
        </ul>
    </section>

    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeUploadModal()">&times;</span>
            <h2>Upload New Work</h2>
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="student_id" value="<?php echo $user_id; ?>">

                <label for="class">Select Class:</label>
                <select name="class_id" id="class" required>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['class_id']; ?>">
                            <?php echo htmlspecialchars($class['class_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="category">Select Category:</label>
                <select name="category_id" id="category" required>
                    <option value="1">Homework</option>
                    <option value="4">Project</option>
                    <option value="3">Quiz</option>
                </select>

                <label for="title">Title:</label>
                <input type="text" name="title" id="title" required>

                <label for="description">Description:</label>
                <textarea name="description" id="description"></textarea>

                <label for="fileToUpload">Select file to upload:</label>
                <input type="file" name="fileToUpload" id="fileToUpload" required>

                <button type="submit" name="upload">Upload</button>
            </form>
        </div>
    </div>


    <!-- <div id="viewUploadsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('viewUploadsModal')">&times;</span>
        <h2 id="modalTitle">Uploaded Files</h2>
        
        <label for="filterClass">Filter by Class:</label>
        <select id="filterClass">
            Classes will be dynamically populated -->
        <!-- </select>

        <button onclick="fetchUploads()">Fetch Uploads</button>

        <div id="uploadsList" class="uploads-overview">
            Uploads will be dynamically inserted here -->
        <!-- </div>
    </div> -->
</div>


</div>
    <!-- Settings Modal -->
    <div id="settingsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeSettingsModal()">&times;</span>
        <h2>Account Settings</h2>
        <form action="settings.php" method="post">
            <div class="form-container">
                <div class="form-fields">
                    <div class="form-group">
                        <label for="username">Your Name:</label>
                        <div class="input-container">
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                            <a href="#" class="change-link" onclick="toggleUsernameEdit()">Change</a>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <div class="input-container">
                            <input type="password" id="password" name="password" value="********" readonly>
                            <a href="#" class="change-link" onclick="togglePasswordEdit()">Change</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <a href="#" class="delete-account" onclick="deleteAccount()">Delete Your Account</a>
                <p>You will receive an email to confirm your decision. Please note, that all boards you have created will be permanently erased.</p>
            </div>

            <div class="actions">
                <button type="button" class="cancel" onclick="closeSettingsModal()">Cancel</button>
                <button type="submit">Save</button>
            </div>
        </form>
    </div>
</div>


    <footer>
        <p>&copy; 2024 Jan Seth POGI</p>
    </footer>

</div>
<script>
    const userId = <?php echo json_encode($user_id); ?>;
    document.addEventListener('DOMContentLoaded', function() {
        const message = <?php echo json_encode($message); ?>;
        if (message) {
            alert(message);
        }
    });
    
</script>
<script src="script.js"></script>

</body>
</html>