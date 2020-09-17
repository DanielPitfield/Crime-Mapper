<?php
require 'dbConfig.php';

if (isset($_POST['Job_ID']))
{
    $job_id = $_POST['Job_ID'];

    // Statement preparation and execution
    $stmt = $db->prepare('SELECT * FROM import_jobs WHERE ID = ? LIMIT 1'); // Rename table
    $stmt->bind_param('i', $job_id);

    if($stmt->execute()) {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        // Echo the percentage completion of job (either import or delete job)
        echo ($row['Processed_Record_Count'] / $row['Total_Record_Count']) * 100;
    }
    else {
        echo $stmt->error;
    }
}
else {
    echo "No Job_ID was able to be used";
}