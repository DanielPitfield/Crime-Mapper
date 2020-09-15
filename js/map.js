var MarkerArray = []; // Local array of marker objects

function convert_crimeTime(crimeTime) {
    if (crimeTime.length == 8) {
        return crimeTime.substring(0, crimeTime.length - 3);
    }
    else {
        return crimeTime;
    }
}

function convert_crimeDate(crimeDate) {
    return moment(crimeDate).format("DD-MM-YYYY");
}

/*
|-----------------------------------------------------------------------------------------------------------
| Adding a marker to the map
|-----------------------------------------------------------------------------------------------------------
*/

function placeMarker(marker, position, map) {
    const googleMapsMarker = new google.maps.Marker({
        title: '',
        position,
        map,
        ...marker,
    });
    MarkerArray.push(googleMapsMarker);

    googleMapsMarker.title = googleMapsMarker.crimeType; // Shown on hover

    google.maps.event.addListener(googleMapsMarker, 'click', function () { // Marker is clicked
        if (typeof (googleMapsMarker.info) !== "undefined") { // Marker has an InfoWindow
            if (googleMapsMarker.info.getMap() != null) { // And it is already open
                googleMapsMarker.info.close(); // Close it
            }
        }

        // Make a new InfoWindow
        googleMapsMarker.info = new google.maps.InfoWindow({
            content: '<div id="iw-container">' + '<div class="iw-content">' +
                '<b>ID: </b>' + googleMapsMarker.id + '<br> <b style="word-wrap: break-word;">Crime Type: </b>' + googleMapsMarker.crimeType + '<br> <b>Date: </b>' + convert_crimeDate(googleMapsMarker.crimeDate) +
                '<br><b>Time: </b>' + convert_crimeTime(googleMapsMarker.crimeTime) + '<br></br>' + '<i style="word-wrap: break-word;">' + googleMapsMarker.description + '</i>' +
                '<br></br> <button type="button" class="btn btn-secondary" style="width:50%;" onclick=EditMarker(' + googleMapsMarker.id + ')>Edit</button>' +
                '<button type="button" class="btn btn-danger" style="width:50%;" onclick=DeleteMarker(' + googleMapsMarker.id + ')>Delete</button>' + '</div>' + '</div>',
            minWidth: 200,
            maxWidth: 500
        });

        if (typeof (googleMapsMarker.info) !== "undefined") { // InfoWindow successfully created
            googleMapsMarker.info.open(map, googleMapsMarker); // Open this new InfoWindow
        }

    });
}

/*
|-----------------------------------------------------------------------------------------------------------
| UI Functionality
|-----------------------------------------------------------------------------------------------------------
*/

function ShowErrorAlert(message, anchor) {
    // Get the body associated with the specified modal content (anchor)
    const body = anchor.querySelector(".modal-body");

    const error_alert = document.getElementById("Alert_Error");
    error_alert.classList.remove("hidden");
    document.getElementById('Alert_Error_Message').innerHTML = message;

    // Move the error alert inside the body of the modal
    body.insertAdjacentElement("beforeend", document.getElementById("Alert_Error"));
}

function HideErrorAlert() {
    const error_alert = document.getElementById("Alert_Error");
    error_alert.classList.add("hidden");
}

function ShowWarningAlert(message, anchor) {
    const body = anchor.querySelector(".modal-body");
    const warning_alert = document.getElementById("Alert_Warning");
    warning_alert.classList.remove("hidden");
    document.getElementById('Alert_Warning_Message').innerHTML = message;

    body.insertAdjacentElement("beforeend", document.getElementById("Alert_Warning"));
}

function HideWarningAlert() {
    const warning_alert = document.getElementById("Alert_Warning");
    warning_alert.classList.add("hidden");
}

function ShowProgressAlert() {
    Progress_Alert = document.getElementById("Alert_Progress");
    Progress_Alert.style.left = "34%";
    Progress_Alert.style.top = "500px";
    Progress_Alert.style.display = "block";
}

function HideProgressAlert() {
    Progress_Alert = document.getElementById("Alert_Progress");
    Progress_Alert.style.left = "-500px";
    Progress_Alert.style.top = "-500px";
    Progress_Alert.style.display = "none";
}

document.querySelectorAll('#close_alert_error, #close_add, #close_filter, #close_edit, #close_import')
    .forEach(el => el.addEventListener("click", () => HideErrorAlert()));

document.querySelectorAll('#close_import, #close_alert_warning')
    .forEach(el => el.addEventListener("click", () => HideWarningAlert()));

document.getElementById('close_alert_progress').addEventListener("click", () => {
    HideProgressAlert();
});

document.getElementById('Filter_Clear').addEventListener("click", () => {
    document.querySelectorAll('#Filter_ID, #Filter_minDate, #Filter_maxDate, #Filter_minTime, #Filter_maxTime')
        .forEach(el => el.value = ""); // Clear all fields (if filter criteria is requested to be cleared)

    document.querySelectorAll('#Filter_Crime_Type, #Filter_Crime_Type_sub')
        .forEach(el => el.value = "[ALL]"); // The two dropdowns have a default hidden value of [ALL]

    // All options except this hidden default value should be removed from the sub-dropdown
    document.querySelectorAll('Filter_Crime_Type_sub option:not(:first-child)').forEach(el => el.remove());
    // Reset the element that provides the functionality for filtering by location
    document.getElementById('Filter_Location').setAttribute("selectedIndex", 0);
    document.getElementById('Filter_Location').setAttribute('disabled', true);
});

/*
|-----------------------------------------------------------------------------------------------------------
| Editing a marker
|-----------------------------------------------------------------------------------------------------------
*/

function UpdateMarkerInfo(marker) {
    marker.title = marker.crimeType; // Shown on hover

    marker.info.setContent('<div id="iw-container">' + '<div class="iw-content">' +
        '<b>ID: </b>' + marker.id + '<br> <b style="word-wrap: break-word;">Crime Type: </b>' + marker.crimeType + '<br> <b>Date: </b>' + convert_crimeDate(marker.crimeDate) +
        '<br><b>Time: </b>' + convert_crimeTime(marker.crimeTime) + '<br></br>' + '<i style="word-wrap: break-word;">' + marker.description + '</i>' +
        '<br></br> <button id="btn_edit" type="button" class="btn btn-secondary" style="width:50%;" onclick=EditMarker(' + marker.id + ')>Edit</button>' +
        '<button type="button" class="btn btn-danger" style="width:50%;" onclick=DeleteMarker(' + marker.id + ')>Delete</button>' + '</div>' + '</div>');
}

function LoadCurrentValues(marker) {
    // Find which main category of crime that marker.crimeTime belongs to
    const foundMappingEdit = crimeTypeMappings.find(x => x.options.includes(marker.crimeType));

    if (foundMappingEdit) { // Selected crime type from dropdowns
        // Re-enable dropdown fields (if they were previously disabled)
        document.getElementById('Edit_Crime_Type').removeAttribute('disabled');
        document.getElementById('Edit_Crime_Type_sub').removeAttribute('disabled');

        // Set main category of crime (the first dropdown) to found value
        document.getElementById('Edit_Crime_Type').value = foundMappingEdit.value;
        // Invoke event so that the dropdown for the subcategory updates (updates when value is changed not just when set)
        var event = new Event('change');
        document.getElementById('Edit_Crime_Type').dispatchEvent(event);
    }
    else { // Imported crime type
        document.getElementById('Edit_Crime_Type').value = 'Other';
        // Add option which hasn't been added as a default option for the 'Other' category
        AddOptions(edit_sub_select, [MarkerToEdit.crimeType]);

        // Disable both dropdown fields to prevent the imported type being lost
        document.getElementById('Edit_Crime_Type').setAttribute('disabled', true);
        document.getElementById('Edit_Crime_Type_sub').setAttribute('disabled', true);
    }

    // Set the fields for the remaining properties
    document.getElementById('Edit_Crime_Type_sub').value = marker.crimeType;
    document.getElementById('Edit_Date').value = marker.crimeDate;
    document.getElementById('Edit_Time').value = convert_crimeTime(marker.crimeTime);
    document.getElementById('Edit_Description').value = marker.description;
}

function EditMarker(id) {
    const MarkerToEdit = MarkerArray.find(marker => marker.id === id); // Find marker to edit by ID
    MarkerToEdit.info.close(); // Close marker's info window (as the information it holds may change)

    // Determine and display the current values for the marker
    LoadCurrentValues(MarkerToEdit);

    // Now show the modal with this information
    $('#modal_edit').modal('show');

    // Set up smaller map in 'Edit Crime' window to allowing for adjutsing the location of the marker
    const EditMapOptions = {
        center: MarkerToEdit.position,
        zoom: 12,
        disableDefaultUI: true, // Remove all controls but street view
        streetViewControl: true,
    };

    const edit_map = new google.maps.Map(document.getElementById("edit_map"), EditMapOptions);

    var Draggable_marker_edit = new google.maps.Marker({ // Add a single draggable marker to smaller map
        position: MarkerToEdit.position,
        draggable: true,
        map: edit_map
    });

    var Edit_SmallMarkerMoved = false;
    // Record geographical information of marker 
    const Edit_FirstLocation = MarkerToEdit.position;
    var Edit_Latitude = Edit_FirstLocation.lat();
    var Edit_Longitude = Edit_FirstLocation.lng();

    // Record where the marker is moved to (if an adjustment is made)
    google.maps.event.addListener(Draggable_marker_edit, 'dragend', function (evt) {
        var SecondLocation = evt.latLng;
        // Record the new geographical information
        Edit_Latitude = SecondLocation.lat();
        Edit_Longitude = SecondLocation.lng();
        Edit_SmallMarkerMoved = true;
    });

    // Edit modal confirmation
    document.getElementById('edit_submit_form').addEventListener("submit", function (e) {
        e.preventDefault();

        const description_edit = document.getElementById('Edit_Description').value;
        const validDescription = description_edit.length <= 500;

        if (validDescription) {
            var formData = $('#edit_submit_form').serialize(); // Collate data from form
            var Vars = { id: id, Latitude: Edit_Latitude, Longitude: Edit_Longitude }; // Additional two variables
            var varsData = $.param(Vars); // Convert to format to send as part of bundle
            var data = formData + '&' + varsData; // Combine all information as one bundle

            ShowLoading();

            $.ajax({
                url: 'EditMarkers.php',
                type: 'POST',
                data: data,
                success: function (result) {
                    const dropdown = document.getElementById('Edit_Crime_Type_sub');

                    // Update values locally (the marker's properties)
                    MarkerToEdit.crimeDate = document.getElementById('Edit_Date').value;
                    MarkerToEdit.crimeTime = document.getElementById('Edit_Time').value;
                    MarkerToEdit.crimeType = dropdown.options[dropdown.selectedIndex].value;
                    MarkerToEdit.description = document.getElementById('Edit_Description').value;

                    if (Edit_SmallMarkerMoved == true) { // If adjustment made on smaller map
                        MarkerToEdit.position = SecondLocation; // Set the newly recorded position
                        Edit_SmallMarkerMoved = false;
                    }

                    MarkerToEdit.setPosition(MarkerToEdit.position); // Update the displayed location
                    UpdateMarkerInfo(MarkerToEdit); // Prepare InfoWindow with new information
                    HideLoading();
                    $('#modal_edit').modal('hide');
                    HideErrorAlert();
                }
            });
        }
        else {
            const edit_err_string = "The 'Description' field can only be a maximum of 500 characters<br>";
            ShowErrorAlert(edit_err_string, document.getElementById('modal_edit_content'));
        }
    });
}

/*
|-----------------------------------------------------------------------------------------------------------
| Deleting a single marker
|-----------------------------------------------------------------------------------------------------------
*/

function DeleteMarker(id) {
    ShowLoading();

    const MarkerToDelete = MarkerArray.find(marker => marker.id === id); // Find object/marker
    const MarkerToDelete_index = MarkerArray.findIndex(marker => marker.id === id); // Find index

    if (MarkerToDelete) { // Object found
        // Close InfoWindow (so it isn't left behind/open after the marker is deleted)
        if (typeof (MarkerToDelete.info) !== "undefined") {
            if (MarkerToDelete.info.getMap() != null) {
                MarkerToDelete.info.close();
            }
        }
        // Send ID to be used in DELETE statement
        $.ajax({
            url: 'DeleteMarker.php',  // Delete from Database
            type: 'POST',
            data: { id: id },
            success: function (data) {
                MarkerToDelete.setVisible(false); // Delete from View
                if (MarkerToDelete_index) {
                    if (MarkerToDelete_index !== -1) { // Index found
                        MarkerArray.splice(MarkerToDelete_index, 1); // Delete from Array
                    }

                }
            }
        });
    }
    HideLoading();
}

/*
|-----------------------------------------------------------------------------------------------------------
| Deleting multiple markers
|-----------------------------------------------------------------------------------------------------------
*/

// TODO Delete mulitiple markers - multi-user interaction

function ResetProgressBar(progress_bar) {
    if (progress_bar.contains('progress-bar bg-danger progress-bar-striped progress-bar-animated')) { // If class was to changed to class used for showing errors
        progress_bar.setAttribute('class', 'progress-bar progress-bar-striped progress-bar-animated'); // Change it back to default animated class
    }
    progress_bar.style.width = "0%"; // Also reset progress
}

document.getElementById('Delete_Filtered_Markers').addEventListener("click", () => {
    const progress_delete = document.getElementById('progress_delete');
    ResetProgressBar(progress_delete);

    const visibleMarkers = MarkerArray.filter(marker => marker.getVisible()); // Array of visible markers
    const visibleMarkers_IDs = visibleMarkers.map(marker => marker.id); // Array of ids for these visible markers

    const num_markers = visibleMarkers_IDs.length;
    const within_marker_capacity = num_markers > 0 && num_markers < 50000;

    if (within_marker_capacity) { // If manageable amount of markers to delete
        $('#modal_filter').modal('hide');
        ShowProgressAlert();

        // Set position of alert
        const alert_progress = document.getElementById('alert_progress');
        alert_progress.style.top = document.getElementById('modal_filter_content').offsetTop + (document.getElementById('modal_filter_content').height / 2);
        alert_progress.style.left = document.getElementById('modal_filter_content').offsetLeft;
        alert_progress.style.width = document.getElementById('modal_filter_content').width;

        $.ajax({
            url: 'DeleteMarker.php',
            type: 'POST',
            data: { visibleMarkers_IDs: visibleMarkers_IDs }, // Send IDs
            success: function (data) {
                //
            }
        });
    }
    else {
        HideProgressAlert();
        const filter_delete_string = (num_markers == 0) ? "There are no visible or filtered markers to delete<br>" :
            "Only 50,000 markers can be deleted at once, refine the filter to select fewer markers<br>";
        ShowErrorAlert(filter_delete_string, document.getElementById('modal_filter_content'));
    }
});

/*
|-----------------------------------------------------------------------------------------------------------
| Google Map Setup
|-----------------------------------------------------------------------------------------------------------
*/

function initMap() {
    const initial_location = { lat: 51.454266, lng: -0.978130 };
    // Main map object
    const map = new google.maps.Map(document.getElementById("map"), { zoom: 8, center: initial_location });

    const ContextMenu = document.getElementById("menu");

    /*
    |-----------------------------------------------------------------------------------------------------------
    | Location Search Bar Implementation
    |-----------------------------------------------------------------------------------------------------------
    */

    const input = document.getElementById('pac-input'); // Create a text input
    const searchBox = new google.maps.places.SearchBox(input); // Link it to search bar element

    searchBox.addListener('places_changed', function () { // Selecting a prediction from the list
        const places = searchBox.getPlaces(); // Can be more than one place if using text-based geographic search

        if (places.length == 0) {
            return;
        }

        const bounds = new google.maps.LatLngBounds();
        places.forEach(function (place) {
            if (!place.geometry) {
                return;
            }

            if (place.geometry.viewport) {
                bounds.union(place.geometry.viewport); // Only geocodes have viewport
            } else {
                bounds.extend(place.geometry.location);
            }
        });
        map.fitBounds(bounds); // Move map to place location
    });

    /*
    |-----------------------------------------------------------------------------------------------------------
    | Placing markers (from database)
    |-----------------------------------------------------------------------------------------------------------
    */

    function LoadMarkers() {
        markers.forEach(marker =>
            // Placing the markers stored in the database
            placeMarker(marker, new google.maps.LatLng(marker.latitude, marker.longitude), map)
        );
    }

    LoadMarkers();
    HideLoading(); // Matching HideLoading() for ShowLoading() in setup.js

    /*
    |-----------------------------------------------------------------------------------------------------------
    | Marker Clustering
    |-----------------------------------------------------------------------------------------------------------
    */

    // Setup
    const clusterStyles = [
        {
            textColor: 'white',
            url: '/cluster_images/SmallCluster.png',
            height: 53,
            width: 53
        },
        {
            textColor: 'white',
            url: '/cluster_images/MediumCluster.png',
            height: 56,
            width: 56
        },
        {
            textColor: 'white',
            url: '/cluster_images/LargeCluster.png',
            height: 66,
            width: 66
        }
    ];

    const mcOptions = {
        gridSize: 50,
        styles: clusterStyles,
        maxZoom: 15
    };

    const markerCluster = new MarkerClusterer(null, MarkerArray, mcOptions);
    markerCluster.setIgnoreHidden(true); // Object will not cluster markers which are hidden

    google.maps.event.addListener(markerCluster, 'clusteringstart', ShowLoading);
    google.maps.event.addListener(markerCluster, 'clusteringend', HideLoading);

    // Invocation
    var Cluster_Active = false; // Clusterer flagged as off to begin with
    document.getElementById("btn_analyse").addEventListener('click', event => {
        hideContextMenu();
        ShowLoading();

        if (Cluster_Active == true) { // If already active and 'Analyse Crime' button was pressed
            markerCluster.setMap(null); // Turn off clustering
            Cluster_Active = false;
        }
        else { // Turn on clustering
            markerCluster.addMarkers(MarkerArray); // Update markers to cluster
            markerCluster.setMap(map);
            markerCluster.repaint(); // Redraw and show clusterer
            Cluster_Active = true;
        }

        HideLoading();
    });

    /*
    |-----------------------------------------------------------------------------------------------------------
    | Filtering Markers
    |-----------------------------------------------------------------------------------------------------------
    */

    function HideMarker(marker) {
        // InfoWindow
        if (typeof (marker.info) !== "undefined") {
            if (marker.info.getMap() != null) {
                marker.info.close();
            }
        }
        // Marker
        marker.setVisible(false);
    }

    // Displaying and recording information regarding filtering by location
    let filter_marker;
    let search_area;

    $('#modal_filter').on('shown.bs.modal', function () { // When filter modal opens
        // Set location filtering element back to its default value (serach radius of [ALL])
        document.getElementById('Filter_Location').selectedIndex = "0";
        document.getElementById('Filter_Location').setAttribute('disabled', true);

        // Map
        const UK_center = new google.maps.LatLng(52.636879, -1.139759);
        const FilterMapOptions = { // Provide smaller map to specify the center of a geographical area
            center: UK_center,
            zoom: 6,
            disableDefaultUI: true,
            streetViewControl: true,
        };

        const filter_map = new google.maps.Map(document.getElementById("filter_map"), FilterMapOptions);

        google.maps.event.addListener(filter_map, 'click', function (event) {
            if (!filter_marker.getVisible()) { // If the marker is not yet shown on the map
                document.getElementById('Filter_Location').removeAttribute('disabled');
                filter_marker.setVisible(true); // Show the marker
            }
            filter_marker.setPosition(event.latLng); // Just move the marker if it is already on the map
        });

        // Marker
        filter_marker = new google.maps.Marker({ // Assign a marker which can't initially be seen (to filter map)
            position: null,
            visible: false,
            map: filter_map
        });

        // Circle object used to display the currently selected area to filter by
        search_area = new google.maps.Circle({
            map: filter_map,
            visible: false,
            radius: 1,
            fillColor: '#AA0000'
        });

        // When the search radius is selected/changed in the relevant dropdown
        document.getElementById('Filter_Location').addEventListener("change", (event) => {
            const search_radius = event.target.value;

            const AllAreas = (search_radius == "[ALL]") || (search_radius == null);
            if (!AllAreas) { // If a quantifiable (numeric) value for search radius is chosen
                if (filter_marker.getVisible()) { // And a center point of the search area is specified
                    // Update the circle object to this center point with a radius of the search radius
                    search_area.setVisible(true);
                    search_area.bindTo('center', filter_marker, 'position');
                    search_area.setRadius(search_radius * 1609); // Convert miles to metres
                }
            }
            else {
                // If [ALL] option is selected, hide the marker and area
                document.getElementById('Filter_Location').setAttribute('disabled', true);
                search_area.setVisible(false);
                filter_marker.setVisible(false);
            }
        });
    });

    // Filter by ID
    document.getElementById('ID_Search').addEventListener("click", () => {
        const Filter_ID = document.getElementById('Filter_ID').value;
        const isEmpty = Filter_ID == "";
        if (!isEmpty) {
            // Identify marker with requested ID
            const MarkerToShow = MarkerArray.find(marker => marker.id == Filter_ID);

            // Hide all markers
            MarkerArray.forEach(marker =>
                HideMarker(marker)
            );

            // Show identified marker
            if (MarkerToShow) {
                MarkerToShow.setVisible(true);
            }
        }
    });

    function FilterMarkers() {
        ShowLoading();

        // Main Crime Type
        const main_dropdown = document.getElementById("Filter_Crime_Type");
        const Main_Crime_Type = main_dropdown.options[main_dropdown.selectedIndex].value;
        // If field is empty or the all option is implicitly selected, set flag to true
        const AllCrimes = (Main_Crime_Type == null) || (Main_Crime_Type == "[ALL]");

        // Sub Crime Type
        const sub_dropdown = document.getElementById("Filter_Crime_Type_sub");
        const Sub_Crime_Type = sub_dropdown.options[sub_dropdown.selectedIndex].value;
        const AllSubCrimes = (Sub_Crime_Type == null) || (Sub_Crime_Type == "[ALL]");

        // Date
        const min_Date = document.getElementById("Filter_minDate").value;
        const max_Date = document.getElementById("Filter_maxDate").value;

        const min_Date_Entered = min_Date.length > 0;
        const max_Date_Entered = max_Date.length > 0;
        const both_Dates_Entered = min_Date_Entered && max_Date_Entered;

        if (min_Date_Entered) {
            min_Date = new Date(min_Date);
        }

        if (max_Date_Entered) {
            max_Date = new Date(max_Date);
        }

        // Time
        const min_Time = document.getElementById("Filter_minTime").value;
        const max_Time = document.getElementById("Filter_maxTime").value;

        const both_Times_Entered = min_Time.length > 0 && max_Time.length > 0;
        const single_Time_Entered = min_Time.length > 0 || max_Time.length > 0;

        const errorConditions =
            [
                {
                    isMet: both_Dates_Entered && min_Date > max_Date,
                    errorMessage: "The 'Minimum Date' field can't be a date after the 'Maximum Date' field"
                },
                {
                    isMet: both_Times_Entered && min_Time > max_Time,
                    errorMessage: "The 'Minimum Time' can't be a time after the 'Maximum Time' field"
                },
                {
                    isMet: single_Time_Entered,
                    errorMessage: "Both the 'Minimum Time' and 'Maximum Time' fields are required"
                }
            ];

        // Get all error conditions which have been met
        const metErrorConditions = errorConditions.filter(x => x.isMet);

        // If any error conditions are met
        if (metErrorConditions.length > 0) {
            ShowErrorAlert(metErrorConditions.map(x => x.errorMessage).join("<br>"), document.getElementById('modal_filter_content'));
            return;
        }

        // Remove any previous filters
        MarkerArray.forEach(marker =>
            marker.setVisible(true)
        );

        // Before filtering the markers, determine if the filter criteria includes location
        const location_filtering = filter_marker.getVisible() && search_area.getVisible();

        let filter_center;
        let search_radius;

        // If so, determine the center point and search radius
        if (location_filtering) {
            // LatLng object of marker added to smaller map (center of search area)
            filter_center = new google.maps.LatLng(filter_marker.getPosition().lat(), filter_marker.getPosition().lng());
            search_radius = document.getElementById("Filter_Location").value;
        }

        // Filter the markers which need to be hidden
        const Filtered_MarkerArray = MarkerArray.filter(marker => {
            // The properties which are less computationally expensive to filter by should come first
            if (min_Date_Entered) {
                if (new Date(marker.crimeDate) < min_Date) {
                    return true;
                }
            }

            if (max_Date_Entered) {
                if (new Date(marker.crimeDate) > max_Date) {
                    return true;
                }
            }

            if (both_Times_Entered) {
                if (marker.crimeTime < min_Time || marker.crimeTime > max_Time) {
                    return true;
                }
            }

            /*
            More computationally expensive to filter by crime type
            But will not be reached if the marker was filtered out by date or time (above)
            */

            if (!AllCrimes) {
                if (!AllSubCrimes) { // One specific crime
                    if (marker.crimeType != Sub_Crime_Type) {
                        return true;
                    }
                }
                else { // A (main) category of crime
                    // Find the array (of individual crimes) that corresponds to this main category
                    const foundMappingFilter = crimeTypeMappings.find(x => Main_Crime_Type == x.value);
                    if (foundMappingFilter) {
                        // If the marker's crime type is not found within this array (the category)
                        if (foundMappingFilter.options.includes(marker.crimeType) === false) {
                            return true;
                        }
                    }
                }
            }

            if (location_filtering) { // Filter criteria included location
                // Shortest distance between search area center and the current marker
                const distanceInMetres = google.maps.geometry.spherical.computeDistanceBetween(filter_center, marker.getPosition());
                const distanceInMiles = (distanceInMetres / 1609); // Convert to miles

                if (distanceInMiles > search_radius) { // If marker is outside of search area
                    return true;
                }
            }

        });

        // Hide the markers identified
        Filtered_MarkerArray.forEach(marker => HideMarker(marker));

        $('#modal_filter').modal('hide');
        HideErrorAlert();
        HideLoading();
    }

    document.getElementById('btn_filter_confirm').addEventListener("click", () => {
        FilterMarkers();
    });

    /*
    |-----------------------------------------------------------------------------------------------------------
    | Custom right click context menu
    |-----------------------------------------------------------------------------------------------------------
    */

    // Move off visible screen and set display property to none
    function hideContextMenu() {
        if (ContextMenu.style.display == "block") {
            ContextMenu.style.left = -500 + "px";
            ContextMenu.style.top = -500 + "px";
            ContextMenu.style.display = "none";
        }
    }

    var Add_Latitude;
    var Add_Longitude;

    // Record location of this click in terms of on the map and on the screen
    map.addListener('rightclick', function (e) {
        // Map (Latitude and Longitude)
        var Add_FirstLocation = e.latLng; // Record click location as latLng object
        Add_Latitude = Add_FirstLocation.lat();
        Add_Longitude = Add_FirstLocation.lng();

        // Context Menu (pixels)
        for (prop in e) {
            if (e[prop] instanceof MouseEvent) {
                // Record click location
                mouseEvt = e[prop];
                const left = mouseEvt.clientX;
                const top = mouseEvt.clientY;

                // Show Context Menu at this location (actually very slightly beside)
                ContextMenu.style.left = (left + 1) + "px";
                ContextMenu.style.top = (top - 1) + "px";
                ContextMenu.style.display = "block";
            }
        }
    });

    // Close the context menu when an implicit action away from it is made
    ['click', 'drag'].forEach(evt =>
        map.addListener(evt, hideContextMenu)
    );

    /*
    |-----------------------------------------------------------------------------------------------------------
    | Adding a crime/marker
    |-----------------------------------------------------------------------------------------------------------
    */

    $('#modal_add').on('shown.bs.modal', function () {
        document.querySelectorAll('#Add_Crime_Type', '#Add_Crime_Type_sub', '#Add_Description')
            .forEach(el => el.value = "");
        document.getElementById('Add_Date').value = new Date().toISOString().split("T")[0];
        document.getElementById('Add_Time').value = "00:00";
        document.querySelectorAll('Add_Crime_Type_sub option:not(:first-child)').forEach(el => el.remove());
    });

    var SmallMarkerMoved = false;

    document.getElementById("btn_add").addEventListener('click', event => {
        hideContextMenu();

        $('#modal_add').modal('show');

        const CurrentZoom = map.getZoom(); // Get zoom level when add button was clicked
        const RefinedZoom = CurrentZoom + 1; // Enhance zoom level by one level

        const AddMapOptions = {
            center: FirstLocation,
            zoom: RefinedZoom,
            disableDefaultUI: true,
            streetViewControl: true,
        };

        const add_map = new google.maps.Map(document.getElementById("add_map"), AddMapOptions); // Show smaller map

        var Draggable_marker_add = new google.maps.Marker({ // Add a single draggable marker to smaller map
            position: FirstLocation,
            draggable: true,
            map: add_map
        });

        // Record position of marker if an adjustment is made
        google.maps.event.addListener(Draggable_marker_add, 'dragend', function (evt) {
            var Add_SecondLocation = evt.latLng;
            Add_Latitude = Add_SecondLocation.lat();
            Add_Longitude = Add_SecondLocation.lng();
            SmallMarkerMoved = true;
        });

        // TODO 3D View (adding markers in street view)
    });

    document.getElementById('add_submit_form').addEventListener("submit", function (e) {
        e.preventDefault();

        const dropdown = document.getElementById("Add_Crime_Type");
        const sub_dropdown = document.getElementById("Add_Crime_Type_sub"); // Initial step of getting crimeType

        const Crime_Category = dropdown.options[dropdown.selectedIndex].value;

        // Record values entered into input fields
        const crimeDate = document.getElementById("Add_Date").value;
        const crimeTime = document.getElementById("Add_Time").value;
        const crimeType = sub_dropdown.options[sub_dropdown.selectedIndex].value;
        const description = document.getElementById("Add_Description").value;

        const errorConditions =
            [
                {
                    isMet: Crime_Category == "",
                    errorMessage: "The 'Crime Type - Main Category' field is a required field"
                },
                {
                    isMet: crimeType == "",
                    errorMessage: "The 'Crime Type - Subcategory' field is a required field"
                },
                {
                    isMet: description.length > 500,
                    errorMessage: "The 'Description' field can only be a maximum of 500 characters"
                }
            ];

        const metErrorConditions = errorConditions.filter(x => x.isMet);

        if (metErrorConditions.length > 0) {
            ShowErrorAlert(metErrorConditions.map(x => x.errorMessage).join("<br>"), document.getElementById('modal_add_content'));
            return;
        }
        else {
            var formData = $('#add_submit_form').serialize(); // Collate data from form
            var Vars = { Latitude: Add_Latitude, Longitude: Add_Longitude }; // Additional two variables
            var varsData = $.param(Vars); // Convert to format to send as part of bundle
            var data = formData + '&' + varsData; // Combine all information as one bundle

            ShowLoading();

            $.ajax({
                url: 'SaveMarkers.php',
                type: 'POST',
                data: data,
                success: function (id) {
                    if (!SmallMarkerMoved) {
                        // No adjustment was made so use the map location where the context menu to add a crime was requested
                        placeMarker({ id: parseInt(id), crimeType, crimeDate, crimeTime, description }, FirstLocation, map);
                    }
                    else {
                        // An adjutsment was made, so use the map location of where the draggable marker was at the time of submit
                        placeMarker({ id: parseInt(id), crimeType, crimeDate, crimeTime, description }, SecondLocation, map);
                    }

                    SmallMarkerMoved = false; // Reset for the next time a marker is added
                    HideLoading();
                    $('#modal_add').modal('hide');
                    HideErrorAlert();
                },
                fail: function () {
                    HideLoading();
                }
            });
        }
    });

    /*
    |-----------------------------------------------------------------------------------------------------------
    | Importing crimes
    |-----------------------------------------------------------------------------------------------------------
    */

    // TODO Readibility/Comments for the 'Import Crime' module (below)
    $('#modal_import').on('shown.bs.modal', function () {
        const progress_file_upload = document.getElementById('progress_file_upload');
        const progress_insert_upload = document.getElementById('progress_insert_upload');

        ResetProgressBar(progress_file_upload);
        ResetProgressBar(progress_insert_upload)
    });

    var isFileSelected;
    var isCSV;

    document.getElementById('Import_Input').addEventListener("change", () => {
        isFileSelected = false;
        isCSV = false;

        const files = document.getElementById('Import_Input').files;
        const file_label = document.getElementById('import_lbl');

        if (files.length >= 1) { // If input has a file selected
            isFileSelected = true; // Toggle presence of file
            const fileName = files[0].name;
            const ext = fileName.substring(fileName.lastIndexOf('.') + 1).toLowerCase();

            if (ext == 'csv') {
                isCSV = true; // Toggle CSV check
            }

            file_label.innerHTML = fileName; // Change label of input to filename
        }
        else {
            isFileSelected = false;
            file_label.innerHTML = "Choose file"; // Set back to default text
        }

    });

    function ShowUploadError() {
        const progress_file_upload = document.getElementById('progress_file_upload');
        progress_file_upload.setAttribute('class', 'progress-bar bg-danger progress-bar-striped progress-bar-animated');
        progress_file_upload.style.width = "100%";
        progress_file_upload.innerHTML = "File Upload (Fail/Error)";
    }

    document.getElementById('btn_import_confirm').addEventListener('click', () => { // Sending selected file to PHP file (to be handled)
        HideErrorAlert();
        HideWarningAlert();

        document.querySelectorAll('#btn_import_confirm, #close_import')
            .forEach(el => el.setAttribute('disabled', true)); // Disable both these buttons during import process

        if (document.getElementById('Import_Input').files.length > 0 && (isCSV === true)) {
            file = document.getElementById('Import_Input').files[0];

            const progress_file_upload = document.getElementById('progress_file_upload');

            formdata = new FormData();
            formdata.append("ImportFile", file);

            $.ajax({
                // Progress of sending file to server (file upload progress)
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            // Get file upload progress
                            var upload_percentage = (evt.loaded / evt.total) * 100;

                            // Update progress bar width and text using progress
                            progress_file_upload.style.width = Math.round(upload_percentage) + "%";
                            progress_file_upload.style.innerHTML = "File Upload (" + upload_percentage + "%)";

                            if (upload_percentage == 100) { // Use 'Complete' text instead of 100% on completion
                                progress_file_upload.style.innerHTML = "File Upload (Complete)";
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
                fail: function () {
                    ShowUploadError();
                },
                error: function () {
                    ShowUploadError();
                }
            });

            // Get Job ID in above POST request

            // Every 1 second, getTimeout()
            $.ajax({
                url: 'GetImportProgress.php',
                type: 'POST',
                // Use Job ID as data paramater in this request
                data: jobID,
                success: function (response) {
                    console.log(response);
                }

            });

            // TODO Display of the progress of adding records to database (from an imported file)

            /*
            (ImportMarkers.php)
            Return job ID to the client
            */

            /*
            (map.js)
            Recieve job ID
            Periodically query the database using job ID to determine the progress (GetImportProgress.php endpoint)
            Update the progress bar
            */
        }
        else {
            const selectfile_err_string = (!isFileSelected) ? "No file has been selected for import" : "The file is not a .csv file";
            ShowErrorAlert(selectfile_err_string, document.getElementById('modal_import_content'));
        }

        document.querySelectorAll('#btn_import_confirm, #close_import')
            .forEach(el => el.removeAttribute('disabled')); // Re-enable these buttons after handling an import
    });
}