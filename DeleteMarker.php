<?php
require 'dbConfig.php';

// Single marker
if(isset($_POST['id']))
{
    $id = $_POST['id'];

    $stmt = $db->prepare('DELETE FROM markers WHERE ID = ?');
    $stmt->bind_param('i', $id);

    if(!$stmt->execute()) echo $stmt->error;
}

// Multiple markers
if(isset($_POST['visibleMarkers_IDs']))
{
    file_put_contents("delete_progress.txt", "0");
    $Marker_Array = $_POST['visibleMarkers_IDs'];
    $MarkerID_m = -1;
    
    $num_markers = count($Marker_Array);
    $check_interval = $num_markers / 20;
    $check_interval = ceil($check_interval);

    for ($i = 0; $i < count($Marker_Array); $i++) {
        $MarkerID_m = $Marker_Array[$i];

        $stmt = $db->prepare('DELETE FROM markers WHERE ID = ?');
        $stmt->bind_param('i', $MarkerID_m);

        if(!$stmt->execute()) echo $stmt->error;
        
        if ($i % $check_interval == 0) {
            file_put_contents("delete_progress.txt",(($i/$num_markers)*100));
        }        
    }
    file_put_contents("delete_progress.txt","100");
}
?>