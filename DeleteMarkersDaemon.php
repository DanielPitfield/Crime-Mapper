<?php
require 'dbConfig.php';

// Delete records that are older than 30 minutes
$stmt = $db->prepare('DELETE FROM operation_jobs WHERE Start_Time < (NOW() - INTERVAL 30 MINUTE)');
if (!$stmt->execute()) echo $stmt->error; 

if (count($argv) != 2) {
    echo "Expected 2 paramters; filePath and Job ID";
    exit;
}

$job_id = $argv[1];

if (empty($job_id)) {
    echo "No Job ID";
    exit;
}

$stmt = $db->prepare('SELECT File_Content FROM operation_jobs WHERE ID = ?');
$stmt->bind_param('i', $job_id);
if ($stmt->execute()) {
    $stmt->store_result();
    $stmt->bind_result($file);
    $stmt->fetch();
    if ($stmt->num_rows != 1) {
        echo "Expected 1 matching record; got " . $stmt->num_rows;
        exit;
    }
    $Marker_Array = json_decode($file);
} 
else {
    echo $stmt->error;
}

$processed = 0;

$total_records = count($Marker_Array);

// Determine how often the progress record is updated
$check_interval = $total_records / 20;
$check_interval = ceil($check_interval);

// Process each ID
for ($i = 0; $i <= $total_records; $i++) {
    // DELETE statement (delete marker)
    $stmt = $db->prepare('DELETE FROM markers WHERE ID = ?');

    $marker = $Marker_Array[$i]; // Must be passed by reference
    $stmt->bind_param('i', $marker);

    if(!$stmt->execute()) echo $stmt->error;

    // UPDATE statement (progress)
    $stmt = $db->prepare('UPDATE operation_jobs SET Processed_Record_Count = ? WHERE ID = ?');

    $processed = $i; // Must be passed by reference
    $stmt->bind_param('ii', $processed, $job_id); // Update the processed number of rows

    if (!$stmt->execute()) echo $stmt->error;           
}
?>