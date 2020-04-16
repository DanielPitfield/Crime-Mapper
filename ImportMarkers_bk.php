<?php
require 'dbConfig.php';

file_put_contents("counts.txt", "");

// Check there are no errors with file upload
if($_FILES['fileToUpload']['error'] == 0){
    $name = $_FILES['fileToUpload']['name'];
    $ext = strtolower(end(explode('.', $_FILES['fileToUpload']['name'])));
    $tmpName = $_FILES['fileToUpload']['tmp_name'];

    // Check file is a csv file
    if($ext === 'csv'){
        // Convert csv to array
        $csvAsArray = array_map('str_getcsv', file($tmpName));
        // Get the first line
        $firstline = $csvAsArray[0];
        // Get the length of this first line
        $header_count = sizeof($firstline);
        
        // State acceptable column headers
        $Date_headers = array("Date", "date", "Month", "month");
        $Latitude_headers = array("Latitude", "latitude", "Lat", "lat");
        $Longitude_headers = array("Longitude", "longitude", "Long", "long", "Lng", "lng");
        $CrimeType_headers = array("Crime type", "Crime Type", "crime type", "CrimeType", "crimetype", "Type", "type");
        $Description_headers = array("Context", "context", "Description", "description", "Notes", "notes");
        
        $Date_index;
        $Latitude_index;
        $Longitude_index;
        $CrimeType_index;
        $Description_index;
        
        for ($i = 0; $i < $header_count; $i++)
        {
           $actual_header = $firstline[$i]; // Get each header
           
           // Find which of the arrays it belongs to and update the index
           if (in_array($actual_header, $Date_headers)) {
               $Date_index = $i;
           }
           if (in_array($actual_header, $Latitude_headers)) {
               $Latitude_index = $i;
           }
           if (in_array($actual_header, $Longitude_headers)) {
               $Longitude_index = $i;
           }
           if (in_array($actual_header, $CrimeType_headers)) {
               $CrimeType_index = $i;
           }
           if (in_array($actual_header, $Description_headers)) {
               $Description_index = $i;
           }
           
        }
        
        $timeToSend = '00:00:00'; // Hardcoded time value
        $num_rows = count($csvAsArray);
        $check_interval = $num_rows / 20;
        $check_interval = round($check_interval, 0);
            
        for ($j = 1; $j < $num_rows; $j++)
        {
            // Date
            $dateRead = "";
            $dateToSend = "";
                
            if (isset($csvAsArray[$j][$Date_index])) {
                $dateRead = $csvAsArray[$j][$Date_index];
            }
                
            if ($dateRead != null) {
                if (strlen($dateRead) == 7) {
                    $dateRead = $dateRead . "-01";
                }
                if (strtotime($dateRead)) {
                    $dateToSend = $dateRead;
                }
            }
                
            // Latitude
            $validLatitude = 0;
                
            $latitudeRead = -1000;
            $latitudeToSend = -1000;
                
            if (isset($csvAsArray[$j][$Latitude_index])) {
                $latitudeRead = $csvAsArray[$j][$Latitude_index];
            }
                
            if (is_numeric($latitudeRead)) {
                if ($latitudeRead >= -90 && $latitudeRead <= 90) {
                    $latitudeToSend = $latitudeRead;
                    $validLatitude = 1;
                }
            }
                
            // Longitude
            $validLongitude = 0;
                
            $longitudeRead = -1000;
            $longitudeToSend = -1000;
                
            if (isset($csvAsArray[$j][$Longitude_index])) {
                $longitudeRead = $csvAsArray[$j][$Longitude_index];
            }
                
            if (is_numeric($longitudeRead)) {
                if ($longitudeRead >= -180 && $longitudeRead <= 180) {
                    $longitudeToSend = $longitudeRead;
                    $validLongitude = 1;
                }
            }
                
            // Crime Type
            $crimeRead = "";
            $crimeToSend = "Unknown";
                
            if (isset($csvAsArray[$j][$CrimeType_index])) {
                $crimeRead = $csvAsArray[$j][$CrimeType_index];
                if (ctype_space($crimeRead) == false && $crimeRead != '') {
                    if (is_string($crimeRead)) {
                        $crimeToSend = $crimeRead;
                    }
                }
            }
                
            // Description
            $descriptionRead = "";
            $descriptionToSend = "-";
                
            if (isset($csvAsArray[$j][$Description_index])) {
                $descriptionRead = $csvAsArray[$j][$Description_index];
                if (ctype_space($descriptionRead) == false && $descriptionRead != '') {
                    if (is_string($descriptionRead)) {
                        $descriptionToSend = $descriptionRead;
                    }
                }
            }
                
            // Upload
            if ($validLatitude == 1 && $validLongitude == 1) {
                $stmt = $db->prepare('INSERT INTO markers (Crime_Type, Crime_Date, Crime_Time, Description, Latitude, Longitude) VALUES (?,?,?,?,?,?)');
                $stmt->bind_param('ssssdd', $crimeToSend, $dateToSend, $timeToSend, $descriptionToSend, $latitudeToSend, $longitudeToSend);
                if($stmt->execute()) {
                    $lines_success++;
                }
                else {
                    $lines_fail++;
                    continue;
                }
                $lines_total++;
                if ($lines_total % $check_interval == 0) {
                    file_put_contents("counts.txt",($lines_total/($num_rows-1))*100);
                }

            }
        }
    }
}

?>