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
if(isset($_POST['ID']))
{
    $id = $_POST['ID'];
}

if(isset($_POST['Latitude']))
{
    $latitude = $_POST['Latitude'];
}

if(isset($_POST['Longitude']))
{
    $longitude = $_POST['Longitude'];
}

// Insert information into database
$sql = "UPDATE markers
        SET Crime_Type = '$crime_type', Crime_Date = '$date', Crime_Time = '$time', Description = '$description', Latitude = '$latitude', Longitude = '$longitude' WHERE ID = '$id';";
		
$db->query($sql);
?>