<?php

$host = 'localhost';
$dbname = 'st_alphonsus_school';
$username = 'root';
$password = ''; // Standard XAMPP/MAMP password is empty or 'root'

try {
    // create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set error mode to exception to catch any database issues immediately
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to Associative Array (key-value pairs)
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    // If connection fails, stop everything and show a clean error message
    // In a real production app, we would log this to a file instead of showing the user
    die("<h3>Database Connection Failed</h3><p>" . $e->getMessage() . "</p>");
}
?>