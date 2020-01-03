<?php
require 'dbConfig.php';

if(isset($_POST['MarkerID']))
{
    $MarkerID = $_POST['MarkerID'];
}

// Delete marker from database
$sql = "DELETE FROM markers WHERE id = $MarkerID";
$db->query($sql);		
?>