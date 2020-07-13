<?php 
// Database configuration 
$dbHost     = "localhost"; 
$dbUsername = "hq017496_test"; 
$dbPassword = "MOCK INFO"; 
$dbName     = "hq017496_crime_mapper"; 
 
// Create database connection 
$db = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName); 
 
// Check connection 
if (!$db) { 
    die("Connection failed: " . $db->connect_error()); 
}
?>