<?php
session_start(); // Start the session
if (isset($_SESSION['user_id'])) {
    echo "Logged in as user ID: " . $_SESSION['user_id'];
} else {
    echo "User not logged in.";
}
?>