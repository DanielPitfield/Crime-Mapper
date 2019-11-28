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
body {
margin:0;
height: 100%
width: 100%;
}

.icon-bar {
  width: 100%;
  background-color: #555;
  overflow: auto;
}

#MapBtn {
  float: left;
  width: 25%;
  border:none;
  text-align: center;
  padding: 10px 0;
  font-weight:bold;
  font-family:Arial;
  text-decoration:none;
  color: white;
  font-size: 20px;
  background-color: #555;
}

#MapBtn:hover {
  background-color: #000;
}

#MapBtn.active {
  background-color: #4CAF50;
}

#SigninBtn {
  float: right;
  width: 20%;
  border:none;
  text-align: center;
  padding: 10px 0;
  font-weight:bold;
  font-family:Arial;
  text-decoration:none;
  color: white;
  font-size: 20px;
  background-color: #555;
}

#SigninBtn:hover {
  background-color: #000;
}

#SigninBtn.active {
  background-color: #4CAF50;
}

#SettingsBtn {
  float: right;
  width: 5%;
  border:none;
  text-align: center;
  padding: 10px 0;
  font-weight:bold;
  font-family:Arial;
  text-decoration:none;
  color: white;
  font-size: 20px;
  background-color: #555;
}

#SettingsBtn:hover {
  background-color: #000;
}

#SettingsBtn.active {
  background-color: #4CAF50;
}

#map {
position:absolute;
height: 95%;
width: 100%;
}

#description {
        font-family: Roboto;
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
        font-family: Roboto;
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
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
      }

      #pac-input {
        background-color: #fff;
        font-family: Roboto;
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
	  
	  .custom_contextmenu_add {
		cursor: pointer;
		padding: 8px 15px;
		background-color: #4CAF50;
		
		font-weight:bold;
		font-family:Arial;
		text-decoration:none;
		color: white;
		font-size: 14px;
		text-align: center;
	  }
	  
	  .custom_contextmenu_add:hover {
		background-color: #000;
	  }
	  
	  .custom_contextmenu_view {
		cursor: pointer;
		padding: 8px 15px;
		background-color: #555;
		
		font-weight:bold;
		font-family:Arial;
		text-decoration:none;
		color: white;
		font-size: 14px;
		text-align: center;
	  }
	  
	  .custom_contextmenu_view:hover {
		background-color: #000;
	  }
	  
</style>

<body oncontextmenu="return false;">  <!-- Disable the default right click context menu for the body of the page -->

<div class="icon-bar">
   <a class="active" href="#" id="MapBtn"><i class="fa fa-map-marker"></i> Map</a> <!-- Tab/Page -->
   <input id="pac-input" class="controls" type="text" placeholder="Enter a town, city or postcode"> <!-- Search box -->
   <a href="settings.html" id="SettingsBtn"><i class="fa fa-cog"></i></a> 
   <a href="signin.html" id="SigninBtn"><i class="fa fa-sign-in"></i> Sign in</a>
</div>

<div id="map"></div>

<div class="custom_contextmenu" id="menu"> <!-- Context Menu -->
	<div class="custom_contextmenu_add" id="btn_add">Add crime</div>
	<div class="custom_contextmenu_view" id="btn_view">View region information</div>
</div>

<script>
	/*
	|-----------------------------------------------------------------------------------------------------------
	| Map functions, variables and elements
	|-----------------------------------------------------------------------------------------------------------
	*/
	
	function placeMarker(Location, map) {
		var marker = new google.maps.Marker({
		position: Location,
		map: map
		});
	}

	function initMap() {
		var ContextMenu = null;
		var menuDisplayed = false;
		var Latitude = 0;
		var Longitude = 0;
		
		var location = {lat: 51.454266, lng: -0.978130};
		var map = new google.maps.Map(document.getElementById("map"), {zoom: 8, center: location});
		
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
			Location = e.latLng // Hold position in global variable (for placing marker later)
			Latitude = Location.lat();
			Longitude = Location.lng();
			for (prop in e) {
				if (e[prop] instanceof MouseEvent) {
					mouseEvt = e[prop];
					var left = mouseEvt.clientX;
					var top = mouseEvt.clientY;
						
					ContextMenu = document.getElementById("menu");
					ContextMenu.style.left = (left+5) + "px"; // Small adjustment to its position (HARDCODED)
					ContextMenu.style.top = (top-30) + "px";
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

	const add_btn = document.querySelector('.custom_contextmenu_add'); // 'Add crime' button
	add_btn.addEventListener('click', event => {
		// Should probably first open a settings window for the crime (date, description)
		placeMarker(Location,map); // Just go striaght to showing marker for now
		
		$.ajax({
        url: 'SaveMarker.php',
        type: 'POST',
		// Ability to send other data (fom future settings window?)
		// New data must also be caught in SaveMarker.php
        data: {Latitude: Latitude, Longitude: Longitude},
        success: function (data)
		{
			// Can create an alert to confirm data has been sent
        }
	});

		hideContextMenu();
	});
		
	const view_btn = document.querySelector('.custom_contextmenu_view'); // 'View region information' button
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
	  
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDDpgBmZOTCzsVewLlzsx77Y5bDUVS_MZg&libraries=places&callback=initMap" async defer> <!-- API Key, Libraries and map function -->
</script>

</body>
</html> 
