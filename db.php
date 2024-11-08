<?php
$servername = "whpeu.h.filess.io";
$username = "portfoliodatabase_sentmetthy";
$password = "84b7545529089cca782230034da28d547976fff7";
$dbname = "portfoliodatabase_sentmetthy";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
