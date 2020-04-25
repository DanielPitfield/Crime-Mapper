<?php
require 'dbConfig.php';

file_put_contents("counts.txt", "0");

// Check there are no errors with file upload
if($_FILES['fileToUpload']['error'] == 0){
    $name = $_FILES['fileToUpload']['name'];
    $ext = strtolower(end(explode('.', $_FILES['fileToUpload']['name'])));
    $type = $_FILES['fileToUpload']['type'];
    $tmpName = $_FILES['fileToUpload']['tmp_name'];
    
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
    if($ext === 'csv' && (in_array($type, $csvMimes) === true)){
        // Convert csv to array
        $csvAsArray = array_map('str_getcsv', file($tmpName));
        // Get the first line
        $firstline = $csvAsArray[0];
        // Get the length of this first line
        $header_count = sizeof($firstline);
        
        // State acceptable column headers
        $Date_headers = array("Date", "date", "Month", "month");
        $Time_headers = array("Time", "time", "Timestamp", "timestamp");
        $Latitude_headers = array("Latitude", "latitude", "Lat", "lat");
        $Longitude_headers = array("Longitude", "longitude", "Long", "long", "Lng", "lng");
        $CrimeType_headers = array("Crime type", "Crime Type", "crime type", "CrimeType", "crimetype", "Type", "type");
        $Description_headers = array("Context", "context", "Description", "description", "Notes", "notes");
        
        $Date_index = -1;
        $Time_index = -1;
        $Latitude_index = -1;
        $Longitude_index = -1;
        $CrimeType_index = -1;
        $Description_index = -1;
        
        for ($i = 0; $i < $header_count; $i++)
        {
           $actual_header = $firstline[$i]; // Get each header
           
           // Find which of the arrays it belongs to and update the index
           if (in_array($actual_header, $Date_headers)) {
               $Date_index = $i;
           }
           if (in_array($actual_header, $Time_headers)) {
               $Time_index = $i;
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
        
        $num_rows = count($csvAsArray);
        $check_interval = $num_rows / 20;
        $check_interval = ceil($check_interval);
        
        date_default_timezone_set("Europe/London");
            
        for ($j = 1; $j < $num_rows; $j++)
        {
            // Latitude
            $validLatitude = 0;
                
            if ($Latitude_index != 1) {
                $latitudeRead = 0;
                $latitudeToSend = 0;
                
                if (isset($csvAsArray[$j][$Latitude_index])) {
                    $latitudeRead = $csvAsArray[$j][$Latitude_index];
                }
                    
                if (is_numeric($latitudeRead)) {
                    if ($latitudeRead >= -90 && $latitudeRead <= 90) {
                        $latitudeToSend = $latitudeRead;
                        $validLatitude = 1;
                    }
                }
            }
                
            // Longitude
            $validLongitude = 0;
            
            if ($validLatitude == 1) {
                if ($Longitude_index != 1) {
                    $longitudeRead = 0;
                    $longitudeToSend = 0;
                        
                    if (isset($csvAsArray[$j][$Longitude_index])) {
                        $longitudeRead = $csvAsArray[$j][$Longitude_index];
                    }
                        
                    if (is_numeric($longitudeRead)) {
                        if ($longitudeRead >= -180 && $longitudeRead <= 180) {
                            $longitudeToSend = $longitudeRead;
                            $validLongitude = 1;
                        }
                    }
                }
            }
            
            if ($validLatitude == 1 && $validLongitude == 1) {
                // Date
                $dateToSend = date("Y-m-d");
                
                if ($Date_index != -1) {
                    $dateRead = "";
                    
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
                }
                
                // Time
                $timeToSend = date("h:i");
                
                if ($Time_index != -1) {
                    $timeRead = "";
                    
                    if (isset($csvAsArray[$j][$Time_index])) {
                        $timeRead = $csvAsArray[$j][$Time_index];
                    }
                    
                    if ($timeRead != null) {
                        if (strtotime($timeRead)) {
                            $timeToSend = $timeRead;
                        }
                    }
    
                }
                    
                // Crime Type
                $crimeToSend = "Unknown";
                
                if ($CrimeType_index != 1)  {
                    $crimeRead = "";
                    if (isset($csvAsArray[$j][$CrimeType_index])) {
                        $crimeRead = $csvAsArray[$j][$CrimeType_index];
                        if (ctype_space($crimeRead) == false && $crimeRead != '') {
                            if (is_string($crimeRead)) {
                                $crimeToSend = $crimeRead;
                            }
                        }
                    }
                }
                    
                // Description
                $descriptionToSend = "-";
                
                if ($CrimeType_index != 1) {
                    $descriptionRead = "";
                    if (isset($csvAsArray[$j][$Description_index])) {
                        $descriptionRead = $csvAsArray[$j][$Description_index];
                        if (ctype_space($descriptionRead) == false && $descriptionRead != '') {
                            if (is_string($descriptionRead)) {
                                if (strpos($descriptionRead, '>') === false || strpos($descriptionRead, '<') === false) {
                                    // Not both opening and closing tags
                                    if (strlen($descriptionRead) <=500) { // If read description is <=500 characters
                                    	$descriptionToSend = $descriptionRead; // Assign to send variable
                                    }
                                    else {
                                        $descriptionToSend = substr($descriptionRead,0,500); // Otherwise, only take and assign first 500 characters
                                    }
                                }
                                    
                            }
                        }
                    }
                }
                
                // Upload
                $stmt = $db->prepare('INSERT INTO markers (Crime_Type, Crime_Date, Crime_Time, Description, Latitude, Longitude) VALUES (?,?,?,?,?,?)');
                $stmt->bind_param('ssssdd', $crimeToSend, $dateToSend, $timeToSend, $descriptionToSend, $latitudeToSend, $longitudeToSend);
                if($stmt->execute()) {
                    $lines_success++;
                }
                else {
                    $lines_fail++;
                    continue;
                }
  
            }
            $lines_total++;
            if ($lines_total % $check_interval == 0) {
                file_put_contents("counts.txt",($lines_total/($num_rows-1))*100);
            }
            
        }
    }
}
else {
  file_put_contents("counts.txt", "-1000");  
}
?>