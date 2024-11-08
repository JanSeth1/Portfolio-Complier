<?php
$host = 'bqryt896i0vmg4w1qquw-mysql.services.clever-cloud.com';
$dbname = 'bqryt896i0vmg4w1qquw';
$username = 'u5bbeglcixrwa0jc';
$password = 'iVYr5zqUNf3A3gLBgy5N';
$port = 3306;

// Set DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$dbname;port=$port";

try {
    // Create a PDO instance (connect to the database)
    $pdo = new PDO($dsn, $username, $password);
    
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // If the connection is successful
    echo "Connected successfully";
} catch (PDOException $e) {
    // If there is an error during connection
    echo "Connection failed: " . $e->getMessage();
}
?>
