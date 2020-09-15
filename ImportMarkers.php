<?php
require 'dbConfig.php';

date_default_timezone_set("Europe/London");

// Check for any errors with file upload
if ($_FILES['ImportFile']['error'] == 0) {
    $name = $_FILES['ImportFile']['name'];
    $ext = strtolower(end(explode('.', $_FILES['ImportFile']['name'])));
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

    echo $type;

    // Check file is a csv file (extension and type)
    if (($ext == "csv") && (in_array($type, $csvMimes))) {
        $csvAsArray = array_map('str_getcsv', file($tmpName)); // Convert csv to array
        $firstline = $csvAsArray[0]; // Get the first line
        $header_count = sizeof($firstline); // Number of columns
        $total_records = count($csvAsArray); // Number of rows (lines)        

        // State acceptable column headers
        $Latitude_headers = array("Latitude", "latitude", "Lat", "lat");
        $Longitude_headers = array("Longitude", "longitude", "Long", "long", "Lng", "lng");
        $Date_headers = array("Date", "date", "Month", "month");
        $Time_headers = array("Time", "time", "Timestamp", "timestamp");
        $CrimeType_headers = array("Crime type", "Crime Type", "crime type", "CrimeType", "crimetype", "Type", "type");
        $Description_headers = array("Context", "context", "Description", "description", "Notes", "notes");

        $hasLatitudeColumn = false;
        $hasLongitudeColumn = false;

        for ($i = 0; $i < $header_count; $i++) {
            $actual_header = $firstline[$i]; // Get each header

            // Find which of the arrays it belongs to and update the index
            if (in_array($actual_header, $Latitude_headers)) {
                $Latitude_index = $i;
                $hasLatitudeColumn = true;
            }
            if (in_array($actual_header, $Longitude_headers)) {
                $Longitude_index = $i;
                $hasLongitudeColumn = true;
            }
            if (in_array($actual_header, $Date_headers)) {
                $Date_index = $i;
            }
            if (in_array($actual_header, $Time_headers)) {
                $Time_index = $i;
            }
            if (in_array($actual_header, $CrimeType_headers)) {
                $CrimeType_index = $i;
            }
            if (in_array($actual_header, $Description_headers)) {
                $Description_index = $i;
            }
        }

        if ($hasLatitudeColumn && $hasLongitudeColumn) {
            $timestamp = date('Y-m-d H:i:s'); // Current timestamp
            $processed = 0;

            // Set up new record (to track progress)
            $stmt = $db->prepare('INSERT INTO import_jobs (Start_Time, Processed_Record_Count, Total_Record_Count) VALUES (?,?,?)');
            $stmt->bind_param('sii', $timestamp, $processed, $total_records);
            if ($stmt->execute()) {
                $job_id = mysqli_insert_id($db);
                echo $job_id;
            }

            // Determine how often the record is updated
            $check_interval = $num_rows / 20;
            $check_interval = ceil($check_interval);

            // Process each line
            for ($j = 1; $j < $num_rows; $j++) {

                // Latitude
                $isValid_Latitude = false;
                if (isset($csvAsArray[$j][$Latitude_index])) { // Value is found in column for current line
                    $Latitude = $csvAsArray[$j][$Latitude_index];
                    $isValid_Latitude = is_numeric($Latitude) && ($Latitude >= -90) && ($Latitude <= 90); // Value is numeric and between -90 and 90
                }

                // Longitude
                $isValid_Longitude = false;
                if (isset($csvAsArray[$j][$Longitude_index])) {
                    $Longitude = $csvAsArray[$j][$Longitude_index];
                    $isValid_Longitude = is_numeric($Longitude) && ($Longitude >= -180) && ($Longitude <= 180);
                }

                if ($isValid_Latitude && $isValid_Longitude) { // Only proceed if location can be derived from the line
                    // Date
                    $Date_Send = date("Y-m-d");
                    $isValid_Date = false;

                    if ($Date_index) {
                        if (isset($csvAsArray[$j][$Date_index])) {
                            $Date = $csvAsArray[$j][$Date_index];
                        }
                        if (strlen($Date) == 7) {
                            $Date = $Date . "-01";
                        }

                        $isValid_Date = ($Date != null) && (strtotime($Date)); // Not null and can be converted to a UNIX timestamp

                        if ($isValid_Date) {
                            $Date_Send = $Date;
                        }
                    }

                    // Time
                    $Time_Send = date("H:i");
                    $isValid_Time = false;

                    if ($Time_index) {
                        if (isset($csvAsArray[$j][$Time_index])) {
                            $Time = $csvAsArray[$j][$Time_index];
                        }

                        $isValid_Time = ($Time != null) && (strtotime($Time));

                        if ($isValid_Time) {
                            $Time_Send = $Time;
                        }
                    }

                    // Crime Type
                    $crimeType_Send = "Unknown";
                    $isValid_crimeType = false;

                    if ($CrimeType_index) {
                        if (isset($csvAsArray[$j][$CrimeType_index])) {
                            $crimeType = $csvAsArray[$j][$CrimeType_index];
                        }

                        $isValid_crimeType = ($crimeType != '') && (ctype_space($crimeType) == false) && (is_string($crimeType));

                        if ($isValid_crimeType) {
                            $crimeType_Send = $crimeType;
                        }
                    }

                    // Description
                    $description_Send = "-";
                    $isValid_description = false;

                    if ($description_index) {
                        if (isset($csvAsArray[$j][$Description_index])) {
                            $description = $csvAsArray[$j][$Description_index];
                        }

                        $isValid_description = ($description != '') && (ctype_space($description) == false) && (is_string($description))
                            && ((strpos($description, '>') === false || strpos($description, '<') === false));

                        if ($isValid_description) {
                            if (strlen($description) <= 500) {
                                $description_Send = $description;
                            } else {
                                $description_Send = substr($description, 0, 500); // Only use first 500 characters
                            }
                        }
                    }

                    // Upload
                    $stmt = $db->prepare('INSERT INTO markers (Crime_Type, Crime_Date, Crime_Time, Description, Latitude, Longitude) VALUES (?,?,?,?,?,?)');
                    $stmt->bind_param('ssssdd', $crimeType_Send, $Date_Send, $Time_Send, $description_Send, $Latitude, $Longitude);
                    if ($stmt->execute()) {
                        //
                    }
                    $processed++;

                    if ($processed % $check_interval == 0) { // Every interval of rows
                        $stmt = $db->prepare('UPDATE import_jobs SET Processed_Record_Count = ? WHERE ID = ?');
                        $stmt->bind_param('ii', $processed, $job_id); // Update the processed number of rows
                        if ($stmt->execute()) {
                            //
                        }
                    }
                }
            }
        }
        else {
            echo "The file does not have both a Latitude and Longitude column";
        }
    }
    else {
        echo $type;
        echo "The file is not a .csv file (the type is shown above)";
    }
}
else {
    echo "There was an error with processing the imported file";
}
?>`