<?php 
require 'dbConfig.php'; // Include the database configuration file
?>

<!DOCTYPE html>
<html lang="en">

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Crime Mapper</title>
<link rel="shortcut icon" href="#"> <!-- Website tab icon, change link to ico file -->

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">  <!-- For navigation bar icons -->
<link rel="stylesheet" href="layout.css">  <!-- Everything else -->

<body oncontextmenu="return false;">  <!-- Disable the default right click context menu for the body of the page -->

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
  
  <ul class="navbar-nav">
    
    <!-- Filter Crime -->
    <li class="col-5 px-1">
        	<button class="btn btn-outline-primary btn-block" role="button" data-toggle="modal" data-target="#modal_filter" style="margin-left:5px;">Filter Crime</button>
    </li>
    
    <!-- Import Crime -->
    <li class="col-5 px-1">
        	<button class="btn btn-outline-primary btn-block" role="button" data-toggle="modal" data-target="#modal_import" style="margin-left:2px;">Import Crime</button>
    </li>
    
    <!-- Location Search Bar -->
    <li class="col-10 px-1">
        <input id="pac-input" class="controls" type="text" placeholder="Location Search" style="margin-left:2px;">
    </li>

    <!-- Analyse Crime -->
    <li class="col-5 px-1">
        <button class="btn btn-outline-primary btn-block" id="btn_marker_cluster" role="button" style="margin-left:2px;margin-right:0px;">Analyse Crime</button>
    </li>
    
    <!-- Predict Crime (disabled) -->
    <li class="col-5 px-1">
        	<button class="btn btn-outline-primary btn-block disabled" role="button" style="margin-right:0px;">Predict Crime</button>
    </li>
	
  </ul>
  
</nav>

<!-- Map -->
<div id="map"></div>

<!-- Context Menu -->
<div class="custom_contextmenu" id="menu">
	<div class="custom_contextmenu_btn add" id="btn_add">Add crime</div>
</div>

<!-- Filter modal -->
<div class="modal fade bd-example-modal-xl" data-backdrop="false" tabindex="-1" role="dialog" id="modal_filter">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
		<h5 class="modal-title">Filter</h5>
		<button type="button" class="close" data-dismiss="modal">
			<span>&times;</span>
		</button>
	   </div>
	   <div class="modal-body">
	   <div class="form-group">
		Date (from):
		<input type="date" id="Filter_minDate" min="1970-01-01" value="" max="<?php echo date("Y-m-d"); ?>">
		(to):
		<input type="date" id="Filter_maxDate" min="1970-01-01" value="" max="<?php echo date("Y-m-d"); ?>">
		</div>
		
		<div class="form-group">
		Time (from):
		<input type="time" id="Filter_minTime" value="">
		(to):
		<input type="time" id="Filter_maxTime" value="">
		</div>
		
		<div class="form-group">
        <select class="select form-control" id="Filter_Crime_Type">
        <option value="All" selected disabled hidden>Crime Type - Main Category</option>
        </select>
        <select class="select form-control" id="Filter_Crime_Type_sub" name="Crime_Type">
        <option value="All" selected disabled hidden>Crime Type - Subcategory</option>
        </select>
        </div>
		
		<button id="btn_filter_confirm" class="btn btn-success" style="width:100%;">Confirm</button>
	   </div>
    </div>
  </div>
</div>

<!-- Add crime modal -->
<div class="modal fade bd-example-modal-xl" data-backdrop="false" tabindex="-1" role="dialog" id="modal_add">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
		<h5 class="modal-title">Add Crime</h5>
		<button type="button" class="close" data-dismiss="modal">
			<span>&times;</span>
		</button>
	   </div>
	   <div class="modal-body">
		<form name="add_submit_form" id="add_submit_form" action="SaveMarkers.php" method="post">
		    
		<div class="form-group">
		<label class="control-label " for="Add_Date">Date</label>
		<input id="Add_Date" type="date" name="Date" min="1970-01-01" value="<?php echo date("Y-m-d"); ?>" max="<?php echo date("Y-m-d"); ?>" required>

		<label class="control-label " for="Add_Time">Time</label>
		<input id="Add_Time" type="time" name="Time" value="00:00" required>
		</div>
		
		<div class="form-group">
        <select class="select form-control" id="Add_Crime_Type">
        <option value="" selected disabled hidden>Crime Type - Main Category</option>
        </select>
        <select class="select form-control" id="Add_Crime_Type_sub" name="Crime_Type">
        <option value="" selected disabled hidden>Crime Type - Subcategory</option>
        </select>
        </div>
		
		<div class="form-group">
        <textarea class="form-control" id="Add_Description" name="Description" rows="3" placeholder="Description"></textarea>
        </div>
        
		<div id="map2"></div>
		
		<button type="submit" id="btn_add_confirm" class="btn btn-success" style="width:100%;margin-top:10px;">Confirm</button>
		</form>
	   </div>
    </div>
  </div>
</div>

<!-- Edit crime modal -->
<div class="modal fade bd-example-modal-xl" data-backdrop="false" tabindex="-1" role="dialog" id="modal_edit">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
		<h5 class="modal-title">Edit Crime</h5>
		<button type="button" class="close" data-dismiss="modal">
			<span>&times;</span>
		</button>
	   </div>
	   <div class="modal-body">
		<form name="edit_submit_form" id="edit_submit_form" action="EditMarkers.php" method="post">
		    
		<div class="form-group">
		<label class="control-label " for="Edit_Date">Date</label>
		<input id="Edit_Date" type="date" name="Date" min="1970-01-01" value="<?php echo date("Y-m-d"); ?>" max="<?php echo date("Y-m-d"); ?>" required>
		
		<label class="control-label " for="Edit_Time">Time</label>
		<input id="Edit_Time" type="time" name="Time" value="00:00" required>
		</div>
		
		<div class="form-group">
        <select class="select form-control" id="Edit_Crime_Type">
        <option value="" selected disabled hidden>Crime Type - Main Category</option>
        </select>
        <select class="select form-control" id="Edit_Crime_Type_sub" name="Crime_Type">
        <option value="" selected disabled hidden>Crime Type - Subcategory</option>
        </select>
        </div>
		
		<div class="form-group">
        <textarea class="form-control" id="Edit_Description" name="Description" rows="3" placeholder="Description"></textarea>
        </div>
		
		<div id="map3"></div>
		<button type="submit" id="btn_edit_confirm" class="btn btn-success" style="width:100%;margin-top:10px;">Update</button>
		</form>
	   </div>
    </div>
  </div>
</div>

<!-- Import modal -->
<div class="modal fade bd-example-modal-xl" data-backdrop="false" tabindex="-1" role="dialog" id="modal_import">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
		<h5 class="modal-title">Import</h5>
		<button type="button" class="close" data-dismiss="modal">
			<span>&times;</span>
		</button>
	   </div>
	   <div class="modal-body">
        <div class="custom-file mb-3">
        <input type="file" class="custom-file-input" id="Import_input" accept=".csv" multiple>
        <label class="custom-file-label" id="import_lbl" for="customFile" style="display: inline-block;overflow: hidden; text-overflow:clip">Choose file</label>
        <a href="template.csv" class="btn btn-secondary" role="button" style="width:100%;margin-top:8px;">Download Template</a>
        <button type="submit" id="btn_import_confirm" class="btn btn-success" style="width:100%;margin-top:8px;">Import</button>
            <div class="progress" style="margin-top:8px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%;">Progress Bar
                </div>
            </div>
        </div>
	   </div>
    </div>
  </div>
</div>

<script src="moment.js"></script> <!-- Moment.js library -->

<script>
	/*
	|-----------------------------------------------------------------------------------------------------------
	| Map functions, variables and elements
	|-----------------------------------------------------------------------------------------------------------
	*/
	
	var MarkerArray = [];
	var FilteredMarkerArray = [];
	
	function placeMarker(ID,Crime_Type,Crime_Date,Crime_Time,Description,CenterLocation,map) {
		var marker = new google.maps.Marker({
		ID: ID,
		Crime_Type: Crime_Type,
		Crime_Date: Crime_Date,
		Crime_Time: Crime_Time,
		Description: Description,
		position: CenterLocation,
		title: '',
		map: map
		});
		MarkerArray.push(marker);
		
		marker.title = marker.Crime_Type; // Shown on hover
		
		var MarkerDate = moment(marker.Crime_Date).format("DD-MM-YYYY"); // Convert to UK format

        var MarkerTime = marker.Crime_Time;
        if (MarkerTime.length == 8) { // If time is retirved form database which includes seconds
            MarkerTime = MarkerTime.substring(0, MarkerTime.length - 3); // Remove the seconds for display purposes
        }
		
		marker.info = new google.maps.InfoWindow({
			content: '<div id="iw-container">' + '<div class="iw-content">' + 
					 '<b>ID: </b>' + marker.ID + '<br> <b>Crime Type: </b>' + marker.Crime_Type +'<br> <b>Date: </b>' + MarkerDate +
					 '<br><b>Time: </b>' + MarkerTime + //'<br><b>Description: </b>' + marker.Description +
					 '<br></br> <button id="btn_edit" type="button" class="btn btn-secondary" onclick=EditMarker('+marker.ID+')>Edit</button>' +
					 '<button id="btn_delete" type="button" class="btn btn-danger" onclick=DeleteMarker('+marker.ID+')>Delete</button>' + '</div>' + '</div>' // Send marker not marker.ID?
		});

		google.maps.event.addListener(marker, 'click', function() {
			marker.info.open(map,marker);
		});
	}
	
	function UpdateMarkerInfo(marker) {
	 
	marker.title = marker.Crime_Type; // Shown on hover
		
	var MarkerDate = moment(marker.Crime_Date).format("DD-MM-YYYY"); // Convert to UK format

    var MarkerTime = marker.Crime_Time;
    if (MarkerTime.length == 8) { // If time is retirved form database which includes seconds
        MarkerTime = MarkerTime.substring(0, MarkerTime.length - 3); // Remove the seconds for display purposes
    }
	
	    marker.info.setContent('<div id="iw-container">' + '<div class="iw-content">' + 
					 '<b>ID: </b>' + marker.ID + '<br> <b>Crime Type: </b>' + marker.Crime_Type +'<br> <b>Date: </b>' + MarkerDate +
					 '<br><b>Time: </b>' + MarkerTime + //'<br><b>Description: </b>' + marker.Description +
					 '<br></br> <button id="btn_edit" type="button" class="btn btn-secondary" onclick=EditMarker('+marker.ID+')>Edit</button>' +
					 '<button id="btn_delete" type="button" class="btn btn-danger" onclick=DeleteMarker('+marker.ID+')>Delete</button>' + '</div>' + '</div>');
	}
	
	
	function ShowAllMarkerInfo() {
	    for(i = 0; i < MarkerArray.length; i++){
	            if (MarkerArray[i].getVisible() == true) {
	                MarkerArray[i].info.open(map, MarkerArray[i]);
	            }
		}
	}
	
	function HideAllMarkerInfo() {
	    for(i = 0; i < MarkerArray.length; i++){
            MarkerArray[i].info.close();
		}
	}
	
	function EditMarker(ID) {
		
		for(i = 0; i < MarkerArray.length; i++){
			if (MarkerArray[i].ID == ID)
				var MarkerToEdit = MarkerArray[i]; // Get actual marker
				var index = i; // Position in MarkerArray
		}
		
		MarkerToEdit.info.close(); // Close marker's info window (as the information it holds may change)
		
		var modal = $('#modal_edit');

	    if (violence_sub_options.includes(MarkerToEdit.Crime_Type) === true) {
	        $('#Edit_Crime_Type').val('Violence against the person').change();
	    }
	    else if (public_sub_options.includes(MarkerToEdit.Crime_Type) === true) {
	        $('#Edit_Crime_Type').val('Public Order').change();
	    }
	    else if (drug_sub_options.includes(MarkerToEdit.Crime_Type) === true) {
	        $('#Edit_Crime_Type').val('Drug offences').change();
	    }
	    else if (vehicle_sub_options.includes(MarkerToEdit.Crime_Type) === true) {
	        $('#Edit_Crime_Type').val('Vehicle offences').change();
	    }
	    else if (sexual_sub_options.includes(MarkerToEdit.Crime_Type) === true) {
	        $('#Edit_Crime_Type').val('Sexual offences').change();
	    }
	    else if (drug_sub_options.includes(MarkerToEdit.Crime_Type) === true) {
	        $('#Edit_Crime_Type').val('Drug offences').change();
	    }
	    else if (arson_sub_options.includes(MarkerToEdit.Crime_Type) === true) {
	        $('#Edit_Crime_Type').val('Arson and criminal damage').change();
	    }
	    else if (weapons_sub_options.includes(MarkerToEdit.Crime_Type) === true) {
	        $('#Edit_Crime_Type').val('Possession of weapons').change();
	    }
	    else if (theft_sub_options.includes(MarkerToEdit.Crime_Type) === true) {
	        $('#Edit_Crime_Type').val('Theft').change();
	    }
	    else if (burglary_sub_options.includes(MarkerToEdit.Crime_Type) === true) {
	        $('#Edit_Crime_Type').val('Burglary').change();
	    }
	    else if (robbery_sub_options.includes(MarkerToEdit.Crime_Type) === true) {
	        $('#Edit_Crime_Type').val('Robbery').change();
	    }
	    else if (robbery_sub_options.includes(MarkerToEdit.Crime_Type) === true) {
	        $('#Edit_Crime_Type').val('Robbery').change();
	    }
	    else if (misc_sub_options.includes(MarkerToEdit.Crime_Type) === true) {
	        $('#Edit_Crime_Type').val('Miscellaneous crimes against society').change();
	    }
	    else if (other_sub_options.includes(MarkerToEdit.Crime_Type) === true) {
	        $('#Edit_Crime_Type').val('Other').change();
	    }
	    else {
	        console.log("Unexpected main category chosen (Load/Edit)");
	        $('#Edit_Crime_Type').val('[Import]').change();
	        //$('#Edit_Crime_Type_sub').val(MakerToEdit.Crime_Type).change();
	    }
		
		$('#Edit_Crime_Type_sub').val(MarkerToEdit.Crime_Type).change();
		
		modal.find('#Edit_Date').val(MarkerToEdit.Crime_Date);
		modal.find('#Edit_Time').val(MarkerToEdit.Crime_Time);
		modal.find('#Edit_Description').val(MarkerToEdit.Description);
		
		modal.modal('show');
		
		var EditMapOptions = {
			center: MarkerToEdit.position,
			zoom: 10,
			disableDefaultUI: true, // Remove all controls but street view
			streetViewControl: true,
		};

		var map3 = new google.maps.Map(document.getElementById("map3"), EditMapOptions); // Show smaller map

		var Draggable_marker = new google.maps.Marker({ // Add a single draggable marker to smaller map
		position: MarkerToEdit.position,
		draggable: true,
		map: map3
		});
		
		var Edit_SmallMarkerMoved = false;
		var FirstLocation = MarkerToEdit.position;
		var Latitude = FirstLocation.lat();
		var Longitude = FirstLocation.lng();
		
		/* ----------- */
		
		google.maps.event.addListener(Draggable_marker, 'dragend', function (evt) {
			SecondLocation = evt.latLng;
			Latitude = SecondLocation.lat(); // Information to be sent
			Longitude = SecondLocation.lng();
			Edit_SmallMarkerMoved = true;
		});
		
		$("#edit_submit_form").submit(function(e) {
		e.preventDefault();
					
		var dropdown = document.getElementById("Edit_Crime_Type_sub"); // Initial step of getting crime type
		
		/* Update values locally */
		var Crime_Date = document.getElementById("Edit_Date").value;
		MarkerToEdit.Crime_Date = Crime_Date;
		
		var Crime_Time = document.getElementById("Edit_Time").value;
		MarkerToEdit.Crime_Time = Crime_Time;
		
		var Crime_Type = dropdown.options[dropdown.selectedIndex].value;
		MarkerToEdit.Crime_Type = Crime_Type;
		
		var Description = document.getElementById("Edit_Description").value;
		MarkerToEdit.Description = Description;
		
		if (Edit_SmallMarkerMoved == true) {
			MarkerToEdit.position = SecondLocation;
			Edit_SmallMarkerMoved = false;
		}

		/* Also send to database */	
		var formData = $("#edit_submit_form").serialize();
		
		var Vars = {ID: ID, Latitude: Latitude, Longitude: Longitude};
		var varsData = $.param(Vars);

		var data = formData + '&' + varsData;

		$.ajax({
			url: 'EditMarkers.php',
			type: 'POST',
			data: data,
			success: function(result)
			{
                //
			}
			
		});
		
		MarkerToEdit.setPosition(MarkerToEdit.position);
		UpdateMarkerInfo(MarkerToEdit);
		$("#modal_edit").modal('hide');
		
		});
		
	}
	
	function DeleteMarker(ID) { // Refactor to use marker not marker.ID (saves looping through entire marker array to get marker from ID)
		
		for(i = 0; i < MarkerArray.length; i++){
			if (MarkerArray[i].ID == ID)
				var MarkerToDelete = MarkerArray[i]; // Get actual marker
				var index = i; // Position in MarkerArray
		}
		
		if (MarkerToDelete.info != null) { // Additional check
		    MarkerToDelete.info.close(); // Close infowindow
		}
		
		MarkerToDelete.setVisible(false); // Hide marker
		
		if (index !== -1) MarkerArray.splice(index, 1); // Remove marker from array
			
		var MarkerID = ID; // Assign to send variable
		
			$.ajax({
				url: 'DeleteMarker.php',  // Remove from database
				type: 'POST',
				data: {MarkerID: MarkerID},
				success: function(data)
				{
					//
				}
			});
		
	}

	function initMap() {
		var ContextMenu = null;
		var menuDisplayed = false;
		var Latitude = 0;
		var Longitude = 0;
		var Clusterer_count = 0;
		
		var initial_location = {lat: 51.454266, lng: -0.978130};
		var map = new google.maps.Map(document.getElementById("map"), {zoom: 8, center: initial_location});
		
		// Create the search box and link it to the UI element.
		var input = document.getElementById('pac-input');
		var searchBox = new google.maps.places.SearchBox(input);
		//map.controls[google.maps.ControlPosition.LEFT].push(input);

		// Bias the SearchBox results towards current map's viewport.
		map.addListener('bounds_changed', function() {
			searchBox.setBounds(map.getBounds());
		});
	
	/*
	|-----------------------------------------------------------------------------------------------------------
	| Retrieving and placing database markers
	|-----------------------------------------------------------------------------------------------------------
	*/
	
	function LoadMarkers() {	
		var markers = [
			<?php 
			$result = $db->query("SELECT * FROM markers"); // Returns output of statement
			if($result->num_rows > 0){ 
				while($row = $result->fetch_assoc()){
					echo '['.$row['ID'].',"'.$row['Crime_Type'].'","'.$row['Crime_Date'].'","'.$row['Crime_Time'].'",
					"'.$row['Description'].'",'.$row['Latitude'].','.$row['Longitude'].'],';
				} 
			} 
			?>
		];

		for( i = 0; i < markers.length; i++ ) { // Placing the markers stored in the database
			var ID = markers[i][0];
			var Crime_Type = markers[i][1];
			var Crime_Date = markers[i][2];
			var Crime_Time = markers[i][3];
			var Description = markers[i][4];
			var Point = new google.maps.LatLng(markers[i][5], markers[i][6]);
			placeMarker(ID,Crime_Type,Crime_Date,Crime_Time,Description,Point,map);		
		}
		
	}
	LoadMarkers();
	
	var markerCluster = new MarkerClusterer(null, MarkerArray,
                {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
    markerCluster.setIgnoreHidden(true);
	
	function FilterMarkers() {
		
		var AllMainSelected = false;
		var AllSubSelected = false;
		var isMinDate = true;
		var isMaxDate = true;
		var isMinTime = true;
		var isMaxTime = true;
		var invalidInput = false;
		
		/* -------- Getting input values -------- */
		
		/* ---- Main Crime Type ---- */
		var main_dropdown = document.getElementById("Filter_Crime_Type");
		
		if (main_dropdown.options[main_dropdown.selectedIndex].value == "[ALL]")
		{
			AllMainSelected = true;
		}
		else {
			var Main_Crime_Type = main_dropdown.options[main_dropdown.selectedIndex].value;
		}
		
		/* ---- Sub Crime Type ---- */
		var sub_dropdown = document.getElementById("Filter_Crime_Type_sub");
		
		if (sub_dropdown.options[sub_dropdown.selectedIndex].value == "[ALL]")
		{
			AllSubSelected = true;
		}
		else {
			var Sub_Crime_Type = sub_dropdown.options[sub_dropdown.selectedIndex].value;
		}
		
		/* ---- Date ---- */
		if (document.getElementById("Filter_minDate").value == "") {
			isMinDate = false;
		}
		else {
			minDate = document.getElementById("Filter_minDate").value;
			minDate = new Date(minDate);
		}
		
		if (document.getElementById("Filter_maxDate").value == "") {
			isMaxDate = false;
		}
		else {
			maxDate = document.getElementById("Filter_maxDate").value;
			maxDate = new Date(maxDate);
		}
		
		/* ---- Time ---- */
		if (document.getElementById("Filter_minTime").value == "") {
			isMinTime = false;
		}
		else {
			var minTime = document.getElementById("Filter_minTime").value;
		}
		
		if (document.getElementById("Filter_maxTime").value == "") {
			isMaxTime = false;
		}
		else {
			var maxTime = document.getElementById("Filter_maxTime").value;
			maxTime = maxTime + ":00"; // MarkerTime has seconds, not an issue for minTime but will hide on boundary of maxTime
		}
		
		// Also by ID or last x/10/100 crimes?
		// Also by date (after date or range of dates)
		
		/* -------- Input validation -------- */
		
		if (isMinDate == true && isMaxDate == true) {
		    if (minDate > maxDate) {
		        alert("The lower date boundary can't be later than the higher date boundary");
		        invalidInput = true;
		    }
		}
		
		if (isMinTime == true && isMaxTime == true) {
		    if (minTime > maxTime) {
		        alert("The lower time boundary can't be after the higher time boundary");
		        invalidInput = true;
	    	}
		}
	    	
		if (isMinTime == false && isMaxTime == true || isMaxTime == false && isMinTime == true) {
		        alert("Enter a value for both time fields");
		        invalidInput = true;
		}
		
		/* -------- Filtering -------- */
		
		/* ---- Remove any previous filters ---- */
		if (invalidInput == false) {
		    for (i = 0; i < MarkerArray.length; i++){
			    MarkerArray[i].setVisible(true);
			    
			    var MarkerDate = moment(MarkerArray[i].Crime_Date).format("YYYY-MM-DD"); // Convert date
			    MarkerDate = new Date(MarkerDate);
			
			    var MarkerTime = MarkerArray[i].Crime_Time;

    			if (AllMainSelected == false) {
    			    if (AllSubSelected == false) { // One specific crime
    			        if (MarkerArray[i].Crime_Type != Sub_Crime_Type) {
    			            MarkerArray[i].setVisible(false);
    			        }
    			    }
    			    if (AllSubSelected == true) { // One main category of crime
    			        if (Main_Crime_Type == "Violence against the person") {
    			            if (violence_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                MarkerArray[i].setVisible(false);
    			            }
    			        }
    			        if (Main_Crime_Type == "Public Order") {
    			            if (public_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                MarkerArray[i].setVisible(false);
    			            } 
    			        }
    			        if (Main_Crime_Type == "Drug offences") {
    			            if (drug_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                MarkerArray[i].setVisible(false);
    			            } 
    			        }
    			        if (Main_Crime_Type == "Vehicle offences") {
    			            if (vehicle_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                MarkerArray[i].setVisible(false);
    			            } 
    			        }
    			        if (Main_Crime_Type == "Sexual offences") {
    			            if (sexual_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                MarkerArray[i].setVisible(false);
    			            } 
    			        }
    			        if (Main_Crime_Type == "Arson and criminal damage") {
    			            if (arson_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                MarkerArray[i].setVisible(false);
    			            } 
    			        }
    			        if (Main_Crime_Type == "Posession of weapons") {
    			            if (weapons_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                MarkerArray[i].setVisible(false);
    			            } 
    			        }
    			        if (Main_Crime_Type == "Theft") {
    			            if (theft_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                MarkerArray[i].setVisible(false);
    			            } 
    			        }
    			        if (Main_Crime_Type == "Burglary") {
    			            if (burglary_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                MarkerArray[i].setVisible(false);
    			            } 
    			        }
    			        if (Main_Crime_Type == "Robbery") {
    			            if (robbery_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                MarkerArray[i].setVisible(false);
    			            } 
    			        }
    			        if (Main_Crime_Type == "Miscellaneous crimes against society") {
    			            if (misc_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                MarkerArray[i].setVisible(false);
    			            } 
    			        }
    			        if (Main_Crime_Type == "Other") {
    			            if (other_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                MarkerArray[i].setVisible(false);
    			            } 
    			        }
    			    }
    			
    			if (isMinDate == true) { // If a minimum date was entered
    				if (MarkerDate < minDate) { // And the marker's date is before than that date
    					MarkerArray[i].setVisible(false); // Hide it
    				}
    			}
    			
    			if (isMaxDate == true) {
    				if (MarkerDate > maxDate) {
    					MarkerArray[i].setVisible(false);
    				}
    			}
    			
    			if (isMinTime == true) {
    				if (MarkerTime < minTime) {
    					MarkerArray[i].setVisible(false);
    				}
    			}
    			
    			if (isMaxTime == true) {
    				if (MarkerTime > maxTime) {
    					MarkerArray[i].setVisible(false);
    				}
    			}
    			$("#modal_filter").modal('hide');
    			
    		}
		    
		  }
		
	    }
	}

	/*
	|-----------------------------------------------------------------------------------------------------------
	| Custom right click context menu
	|-----------------------------------------------------------------------------------------------------------
	*/		
			
	function hideContextMenu() {
		ContextMenu = document.getElementById("menu");
		ContextMenu.style.left = -500 + "px"; // Hide off page
		ContextMenu.style.top = -500 + "px";
		ContextMenu.style.display = "none"; // For good measure
		menuDisplayed = false;
	}
	
	map.addListener('rightclick', function(e) {
		if (menuDisplayed == true) { // If menu is already open and user right clicks again
			hideContextMenu();
		}
		else { // Open the context menu
			FirstLocation = e.latLng;
			Latitude = FirstLocation.lat();
			Longitude = FirstLocation.lng();
			for (prop in e) {
				if (e[prop] instanceof MouseEvent) {
					mouseEvt = e[prop];
					var left = mouseEvt.clientX;
					var top = mouseEvt.clientY;
						
					ContextMenu = document.getElementById("menu");
					// Position context menu one pixel to the right and below location of click
					// (so that hover styling is not seen immediately)
					ContextMenu.style.left = (left+1) + "px";
					ContextMenu.style.top = (top-1) + "px";
					ContextMenu.style.display = "block";
					menuDisplayed = true;
				}
			}
		}
	});
		
	map.addListener("click", function(e) { // Left click away from it
		if (menuDisplayed == true) {
			hideContextMenu();
		}		
	});
		
	map.addListener("drag", function(e) { // Drag away from it
		if (menuDisplayed == true) {
			hideContextMenu();
		}		
	});
	
	/*
	|-----------------------------------------------------------------------------------------------------------
	| 'Add crime' input window
	|-----------------------------------------------------------------------------------------------------------
	*/
	
	var SmallMarkerMoved = false;
			
	const add_btn = document.getElementById("btn_add"); // 'Add crime' button
	add_btn.addEventListener('click', event => {
		hideContextMenu();
		
		var modal = $('#modal_add');
		modal.modal('show');
		
		var CurrentZoom = map.getZoom(); // Get zoom level when add button was clicked
		var RefinedZoom = CurrentZoom + 1; // Enhance zoom level by one level
		
		var SmallMapOptions = {
			center: FirstLocation,
			zoom: RefinedZoom,
			disableDefaultUI: true, // Remove all controls but street view
			streetViewControl: true,
		};

		var map2 = new google.maps.Map(document.getElementById("map2"), SmallMapOptions); // Show smaller map

		var Draggable_marker = new google.maps.Marker({ // Add a single draggable marker to smaller map
		position: FirstLocation,
		draggable: true,
		map: map2
		});
		
		google.maps.event.addListener(Draggable_marker, 'dragend', function (evt) {
			SecondLocation = evt.latLng; // To be used to place static marker on main map
			Latitude = SecondLocation.lat(); // Information to be sent
			Longitude = SecondLocation.lng();
			SmallMarkerMoved = true;
		});
			
		// 3D View (adding markers in street view)
	});
	
	$("#add_submit_form").submit(function(e) {
		e.preventDefault();
					
		var dropdown = document.getElementById("Add_Crime_Type_sub"); // Initial step of getting crime type
		
		/* Take values locally */
		var Crime_Date = document.getElementById("Add_Date").value;
		var Crime_Time = document.getElementById("Add_Time").value;
		var Crime_Type = dropdown.options[dropdown.selectedIndex].value;
		var Description = document.getElementById("Add_Description").value;

		/* Also send to database */	
		var formData = $("#add_submit_form").serialize();
		
		var Vars = {Latitude: Latitude, Longitude: Longitude};
		var varsData = $.param(Vars);

		var data = formData + '&' + varsData;

		$.ajax({
			url: 'SaveMarkers.php',
			type: 'POST',
			data: data,
			success: function(result)
			{
				if (SmallMarkerMoved == true) {
					placeMarker(result,Crime_Type,Crime_Date,Crime_Time,Description,SecondLocation,map); // Place a static marker on the main map
				}
				else {
					placeMarker(result,Crime_Type,Crime_Date,Crime_Time,Description,FirstLocation,map); // Place a static marker on the main map
				}
				SmallMarkerMoved = false;
				$("#modal_add").modal('hide');
			}
			
		});

	});
	
	/*
	|-----------------------------------------------------------------------------------------------------------
	| Filtering crimes
	|-----------------------------------------------------------------------------------------------------------
	*/
		
	$("#btn_filter_confirm").click(function() {
		FilterMarkers();
	});
	
	/*
	|-----------------------------------------------------------------------------------------------------------
	| 'Analyse' - Crime Analysis Techniques
	|-----------------------------------------------------------------------------------------------------------
	*/
	
	var Cluster_Active = false; // Clusterer initialised as unactive
	const analyse_btn = document.getElementById("btn_marker_cluster"); // 'Analyse' button
	analyse_btn.addEventListener('click', event => {
		hideContextMenu();
		
		if (Cluster_Active == true) { // If active and button was pressed
		    //$("#btn_marker_cluster").text('Clustering (enable)');
		    markerCluster.setMap(null); // Hide clusterer
		    Cluster_Active = false; // Alternate variable
		}
		else {
		    //$("#btn_marker_cluster").text('Clustering (disable)');
            markerCluster.addMarkers(MarkerArray); // Update markers to cluster
            markerCluster.setMap(map);
            markerCluster.repaint(); // Redraw and show clusterer
            Cluster_Active = true;
		}

	});
	
	/*
	|-----------------------------------------------------------------------------------------------------------
	| Importing crimes
	|-----------------------------------------------------------------------------------------------------------
	*/
		
	$("#btn_import_confirm").click(function() {
	    files = $("#Import_input")[0].files;
	    $(".progress-bar").css("width", "0%").text("Ready");

        for (i = 0, numFiles = files.length; i < numFiles; i++) { // For all files selected
            var fileToRead = files[i];
            var reader = new FileReader();
            reader.readAsText(fileToRead);
            
            reader.onload = function(event) {
                // Needs better validation/error handling
                var default_value = -1;
                var Date_index = default_value;
                var Latitude_index = default_value;
                var Longitude_index = default_value;
                var CrimeType_index = default_value;
                var Description_index = default_value;
                
                var Accepted_Date_headers = ["Date", "date", "Month", "month"];
                var Accepted_Latitude_headers = ["Latitude", "latitude", "Lat", "lat"];
                var Accepted_Longitude_headers = ["Longitude", "longitude", "Long", "long", "Lng", "lng"];
                var Accepted_CrimeType_headers = ["Crime type", "Crime Type", "crime type", "CrimeType", "crimetype", "Type", "type"];
                var Accepted_Description_headers = ["Context", "context", "Description", "description", "Notes", "notes"];
                
                var csv = event.target.result;
                var rows = csv.split('\n'); // The rows are split by new lines
                headers = rows[0].split(','); // The first row split by commas give the headers
            
                for (var i = 0; i < headers.length; i++) {
                    headers[i] = $.trim(headers[i].replace(/[\t\n]+/g,' ')); // Remove any whitespace (e.g before first header or after last header)
                    //console.log(headers[i]);
                    if (Accepted_Date_headers.indexOf(headers[i]) !== -1) {
                        Date_index = i;
                    }
                    if (Accepted_Latitude_headers.indexOf(headers[i]) !== -1) {
                        Latitude_index = i;
                    }
                    if (Accepted_Longitude_headers.indexOf(headers[i]) !== -1) {
                        Longitude_index = i;
                    }
                    if (Accepted_CrimeType_headers.indexOf(headers[i]) !== -1) {
                        CrimeType_index = i;
                    }
                    if (Accepted_Description_headers.indexOf(headers[i]) !== -1) {
                        Description_index = i;
                    }
                }
                
                var validFile = true;
                var err_str = "File is missing columns titled/for: ";
                
                if (Date_index === default_value) {
                    err_str = err_str + "\nDate";
                    var validFile = false;
                }
                if (Latitude_index === default_value) {
                    err_str = err_str + "\nLatitude";
                    var validFile = false;
                }
                if (Longitude_index === default_value) {
                    err_str = err_str + "\nLongitude";
                    var validFile = false;
                }
                if (CrimeType_index === default_value) {
                    err_str = err_str + "\nCrime Type";
                    var validFile = false;
                }
                if (Description_index === default_value) {
                    err_str = err_str + "\nDescription";
                    var validFile = false;
                }
                
                if (validFile === false) {
                    alert(err_str);
                }
                
                if (validFile === true) {
                    $(".progress-bar").css("width", "0%").text("Ready");
                    numRows = rows.length;
                    
                    if (numRows >= 50 && numRows <=500) {
                        alert("The file has " + numRows + " rows\n" + "The import process may take a while\n" + "Please wait for the progress bar to reach 100% before attempting to perform any other actions");
                    }
                    
                    if (numRows > 500) {
                        alert("The file has " + numRows + " rows\n" + "Only the first 500 rows will be imported\n" + "The import process may take a while\n" + "Please wait for the progress bar to reach 100% before attempting to perform any other actions");
                        numRows = 500;
                    }
                    
                    for (var i = 1; i < numRows; i++) {
                        validLatitude = false;
                        validLongitude = false;
                        var dateRead;
                        
                        row_values = rows[i].split(','); // Split rows for values
                        
                        /* Date */
                        if (row_values[Date_index].length == 7) {
                            dateRead = row_values[Date_index] + "-01"; // Add on day
                        }
                        else
                        {
                            dateRead = row_values[Date_index];
                        }
                        
                        var dateCheck = moment(dateRead);
                        if(dateCheck.isValid() == true) { // If valid date
                            imp_Crime_Date = dateRead;
                        }
                        else
                        {
                            continue;
                        }
                        
                        /* Latitude */
                        if (isNaN(row_values[Latitude_index]) == false && row_values[Latitude_index] >=-90 && row_values[Latitude_index] <=90) {
                            imp_Latitude = row_values[Latitude_index];
                            validLatitude = true;
                        }
                        else
                        {
                            continue;
                        }
                        
                        /* Longitude */
                        if (isNaN(row_values[Longitude_index]) == false && row_values[Longitude_index] >=-180 && row_values[Longitude_index] <=180) {
                            imp_Longitude = row_values[Longitude_index];
                            validLongitude = true;
                        }
                        else
                        {
                            continue;
                        }
                        
                        /* Location */
                        if (validLatitude == true && validLongitude == true) { 
                            var imp_Location = new google.maps.LatLng(imp_Latitude, imp_Longitude);
                        }
                        else
                        {
                            continue;
                        }
                        
                        /* Crime Type */
                        var checkFor = new RegExp("[0-9a-zA-Z]"); /* Atleast one alphanumeric character */
                        
                        if (typeof row_values[CrimeType_index] === 'string' || row_values[CrimeType_index] instanceof String) {
                            imp_Crime_Type = "Unknown";
                            if (checkFor.test(row_values[CrimeType_index]) == true) {
                                imp_Crime_Type = row_values[CrimeType_index];
                            }
                        }
                        else 
                        {
                            imp_Crime_Type = "Unknown";
                        }
                        
                        /* Description */
                        if (typeof row_values[Description_index] === 'string' || row_values[Description_index] instanceof String) {
                            imp_Description = "-";
                            if (checkFor.test(row_values[Description_index]) == true) {
                                imp_Description = row_values[Description_index];
                            }
                        }
                        else 
                        {
                            imp_Description = "-";
                        }
                        
                    	$.ajax({
                    		url: 'ImportMarkers.php',
                    		type: 'POST',
                    		async: false,
                    		data: {
                    			   imp_Crime_Date: imp_Crime_Date, 
                    			   imp_Latitude: imp_Latitude, 
                    			   imp_Longitude: imp_Longitude, 
                    			   imp_Crime_Type: imp_Crime_Type, 
                    			   imp_Description: imp_Description
                    		},
                    		success: function(result)
                    		{
                    			placeMarker(result,imp_Crime_Type,imp_Crime_Date,'00:00:00',imp_Description,imp_Location,map);
                    		}
                    			
                    	});
                    }
                    /* Show complete progress bar when all markers imported */
                    $(".progress-bar").css("width", "100%").text("Complete");
                }
                
              }
        }
        //$("#modal_import").modal('hide');
	});
		
	/*
	|-----------------------------------------------------------------------------------------------------------
	| Search box implementation
	|-----------------------------------------------------------------------------------------------------------
	*/

    searchBox.addListener('places_changed', function() { // Selecting a prediction from the list
        var places = searchBox.getPlaces();

        if (places.length == 0) {
			return;
        }

          // For each place, get the icon, name and location.
        var bounds = new google.maps.LatLngBounds();
        places.forEach(function(place) {
			if (!place.geometry) {
				console.log("Returned place contains no geometry");
				return;
            }
            var icon = {
              url: place.icon,
              size: new google.maps.Size(71, 71),
              origin: new google.maps.Point(0, 0),
              anchor: new google.maps.Point(17, 34),
              scaledSize: new google.maps.Size(25, 25)
            };

            if (place.geometry.viewport) {
              // Only geocodes have viewport.
              bounds.union(place.geometry.viewport);
            } else {
              bounds.extend(place.geometry.location);
            }
          });
          map.fitBounds(bounds);
        });
	
	}
	  
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/markerclustererplus/2.1.4/markerclusterer.js"></script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDDpgBmZOTCzsVewLlzsx77Y5bDUVS_MZg&libraries=places&callback=initMap" async defer> // API Key, Libraries and map function
</script>

<!-- Bootstrap Scripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> <!-- JQuery (Google CDN) -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<!-- On Page Load -->
<script>
    $(document).ready(function() {
        /* Main category select elements */ 
        var add_select = document.getElementById("Add_Crime_Type");
        var filter_select = document.getElementById("Filter_Crime_Type");
        var edit_select = document.getElementById("Edit_Crime_Type");
        
        /* Subcategory select elements */ 
        var add_sub_select = document.getElementById("Add_Crime_Type_sub");
        var filter_sub_select = document.getElementById("Filter_Crime_Type_sub");
        var edit_sub_select = document.getElementById("Edit_Crime_Type_sub");
        
        var main_options = ["Violence against the person","Public Order","Drug offences","Vehicle offences","Sexual offences","Arson and criminal damage","Possession of weapons","Theft","Burglary","Robbery","Miscellaneous crimes against society","Other"]; 
        
        function AddOptions(select,options) { /* Add parameter options to parameter select */
            for(var i = 0; i < options.length; i++) {
                    var opt = options[i];
                    var el = document.createElement("option");
                    el.textContent = opt;
                    el.value = opt;
                    select.appendChild(el);
                }
        }
        
        AddOptions(add_select,main_options);
        
        all_option = ["[ALL]"];
        AddOptions(filter_select,all_option);
        AddOptions(filter_select,main_options);
        
        AddOptions(edit_select,main_options);
        
        violence_sub_options = ["Murder","Attempted Murder","Manslaughter","Conspiracy to murder","Threats to kill","Causing death or serious injury by dangerous driving", "Causing death by careless driving under the influence of drink or drugs","Causing death by careless or inconsiderate driving","Causing death or serious injury by driving (unlicensed driver)","Causing death by aggrevated vehicle taking","Corporate manslaughter","Assualt (with intent to cause serious harm)","Endangering life","Harassment","Racially or religiously aggravated harassment","Racially or religiously aggravated assualt with injury","Racially or religiously aggravated assualt without injury","Assualt with injury","Assualt without injury","Assualt with injury on a constable","Assualt without injury on a constable","Stalking","Maliciuos communications","Cruelty to Children/Young Persons","Child abduction","Procuring illegal abortion","Kidnapping","Modern Slavery"];
        
        AddOptions(add_sub_select,violence_sub_options);
        
        public_sub_options = ["Public fear, harm or distress","Racially or religiously aggravated public fear, alarm or distress","Violent disorder","Other offences against the state or public order"];
        
        drug_sub_options = ["Trafficking in controlled drugs","Posession of controlled drugs (Cannabis)","Posession of controlled drugs (excluding Cannabis)","Other drug offences"];
        
        vehicle_sub_options = ["Aggravated vehicle taking","Theft from vehicle","Theft or unauthorised taking of motor vehicle"];
        
        sexual_sub_options = ["Sexual Assualt","Rape","Causing sexual activity without consent","Sexual activity with minor","Sexual activity with a vulnerable person","Sexual exploitation","Abuse of a position of trust of a sexual nature","Sexual grooming","Exposure and voyeurism","Unnatural sexual offences","Other miscellaneous sexual offences"];
        
        arson_sub_options = ["Arson endangering life","Arson not endangering life","Criminal damage to a dwelling","Criminal damage to a building other than a dwelling","Criminal damage to a vehicle","Other criminal damage"];
        
        weapons_sub_options = ["Possession of firearms with intent","Possession of firearms offences","Possession of other weapons","Possession of article with blade or point","Other firearms offences","Other knives offences"];
        
        theft_sub_options = ["Blackmail","Theft from the person","Theft in a dwelling other than from an automatic machine or meter","Theft by an employee","Theft of mail","Dishonest use of electricity","Theft or unauthorised taking of a pedal cycle","Shoplifting","Theft from an automatic machine or meter","Making off without payment","Other theft"];
        
        burglary_sub_options = ["Burglary - Residential","Attempted burglary - Residential","Distraction burglary - Residential","Attempted distraction burglary - Residential","Aggravated burglary in a dwelling","Burglary - Business and Community","Attempted burglary - Business and Community","Aggravated burglary - Business and Community"];
        
        robbery_sub_options = ["Robbery of business property","Robbery of personal property"];
        
        misc_sub_options = ["Concealing an infant death close to birth","Exploitation of prostitution","Bigamy","Soliciting for the purpose of prostitution","Going equipped for stealing","Making, supplying or possessing articles for use in fraud","Profiting from or concealing knowledge of the proceeds of crime","Handling stolen goods","Threat or possession with intent to commit criminal damage","Forgery or use of false drug prescription","Fraud or forgery associated with vehicle or driver records","Other forgery","Possession of false documents","Perjury","Offender Management Act","Aiding suicide","Perverting the course of justice","Absconding from lawful custody","Bail offences","Obscene publications","Disclosure, obstruction, false or misleading statements","Wildlife crime","Dangerous driving","Other notifiable offences"];
        
        other_sub_options = ["Unspecified Crime", "Other crime"];
        
        $("#Add_Crime_Type").change(function() { // When main category selected
            $("#Add_Crime_Type_sub").empty(); // Remove any previous values (if any)
            var el = $(this);
            
            if(el.val() === "Violence against the person") { // Check which main category was chosen
                AddOptions(add_sub_select,violence_sub_options);
            }
            else if (el.val() === "Public Order") {
                AddOptions(add_sub_select,public_sub_options);
            }
            else if (el.val() === "Drug offences") {
                AddOptions(add_sub_select,drug_sub_options);
            }
            else if (el.val() === "Vehicle offences") {
                AddOptions(add_sub_select,vehicle_sub_options);
            }
            else if (el.val() === "Sexual offences") {
                AddOptions(add_sub_select,sexual_sub_options);
            }
            else if (el.val() === "Arson and criminal damage") {
                AddOptions(add_sub_select,arson_sub_options);
            }
            else if (el.val() === "Possession of weapons") {
                AddOptions(add_sub_select,weapons_sub_options);
            }
            else if (el.val() === "Theft") {
                AddOptions(add_sub_select,theft_sub_options);
            }
            else if (el.val() === "Burglary") {
                AddOptions(add_sub_select,burglary_sub_options);
            }
            else if (el.val() === "Robbery") {
                AddOptions(add_sub_select,robbery_sub_options);
            }
            else if (el.val() === "Miscellaneous crimes against society") {
                AddOptions(add_sub_select,misc_sub_options);
            }
            else if (el.val() === "Other") {
                AddOptions(add_sub_select,other_sub_options);
            }
            else {
                console.log("Unexpected main category chosen (Add)");
            }
        });
        
        $("#Filter_Crime_Type").change(function() {
            $("#Filter_Crime_Type_sub").empty();
            var el = $(this);
            
            if(el.val() === "Violence against the person") {
                AddOptions(filter_sub_select,all_option);
                AddOptions(filter_sub_select,violence_sub_options);
            }
            else if (el.val() === "Public Order") {
                AddOptions(filter_sub_select,all_option);
                AddOptions(filter_sub_select,public_sub_options);
            }
            else if (el.val() === "Drug offences") {
                AddOptions(filter_sub_select,all_option);
                AddOptions(filter_sub_select,drug_sub_options);
            }
            else if (el.val() === "Vehicle offences") {
                AddOptions(filter_sub_select,all_option);
                AddOptions(filter_sub_select,vehicle_sub_options);
            }
            else if (el.val() === "Sexual offences") {
                AddOptions(filter_sub_select,all_option);
                AddOptions(filter_sub_select,sexual_sub_options);
            }
            else if (el.val() === "Arson and criminal damage") {
                AddOptions(filter_sub_select,all_option);
                AddOptions(filter_sub_select,arson_sub_options);
            }
            else if (el.val() === "Possession of weapons") {
                AddOptions(filter_sub_select,all_option);
                AddOptions(filter_sub_select,weapons_sub_options);
            }
            else if (el.val() === "Theft") {
                AddOptions(filter_sub_select,all_option);
                AddOptions(filter_sub_select,theft_sub_options);
            }
            else if (el.val() === "Burglary") {
                AddOptions(filter_sub_select,all_option);
                AddOptions(filter_sub_select,burglary_sub_options);
            }
            else if (el.val() === "Robbery") {
                AddOptions(filter_sub_select,all_option);
                AddOptions(filter_sub_select,robbery_sub_options);
            }
            else if (el.val() === "Miscellaneous crimes against society") {
                AddOptions(filter_sub_select,all_option);
                AddOptions(filter_sub_select,misc_sub_options);
            }
            else if (el.val() === "Other") {
                AddOptions(filter_sub_select,all_option);
                AddOptions(filter_sub_select,other_sub_options);
            }
            else if (el.val() === "[ALL]") {
                AddOptions(filter_sub_select,all_option);
            }
            else {
                console.log("Unexpected main category chosen (Filter)");
            }
        });
        
        $("#Edit_Crime_Type").change(function() {
            $("#Edit_Crime_Type_sub").empty();
            var el = $(this);
            
            if(el.val() === "Violence against the person") {
                AddOptions(edit_sub_select,violence_sub_options);
            }
            else if (el.val() === "Public Order") {
                AddOptions(edit_sub_select,public_sub_options);
            }
            else if (el.val() === "Drug offences") {
                AddOptions(edit_sub_select,drug_sub_options);
            }
            else if (el.val() === "Vehicle offences") {
                AddOptions(edit_sub_select,vehicle_sub_options);
            }
            else if (el.val() === "Sexual offences") {
                AddOptions(edit_sub_select,sexual_sub_options);
            }
            else if (el.val() === "Arson and criminal damage") {
                AddOptions(edit_sub_select,arson_sub_options);
            }
            else if (el.val() === "Possession of weapons") {
                AddOptions(edit_sub_select,weapons_sub_options);
            }
            else if (el.val() === "Theft") {
                AddOptions(edit_sub_select,theft_sub_options);
            }
            else if (el.val() === "Burglary") {
                AddOptions(edit_sub_select,burglary_sub_options);
            }
            else if (el.val() === "Robbery") {
                AddOptions(edit_sub_select,robbery_sub_options);
            }
            else if (el.val() === "Miscellaneous crimes against society") {
                AddOptions(edit_sub_select,misc_sub_options);
            }
            else if (el.val() === "Other") {
                AddOptions(edit_sub_select,other_sub_options);
            }
            else {
                console.log("Unexpected main category chosen (Edit)");
            }
        });

        
    })
</script>

<script> // Showing name of file chosen (import)
$("#Import_input").on("change", function() {
    
    files = this.files;
    var allCSV = true;
    
    for (var i=0, l=files.length; i<l; i++) {
        var fileName = files[i].name;
        var ext = fileName.substring(fileName.lastIndexOf('.') + 1).toLowerCase();
        if(ext != 'csv') {
            allCSV = false;
        }
    }
    
    if (allCSV == false) {
        alert('Only files with the file extension CSV are allowed');
        this.val = "";
    }
    else
    {
        // Changing label text to files chosen
        var string = files[0].name; // First
        if (files.length > 1) {
            for (var i=1, l=(files.length)-1; i<l; i++) { // All in between
                string += ", ";
                string += files[i].name;
            }
            string += ", ";
            string += files[files.length-1].name; // Last
        }
        $("#import_lbl").text(string);  
    }
});
</script>

</body>
</html>