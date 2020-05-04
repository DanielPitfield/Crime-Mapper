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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> <!-- JQuery (Google CDN) -->
</head>

<link rel="stylesheet" href="layout.css">  <!-- Everything else -->

<body oncontextmenu="return false;">  <!-- Disable the default right click context menu for the body of the page -->

<!-- 1st - 0% left
     2nd - 0.25% left
     3rd - 0.25% left and right
     4th - 0.25% right
     5th - 0% right
-->

<!-- Navigation Bar -->
<nav class="navbar navbar-dark bg-dark">
    <!-- Filter Crime -->
    <button class="btn btn-outline-primary navbar-btn" role="button" data-toggle="modal" data-target="#modal_filter" style="color:white;width:16%;margin-left:0%;">Filter Crime</button>
    
    <!-- Import Crime -->
    <button class="btn btn-outline-primary navbar-btn" role="button" data-toggle="modal" data-target="#modal_import" style="color:white;width:16%;margin-left:0.25%;">Import Crime</button>
    
    <!-- Location Search Bar -->
    <input id="pac-input" class="controls" type="text" placeholder="Location Search" style="color:black;width:35%;margin-left:0.25%;margin-right:0.25%;">

    <!-- Analyse Crime -->
    <button class="btn btn-outline-primary navbar-btn" id="btn_marker_cluster" role="button" style="color:white;width:16%;margin-right:0.25%;">Analyse Crime</button>
    
    <!-- Options -->
    <button class="btn btn-outline-primary disabled navbar-btn" role="button" style="color:white;width:16%;margin-right:0%;">Predict Crime</button>
</nav>

<!-- Loading Symbol -->
<div class="spinner-border text-primary" role="status" id="loading_symbol">
  <span class="sr-only">Loading...</span>
</div>

<!-- Map -->
<div id="map"></div>

<!-- Context Menu -->
<div class="custom_contextmenu" id="menu">
	<div class="custom_contextmenu_btn add" id="btn_add">Add Crime</div>
</div>

<!-- Filter modal -->
<div class="modal fade bd-example-modal-xl" data-backdrop="false" tabindex="-1" role="dialog" id="modal_filter">
  <div class="modal-dialog modal-xl" id="modal_filter_dialog">
    <div class="modal-content">
      <div class="modal-header">
		<h5 class="modal-title">Filter</h5>
		
		<button class="btn btn-info" id="Filter_Clear" style="font-size:12px;height:20px;padding: 0px 10px 2px 10px;margin-left:10px;margin-top:5px;text-align:center;">Clear Filter</button>
		
		<button type="button" class="close" data-dismiss="modal">
			<span>&times;</span>
		</button>
	   </div>
	   <div class="modal-body">
	   <div id="modal_left">
	   <div class="form-group">
	    Date:
		<input type="date" id="Filter_minDate" min="1970-01-01" value="" max="<?php echo date("Y-m-d"); ?>">
		<input type="date" id="Filter_maxDate" min="1970-01-01" value="" max="<?php echo date("Y-m-d"); ?>">
		</div>
		
		<div class="form-group">
		Time:
		<input type="time" id="Filter_minTime" value="">
		<input type="time" id="Filter_maxTime" value="">
		</div>
		
		<div class="form-group">
        <select class="select form-control" id="Filter_Crime_Type">
        <option value="[ALL]" selected disabled hidden>Crime Type - Main Category</option>
        </select>
        <select class="select form-control" id="Filter_Crime_Type_sub" name="Crime_Type">
        <option value="[ALL]" selected disabled hidden>Crime Type - Subcategory</option>
        </select>
        </div>
        
        <div class="form-group">
            <select class="select form-control" id="Filter_Location" disabled>
        <option value="[ALL]" selected disabled hidden>Search Radius (km)</option>
        </select>
        </div> 
            
        </div>
        
        <div id="modal_right">
		<div id="map4"></div>
		</div>
		
		<button id="btn_filter_confirm" class="btn btn-success" style="width:100%;">Confirm</button>

	   </div>
    </div>
  </div>
</div>

<!-- Add crime modal -->
<div class="modal fade bd-example-modal-xl" data-backdrop="false" tabindex="-1" role="dialog" id="modal_add">
  <div class="modal-dialog modal-xl" id="modal_add_dialog">
    <div class="modal-content">
      <div class="modal-header">
		<h5 class="modal-title">Add Crime</h5>
		<button type="button" class="close" data-dismiss="modal">
			<span>&times;</span>
		</button>
	   </div>
	   <div class="modal-body">
	   <div id="modal_left">
		<form name="add_submit_form" id="add_submit_form" action="SaveMarkers.php" method="post">
		    
		<div class="form-group">
		<label class="control-label " for="Add_Date">Date:</label>
		<input id="Add_Date" type="date" name="Date" min="1970-01-01" value="<?php echo date("Y-m-d"); ?>" max="<?php echo date("Y-m-d"); ?>" required>

		<label class="control-label " for="Add_Time">Time:</label>
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
	
        <textarea class="form-control" id="Add_Description" name="Description" rows="3" placeholder="Description"></textarea>
        </div>
        
        <div id="modal_right">
		<div id="map2"></div>
		</div>
		
		<button type="submit" id="btn_add_confirm" class="btn btn-success" style="width:100%;margin-top:10px;">Confirm</button>
		</form>
	   </div>
    </div>
  </div>
</div>

<!-- Edit crime modal -->
<div class="modal fade bd-example-modal-xl" data-backdrop="false" tabindex="-1" role="dialog" id="modal_edit">
  <div class="modal-dialog modal-xl" id="modal_edit_dialog">
    <div class="modal-content">
      <div class="modal-header">
		<h5 class="modal-title">Edit Crime</h5>
		<button type="button" class="close" data-dismiss="modal">
			<span>&times;</span>
		</button>
	   </div>
	   <div class="modal-body">
	   <div id="modal_left">
		<form name="edit_submit_form" id="edit_submit_form" action="EditMarkers.php" method="post">
		    
		<div class="form-group">
		<label class="control-label " for="Edit_Date">Date:</label>
		<input id="Edit_Date" type="date" name="Date" min="1970-01-01" value="<?php echo date("Y-m-d"); ?>" max="<?php echo date("Y-m-d"); ?>" required>
		
		<label class="control-label " for="Edit_Time">Time:</label>
		<input id="Edit_Time" type="time" name="Time" value="00:00" required>
		</div>
		
		<div class="form-group">
        <select class="select form-control" id="Edit_Crime_Type">
        <option value="" selected disabled hidden>Crime Category</option>
        </select>
        <select class="select form-control" id="Edit_Crime_Type_sub" name="Crime_Type">
        <option value="" selected disabled hidden>Crime Type</option>
        </select>
        </div>
		
        <textarea class="form-control" id="Edit_Description" name="Description" rows="3" placeholder="Description"></textarea>
        </div>
		
		<div id="modal_right">
		<div id="map3"></div>
		</div>
		
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
		
		<a href="template.csv" class="btn btn-info" role="button" style="font-size:12px;height:20px;padding: 0px 10px 2px 10px;margin-left:10px;margin-top:5px;text-align:center;">Download Template</a>
		
		<button type="button" class="close" id="btn_import_close" data-dismiss="modal">
			<span>&times;</span>
		</button>
	   </div>
	   <div class="modal-body">
	       
        <div class="custom-file mb-3">
        <input type="file" id="Import_Input" class="custom-file-input" name="fileToUpload" accept=".csv">
        <label class="custom-file-label" id="import_lbl" for="customFile" style="display:inline-block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Choose file</label>
        <button type="submit" id="btn_import_confirm" class="btn btn-success" style="width:100%;margin-top:8px;">Import</button>
        <div class="progress" style="margin-top:8px;">
            <div id="progress_file_upload" class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%;">Progress Bar
            </div>
        </div>
        <div class="progress" style="margin-top:8px;">
            <div id="progress_insert_upload" class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%;">Progress Bar
            </div>
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

		google.maps.event.addListener(marker, 'click', function() {
		    if (typeof(marker.info) === "undefined"){
                //
            }
            else{ // If marker already has an InfoWindow
                if (marker.info.getMap() != null) { // And it is open
                    marker.info.close(); // Close it and make/open new one
                }
            }
		    
		    var MarkerDate = moment(marker.Crime_Date).format("DD-MM-YYYY"); // Convert to UK format

            var MarkerTime = marker.Crime_Time;
            if (MarkerTime.length == 8) { // If time is retirved form database which includes seconds
                MarkerTime = MarkerTime.substring(0, MarkerTime.length - 3); // Remove the seconds for display purposes
            }
    		
    		marker.info = new google.maps.InfoWindow({
    			content: '<div id="iw-container">' + '<div class="iw-content">' + 
    					 '<b>ID: </b>' + marker.ID + '<br> <b style="word-wrap: break-word;">Crime Type: </b>' + marker.Crime_Type +'<br> <b>Date: </b>' + MarkerDate +
    					 '<br><b>Time: </b>' + MarkerTime + '<br></br>' + '<i style="word-wrap: break-word;">' + marker.Description + '</i>' +
    					 '<br></br> <button id="btn_edit" type="button" class="btn btn-secondary" style="width:50%;" onclick=EditMarker('+marker.ID+')>Edit</button>' +
    					 '<button id="btn_delete" type="button" class="btn btn-danger" style="width:50%;" onclick=DeleteMarker('+marker.ID+')>Delete</button>' + '</div>' + '</div>',
    		minWidth: 200,
    		maxWidth: 500
    		});
    		
			marker.info.open(map,marker);
		});
	}
	
	function ShowLoading() {
	    LoadingSymbol = document.getElementById("loading_symbol");
		LoadingSymbol.style.left = "calc(50% - 50px)";
		LoadingSymbol.style.top = "calc(50% - 50px)";
		LoadingSymbol.style.display = "block";
	}
	
	
	function ShowLoading() {
	    LoadingSymbol = document.getElementById("loading_symbol");
		LoadingSymbol.style.left = "calc(50% - 50px)";
		LoadingSymbol.style.top = "calc(50% - 50px)";
		LoadingSymbol.style.display = "block";
	}
	
	function HideLoading() {
	    LoadingSymbol = document.getElementById("loading_symbol");
		LoadingSymbol.style.left = "-500px";
		LoadingSymbol.style.top = "-500px";
		LoadingSymbol.style.display = "none";
	}

	function UpdateMarkerInfo(marker) {
	 
	marker.title = marker.Crime_Type; // Shown on hover
		
	var MarkerDate = moment(marker.Crime_Date).format("DD-MM-YYYY"); // Convert to UK format

    var MarkerTime = marker.Crime_Time;
    if (MarkerTime.length == 8) { // If time is retrieved form database which includes seconds
        MarkerTime = MarkerTime.substring(0, MarkerTime.length - 3); // Remove the seconds for display purposes
    }
	
	    marker.info.setContent('<div id="iw-container">' + '<div class="iw-content">' + 
					 '<b>ID: </b>' + marker.ID + '<br> <b>Crime Type: </b>' + marker.Crime_Type +'<br> <b>Date: </b>' + MarkerDate +
					 '<br><b>Time: </b>' + MarkerTime + //'<br><b>Description: </b>' + marker.Description +
					 '<br></br> <button id="btn_edit" type="button" class="btn btn-secondary" onclick=EditMarker('+marker.ID+')>Edit</button>' +
					 '<button id="btn_delete" type="button" class="btn btn-danger" onclick=DeleteMarker('+marker.ID+')>Delete</button>' + '</div>' + '</div>');
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
	        console.log("Imported Crime Type");
	        
	        $('#Edit_Crime_Type').val('Other').change();
	        
    	    var opt = MarkerToEdit.Crime_Type;
            var el = document.createElement("option");
            el.textContent = opt;
            el.value = opt;
            var edit_sub_select = document.getElementById("Edit_Crime_Type_sub");
            edit_sub_select.appendChild(el);
	        
	        $('#Edit_Crime_Type').prop('disabled', true);
	        $('#Edit_Crime_Type_sub').prop('disabled', true);
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
    		
    		var edit_containsTags = false;
    		if (Description.includes("<") && Description.includes(">")) {
    		    edit_containsTags = true;
    		}
    
    		if (Description.length <= 500 && edit_containsTags == false) {
        		/* Also send to database */	
        		var formData = $("#edit_submit_form").serialize();
        		
        		var Vars = {ID: ID, Latitude: Latitude, Longitude: Longitude};
        		var varsData = $.param(Vars);
        
        		var data = formData + '&' + varsData;
        		
        		ShowLoading();
        
        		$.ajax({
        			url: 'EditMarkers.php',
        			type: 'POST',
        			data: data,
        			success: function(result)
        			{
                        MarkerToEdit.setPosition(MarkerToEdit.position);
                		UpdateMarkerInfo(MarkerToEdit);
                		HideLoading();
                		$("#modal_edit").modal('hide');
        			}
        			
        		});  
    		}
    		else {
    		    var edit_err_string = "";
    		    if (Description.length > 500) {
    		        edit_err_string += "The description can only be a maximum of 500 characters\n";
    		    }
    		    if (edit_containsTags == true) {
    		        edit_err_string += "The description can not have both < and > characters\n";
    		    }
    		    alert(edit_err_string);
    		}
		
		});
		
	}
	
	function DeleteMarker(ID) { // Refactor to use marker not marker.ID (saves looping through entire marker array to get marker from ID)
	    ShowLoading();
		
		for(i = 0; i < MarkerArray.length; i++){
			if (MarkerArray[i].ID == ID) {
			    var MarkerToDelete = MarkerArray[i]; // Get actual marker
				var index = i; // Position in MarkerArray
			}

		}
		
		if (MarkerToDelete.info.getMap() != null) { // If infowindow is open
		    MarkerToDelete.info.close(); // Close it
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
			
		HideLoading();
		
	}

	function initMap() {
		var ContextMenu = null;
		var menuDisplayed = false;
		var Latitude = 0;
		var Longitude = 0;
		var Clusterer_count = 0;
		
		var initial_location = {lat: 51.454266, lng: -0.978130};
		var map = new google.maps.Map(document.getElementById("map"), {zoom: 8, center: initial_location});
		
		/*
		var styles = {
            default: null,
            hide: [
              {
                featureType: 'poi',
                stylers: [{visibility: 'off'}]
              },
              {
                featureType: 'poi.park',
                stylers: [{visibility: 'off'}]
              },
              {
                featureType: 'poi.business',
                stylers: [{visibility: 'off'}]
              },
              {
                featureType: 'administrative',
                stylers: [{visibility: 'off'}]
              },
              {
                featureType: 'transit',
                elementType: 'labels.icon',
                stylers: [{visibility: 'off'}]
              }
            ]
        };
        map.setOptions({styles: styles['hide']}); */
		
		// Create the search box and link it to the UI element.
		var input = document.getElementById('pac-input');
		var searchBox = new google.maps.places.SearchBox(input);
		//map.controls[google.maps.ControlPosition.LEFT].push(input);

		// Bias the SearchBox results towards current map's viewport.
		/*map.addListener('bounds_changed', function() {
			searchBox.setBounds(map.getBounds());
		});*/
	
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
		setTimeout(() => {HideLoading();}, 500);
		
	}
	
	// t0 = performance.now();
	LoadMarkers();
	//var t1 = performance.now();
	
	//console.log("LoadMarkers() duration: " + (t1-t0) + "ms");
	
	/*
	|-----------------------------------------------------------------------------------------------------------
	| Marker Clustering
	|-----------------------------------------------------------------------------------------------------------
	*/
	
	var clusterStyles = [
      {
        textColor: 'white',
        url: 'SmallCluster.png',
        height: 53,
        width: 53
      },
      {
        textColor: 'white',
        url: 'MediumCluster.png',
        height: 56,
        width: 56
      },
      {
        textColor: 'white',
        url: 'LargeCluster.png',
        height: 66,
        width: 66
      }
    ];
    
    var mcOptions = {
        gridSize: 50,
        styles: clusterStyles,
        maxZoom: 15
    };
	
	var markerCluster = new MarkerClusterer(null, MarkerArray, mcOptions);
    markerCluster.setIgnoreHidden(true);
    
    google.maps.event.addListener(markerCluster, 'clusteringstart', ShowLoading);
    google.maps.event.addListener(markerCluster, 'clusteringend', HideLoading);
    
    /*
	|-----------------------------------------------------------------------------------------------------------
	| Filtering Markers
	|-----------------------------------------------------------------------------------------------------------
	*/
	
	function FilterMarkers(center_loc, distance) {
	    if (distance == null) {
	        distance = "[ALL]";
	    }
	    
	    var AllDistanceSelected = false;
	    if (distance == "[ALL]") {
	        AllDistanceSelected = true;
	    }
	    
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
		
		if (main_dropdown.options[main_dropdown.selectedIndex].value == null)
		{
			AllMainSelected = true;
		}
		
		if (main_dropdown.options[main_dropdown.selectedIndex].value == "[ALL]")
		{
			AllMainSelected = true;
		}
		else {
			var Main_Crime_Type = main_dropdown.options[main_dropdown.selectedIndex].value;
		}
		
		/* ---- Sub Crime Type ---- */
		var sub_dropdown = document.getElementById("Filter_Crime_Type_sub");
		
		if (sub_dropdown.options[sub_dropdown.selectedIndex].value == null)
		{
			AllSubSelected = true;
		}
		
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
		
		function HideMarker(marker) {
		    marker.setVisible(false);
		    if (typeof(marker.info) === "undefined"){
                //
            }
            else{ // If marker has an InfoWindow
                if (marker.info.getMap() != null) { // And it is open
                    marker.info.close(); // Close it
                }
            }
		}
		
		/* ---- Remove any previous filters ---- */
		if (invalidInput == false) {
		    for (i = 0; i < MarkerArray.length; i++){
			    MarkerArray[i].setVisible(true);
			    
			    /* ---- Convert date into comparable object ---- */
			    var MarkerDate = moment(MarkerArray[i].Crime_Date).format("YYYY-MM-DD"); // Convert date
			    MarkerDate = new Date(MarkerDate);
			
			    var MarkerTime = MarkerArray[i].Crime_Time;

    			if (AllMainSelected == false) {
    			    if (AllSubSelected == false) { // One specific crime
    			        if (MarkerArray[i].Crime_Type != Sub_Crime_Type) {
    			            HideMarker(MarkerArray[i]);
    			        }
    			    }
    			    if (AllSubSelected == true) { // One main category of crime
    			        if (Main_Crime_Type == "Violence against the person") {
    			            if (violence_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                HideMarker(MarkerArray[i]);
    			            }
    			        }
    			        if (Main_Crime_Type == "Public Order") {
    			            if (public_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                HideMarker(MarkerArray[i]);
    			            } 
    			        }
    			        if (Main_Crime_Type == "Drug offences") {
    			            if (drug_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                HideMarker(MarkerArray[i]);
    			            } 
    			        }
    			        if (Main_Crime_Type == "Vehicle offences") {
    			            if (vehicle_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                HideMarker(MarkerArray[i]);
    			            } 
    			        }
    			        if (Main_Crime_Type == "Sexual offences") {
    			            if (sexual_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                HideMarker(MarkerArray[i]);
    			            } 
    			        }
    			        if (Main_Crime_Type == "Arson and criminal damage") {
    			            if (arson_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                HideMarker(MarkerArray[i]);
    			            } 
    			        }
    			        if (Main_Crime_Type == "Posession of weapons") {
    			            if (weapons_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                HideMarker(MarkerArray[i]);
    			            } 
    			        }
    			        if (Main_Crime_Type == "Theft") {
    			            if (theft_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                HideMarker(MarkerArray[i]);
    			            } 
    			        }
    			        if (Main_Crime_Type == "Burglary") {
    			            if (burglary_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                HideMarker(MarkerArray[i]);
    			            } 
    			        }
    			        if (Main_Crime_Type == "Robbery") {
    			            if (robbery_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                HideMarker(MarkerArray[i]);
    			            } 
    			        }
    			        if (Main_Crime_Type == "Miscellaneous crimes against society") {
    			            if (misc_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                HideMarker(MarkerArray[i]);
    			            } 
    			        }
    			        if (Main_Crime_Type == "Other") {
    			            if (other_sub_options.includes(MarkerArray[i].Crime_Type) === false) {
    			                HideMarker(MarkerArray[i]);
    			            } 
    			        }
    			    }
    			}
    			
    			if (isMinDate == true) { // If a minimum date was entered
    				if (MarkerDate < minDate) { // And the marker's date is before than that date
    					HideMarker(MarkerArray[i]);
    				}
    			}
    			
    			if (isMaxDate == true) {
    				if (MarkerDate > maxDate) {
    					HideMarker(MarkerArray[i]);
    				}
    			}
    			
    			if (isMinTime == true) {
    				if (MarkerTime < minTime) {
    					HideMarker(MarkerArray[i]);
    				}
    			}
    			
    			if (isMaxTime == true) {
    				if (MarkerTime > maxTime) {
    					HideMarker(MarkerArray[i]);
    				}
    			}
    			
    			if (AllDistanceSelected == false) {
    			    var distanceInMiles = google.maps.geometry.spherical.computeDistanceBetween(center_loc, MarkerArray[i].getPosition());
    			    distanceInMiles = (distanceInMiles / 1609);
    			    if (distanceInMiles > distance) {
    			        HideMarker(MarkerArray[i]);
    			    }
    			}
		    
		  }
		  $("#modal_filter").modal('hide');
		
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
	
	$('#modal_add').on('shown.bs.modal', function () {
	    $('#Add_Date').val("<?php echo date("Y-m-d"); ?>");
	    $('#Add_Time').val("00:00");
	    $('#Add_Crime_Type').val("");
	    $('#Add_Crime_Type_sub option:not(:first)').remove();
	    $('#Add_Crime_Type_sub').val("");
	    $('#Add_Description').val("");
	});
	
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
		
		var add_containsTags = false;
		if (Description.includes("<") && Description.includes(">")) {
		    add_containsTags = true;
		}
		
		if (Description.length <= 500 && add_containsTags == false && MarkerArray.length < 50000) {
		    /* Also send to database */	
    		var formData = $("#add_submit_form").serialize();
    		
    		var Vars = {Latitude: Latitude, Longitude: Longitude};
    		var varsData = $.param(Vars);
    
    		var data = formData + '&' + varsData;
    		
    		ShowLoading();
    
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
    				HideLoading();
    				$("#modal_add").modal('hide');
    			}
    			
    		});
		}
		else {
		    var add_err_string = "";
		    if (Description.length > 500) {
		        add_err_string += "The description can only be a maximum of 500 characters\n";
		    }
		    if (add_containsTags == true) {
		        add_err_string += "The description can not have both < and > characters\n";
		    }
		    if (MarkerArray.length > 50000) {
		        add_err_string += "The mapper is at its capacity of displaying 50,000 crimes\n";
		    }
		    alert(add_err_string);
		}

	});
	
	/*
	|-----------------------------------------------------------------------------------------------------------
	| Filtering crimes
	|-----------------------------------------------------------------------------------------------------------
	*/
	
	$("#Filter_Clear").click(function() {
        $('#Filter_minDate').val("");
	    $('#Filter_maxDate').val("");
	    $('#Filter_minTime').val("");
	    $('#Filter_maxTime').val("");
	    $('#Filter_Crime_Type').val("[ALL]");
	    $('#Filter_Crime_Type_sub option:not(:first)').remove();
	    $('#Filter_Crime_Type_sub').val("[ALL]");
	    $("#Filter_Location").prop("selectedIndex", 0);
	    $('#Filter_Location').prop('disabled', 'disabled');
	});
	
	var filter_marker_hold = [];
	var UK_center = new google.maps.LatLng(52.636879, -1.139759);
	
	var filter_marker = new google.maps.Marker({
                position: UK_center, 
                map: null
                });
    filter_marker_hold.push(filter_marker);
	
	$('#modal_filter').on('shown.bs.modal', function () {
	    $("#Filter_Location").prop("selectedIndex", 0);
	    $('#Filter_Location').prop('disabled', 'disabled');
	    
        var MapOptions = {
			center: UK_center,
			zoom: 6,
			disableDefaultUI: true, // Remove all controls but street view
			streetViewControl: true,
		};

		var map4 = new google.maps.Map(document.getElementById("map4"), MapOptions); // Show smaller map
		
		var marker_placed = false;
        
        google.maps.event.addListener(map4, 'click', function(event) {
		    if (marker_placed == false) {
		        var filter_marker = new google.maps.Marker({
                position: event.latLng, 
                map: map4
                });
                filter_marker_hold[0] = filter_marker;
                
                marker_placed = true;
                $('#Filter_Location').prop('disabled', false);
		    }
		    else
		    {
		        filter_marker_hold[0].setPosition(event.latLng);
		    }
        });
        
        var circle_placed = false;
        var circle_hidden = false;
        var circle_hold = [];
        var distance_val;
        
        $("#Filter_Location").on("change", function() {
            distance_val = $(this).val();
            
            if (distance_val == "[ALL]") {
                $('#Filter_Location').prop('disabled', 'disabled');
                if (circle_placed == true) {
                    circle_hold[0].setMap(null);
                    circle_hidden = true;
                }
                if (marker_placed == true) {
                    filter_marker_hold[0].setMap(null);
                    marker_placed = false;
                }
            }
            else 
            {
                if (circle_hidden == true) {
                    circle_hold[0].setMap(map4);
                }
                
                var f_marker = filter_marker_hold[0];
            
                if (circle_placed == false) {
                    var circle = new google.maps.Circle({
                    map: map4,
                    radius: 1,    // 10 miles in metres
                    fillColor: '#AA0000'
                    });
                
                    circle_hold.push(circle);
                    circle_placed = true;
                }
            
                circle_hold[0].bindTo('center', f_marker, 'position');
                circle_hold[0].setRadius(distance_val*1609); // Convert miles to metres
            }
            
            
        });
        
        $("#btn_filter_confirm").click(function() {
    	    ShowLoading();
    	    
    	    var f_marker = filter_marker_hold[0];
    	    var center_lat = f_marker.getPosition().lat();
    	    var center_lng = f_marker.getPosition().lng();
    	    var center_loc = new google.maps.LatLng(center_lat, center_lng);
    	    
    		FilterMarkers(center_loc, distance_val);
    		HideLoading();
	    });
		
    })
	
	/*
	|-----------------------------------------------------------------------------------------------------------
	| 'Analyse' - Crime Analysis Techniques
	|-----------------------------------------------------------------------------------------------------------
	*/
	
	var Cluster_Active = false; // Clusterer initialised as unactive
	const btn_analyse = document.getElementById("btn_marker_cluster"); // 'Analyse' button
	btn_analyse.addEventListener('click', event => {
		hideContextMenu();
		ShowLoading();
		
		if (Cluster_Active == true) { // If active and button was pressed
		    markerCluster.setMap(null); // Hide clusterer
		    Cluster_Active = false; // Alternate variable
		    HideLoading();
		}
		else {
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
	
	$('#modal_import').on('shown.bs.modal', function () {
	    if ($('#progress_file_upload').hasClass('progress-bar bg-danger progress-bar-striped progress-bar-animated')) { // If class was to changed to class used for showing errors
	        $("#progress_file_upload").attr('class', 'progress-bar progress-bar-striped progress-bar-animated'); // Change it back to default
	    }
	    $("#progress_file_upload").css("width", "0%");
	    
	    if ($('#progress_insert_upload').hasClass('progress-bar bg-danger progress-bar-striped progress-bar-animated')) {
	        $("#progress_insert_upload").attr('class', 'progress-bar progress-bar-striped progress-bar-animated');
	    }
	    $("#progress_insert_upload").css("width", "0%");
	});
	
    var isFileSelected = false;
	var isCSV = false;
	
	$("#Import_Input").on("change", function() {
	    isFileSelected = false;
	    isCSV = false;
	    
        files = this.files;
        
        if (files.length >= 1) { // If input has a file selected
            isFileSelected = true; // Toggle presence of file
            var fileName = files[0].name;
            var ext = fileName.substring(fileName.lastIndexOf('.') + 1).toLowerCase();
            
            if(ext == 'csv') {
                isCSV = true; // Toggle CSV check
            }
            
            $("#import_lbl").text(fileName); // Change label of input 
        }
        else {
            isFileSelected = false;
            $("#import_lbl").text("Choose file"); 
        }
 
    });

    $('#btn_import_confirm').on('click', function() { // Sending selected file to PHP file (to be handled)
    
        var t2 = performance.now();
    
        $('#btn_import_confirm').attr('disabled', true); // Disable import button
        $('#btn_import_close').attr('disabled', true); // Disable close button
        
        if($('#Import_Input').prop('files').length > 0 && (isCSV === true))
        {
            file = $('#Import_Input').prop('files')[0];
            
            var reader = new FileReader();
            reader.readAsText(file);
            
            reader.onload = function(event) {
                var Date_index = -1;
                var Latitude_index = -1;
                var Longitude_index = -1;
                var CrimeType_index = -1;
                var Description_index = -1;
                var Time_index = -1;
                
                var Accepted_Date_headers = ["Date", "date", "Month", "month"];
                var Accepted_Latitude_headers = ["Latitude", "latitude", "Lat", "lat"];
                var Accepted_Longitude_headers = ["Longitude", "longitude", "Long", "long", "Lng", "lng"];
                var Accepted_CrimeType_headers = ["Crime type", "Crime Type", "crime type", "CrimeType", "crimetype", "Type", "type"];
                var Accepted_Description_headers = ["Context", "context", "Description", "description", "Notes", "notes"];
                var Accepted_Time_headers = ["Time", "time", "Timestamp", "timestamp"];
                
                // Read file locally
                var csv = event.target.result;
                var rows = csv.split('\n');
                
                // Check headers
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
                    if (Accepted_Time_headers.indexOf(headers[i]) !== -1) {
                        Time_index = i;
                    }
                }
                
                var validFile = true;
                var import_err_str = "FILE IMPORT ERROR";
                
                var FileWarning = false;
                var import_warning_str = "WARNING";
                
                var Reached_Limit = false;
                
                if (Date_index === -1) {
                    import_warning_str = import_warning_str + "\nMissing 'Date' column in file (the current date will be used)";
                    FileWarning = true;
                }
                if (Latitude_index === -1) {
                    import_err_str = import_err_str + "\nMissing 'Latitude' column in file";
                    validFile = false;
                }
                if (Longitude_index === -1) {
                    import_err_str = import_err_str + "\nMissing 'Longitude' column in file";
                    validFile = false;
                }
                if (CrimeType_index === -1) {
                    import_warning_str = import_warning_str + "\nMissing 'Crime Type' column in file (the crime type 'Unknown' will be used)";
                    FileWarning = true;
                }
                if (Description_index === -1) {
                    import_warning_str = import_warning_str + "\nMissing 'Description' column in file (no description will be used)";
                    FileWarning = true;
                }
                if (Time_index === -1) {
                    import_warning_str = import_warning_str + "\nMissing 'Time' column in file (the current time will be used)";
                    FileWarning = true;
                }
                
                // Check number of rows
                var num_rows = rows.length;
                var num_records = num_rows - 1;
                
                if (num_records <= 0) {
                    validFile = false;
                }
                
                if ((MarkerArray.length + num_records) > 50000) {
                    Reached_Limit = true;
                }
                
                if (num_records > 7500) {
                    import_err_str = import_err_str + "\nOnly 7500 records can be imported at any one time\n(The selected file has " + num_records + " records)";
                    validFile = false;
                }
                
                if (validFile == true && Reached_Limit == false) {
                    if (FileWarning == true) {
                        alert(import_warning_str);
                    }
                    
                    $("#progress_file_upload").css("width", "100%").text("Ready");
                    formdata = new FormData();
                    formdata.append("fileToUpload", file);
                    
                    $.ajax({
                            // File upload progress
                            xhr: function() {
                                var xhr = new window.XMLHttpRequest();
                                xhr.upload.addEventListener("progress", function(evt) {
                                    if (evt.lengthComputable) {
                                        var percentComplete = evt.loaded / evt.total;
                                        var upload_percentage = percentComplete*100;
                                        if (upload_percentage = 100) {
                                            $("#progress_file_upload").css("width", "100%").text("File Upload (Processing)");
                                        }
                                        else {
                                            $("#progress_file_upload").css("width", Math.round(upload_percentage) + "%").text("File Upload (" + upload_percentage + "%)");
                                        }
                                        
                                    }
                               }, false);
                               return xhr;
                            },
                        
                			url: 'ImportMarkers.php',
                			type: 'POST',
                			data: formdata,
                			processData: false,
                            contentType: false,
                			success: function()
                			{
                			    $("#progress_file_upload").css("width", "100%").text("File Upload (Complete)");
                			},
                			fail: function()
                			{
                			    $("#progress_file_upload").attr('class', 'progress-bar bg-danger progress-bar-striped progress-bar-animated');
                                $("#progress_file_upload").css("width", "100%").text("File Upload (Failed)");
                			},
                			error: function()
                			{
                			    $("#progress_file_upload").attr('class', 'progress-bar bg-danger progress-bar-striped progress-bar-animated');
                                $("#progress_file_upload").css("width", "100%").text("File Upload (Error)");
                			}
                	});
                	
                	var NoChangeCounter = 0;
                	var FinishCheckCounter = 0;
                	var TimeoutCounter = 0;
                	var Timed_Out = 0;
                	var data_hold = -10;
                	
                	var t=setInterval(CheckProgressFile,1000); // Run below function every second
                	
                	function CheckProgressFile() {
                        $.ajax({
                            url: "/counts.txt",
                            cache: false,
                            async: false,
                            dataType: "text",
                            success: function( data, textStatus, jqXHR ) {
                                TimeoutCounter += 1;
                                var import_percentage = data;
                                
                                if (import_percentage == 0) {
                                    NoChangeCounter += 1;
                                }
                                
                                if (TimeoutCounter == 30 || import_percentage == "-1000" || NoChangeCounter == 5) { // Timeout, file upload error or no update
                                    clearInterval(t);
                                    // Show full width red progress bar
                                    $("#progress_insert_upload").attr('class', 'progress-bar bg-danger progress-bar-striped progress-bar-animated');
                                    $("#progress_insert_upload").css("width", "100%").text("Import (Failed)");
                                    Timed_Out = 1;
                                    $('#btn_import_confirm').attr('disabled', false);
                                    $('#btn_import_close').attr('disabled', false);
                                }
                                
                                if (Timed_Out == 0 && import_percentage != 0) {
                                    if (import_percentage == data_hold) {
                                        FinishCheckCounter += 1;
                                    }
                                            
                                    if (FinishCheckCounter == 3) {
                                        $("#progress_insert_upload").css("width", "100%").text("Import (Complete)");
                                    }
                                            
                                    if (FinishCheckCounter == 5) {
                         	               clearInterval(t);
                         	               
                         	               //var t3 = performance.now();
	                                       //console.log("LoadMarkers() duration: " + (t3-t2) + "ms");
                         	               
                    	                   ShowLoading();
                    	                   location.reload();
                                    }
     
                                    data_hold = import_percentage;
                                        
                                    if (FinishCheckCounter < 3) {
                                        $("#progress_insert_upload").css("width", Math.round(import_percentage) + "%").text("Import (" + Math.round(import_percentage) + "%)");
                                    }
                                }
                                    
                            }
                        });
                	}
                	
                }
                else {
                    if (num_records <=0) {
                        import_err_str += "\nNo records found in the file";
                    }
                    if (Reached_Limit == true) {
                        import_err_str += "\nImporting this file would exceed the limit of 50,000 markers";
                    }
                    
                    if (FileWarning == true) {
                        alert(import_err_str + "\n\n" + import_warning_str);
                    }
                    else {
                        alert(import_err_str); 
                    }
                    
                    $('#btn_import_confirm').attr('disabled', false);
                    $('#btn_import_close').attr('disabled', false);
                }
                
            }
        }
        else {
            if (isCSV === false) { // File of input is not a .csv file
                if (isFileSelected === false) { // And no file has been added
                    alert("No file has been selected for import");
                }
                else {
                    alert("The file is not a .csv file");
                }
            }
            
            $('#btn_import_confirm').attr('disabled', false);
            $('#btn_import_close').attr('disabled', false);
        }
    });
		
	/*
	|-----------------------------------------------------------------------------------------------------------
	| Search box implementation
	|-----------------------------------------------------------------------------------------------------------
	*/

    searchBox.addListener('places_changed', function() { // Selecting a prediction from the list
        var places = searchBox.getPlaces(); // Can be more than one place if using text-based geographic search

        if (places.length == 0) {
			return;
        }

        var bounds = new google.maps.LatLngBounds();
        places.forEach(function(place) {
			if (!place.geometry) {
				console.log("Returned place contains no geometry");
				return;
            }

            if (place.geometry.viewport) {
              // Only geocodes have viewport.
              bounds.union(place.geometry.viewport);
            } else {
              bounds.extend(place.geometry.location);
            }
          });
          map.fitBounds(bounds); // Move map to place location
        });
	
	}
	  
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/markerclustererplus/2.1.4/markerclusterer.js"></script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDDpgBmZOTCzsVewLlzsx77Y5bDUVS_MZg&libraries=geometry,places&callback=initMap" async defer> // API Key, Libraries and map function
</script>

<!-- Bootstrap Scripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> <!-- JQuery (Google CDN) -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<!-- On Page Load -->
<script>
    $(document).ready(function() {
        ShowLoading();
        
        function AddOptions(select,options) { /* Add parameter options to parameter select */
            for(var i = 0; i < options.length; i++) {
                    var opt = options[i];
                    var el = document.createElement("option");
                    el.textContent = opt;
                    el.value = opt;
                    select.appendChild(el);
                }
        }
        
        function AddLocationOptions(select,options) { /* Add parameter options to parameter select */
            for(var i = 0; i < options.length; i++) {
                    var opt = options[i];
                    var el = document.createElement("option");
                    el.textContent = "Within " + opt + " miles";
                    el.value = opt;
                    select.appendChild(el);
                }
        }
        
        var filter_loc = document.getElementById("Filter_Location");
        
        var loc_options = ["1","3","5","10","15","20","30","40","50","100","250"];
        
        /* Main category select elements */ 
        var add_select = document.getElementById("Add_Crime_Type");
        var filter_select = document.getElementById("Filter_Crime_Type");
        var edit_select = document.getElementById("Edit_Crime_Type");
        
        /* Subcategory select elements */ 
        var add_sub_select = document.getElementById("Add_Crime_Type_sub");
        var filter_sub_select = document.getElementById("Filter_Crime_Type_sub");
        var edit_sub_select = document.getElementById("Edit_Crime_Type_sub");
        
        var main_options = ["Violence against the person","Public Order","Drug offences","Vehicle offences","Sexual offences","Arson and criminal damage","Possession of weapons","Theft","Burglary","Robbery","Miscellaneous crimes against society","Other"]; 
        
        AddOptions(add_select,main_options);
        
        all_option = ["[ALL]"];
        
        AddOptions(filter_loc,all_option); // All at top
        
        var opt = "1/4";
        var el = document.createElement("option");
        el.textContent = "Within " + opt + " miles";
        el.value = 0.25;
        filter_loc.appendChild(el);
                    
        var opt = "1/2";
        var el = document.createElement("option");
        el.textContent = "Within " + opt + " miles";
        el.value = 0.5;
        filter_loc.appendChild(el);
        // Add 1/4 and 1/2 manually becuase their values are different

        AddLocationOptions(filter_loc,loc_options); // Rest of the options
        
        AddOptions(filter_select,all_option);
        AddOptions(filter_select,main_options);
        
        AddOptions(edit_select,main_options);
        
        violence_sub_options = ["Murder","Attempted Murder","Manslaughter","Conspiracy to murder","Threats to kill","Causing death or serious injury by dangerous driving", "Causing death by careless driving under the influence of drink or drugs","Causing death by careless or inconsiderate driving","Causing death or serious injury by driving (unlicensed driver)","Causing death by aggrevated vehicle taking","Corporate manslaughter","Assualt (with intent to cause serious harm)","Endangering life","Harassment","Racially or religiously aggravated harassment","Racially or religiously aggravated assualt with injury","Racially or religiously aggravated assualt without injury","Assualt with injury","Assualt without injury","Assualt with injury on a constable","Assualt without injury on a constable","Stalking","Maliciuos communications","Cruelty to Children/Young Persons","Child abduction","Procuring illegal abortion","Kidnapping","Modern Slavery"];
        
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
            $('#Add_Crime_Type_sub option:not(:first)').remove(); // Remove all but the default hidden value
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
            $('#Filter_Crime_Type_sub option:not(:first)').remove();
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
            $('#Edit_Crime_Type_sub option:not(:first)').remove();
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

</body>
</html>