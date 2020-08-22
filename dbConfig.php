<?php 
// Database configuration 
$dbHost     = "localhost"; 
$dbUsername = "test"; 
$dbPassword = "test_crime_2019"; 
$dbName     = "crime_mapper"; 
 
// Create database connection 
$db = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName); 
 
// Check connection 
if (!$db) { 
    die("Connection failed: " . $db->connect_error()); 
}
?>