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

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">  <!-- For navigation bar icons -->
<link rel="stylesheet" href="layout.css">  <!-- Everything else -->

<body oncontextmenu="return false;">  <!-- Disable the default right click context menu for the body of the page -->

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
  <!-- Logo -->
  <a class="navbar-brand" href="#">Crime Mapper</a>
  
  <!-- <input id="pac-input" class="controls" type="text" placeholder="Enter a town, city or postcode"> <!-- Search box -->

  <ul class="navbar-nav">
    
    <!-- Map Dropdown -->
    <li class="col-8 px-1">
        <button class="btn btn-outline-primary btn-block dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Map<sub><i class="fa fa-angle-down" aria-									hidden="true"></i></sub></button>
        <div class="dropdown-menu w-100">
        	<button class="dropdown-item disabled" type="button">Add Crime</button>
        	<button type="button" class="dropdown-item" data-toggle="modal" data-target="#modal_filter">Filter</button>
        </div>
    </li>
    
    <!-- Analyse Dropdown -->
    <li class="col-8 px-1">
        <button class="btn btn-outline-primary btn-block dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Analyse<sub><i class="fa fa-angle-down" aria-								hidden="true"></i></sub></button>
        <div class="dropdown-menu w-100">
        	<button class="dropdown-item" id="btn_analyse" type="button">MarkerClusterer</button>
        	<button class="dropdown-item disabled" type="button">Clustering</button>
        </div>
    </li>
    
    <!-- Predict Dropdown -->
    <li class="col-8 px-1">
        <button class="btn btn-outline-primary btn-block dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Predict<sub><i class="fa fa-angle-down" aria-								hidden="true"></i></sub></button>
        <div class="dropdown-menu w-100">
        	<button class="dropdown-item disabled" type="button">Warning</button>
        	<button class="dropdown-item disabled" type="button">RTM</button>
        </div>
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
		Date (from):
		<input type="date" id="Filter_minDate" min="1970-01-01" value="" max="<?php echo date("Y-m-d"); ?>">
		(to):
		<input type="date" id="Filter_maxDate" min="1970-01-01" value="" max="<?php echo date("Y-m-d"); ?>">
		<br></br>
		Time (from):
		<input type="time" id="Filter_minTime" value="">
		(to):
		<input type="time" id="Filter_maxTime" value="">
		<br></br>
		Type:
		<select id="Filter_Crime_Type" name="Crime_Type">
		<option value="All">All</option>
		<option value="Arson">Arson</option>
		<option value="Murder">Murder</option>
		<option value="Anti-social Behaviour">Anti-social Behaviour</option>
		</select>
		<br></br>
		<button id="btn_filter_confirm" class="submit_button">Confirm</button>
	   </div>
    </div>
  </div>
</div>

<!-- Add/edit crime modal -->
<div class="modal fade bd-example-modal-xl" data-backdrop="false" tabindex="-1" role="dialog" id="modal_add_edit">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
		<h5 class="modal-title">Title</h5>
		<button type="button" class="close" data-dismiss="modal">
			<span>&times;</span>
		</button>
	   </div>
	   <div class="modal-body">
		<form name="submit_form" id="submit_form" action="SaveMarkers.php" method="post">
		Date:
		<input id="Add_Date" type="date" name="Date" min="1970-01-01" value="<?php echo date("Y-m-d"); ?>" max="<?php echo date("Y-m-d"); ?>" required>
		Time:
		<input id="Add_Time" type="time" name="Time" value="00:00" required>
		<br></br>
		Type:
		<select id="Add_Crime_Type" name="Crime_Type">
		<option value="Arson">Arson</option>
		<option value="Murder">Murder</option>
		<option value="Anti-social Behaviour">Anti-social Behaviour</option>
		</select>
		</br></br>
		<textarea id="Add_Description" name="Description" rows="3" cols="50"></textarea>
		<div id="map2"></div>
		<button type="submit" id="btn_add_confirm" class="submit_button">Button</button>
		</form>
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
            MarkerTime = Crime_Time.substring(0, MarkerTime.length - 3); // Remove the seconds for display purposes
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
	
	function EditMarker(ID) {
		
		for(i = 0; i < MarkerArray.length; i++){
			if (MarkerArray[i].ID == ID)
				var MarkerToEdit = MarkerArray[i]; // Get actual marker
				var index = i; // Position in MarkerArray
		}
		
		var modal = $('#modal_add_edit');
		modal.find('.modal-title').text('Edit Crime');
		modal.find('.submit_button').text('Update');
		
		modal.find('#Add_Crime_Type').val(MarkerToEdit.Crime_Type);
		modal.find('#Add_Date').val(MarkerToEdit.Crime_Date);
		modal.find('#Add_Time').val(MarkerToEdit.Crime_Time);
		modal.find('#Add_Description').val(MarkerToEdit.Description);
		
		modal.modal('show');
		
		var EditMapOptions = {
			center: MarkerToEdit.position,
			zoom: 10,
			disableDefaultUI: true, // Remove all controls but street view
			streetViewControl: true,
		};

		var map2 = new google.maps.Map(document.getElementById("map2"), EditMapOptions); // Show smaller map

		var Draggable_marker = new google.maps.Marker({ // Add a single draggable marker to smaller map
		position: MarkerToEdit.position,
		draggable: true,
		map: map2
		});
		
		// Record if marker moved
		// Update in database
		//MarkerToEdit.setVisible(false);
		//if (index !== -1) MarkerArray.splice(index, 1);
		// Place new marker (adds back to view and array)
		
		// $("#modal_add_edit").modal('hide');
	}
	
	function DeleteMarker(ID) { // Refactor to use marker not marker.ID (saves looping through entire marker array to get marker from ID)
		
		for(i = 0; i < MarkerArray.length; i++){
			if (MarkerArray[i].ID == ID)
				var MarkerToDelete = MarkerArray[i]; // Get actual marker
				var index = i; // Position in MarkerArray
		}
		
		MarkerToDelete.info.close(); // Close infowindow
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
		
		var initial_location = {lat: 51.454266, lng: -0.978130};
		var map = new google.maps.Map(document.getElementById("map"), {zoom: 8, center: initial_location});
		
		/*
		// Create the search box and link it to the UI element.
		var input = document.getElementById('pac-input');
		var searchBox = new google.maps.places.SearchBox(input);
		//map.controls[google.maps.ControlPosition.LEFT].push(input);

		// Bias the SearchBox results towards current map's viewport.
		map.addListener('bounds_changed', function() {
			searchBox.setBounds(map.getBounds());
		});
		*/
	
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
	
	function FilterMarkers() {
		
		var AllSelected = false;
		var isMinDate = true;
		var isMaxDate = true;
		var isMinTime = true;
		var isMaxTime = true;	
		
		/* ---- Crime Type ---- */
		var dropdown = document.getElementById("Filter_Crime_Type");
		
		if (dropdown.options[dropdown.selectedIndex].value == "All") {
			console.log("All selected");
			AllSelected = true;
		}
		else {
			var Crime_Type = dropdown.options[dropdown.selectedIndex].value;
		}
		
		/* ---- Date ---- */
		if (document.getElementById("Filter_minDate").value == "") {
			console.log("No minimum date was entered");
			isMinDate = false;
		}
		else {
			minDate = document.getElementById("Filter_minDate").value;
			minDate = new Date(minDate);
		}
		
		if (document.getElementById("Filter_maxDate").value == "") {
			console.log("No maximum date was entered");
			isMaxDate = false;
		}
		else {
			maxDate = document.getElementById("Filter_maxDate").value;
			maxDate = new Date(maxDate);
		}
		
		/* ---- Time ---- */
		if (document.getElementById("Filter_minTime").value == "") {
			console.log("No minimum time was entered");
			isMinTime = false;
		}
		else {
			var minTime = document.getElementById("Filter_minTime").value;
		}
		
		if (document.getElementById("Filter_maxTime").value == "") {
			console.log("No maximum time was entered");
			isMaxTime = false;
		}
		else {
			var maxTime = document.getElementById("Filter_maxTime").value;
			maxTime = maxTime + ":00"; // MarkerTime has seconds, not an issue for minTime but will hide on boundary of maxTime
		}
		
		// Also by ID or last x/10/100 crimes?
		// Also by date (after date or range of dates)
		
		for(i = 0; i < MarkerArray.length; i++){
			MarkerArray[i].setVisible(true); // Remove any previous filters by showing all markers
		}	
		
		for(i = 0; i < MarkerArray.length; i++){
			var MarkerDate = moment(MarkerArray[i].Crime_Date).format("YYYY-MM-DD"); // Convert date
			MarkerDate = new Date(MarkerDate);
			
			var MarkerTime = MarkerArray[i].Crime_Time;
			console.log("MarkerTime: ", MarkerTime);
			
			/* These date and time values should be assigned to the markers to prevent the conversion
			   for all markers taking place each time a filter is requested */
			   
			/* This way the conversion is only performed once for each marker 
			   regardless of how many times a filter is requested */
			
			
			if (AllSelected == false) { // If a specific crime was selected
				if (MarkerArray[i].Crime_Type != Crime_Type) { // And the marker's crime type is not the same as the one selected
					MarkerArray[i].setVisible(false); // Hide it
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
		
		var modal = $('#modal_add_edit');
		modal.find('.modal-title').text('Add crime');
		modal.find('.submit_button').text('Confirm');		
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
	
	$("#submit_form").submit(function(e) {
		e.preventDefault();
					
		var dropdown = document.getElementById("Add_Crime_Type"); // Initial step of getting crime type
		
		/* Take values locally */
		var Crime_Date = document.getElementById("Add_Date").value;
		console.log("Date: ", Crime_Date);
		var Crime_Time = document.getElementById("Add_Time").value;
		console.log("Time: ", Crime_Time);
		var Crime_Type = dropdown.options[dropdown.selectedIndex].value;
		console.log("Type: ", Crime_Type);
		var Description = document.getElementById("Add_Description").value;
		console.log("Description: ", Description);

		/* Also send to database */	
		var formData = $("#submit_form").serialize();
		
		var Vars = {Latitude: Latitude, Longitude: Longitude};
		var varsData = $.param(Vars);

		var data = formData + '&' + varsData;

		$.ajax({
			url: 'SaveMarkers.php',
			type: 'POST',
			data: data,
			success: function(result)
			{
				alert("ID: ", result); // Result is the id being returned
				if (SmallMarkerMoved == true) {
					placeMarker(result,Crime_Type,Crime_Date,Crime_Time,Description,SecondLocation,map); // Place a static marker on the main map
				}
				else {
					placeMarker(result,Crime_Type,Crime_Date,Crime_Time,Description,FirstLocation,map); // Place a static marker on the main map
				}
				SmallMarkerMoved = false;
				$("#modal_add_edit").modal('hide');
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
		$("#modal_filter").modal('hide');
		/* maxDate and maxTime must be larger than their min counterparts
		Check values wheh confirmed or limit element when one of values is chosen? */
	});
	
	/*
	|-----------------------------------------------------------------------------------------------------------
	| 'Analyse' - Crime Analysis Techniques
	|-----------------------------------------------------------------------------------------------------------
	*/
	
	const analyse_btn = document.getElementById("btn_analyse"); // 'Analyse' button
	analyse_btn.addEventListener('click', event => {
		hideContextMenu();
		
		for(i = 0; i < MarkerArray.length; i++){
			if (MarkerArray[i].getVisible() == true) { // If the marker is shown on the map (unfiltered)
				FilteredMarkerArray.push(MarkerArray[i]); // Add it to a new array
			}
		}
		
		// Use this new array of markers and apply a MarkerClusterer to them
		// Configure its options here
		var markerCluster = new MarkerClusterer(map, FilteredMarkerArray,
            {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
	});		
		
	/*
	|-----------------------------------------------------------------------------------------------------------
	| Search box implementation
	|-----------------------------------------------------------------------------------------------------------
	*/

	/*
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
	*/
	
	}
	
	/*
	|-----------------------------------------------------------------------------------------------------------
	| Main Drop Down Menu (Mapping, Analysis and Prevention) 
	|-----------------------------------------------------------------------------------------------------------
	*/
	
	// Analysis methods should only be applied to markers that have their visible property as true (i.e markers that have not been filtered out)
	  
</script>

<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"> <!-- Marker Clusterer -->
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDDpgBmZOTCzsVewLlzsx77Y5bDUVS_MZg&libraries=places&callback=initMap" async defer> <!-- API Key, Libraries and map function -->
</script>

<!-- Bootstrap Scripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> <!-- JQuery (Google CDN) -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>
</html>