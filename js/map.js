var MarkerArray = []; // Local array of marker objects
const iconBase = 'crime_icons/'; // Start of file path for marker icons
var isDefaultIcon = false; // All markers begin with custom (not default) icons

/*
|-----------------------------------------------------------------------------------------------------------
| Adding a marker object to the map
|-----------------------------------------------------------------------------------------------------------
*/

function placeMarker(marker, position, map) {
    const googleMapsMarker = new google.maps.Marker({
        title: '',
        //animation: google.maps.Animation.DROP,
        position,
        map,
        ...marker,
    });
    MarkerArray.push(googleMapsMarker);

    googleMapsMarker.title = googleMapsMarker.crimeType; // Shown on hover
    UpdateMarkerIcon(googleMapsMarker);

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

function UpdateMarkerIcon(marker) {
    var iconPath = null; // Intialise as null (which is used to specify the default marker icon)

    // Determine the main category of crime the marker being created belongs to
    const foundMappingCrimeCategory = crimeTypeMappings.find(x => x.options.includes(marker.crimeType));

    if (foundMappingCrimeCategory) {
        if (foundMappingCrimeCategory.image_path != null) {
            iconPath = iconBase + foundMappingCrimeCategory.image_path; // Construct complete image path
        }
    }

    marker.setIcon(iconPath);
}

// Toggles between using custom icons and the default icon
function ToggleIcons() {
    MarkerArray.forEach(marker => {
        if (isDefaultIcon) { // If markers are currently all the default icon
            UpdateMarkerIcon(marker); // Update marker icons to custom icons
        }
        else {
            marker.setIcon(null); // Update marker icons to default icon
        }
    });
    isDefaultIcon = !isDefaultIcon; // Alternate boolean flag
}

// Removes seconds from a given time
function convert_crimeTime(crimeTime) {
    if (crimeTime.length == 8) {
        return crimeTime.substring(0, crimeTime.length - 3);
    }
    else {
        return crimeTime;
    }
}

// Formats a given date into UK date format
function convert_crimeDate(crimeDate) {
    return moment(crimeDate).format("DD-MM-YYYY");
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
    const progress_alert = document.getElementById("Alert_Progress");
    progress_alert.classList.remove("hidden");
    progress_alert.style.display = "block";
}

function HideProgressAlert() {
    const progress_alert = document.getElementById("Alert_Progress");
    progress_alert.classList.add("hidden");
}

function ResetProgressBar(progress_bar) {
    // Change progress bar back to default animated class   
    progress_bar.setAttribute('class', 'progress-bar progress-bar-success');
    progress_bar.style.width = "0%"; // Reset progress 
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
| 'Edit Crime' (editing a marker)
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

    if (foundMappingEdit) { // A crime type which can be selected from dropdowns
        // Re-enable dropdown fields (if they were previously disabled)
        document.getElementById('Edit_Crime_Type').removeAttribute('disabled');
        document.getElementById('Edit_Crime_Type_sub').removeAttribute('disabled');

        // Set main category of crime (the first dropdown) to found value
        document.getElementById('Edit_Crime_Type').value = foundMappingEdit.value;
        // Invoke event so that the dropdown for the subcategory updates (updates when value is changed not just when set)
        const event = new Event('change');
        document.getElementById('Edit_Crime_Type').dispatchEvent(event);
    }
    else { // Imported crime type
        document.getElementById('Edit_Crime_Type').value = 'Other';
        // Add option which hasn't been added as a default option for the 'Other' category
        AddOptions(edit_sub_select, [marker.crimeType]);

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

    const Draggable_marker_edit = new google.maps.Marker({ // Add a single draggable marker to smaller map
        position: MarkerToEdit.position,
        //animation: google.maps.Animation.DROP,
        draggable: true,
        map: edit_map
    });
    //Draggable_marker_edit.setAnimation(google.maps.Animation.BOUNCE);

    var Edit_SmallMarkerMoved = false;

    // Record geographical information of marker 
    const Edit_FirstLocation = MarkerToEdit.position;
    let SecondLocation;

    var Edit_Latitude = Edit_FirstLocation.lat();
    var Edit_Longitude = Edit_FirstLocation.lng();

    // Record where the marker is moved to (if an adjustment is made)
    google.maps.event.addListener(Draggable_marker_edit, 'dragend', function (evt) {
        SecondLocation = evt.latLng;
        // Record the new geographical information
        Edit_Latitude = SecondLocation.lat();
        Edit_Longitude = SecondLocation.lng();
        Edit_SmallMarkerMoved = true;
    });

    // Edit modal confirmation
    document.getElementById('edit_submit_form').onsubmit=("submit", function (e) {
        e.preventDefault();

        const description_edit = document.getElementById('Edit_Description').value;
        const isValidDescription = description_edit.length <= 500;

        if (isValidDescription) {
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

                    if (Edit_SmallMarkerMoved) { // If adjustment made on smaller map
                        MarkerToEdit.position = SecondLocation; // Set the newly recorded position
                        Edit_SmallMarkerMoved = false;
                    }

                    MarkerToEdit.setPosition(MarkerToEdit.position); // Update the displayed location
                    UpdateMarkerInfo(MarkerToEdit); // Prepare InfoWindow with new information
                    UpdateMarkerIcon(MarkerToEdit); // Change icon for marker (if required)

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
| 'Delete Crime' (deleting a single marker)
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
            url: 'DeleteMarkers.php',  // Delete from Database
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
| 'Delete Crime' (deleting multiple markers)
|-----------------------------------------------------------------------------------------------------------
*/

document.getElementById('Delete_Filtered_Markers').addEventListener("click", () => {
    const progress_delete = document.getElementById('progress_delete'); // Progress bar
    ResetProgressBar(progress_delete);

    const visibleMarkers = MarkerArray.filter(marker => marker.getVisible()); // Array of visible markers
    const visibleMarkers_IDs = visibleMarkers.map(marker => marker.id); // Array of IDs for these visible markers

    const num_markers = visibleMarkers_IDs.length;
    const isAnyVisibleMarker = num_markers > 0;

    if (isAnyVisibleMarker) { // If manageable amount of markers to delete
        $('#modal_filter').modal('hide');
        ShowProgressAlert();

        $.ajax({
            url: 'DeleteMarkers.php',
            type: 'POST',
            data: { Marker_IDs: visibleMarkers_IDs },
            success: function (result) {
                // Response - The ID of the database record (created to record/track the progress of this job)
                const Job_ID = parseInt(result);

                const delete_progress_poll = setInterval(GetDeleteProgress, 1000);

                // Send the Job_ID to GetJobProgress.php endpoint to determine the progress 
                function GetDeleteProgress() {
                    $.ajax({
                        url: 'GetJobProgress.php',
                        type: 'GET',
                        data: { Job_ID: Job_ID },
                        success: function (result) {
                            const progress = parseFloat(result); // Percentage completion (of import)

                            // Set progress bar length to response value
                            progress_delete.style.width = Math.floor(progress) + "%";
                            progress_delete.innerHTML = `Marker Deletion (${Math.floor(progress)}%)`;

                            if (progress == 100) {
                                clearInterval(delete_progress_poll); // Stop checking the progress
                                progress_delete.innerHTML = "Marker Deletion (Complete)";
                                // After 2 seconds, reload the page to show new markers
                                setTimeout(function () { window.location.reload(); }, 1000);
                            }
                        },
                        error: function ({ responseText, status, statusText }) {
                            clearInterval(delete_progress_poll); // Ensure the polling has stopped

                            // Change contextual class of progress bar to danger (error)
                            progress_delete.setAttribute('class', 'progress-bar progress-bar-danger');

                            // Show an alert with message constructed from HTTP response
                            const delete_progress_err_string = `Polling Delete progress:\n${status} ${statusText}\n${responseText}`;
                            ShowErrorAlert(delete_progress_err_string , document.getElementById('alert_delete_content'));
                        }
                    });
                }
            },
            error: function ({ responseText, status, statusText }) {
                progress_delete.setAttribute('class', 'progress-bar progress-bar-danger');

                const delete_upload_err_string = `Markers ID Array (transmission):\n${status} ${statusText}\n${responseText}`;
                ShowErrorAlert(delete_upload_err_string, document.getElementById('alert_delete_content'));
            }
        });
    }
    else {
        HideProgressAlert();
        const filter_delete_string = "There are no visible or filtered markers to delete<br>";
        ShowErrorAlert(filter_delete_string, document.getElementById('modal_filter_content'));
    }
});

/*
|-----------------------------------------------------------------------------------------------------------
| Google Map Setup
|-----------------------------------------------------------------------------------------------------------
*/

function initMap() {
    const initial_location = { lat: 51.454266, lng: -0.978130 }; // Reading, UK

    // Main map object
    const map = new google.maps.Map(document.getElementById("map"), { zoom: 8, center: initial_location });

    /*
    heatmap = new google.maps.visualization.HeatmapLayer({
        data: getPoints(),
        map: null
    });

    function getPoints() {
        //
    }
    */

    const ContextMenu = document.getElementById("menu");

    /*
    |-----------------------------------------------------------------------------------------------------------
    | Location Search Bar Implementation
    |-----------------------------------------------------------------------------------------------------------
    */

    const input = document.getElementById('pac-input'); // Create a text input
    const searchBox = new google.maps.places.SearchBox(input); // Link it to search bar element

    searchBox.addListener('places_changed', function () { // Selecting a prediction from the (suggestion) list
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
                bounds.extend(place.geometry.location); // Map Bounds (centered around location)
            }
        });
        map.fitBounds(bounds); // Move map to place location (set map to determined bounds)
    });

    /*
    |-----------------------------------------------------------------------------------------------------------
    | Placing existing markers (from database)
    |-----------------------------------------------------------------------------------------------------------
    */

    // The array of markers (declared and assigned within the index.php file)
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

        if (Cluster_Active) { // If already active and 'Analyse Crime' button was pressed
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
    | Marker Heatmap
    |-----------------------------------------------------------------------------------------------------------
    */

    /*
    function toggleHeatmap() {
        heatmap.setMap(heatmap.getMap() ? null : map);
    }

    document.getElementById("btn_predict").addEventListener('click', event => {
        heatmap = new google.maps.visualization.HeatmapLayer({
            data: getPoints(),
            map: map
          });
    });
    */

    /*
    |-----------------------------------------------------------------------------------------------------------
    | 'Filter Crime' (filtering markers)
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

    // Setup
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
    });

    // Recording information for filtering by location (if required)
    let filter_marker;
    let search_area;

    // When the search radius is selected/changed in the relevant dropdown
    document.getElementById('Filter_Location').addEventListener("change", (event) => {
        const search_radius = event.target.value;

        const isAllAreas = (search_radius == "[ALL]") || (search_radius == null);
        if (!isAllAreas) { // If a quantifiable (numeric) value for search radius is chosen
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

    // Filter by ID (handled independently from the below FilterMarkers() function)
    document.getElementById('ID_Search').addEventListener("click", () => {
        const Filter_ID = document.getElementById('Filter_ID').value;
        const isFilterIDEmpty = (Filter_ID == "");
        if (!isFilterIDEmpty) {
            // Identify marker with requested ID
            const MarkerToShow = MarkerArray.find(marker => marker.id == Filter_ID);

            if (MarkerToShow) { // Marker with requested ID exists
                // Hide all markers
                MarkerArray.forEach(marker =>
                    HideMarker(marker)
                );
                MarkerToShow.setVisible(true); // Show identified marker
                $('#modal_filter').modal('hide');
            }
            else {
                // Don't do any filtering, and simply display a warning that no marker has that ID
                ShowWarningAlert(`No marker with ID: ${Filter_ID}`, document.getElementById('modal_filter_content'));
            }
        }
    });

    function FilterMarkers() {
        ShowLoading();

        // Main Crime Type
        const main_dropdown = document.getElementById("Filter_Crime_Type");
        const Main_Crime_Type = main_dropdown.options[main_dropdown.selectedIndex].value;
        // If field is empty or the all option is implicitly selected, set flag to true
        const isAllCrimes = (Main_Crime_Type == null) || (Main_Crime_Type == "[ALL]");

        // Sub Crime Type
        const sub_dropdown = document.getElementById("Filter_Crime_Type_sub");
        const Sub_Crime_Type = sub_dropdown.options[sub_dropdown.selectedIndex].value;
        const isAllSubCrimes = (Sub_Crime_Type == null) || (Sub_Crime_Type == "[ALL]");

        // Date
        const min_Date = document.getElementById("Filter_minDate").value;
        const max_Date = document.getElementById("Filter_maxDate").value;

        const isMinDate = min_Date.length > 0;
        const isMaxDate = max_Date.length > 0;
        const isBothDates = isMinDate && isMaxDate;

        if (isMinDate) {
            min_Date = new Date(min_Date);
        }

        if (isMaxDate) {
            max_Date = new Date(max_Date);
        }

        // Time
        const min_Time = document.getElementById("Filter_minTime").value;
        const max_Time = document.getElementById("Filter_maxTime").value;

        const isBothTimes = min_Time.length > 0 && max_Time.length > 0;
        const isSingleTime = min_Time.length > 0 || max_Time.length > 0;

        const errorConditions =
            [
                {
                    isMet: isBothDates && min_Date > max_Date,
                    errorMessage: "The 'Minimum Date' field can't be a date after the 'Maximum Date' field"
                },
                {
                    isMet: isBothTimes && min_Time > max_Time,
                    errorMessage: "The 'Minimum Time' can't be a time after the 'Maximum Time' field"
                },
                {
                    isMet: isSingleTime,
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
        const isLocationFiltering = filter_marker.getVisible() && search_area.getVisible();

        let filter_center;
        let search_radius;

        // If so, determine the center point and search radius
        if (isLocationFiltering) {
            // LatLng object of marker added to smaller map (center of search area)
            filter_center = new google.maps.LatLng(filter_marker.getPosition().lat(), filter_marker.getPosition().lng());
            search_radius = document.getElementById("Filter_Location").value;
        }

        // Filter the markers which need to be hidden
        const Filtered_MarkerArray = MarkerArray.filter(marker => {
            // The properties which are less computationally expensive to filter by should come first
            if (isMinDate) {
                if (new Date(marker.crimeDate) < min_Date) {
                    return true;
                }
            }

            if (isMaxDate) {
                if (new Date(marker.crimeDate) > max_Date) {
                    return true;
                }
            }

            if (isBothTimes) {
                if (marker.crimeTime < min_Time || marker.crimeTime > max_Time) {
                    return true;
                }
            }

            if (!isAllCrimes) {
                if (!isAllSubCrimes) { // One specific crime
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

            if (isLocationFiltering) { // Filter criteria included location
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

    let Add_FirstLocation;
    let Add_SecondLocation;

    let Add_Latitude;
    let Add_Longitude;

    // Record location of this click in terms of on the map and on the screen
    map.addListener('rightclick', function (e) {
        // Map (Latitude and Longitude)
        Add_FirstLocation = e.latLng; // Record click location as latLng object
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
    | 'Add Crime' (adding a marker)
    |-----------------------------------------------------------------------------------------------------------
    */

    var SmallMarkerMoved = false;

    document.getElementById("btn_add").addEventListener('click', event => {
        hideContextMenu();

        // Reset input fields before showing modal
        document.querySelectorAll('#Add_Crime_Type, #Add_Crime_Type_sub, #Add_Description').forEach(el => el.value = "");
        document.getElementById('Add_Date').value = new Date().toISOString().split("T")[0];
        document.getElementById('Add_Time').value = "00:00";
        document.querySelectorAll('Add_Crime_Type_sub option:not(:first-child)').forEach(el => el.remove());

        $('#modal_add').modal('show');

        const CurrentZoom = map.getZoom(); // Get zoom level when add button was clicked
        const RefinedZoom = CurrentZoom + 1; // Enhance zoom level by one level

        const AddMapOptions = {
            center: Add_FirstLocation,
            zoom: RefinedZoom,
            disableDefaultUI: true,
            streetViewControl: true,
        };

        const add_map = new google.maps.Map(document.getElementById("add_map"), AddMapOptions); // Show smaller map

        const Draggable_marker_add = new google.maps.Marker({ // Add a single draggable marker to smaller map
            position: Add_FirstLocation,
            //animation: google.maps.Animation.DROP,
            draggable: true,
            map: add_map
        });
        //Draggable_marker_add.setAnimation(google.maps.Animation.BOUNCE);

        // Record position of marker if an adjustment is made
        google.maps.event.addListener(Draggable_marker_add, 'dragend', function (evt) {
            Add_SecondLocation = evt.latLng;
            Add_Latitude = Add_SecondLocation.lat();
            Add_Longitude = Add_SecondLocation.lng();
            SmallMarkerMoved = true;
        });

        /* TODO 3D View (adding markers in street view)
        https://developers.google.com/maps/documentation/javascript/streetview
        https://developers.google.com/maps/documentation/javascript/examples/streetview-overlays
        */
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
                        placeMarker({ id: parseInt(id), crimeType, crimeDate, crimeTime, description }, Add_FirstLocation, map);
                    }
                    else {
                        // An adjutsment was made, so use the map location of where the draggable marker was at the time of submit
                        placeMarker({ id: parseInt(id), crimeType, crimeDate, crimeTime, description }, Add_SecondLocation, map);
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
    | 'Import Crime' (adding crime from information in an external file)
    |-----------------------------------------------------------------------------------------------------------
    */

    // 'Import Crime' button is clicked
    document.getElementById('btn_import').addEventListener("click", () => {
        // Reset progress bars
        const progress_insert_upload = document.getElementById('progress_insert_upload');
        const progress_file_upload = document.getElementById('progress_file_upload');

        ResetProgressBar(progress_insert_upload);
        ResetProgressBar(progress_file_upload);
    });

    let isFileSelected;
    let isCSV;

    // File input for uplaoding a file is changed
    document.getElementById('Import_Input').addEventListener("change", () => {
        isFileSelected = false; // Set initially to false and flag to true when proven otherwise
        isCSV = false;

        const files = document.getElementById('Import_Input').files;
        const file_label = document.getElementById('import_lbl');

        if (files.length >= 1) { // If input has a file selected
            isFileSelected = true;
            const fileName = files[0].name; // Get the name of the first file (only)
            const ext = fileName.substring(fileName.lastIndexOf('.') + 1).toLowerCase();

            // Check extension (in file name)
            if (ext == 'csv') {
                isCSV = true;
            }
            // Note: A server side check will confirm the MIME type

            file_label.innerHTML = fileName; // Change label of input to filename
        }
        else {
            file_label.innerHTML = "Choose file"; // Set back to default text
        }
    });

    // 'Import' confirmation button (within modal) is clicked
    document.getElementById('btn_import_confirm').addEventListener('click', () => {
        // Hide any warnings or errors from previous failed/cancelled imports
        HideWarningAlert();
        HideErrorAlert();        

        // Disable both these buttons during import process
        document.querySelectorAll('#btn_import_confirm, #close_import')
            .forEach(el => el.setAttribute('disabled', true));

        const isValidFile = (isFileSelected && isCSV);

        if (isValidFile) {
            // Prepare file for POST request
            file = document.getElementById('Import_Input').files[0];
            formdata = new FormData();
            formdata.append("ImportFile", file);

            // Hold references to progress bars
            const progress_file_upload = document.getElementById('progress_file_upload');
            const progress_insert_upload = document.getElementById('progress_insert_upload');

            // Sending file to endpoint (server)
            $.ajax({
                // Track the progress of the file upload
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            // Get file upload progress
                            const upload_percentage = parseFloat(((evt.loaded / evt.total) * 100));

                            // Update progress bar width and text using progress
                            progress_file_upload.style.width = Math.floor(upload_percentage) + "%";
                            progress_file_upload.innerHTML = `File Upload (${Math.floor(upload_percentage)}%)`;

                            if (upload_percentage == 100) { // Use 'Complete' text instead of 100% on completion
                                progress_file_upload.innerHTML = "File Upload (Complete)";
                            }
                        }
                    }, false);
                    return xhr;
                },

                // Configuration of POST request
                url: 'ImportMarkers.php',
                type: 'POST',
                data: formdata,
                processData: false,
                contentType: false,
                success: function (result) {
                    // Response (result) - The ID of the database record (created to record/track the progress of this job)
                    const Job_ID = parseInt(result);

                    // Every second, execute the function which returns the progress
                    const import_progress_poll = setInterval(GetImportProgress, 1000);

                    // Send the Job_ID to GetJobProgress.php endpoint to determine the progress 
                    function GetImportProgress() {
                        $.ajax({
                            url: 'GetJobProgress.php',
                            type: 'GET',
                            data: { Job_ID: Job_ID },
                            success: function (result) {
                                const progress = parseFloat(result); // Current percentage completion (of import)

                                // Set progress bar length to response value
                                progress_insert_upload.style.width = Math.floor(progress) + "%";
                                progress_insert_upload.innerHTML = `File Import (${Math.floor(progress)}%)`;

                                if (progress == 100) {
                                    clearInterval(import_progress_poll); // Stop checking the progress
                                    progress_insert_upload.innerHTML = "File Import (Complete)";
                                    // After 2 seconds, reload the page to show new markers
                                    setTimeout(function () { window.location.reload(); }, 1000);
                                }
                            },
                            error: function ({ responseText, status, statusText }) {
                                clearInterval(import_progress_poll);

                                progress_insert_upload.setAttribute('class', 'progress-bar progress-bar-danger');

                                const insertfile_err_string = `Polling Import progress:\n${status} ${statusText}\n${responseText}`;
                                ShowErrorAlert(insertfile_err_string, document.getElementById('modal_import_content'));

                                document.querySelectorAll('#btn_import_confirm, #close_import')
                                    .forEach(el => el.removeAttribute('disabled'));
                            }
                        });
                    }
                },
                error: function ({ responseText, status, statusText }) {
                    progress_file_upload.setAttribute('class', 'progress-bar progress-bar-danger');

                    const uploadfile_err_string = `File Upload:\n${status} ${statusText}\n${responseText}`;
                    ShowErrorAlert(uploadfile_err_string, document.getElementById('modal_import_content'));

                    // Re-enable these buttons after a cancelled import
                    document.querySelectorAll('#btn_import_confirm, #close_import')
                        .forEach(el => el.removeAttribute('disabled'));
                }
            });
        }
        else {
            // Either no file has been selected or a file was selected but it wasn't a .csv file (not both, so ternary operator is used)
            const selectfile_err_string = (!isFileSelected) ? "No file has been selected for import" : "The file is not a .csv file";
            ShowErrorAlert(selectfile_err_string, document.getElementById('modal_import_content'));

            document.querySelectorAll('#btn_import_confirm, #close_import')
                .forEach(el => el.removeAttribute('disabled'));
        }
    });
}