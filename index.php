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

<style>
html, body {
margin:0;
height: 100%
width: 100%;
font-family: Arial;
}

.icon-bar {
  width: 100%;
  background-color: #555;
  overflow: auto;
}

.IconBarBtn {
  border: none;
  text-align: center;
  padding: 10px 0;
  font-weight:bold;
  font-family:Arial;
  text-decoration:none;
  color: white;
  font-size: 20px;
  background-color: #555;
}

.IconBarBtn:hover {
  background-color: #000;
}

.IconBarBtn.active {
  background-color: #4CAF50;
}

.Map {
  float: left;
  width: 25%;	
}

.Signin {
  float: right;
  width: 5%;
}

.Settings {
  float: right;
  width: 20%;
}

#map {
  position:absolute;
  height: calc(100% - 45px);
  width: 100%;
}


#map2 {
  float:right;
  width:55%;
  height:270px;  
}

.submit_button {
  background-color: #4CAF50;
  color: white;
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  cursor: pointer;
  width: 100%;
  font: bold 15px Arial;
}

.submit_button:hover {
  opacity: 0.8;
}

#description {
        font-family: Arial;
        font-size: 15px;
        font-weight: 300;
      }

      #infowindow-content .title {
        font-weight: bold;
      }

      #infowindow-content {
        display: none;
      }

      #map #infowindow-content {
        display: inline;
      }

      .pac-card {
        margin: 10px 10px 0 0;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        background-color: #fff;
        font-family: Arial;
      }

      #pac-container {
        padding-bottom: 12px;
        margin-right: 12px;
      }

      .pac-controls {
        display: inline-block;
        padding: 5px 11px;
      }

      .pac-controls label {
        font-family: Arial;
        font-size: 13px;
        font-weight: 300;
      }

      #pac-input {
        background-color: #fff;
        font-family: Arial;
        font-size: 15px;
        font-weight: 300;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 48.5%;
		height: 40px;
		text-align: center;
      }

      #pac-input:focus {
        border-color: #4d90fe;
      }

      #title {
        color: #fff;
        background-color: #4d90fe;
        font-size: 25px;
        font-weight: 500;
        padding: 6px 12px;
      }
	  
      #target {
        width: 345px;
      }
	  
.custom_contextmenu {
  background-color: white;
  position: fixed;
  width: 10%;
  display: none;
}
	  
.custom_contextmenu_btn {
  cursor: pointer;
  padding: 8px 15px;		
  font-weight:bold;
  font-family:Arial;
  text-decoration:none;
  color: white;
  font-size: 14px;
  text-align: center;
}

.custom_contextmenu_btn:hover {
  background-color: #000;
}

.add {
  background-color: #4CAF50;
}

.view {
  background-color: #555;
}

.modal {
  display: none;
  position: fixed;
  z-index: 1;
  padding-top: 100px;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgb(0,0,0);
  background-color: rgba(0,0,0,0.4);
}


.modal-content {
  position: relative;
  background-color: #fefefe;
  margin: auto;
  padding: 0;
  border: 1px solid #888;
  width: 40%;
  box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
  -webkit-animation-name: animatetop;
  -webkit-animation-duration: 0.4s;
  animation-name: animatetop;
  animation-duration: 0.4s
}

@-webkit-keyframes animatetop {
  from {top:-300px; opacity:0} 
  to {top:0; opacity:1}
}

@keyframes animatetop {
  from {top:-300px; opacity:0}
  to {top:0; opacity:1}
}

.close {
  color: white;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}

.modal-header {
  padding: 2px 18px;
  background-color: #5cb85c;
  color: white;
}

.modal-body {padding: 14px 16px;}

input[type=date], input[type=time], select {
 width:17.5%;
 height:25px;
 font-family:Arial;
 font-size:18px;
 background-color: grey;
 outline: none;
 border: 0;
 border-radius: 3px;
 padding: 5px;
 color: white;
}

select {
	width:37%;
	padding: 0px;
}

input[type=time] {
	width:10%;
}

textarea {
   resize: none;
   background-color: grey;
}	

input[type="date"]::-webkit-calendar-picker-indicator { opacity:1; }
input[type="date"]::-webkit-inner-spin-button,
input[type="date"]::-webkit-outer-spin-button,       
input[type="date"]::-webkit-clear-button { -webkit-appearance: none;display: none; }

input[type="time"]::-webkit-inner-spin-button,
input[type="time"]::-webkit-outer-spin-button { opacity:1; } 
input[type="time"]::-webkit-clear-button { -webkit-appearance: none;display: none; }
	  
</style>

<body oncontextmenu="return false;">  <!-- Disable the default right click context menu for the body of the page -->

<!-- Navigation/Icon Bar -->
<div class="icon-bar">
   <a class="IconBarBtn Map active" href="#"><i class="fa fa-map-marker"></i> Map</a> <!-- Tab/Page -->
   <input id="pac-input" class="controls" type="text" placeholder="Enter a town, city or postcode"> <!-- Search box -->
   <a href="signin.html" class="IconBarBtn Signin"><i class="fa fa-sign-in"></i></a>
   <a href="settings.html" class="IconBarBtn Settings"><i class="fa fa-cog"></i> Options</a> 
</div>

<!-- Map -->
<div id="map"></div>

<!-- Context Menu -->
<div class="custom_contextmenu" id="menu">
	<div class="custom_contextmenu_btn add" id="btn_add">Add crime</div>
	<div class="custom_contextmenu_btn view" id="btn_view">View region information</div>
</div>

<!-- The Modal -->
<div id="myModal" class="modal">

  <!-- Modal content -->  
  <div class="modal-content">
    <div class="modal-header">
      <span class="close">&times;</span>
      <h2>Add Crime</h2>
    </div>
    <div class="modal-body">
      <form action="SaveMarkers.php" method="post">
	    <div id="map2"></div>
		Date:
		<input type="date" name="Date" value="<?php echo date("Y-m-d"); ?>" max="<?php echo date("Y-m-d"); ?>" required>
		Time:
		<input type="time" name="Time" value="00:00" required>
		<br></br>
		Type:
		<select name="Crime_Type">
		<option value="Arson">Arson</option>
		<option value="Murder">Murder</option>
		<option value="Anti-social Behaviour">Anti-social Behaviour</option>
		</select>
		<br></br>
		<textarea id="description" name="Description" rows="10" cols="37">
		</textarea>
		<button type="submit" id="btn_confirm" class="submit_button">Confirm</button>
		
		<!-- Log when crime is added (crime reported) -->
		<!-- Range of time (toggle?) -->
		<!-- ID -->

		<!-- Input information on left side is sent to action_page.php -->
		
	  </form>
    </div>
  </div>
</div>

<script>
	/*
	|-----------------------------------------------------------------------------------------------------------
	| Map functions, variables and elements
	|-----------------------------------------------------------------------------------------------------------
	*/
	
	function placeMarker(CenterLocation, map) {
		var marker = new google.maps.Marker({
		position: CenterLocation,
		map: map
		});
	}
	
	function placeDraggableMarker(CenterLocation, map) {
		var marker = new google.maps.Marker({
		position: CenterLocation,
		draggable: true,
		map: map
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
		
	var markers = [
		<?php 
		$result = $db->query("SELECT Latitude, Longitude FROM markers"); // Returns output of statement
		if($result->num_rows > 0){ 
			while($row = $result->fetch_assoc()){ 
				echo '['.$row['Latitude'].', '.$row['Longitude'].'],'; 
			} 
		} 
		?>
	];

	for( i = 0; i < markers.length; i++ ) { // Placing the markers stored in the database
		var Point = new google.maps.LatLng(markers[i][0], markers[i][1]);
		// [Marker Number, Latitude],[Marker Number, Longitude]
		placeMarker(Point,map);		
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
	
	var modal = document.getElementById("myModal");
	var span = document.getElementsByClassName("close")[0];

	const add_btn = document.getElementById("btn_add"); // 'Add crime' button
	add_btn.addEventListener('click', event => {
		hideContextMenu();
		modal.style.display = "block"; // Show input window
		
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
		});
			
		// 3D View (adding markers in street view)
	});
	
	const confirm_btn = document.getElementById("btn_confirm"); // Confirm button for form
	confirm_btn.addEventListener('click', event => {		
		$.ajax({ // Send locational information to be stored into database
        url: 'SaveMarkers.php',
        type: 'POST',
        data: {Latitude: Latitude, Longitude: Longitude},
        success: function (data)
		{
			// Can create an alert to confirm data has been sent
        }
	});
		placeMarker(SecondLocation,map); // Place a static marker on the main map
	});

	span.onclick = function() { // Close button for add crime input window
		modal.style.display = "none";
	}
		
	const view_btn = document.getElementById("btn_view"); // 'View region information' button
	view_btn.addEventListener('click', event => {
		alert("View button clicked");
		hideContextMenu();
		// Functionality
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
	  
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDDpgBmZOTCzsVewLlzsx77Y5bDUVS_MZg&libraries=places&callback=initMap" async defer> <!-- API Key, Libraries and map function -->
</script>

</body>
</html> 