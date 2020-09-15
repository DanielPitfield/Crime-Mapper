<?php
require 'dbConfig.php';

if (isset($_POST['Job_ID']))
{
    $job_id = $_POST['Job_ID'];
}

$query  = "SELECT * FROM Users WHERE `user_name` = '$from' LIMIT 1";

// Statement preparation and execution
$stmt = $db->prepare('SELECT * FROM import_jobs WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $job_id);

if($stmt->execute()) {
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    echo $row['Processed_Record_Count'] / $row['Total_Record_Count'];
}
else {
    echo $stmt->error;
}
?>