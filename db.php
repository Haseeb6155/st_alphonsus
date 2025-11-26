<?php
$host = 'localhost';
$dbname = 'st_alphonsus_school';
$username = 'root';
$password = ''; // XAMPP default password is empty

try {
    // This line creates the connection using PDO (PHP Data Objects)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // This tells the connection to throw an error if something goes wrong
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
     //echo "Connected successfully"; 
} catch(PDOException $e) {
    // If it fails, this message will show
    echo "Connection failed: " . $e->getMessage();
}
?>