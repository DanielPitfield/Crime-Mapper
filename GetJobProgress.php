<?php
require 'dbConfig.php';

if (isset($_GET['Job_ID'])) {
    $job_id = $_GET['Job_ID'];

    // Statement preparation and execution
    $stmt = $db->prepare('SELECT * FROM operation_jobs WHERE ID = ? LIMIT 1'); // Rename table
    $stmt->bind_param('i', $job_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            http_response_code(404);
            echo "JOB_ID '" . $job_id . "' was not found";
        }

        $row = $result->fetch_assoc();

        // Percentage completion of job (either import or delete job)
        $percentageProgress = ($row['Processed_Record_Count'] / $row['Total_Record_Count']) * 100;

        if ($percentageProgress == 100) {
            http_response_code(200);
            echo $percentageProgress;
        } else {
            http_response_code(206);
            echo $percentageProgress;
        }
    } else {
        http_response_code(500);
        echo $stmt->error;
    }
} else {
    http_response_code(400);
    echo "No Job_ID was able to be used";
}
?>