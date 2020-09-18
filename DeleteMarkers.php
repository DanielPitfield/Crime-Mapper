<?php
require 'dbConfig.php';

// Single marker
if(isset($_GET['id']))
{
    $id = $_GET['id'];

    $stmt = $db->prepare('DELETE FROM markers WHERE ID = ?');
    $stmt->bind_param('i', $id);

    if(!$stmt->execute() {
        http_response_code(500);
        echo $stmt->error;
    }
}

// Multiple markers
if(isset($_GET['Marker_IDs']))
{
    $timestamp = date('Y-m-d H:i:s'); // Current timestamp
    $processed = 0;

    $Marker_Array = $_GET['Marker_IDs'];

    $total_records = count($Marker_Array);

    // TODO Fix INSERT statement

    // Set up new record (to track progress)
    $stmt = $db->prepare('INSERT INTO operation_jobs (Start_Time, Processed_Record_Count, Total_Record_Count, File_Content) VALUES (?,?,?,?)');
    $empty = "";
    $stmt->bind_param('siib', $timestamp, $processed, $total_records, $empty);
    $stmt->send_long_data(3, json_encode($Marker_Array));

    if ($stmt->execute()) {
        $job_id = mysqli_insert_id($db);
        
        // TODO Specifying path (deployment not local)
        shell_exec('C:\laragon\bin\php\php-7.2.19-Win32-VC15-x64\php.exe -q DeleteMarkersDaemon.php ' . $job_id);

        http_response_code(202);
        echo $job_id;
    }
    else {
        http_response_code(500);
        echo $stmt->error;
    }
}
else {
    http_response_code(400);
    echo "The collection of Marker IDs was not found";
}
?>