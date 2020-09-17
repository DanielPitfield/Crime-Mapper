<?php
require 'dbConfig.php';

// Single marker
if(isset($_GET['id']))
{
    $id = $_GET['id'];

    $stmt = $db->prepare('DELETE FROM markers WHERE ID = ?');
    $stmt->bind_param('i', $id);

    if(!$stmt->execute()) echo $stmt->error;
}

// Multiple markers
if(isset($_GET['Markers_IDs']))
{
    $timestamp = date('Y-m-d H:i:s'); // Current timestamp
    $processed = 0;

    $Marker_Array = $_GET['Markers_IDs'];
    $total_records = count($Marker_Array);

    // Set up new record (to track progress)
    $stmt = $db->prepare('INSERT INTO import_jobs (Start_Time, Processed_Record_Count, Total_Record_Count) VALUES (?,?,?)');
    $stmt->bind_param('sii', $timestamp, $processed, $total_records);
    if ($stmt->execute()) {
        $job_id = mysqli_insert_id($db);
        echo $job_id;
    }
    else {
        echo $stmt->error;
    }

    // Determine how often the record is updated
    $check_interval = $total_records / 20;
    $check_interval = ceil($check_interval);

    // Process each ID
    for ($i = 0; $i < count($Marker_Array); $i++) {
        $stmt = $db->prepare('DELETE FROM markers WHERE ID = ?');
        $stmt->bind_param('i', $Marker_Array[$i]);

        if(!$stmt->execute()) echo $stmt->error;

        $processed++;

        // Check if progress needs to be reported to database after every ID that is processed
        if ($processed % $check_interval == 0) {
            $stmt = $db->prepare('UPDATE import_jobs SET Processed_Record_Count = ? WHERE ID = ?');
            $stmt->bind_param('ii', $processed, $job_id); // Update the processed number of rows
            if(!$stmt->execute()) echo $stmt->error;
        }

        // Check for last interval (completion)
        if ($processed == $total_records) {
            $stmt = $db->prepare('UPDATE import_jobs SET Processed_Record_Count = ? WHERE ID = ?');
            $stmt->bind_param('ii', $processed, $job_id);
            if(!$stmt->execute()) echo $stmt->error;
        }             
    }
}
else {
    echo "The collection of Marker IDs was not found";
}
?>