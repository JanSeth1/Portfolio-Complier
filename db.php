<?php
$servername = "sql.freedb.tech";
$username = "freedb_janseth";
$password = "9tHVz52!#ASjW6f";
$dbname = "freedb_portfoliocompiler";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
