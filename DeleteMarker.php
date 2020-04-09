<?php
require 'dbConfig.php';

if(isset($_POST['MarkerID']))
{
    $MarkerID = $_POST['MarkerID'];
}

$stmt = $db->prepare('DELETE FROM markers WHERE ID = ?');

$stmt->bind_param('i', $MarkerID);

$stmt->execute();
?>