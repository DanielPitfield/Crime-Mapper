<?php
require 'dbConfig.php';

$tmpName = $_FILES['fileToUpload']['tmp_name'];

// Check there are no errors with file upload
if($_FILES['fileToUpload']['error'] == 0){
    $name = $_FILES['fileToUpload']['name'];
    $ext = strtolower(end(explode('.', $_FILES['fileToUpload']['name'])));
    $type = $_FILES['fileToUpload']['type'];
    $tmpName = $_FILES['fileToUpload']['tmp_name'];

    // Check file is a csv file
    if($ext === 'csv'){
        echo "File Name: " . $name . "<br>";
        echo "File Extension: " . $ext . "<br>";
        
        // Read column headers
        $file = fopen($tmpName, 'r');
        $firstline = fgetcsv($file);
        echo "<pre>"; print_r($firstline); echo "</pre>";
        fclose($file);
        
        // Check column headers
        $Date_headers = array("Date", "date", "Month", "month");
        $Latitude_headers = array("Latitude", "latitude", "Lat", "lat");
        $Longitude_headers = array("Longitude", "longitude", "Long", "long", "Lng", "lng");
        $CrimeType_headers = array("Crime type", "Crime Type", "crime type", "CrimeType", "crimetype", "Type", "type");
        $Description_headers = array("Context", "context", "Description", "description", "Notes", "notes");
        
        $default_value = -1;
        $Date_index = $default_value;
        $Latitude_index = $default_value;
        $Longitude_index = $default_value;
        $CrimeType_index = $default_value;
        $Description_index = $default_value;
        
        for ($i = 0; $i < count($firstline); $i++)
        {
           $actual_header = $firstline[$i];
           
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
        
        $validFile = 1;
        if ($Date_index == -1 || $Latitude_index == -1 || $Longitude_index == -1 || $CrimeType_index == -1 || $Description_index == -1) {
            $validFile = 0;
        }
        
        if ($validFile == 1) {
            $csvAsArray = array_map('str_getcsv', file($tmpName));
            echo '<pre>'; print_r($csvAsArray); echo '</pre>';
            
            for ($j = 1; $j < count($csvAsArray); $j++)
            {
                $validLongitude = 0;
                
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
                echo "Latitude: " . $latitudeToSend . "<br>";
                
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
                echo "Longitude: " . $longitudeToSend . "<br>";
                
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
                echo "Crime Type: " . $crimeToSend . "<br>";
                
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
                echo "Description: " . $descriptionToSend . "<br>";
                
                // Upload
                if ($validLatitude == 1 && $validLongitude == 1) {
                    $timeToSend = '00:00:00';
                    
                    echo "A record saved";
                    
                    $stmt = $db->prepare('INSERT INTO markers (Crime_Type, Crime_Date, Crime_Time, Description, Latitude, Longitude) VALUES (?,?,?,?,?,?)');
                    $stmt->bind_param('ssssdd', $crimeToSend, $dateToSend, $timeToSend, $descriptionToSend, $latitudeToSend, $longitudeToSend);
                    $stmt->execute();
                }
    

            } // End of for loop
            
            
        }

    }
}
?>