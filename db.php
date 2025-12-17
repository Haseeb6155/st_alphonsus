<?php
$host = 'localhost';
$dbname = 'st_alphonsus_school';
$username = 'root';
$password = ''; 

try {
    // Establish database connection using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Enable exception handling for database errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative arrays
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    // Terminate script and display error on connection failure
    die("<h3>Database Connection Failed</h3><p>" . $e->getMessage() . "</p>");
}
?>