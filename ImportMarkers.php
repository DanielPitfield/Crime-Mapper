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

if(isset($_POST['imp_Crime_Time']))
{
    $time = $_POST['imp_Crime_Time'];
}
else {
    $time = '00:00:00';
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

$stmt = $db->prepare('INSERT INTO markers (Crime_Type, Crime_Date, Crime_Time, Description, Latitude, Longitude)
		VALUES (?,?,?,?,?,?)');

$stmt->bind_param('ssssdd', $crime_type, $date, $time, $description, $latitude, $longitude);

$stmt->execute();

$id = mysqli_insert_id($db);
echo $id;	
?>