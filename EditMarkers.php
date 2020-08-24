<?php
require 'dbConfig.php';

// From submit form
if (isset($_POST['Crime_Type']))
{
    $crime_type = $_POST['Crime_Type'];
}
if (isset($_POST['Description']))
{
    $description = $_POST['Description'];
}
if (isset($_POST['Date']))
{
    $date = $_POST['Date'];
}
if (isset($_POST['Time']))
{
    $time = $_POST['Time'];
}

// From AJAX call
if (isset($_POST['id']))
{
    $id = $_POST['id'];
}

if (isset($_POST['Latitude']))
{
    $latitude = $_POST['Latitude'];
}

if (isset($_POST['Longitude']))
{
    $longitude = $_POST['Longitude'];
}

// Statement preparation and execution
$stmt = $db->prepare('UPDATE markers SET Crime_Type = ?, Crime_Date = ?, Crime_Time = ?, Description = ?, Latitude = ?, Longitude = ? WHERE ID = ?');
$stmt->bind_param('ssssddi', $crime_type, $date, $time, $description, $latitude, $longitude, $id);

if(!$stmt->execute()) echo $stmt->error; // Does execute but also returns error if unsuccessful
?>