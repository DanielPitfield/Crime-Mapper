<?php
require 'dbConfig.php';

// From ajax call
if(isset($_POST['imp_Crime_Type']))
{
    $crime_type = $_POST['imp_Crime_Type'];
}

if(isset($_POST['imp_Crime_Date']))
{
    $date = $_POST['imp_Crime_Date'];
}

if(isset($_POST['imp_Description']))
{
    $description = $_POST['imp_Description'];
}

if(isset($_POST['imp_Latitude']))
{
    $latitude = $_POST['imp_Latitude'];
}

if(isset($_POST['imp_Longitude']))
{
    $longitude = $_POST['imp_Longitude'];
}

// Insert information into database
$sql = "INSERT INTO markers (Crime_Type, Crime_Date, Crime_Time, Description, Latitude, Longitude)
		VALUES ('$crime_type', '$date', '00:00:00', '$description', '$latitude', '$longitude')";
$db->query($sql);

$id= mysqli_insert_id($db);
echo $id;	
?>