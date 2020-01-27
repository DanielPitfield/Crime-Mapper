<?php 
require 'dbConfig.php'; // Include the database configuration file
?>

<!DOCTYPE html>
<html>

<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> <!-- JQuery (Google CDN) -->
</head>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">  <!-- For navigation bar icons -->
<link rel="stylesheet" href="layout.css">  <!-- Everything else -->

<body oncontextmenu="return false;">  <!-- Disable the default right click context menu for the body of the page -->

<!-- Navigation/Icon Bar -->
<div class="icon-bar">
   <a class="IconBarBtn Map active" href="#"><i class="fa fa-map-marker"></i> Map</a> <!-- Tab/Page -->
   <input id="pac-input" class="controls" type="text" placeholder="Enter a town, city or postcode"> <!-- Search box -->
   <a href="signin.html" class="IconBarBtn Signin"><i class="fa fa-sign-in"></i></a>
   <a class="IconBarBtn Settings"><i class="fa fa-cog"></i> Options</a> 
</div>

<!-- Map -->
<div id="map"></div>

<!-- Context Menu -->
<div class="custom_contextmenu" id="menu">
	<div class="custom_contextmenu_btn add" id="btn_add">Add crime</div>
	<div class="custom_contextmenu_btn" id="btn_filter">Filter</div>
	<div class="custom_contextmenu_btn" id="btn_analyse">Analyse</div>
</div>

<div id="modal_add" class="modal"> 
  <div class="modal-content">
    <div class="modal-header">
      <span class="close">&times;</span>
      <h2>Add Crime</h2>
    </div>
    <div class="modal-body">
      <form name="submit_form" id="submit_form" action="SaveMarkers.php" method="post">
	    <div id="map2"></div>
		Date:
		<input type="date" name="Date" min="1970-01-01" value="<?php echo date("Y-m-d"); ?>" max="<?php echo date("Y-m-d"); ?>" required>
		Time:
		<input type="time" name="Time" value="00:00" required>
		<br></br>
		Type:
		<select id="Add_Crime_Type" name="Crime_Type">
		<option value="Arson">Arson</option>
		<option value="Murder">Murder</option>
		<option value="Anti-social Behaviour">Anti-social Behaviour</option>
		</select>
		<br></br>
		<textarea id="description" name="Description" rows="10" cols="37"></textarea>
		<button type="submit" id="btn_add_confirm" class="submit_button">Confirm</button>
		
		<!-- Log when crime is added (crime reported) -->
		<!-- Range of time (toggle?) -->
		<!-- ID -->		
	  </form>
    </div>
  </div>
</div>

<div id="modal_filter" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <span class="close">&times;</span>
      <h2>Filter</h2>
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
		<button id="btn_filter_confirm" class="submit_button">Confirm</button>
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
	
	function placeMarker(ID,Crime_Type,Date_Time,Description,CenterLocation,map) {
		var marker = new google.maps.Marker({
		ID: ID,
		Crime_Type: Crime_Type,
		Date_Time: Date_Time,
		Description: Description,
		position: CenterLocation,
		map: map
		});
		MarkerArray.push(marker);

		google.maps.event.addListener(marker, 'click', function() {
			// Should open small context menu with red option to 'Delete' first, then do the following...
			// Delete from ...
			marker.setVisible(false); // View

			var index = MarkerArray.indexOf(marker);
			if (index !== -1) MarkerArray.splice(index, 1); // Array
			
			var MarkerID = marker.ID;
			//alert(MarkerID);

			$.ajax({
				url: 'DeleteMarker.php',  // Database
				type: 'POST',
				data: {MarkerID: MarkerID},
				success: function(data)
				{
					//
				}
			});
		});
	}

	function initMap() {
		var ContextMenu = null;
		var menuDisplayed = false;
		var Latitude = 0;
		var Longitude = 0;
		
		var initial_location = {lat: 51.454266, lng: -0.978130};
		var map = new google.maps.Map(document.getElementById("map"), {zoom: 8, center: initial_location});
		
		// Create the search box and link it to the UI element.
		var input = document.getElementById('pac-input');
		var searchBox = new google.maps.places.SearchBox(input);
		<!-- map.controls[google.maps.ControlPosition.LEFT].push(input); -->

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
					$raw_date = $row['Date_Time'];
					$date = new DateTime($raw_date);
					$timestamp = date_timestamp_get($date); 
					/* 
					JS Array can't hold datetimeobject, use timestamp instead
					Also easier for filtering (comparisons)
					Can be converted back when displaying date and time to user
					*/

					echo '['.$row['ID'].',"'.$row['Crime_Type'].'",';
					echo $timestamp;
					echo ',"'.$row['Description'].'",'.$row['Latitude'].','.$row['Longitude'].'],';
				} 
			} 
			?>
		];

		for( i = 0; i < markers.length; i++ ) { // Placing the markers stored in the database
			var ID = markers[i][0];
			var Crime_Type = markers[i][1];
			var Date_Time = markers[i][2];
			console.log(Date_Time);
			var Description = markers[i][3];
			var Point = new google.maps.LatLng(markers[i][4], markers[i][5]);
			placeMarker(ID,Crime_Type,Date_Time,Description,Point,map);		
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
		}
		
		// Also by ID or last x/10/100 crimes?
		// Also by date (after date or range of dates)
		
		for(i = 0; i < MarkerArray.length; i++){
			MarkerArray[i].setVisible(true); // Remove any previous filters by showing all markers
		}	
		
		for(i = 0; i < MarkerArray.length; i++){
			var MarkerDate = moment(MarkerArray[i].Date_Time * 1000).add(1, 'hours').format("YYYY-MM-DD"); // Convert date
			MarkerDate = new Date(MarkerDate);
			
			var MarkerTime = moment(MarkerArray[i].Date_Time * 1000).add(1, 'hours').format("HH:mm"); // Convert time
			
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
			FirstLocation = e.latLng; // Initial position specified
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

	var modal_add = document.getElementById("modal_add");
	var span_add = document.getElementsByClassName("close")[0];
	var SmallMarkerMoved = false;
			
	const add_btn = document.getElementById("btn_add"); // 'Add crime' button
	add_btn.addEventListener('click', event => {
		hideContextMenu();
		modal_add.style.display = "block"; // Show input window
		
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
			Latitude = evt.latLng.lat(); // Information to be sent
			Longitude = evt.latLng.lng();
			SecondLocation = evt.latLng; // To be used to place static marker on main map
			SmallMarkerMoved = true;
		});
			
		// 3D View (adding markers in street view)
	});
	
	$("#submit_form").submit(function(e) {
		e.preventDefault();
					
		var dropdown = document.getElementById("Add_Crime_Type"); // Initial step of getting crime type
		
		/* Take values locally */	
		var Crime_Type = dropdown.options[dropdown.selectedIndex].value;
		var Description = document.getElementById("description").value;

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
				//alert(result); // Result is the id being returned
				if (SmallMarkerMoved == true) {
					placeMarker(result,Crime_Type,Description,SecondLocation,map); // Place a static marker on the main map
				}
				else {
					placeMarker(result,Crime_Type,Description,FirstLocation,map); // Place a static marker on the main map
				}
				SmallMarkerMoved = false;
				modal_add.style.display = "none"; // Close input window
			}
			
		});
	});

	span_add.onclick = function() { // Close button for add crime input window
		modal_add.style.display = "none";
	}
	
	/*
	|-----------------------------------------------------------------------------------------------------------
	| 'Filter' input window
	|-----------------------------------------------------------------------------------------------------------
	*/
	
	var modal_filter = document.getElementById("modal_filter");
	var span_filter = document.getElementsByClassName("close")[1];
	
	const filter_btn = document.getElementById("btn_filter"); // 'Filter' button
	filter_btn.addEventListener('click', event => {
		hideContextMenu();
		modal_filter.style.display = "block";
		
		$("#btn_filter_confirm").click(function() {
			// maxDate and maxTime must be larger than their min counterparts
			// Check values wheh confirmed or limit element when one of values is chosen?
			FilterMarkers();
			modal_filter.style.display = "none";	
		});
	});
	
	span_filter.onclick = function() {
		modal_filter.style.display = "none";
	}
	
	/*
	|-----------------------------------------------------------------------------------------------------------
	| 'View region information' information window
	|-----------------------------------------------------------------------------------------------------------
	*/
	
	const analyse_btn = document.getElementById("btn_analyse"); // 'View region information' button
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

</body>
</html> 
