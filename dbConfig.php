<?php 
// Database configuration 
$dbHost     = "localhost"; 
$dbUsername = "hq017496_test"; 
$dbPassword = "crime_mapper_2019"; 
$dbName     = "hq017496_crime_mapper"; 
 
// Create database connection 
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName); 
 
// Check connection 
if ($db->connect_error) { 
    die("Connection failed: " . $db->connect_error); 
}
?>