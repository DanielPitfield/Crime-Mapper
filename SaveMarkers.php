<?php
require 'dbConfig.php';

if(isset($_POST['Latitude']))
{
    $latitude = $_POST['Latitude'];
}

if(isset($_POST['Longitude']))
{
    $longitude = $_POST['Longitude'];
}

// Insert information into database
$sql = "INSERT INTO markers (Crime_Type, Description, Latitude, Longitude)
		VALUES ('Placeholder', 'Placeholder', '$latitude', '$longitude')";
$db->query($sql);		
?>	