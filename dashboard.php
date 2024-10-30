<?php
session_start();
include 'db.php'; 

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id']; 
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
        <li><a href="view_portfolio.php?category=quizzes"><i class="fas fa-book"></i>View Quizzes</a></li>
        <li><a href="view_portfolio.php?category=homework"><i class="fas fa-pencil-alt"></i>View Homework</a></li>
        <li><a href="view_portfolio.php?category=projects"><i class="fas fa-project-diagram"></i>View Projects</a></li>
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
            <div class="card" onclick="openModal('quizzes')">
                <h3>Total Quizzes</h3>
                <p>5 submitted</p>
            </div>
            <div class="card" onclick="openModal('homework')">
                <h3>Total Homework</h3>
                <p>8 submitted</p>
            </div>
            <div class="card" onclick="openModal('projects')">
                <h3>Total Projects</h3>
                <p>3 submitted</p>
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

    <!-- Upload Modal Section -->
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUploadModal()">&times;</span>
            <h2 style="text-align: center; color: #4caf50;">Upload New Work</h2>
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <label for="category">Select Category:</label>
                <select name="category" id="category" required>
                    <option value="homework">Homework</option>
                    <option value="quizzes">Quizzes</option>
                    <option value="projects">Projects</option>
                </select>

                <label for="fileToUpload">Select file to upload:</label>
                <input type="file" name="fileToUpload" id="fileToUpload" required>

                <input type="submit" value="Upload" name="upload">
            </form>
        </div>
    </div>

    <!-- Modal Section -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle"></h2>
            <div id="modalBody" class="uploads-overview">Detailed information about the selected category will appear here.</div>
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
                                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>
                                <a href="#" class="change-link">Change</a>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password">Password:</label>
                            <div class="input-container">
                                <input type="password" id="password" name="password" value="********" readonly>
                                <a href="#" class="change-link">Change</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <a href="#" class="delete-account">Delete Your Account</a>
                    <p>You will receive an email to confirm your decision. Please note, that all boards you have created will be permanently erased.</p>
                </div>

                <div class="actions">
                    <button type="button" class="cancel">Cancel</button>
                    <button type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Jan Seth POGI</p>
    </footer>

</div>
<script src="script.js"></script>

</body>
</html>