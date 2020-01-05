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

$date_time = $date . ' ' . $time; // Combine date and time together

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
$sql = "INSERT INTO markers (Crime_Type, Date_Time, Description, Latitude, Longitude)
		VALUES ('$crime_type', '$date_time', '$description', '$latitude', '$longitude')";
$db->query($sql);

$id= mysqli_insert_id($db);
echo $id;	
?>
