<?php
require 'dbConfig.php';

// From submit form
if(isset($_POST['Crime_Type']))
{
    $crime_type = $_POST['Crime_Type'];
}

if(isset($_POST['Description']))
{
    $description = $_POST['Description'];
}

if(isset($_POST['Date']))
{
    $date = $_POST['Date'];
}

if(isset($_POST['Time']))
{
    $time = $_POST['Time'];
}

// From ajax call
if(isset($_POST['Latitude']))
{
    $latitude = $_POST['Latitude'];
}

if(isset($_POST['Longitude']))
{
    $longitude = $_POST['Longitude'];
}

// Insert information into database
$stmt = $db->prepare('INSERT INTO markers (Crime_Type, Crime_Date, Crime_Time, Description, Latitude, Longitude)
		VALUES (?,?,?,?,?,?)');

$stmt->bind_param('ssssdd', $crime_type, $date, $time, $description, $latitude, $longitude);

$stmt->execute();

$id = mysqli_insert_id($db);
echo $id;	
?>