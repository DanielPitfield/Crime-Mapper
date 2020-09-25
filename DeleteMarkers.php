<?php
require 'dbConfig.php';

// Single marker
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $db->prepare('DELETE FROM markers WHERE ID = ?');
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        http_response_code(202);
    } else {
        http_response_code(500);
        echo $stmt->error;
    }
}

// Multiple markers
else {
    $body = file_get_contents('php://input');
    if (isset($body)) {
        $body = str_replace("&", "", $body);
        $Marker_Array = explode("Job_ID=", $body);

        $timestamp = date('Y-m-d H:i:s'); // Current timestamp
        $processed = 0;

        $total_records = count($Marker_Array);

        // Set up new record (to track progress)
        $stmt = $db->prepare('INSERT INTO operation_jobs (Start_Time, Processed_Record_Count, Total_Record_Count, File_Content) VALUES (?,?,?,?)');
        $empty = "";
        $stmt->bind_param('siib', $timestamp, $processed, $total_records, $empty);
        $stmt->send_long_data(3, json_encode($Marker_Array));

        if ($stmt->execute()) {
            $job_id = mysqli_insert_id($db);

            /* 
            Background process (multithreading)
            Use exec() command as the environment is Linux (AWS EC2 - Amazon Linux AMI)
            Any output is directed to /dev/null
            & operator puts command in the background
            */
            exec('php DeleteMarkersDaemon.php ' . $job_id . ' > /dev/null 2>&1 & echo $');

            http_response_code(202);
            echo $job_id;
        } else {
            http_response_code(500);
            echo $stmt->error;
        }
    } else {
        http_response_code(400);
        echo "No collection of Marker IDs could be found";
    }
}
