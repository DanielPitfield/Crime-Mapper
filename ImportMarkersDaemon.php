<?php
require 'dbConfig.php';

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
    $csvAsArray = json_decode($file);
} 
else {
    echo $stmt->error;
}

$firstline = $csvAsArray[0]; // Get the first line
$header_count = count($firstline); // Number of columns

// State acceptable column headers
$Latitude_headers = array("Latitude", "latitude", "Lat", "lat");
$Longitude_headers = array("Longitude", "longitude", "Long", "long", "Lng", "lng");
$Date_headers = array("Date", "date", "Month", "month");
$Time_headers = array("Time", "time", "Timestamp", "timestamp");
$CrimeType_headers = array("Crime type", "Crime Type", "crime type", "CrimeType", "crimetype", "Type", "type");
$Description_headers = array("Context", "context", "Description", "description", "Notes", "notes");

$Latitude_index = -1;
$Longitude_index = -1;
$Date_index = -1;
$Time_index = -1;
$CrimeType_index = -1;
$Description_index = -1;

for ($i = 0; $i < $header_count; $i++) {
    $actual_header = $firstline[$i]; // Get each header
    // Find which of the arrays it belongs to and update the index
    if (in_array($actual_header, $Latitude_headers)) {
        $Latitude_index = $i;
    }
    if (in_array($actual_header, $Longitude_headers)) {
        $Longitude_index = $i;
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

$processed = 1; // Start at 1 (as the header line has been processed)

$total_records = count($csvAsArray);

// Determine how often the progress record is updated
$check_interval = $total_records / 20;
$check_interval = ceil($check_interval);

// Process each line
for ($j = 1; $j < $total_records; $j++) {
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
        if ($Date_index != -1) {
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
        if ($Time_index != -1) {
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
        if ($CrimeType_index != -1) {
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
        if ($Description_index != -1) {
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
        if (!$stmt->execute()) echo $stmt->error;
    }
    $processed++; // Increment this regardless of whether a location could be resolved

    // Check if progress needs to be reported to database after every row that is processed
    if ($processed % $check_interval == 0) {
        $stmt = $db->prepare('UPDATE operation_jobs SET Processed_Record_Count = ? WHERE ID = ?');
        $stmt->bind_param('ii', $processed, $job_id); // Update the processed number of rows
        if (!$stmt->execute()) echo $stmt->error;
    }

    // Check for last interval (completion)
    if ($processed == $total_records) {
        $stmt = $db->prepare('UPDATE operation_jobs SET Processed_Record_Count = ? WHERE ID = ?');
        $stmt->bind_param('ii', $processed, $job_id);
        if (!$stmt->execute()) echo $stmt->error;
    }
}
?>