<?php
require 'dbConfig.php';

date_default_timezone_set("Europe/London");

// Check for any errors with file upload
if ($_FILES['ImportFile']['error'] == 0) {
    $name = $_FILES['ImportFile']['name'];

    $tmp = explode('.', $name);
    $ext = end($tmp);

    $type = $_FILES['ImportFile']['type'];
    $tmpName = $_FILES['ImportFile']['tmp_name'];

    // Acceptable mime types
    $csvMimes = array(
        'text/csv',
        'text/plain',
        'application/csv',
        'text/comma-separated-values',
        'application/excel',
        'application/vnd.ms-excel',
        'application/vnd.msexcel',
        'text/anytext',
        'application/octet-stream',
        'application/txt',
    );

    // Check file is a csv file (extension and type)
    if (($ext == "csv") && (in_array($type, $csvMimes))) {
        $csvAsArray = array_map('str_getcsv', file($tmpName)); // Convert csv to array
        $firstline = $csvAsArray[0]; // Get the first line
        $header_count = count($firstline); // Number of columns

        // State acceptable column headers for required columns
        $Latitude_headers = array("Latitude", "latitude", "Lat", "lat");
        $Longitude_headers = array("Longitude", "longitude", "Long", "long", "Lng", "lng");

        $hasLatitudeColumn = false;
        $hasLongitudeColumn = false;

        for ($i = 0; $i < $header_count; $i++) {
            $actual_header = $firstline[$i]; // Get each header

            if (in_array($actual_header, $Latitude_headers)) {
                $hasLatitudeColumn = true;
            }
            if (in_array($actual_header, $Longitude_headers)) {
                $hasLongitudeColumn = true;
            }
        }

        if ($hasLatitudeColumn && $hasLongitudeColumn) {
            $timestamp = date('Y-m-d H:i:s'); // Current timestamp
            $processed = 1; // Start at 1 (as the header line has been processed)

            $total_records = count($csvAsArray);

            // Set up new record (to track progress)
            $stmt = $db->prepare('INSERT INTO operation_jobs (Start_Time, Processed_Record_Count, Total_Record_Count, File_Content) VALUES (?,?,?,?)');
            $empty = "";
            $stmt->bind_param('siib', $timestamp, $processed, $total_records, $empty);
            $stmt->send_long_data(3, json_encode($csvAsArray));

            if ($stmt->execute()) {
                $job_id = mysqli_insert_id($db);
                // TODO Specifying path (deployment not local)
                // Background process (multithreading)
                shell_exec('C:\laragon\bin\php\php-7.2.19-Win32-VC15-x64\php.exe -q ImportMarkersDaemon.php ' . $job_id . ' | at now');

                http_response_code(202);
                echo $job_id;
            }
            else {
                http_response_code(500);
                echo $stmt->error;
            }
        }
    }
    else {
        http_response_code(400);
        echo "The file is not a .csv file";
    }
}
else {
    http_response_code(400);
    echo "There was an error with processing the imported file";
}

function execInBackground($cmd) {
    if (substr(php_uname(), 0, 7) == "Windows"){
        pclose(popen("start /B ". $cmd, "r")); 
    }
    else {
        exec($cmd . " > /dev/null &");  
    }
}
?>