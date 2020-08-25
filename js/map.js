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

        /* Make a new InfoWindow */
        googleMapsMarker.info = new google.maps.InfoWindow({
            content: '<div id="iw-container">' + '<div class="iw-content">' +
                '<b>id: </b>' + googleMapsMarker.id + '<br> <b style="word-wrap: break-word;">Crime Type: </b>' + googleMapsMarker.crimeType + '<br> <b>Date: </b>' + convert_crimeDate(googleMapsMarker.crimeDate) +
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
    ProgressAlertOpen = true;
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
    document.querySelectorAll('#Filter_minDate, #Filter_maxDate, #Filter_minTime, #Filter_maxTime')
    .forEach(el => el.value = "");

    document.querySelectorAll('#Filter_Crime_Type, #Filter_Crime_Type_sub')
    .forEach(el => el.value = "[ALL]");

    document.querySelectorAll('Filter_Crime_Type_sub option:not(:first-child)').forEach(el => el.remove());
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
        '<b>id: </b>' + marker.id + '<br> <b style="word-wrap: break-word;">Crime Type: </b>' + marker.crimeType + '<br> <b>Date: </b>' + convert_crimeDate(marker.crimeDate) +
        '<br><b>Time: </b>' + convert_crimeTime(marker.crimeTime) + '<br></br>' + '<i style="word-wrap: break-word;">' + marker.description + '</i>' +
        '<br></br> <button id="btn_edit" type="button" class="btn btn-secondary" style="width:50%;" onclick=EditMarker(' + marker.id + ')>Edit</button>' +
        '<button type="button" class="btn btn-danger" style="width:50%;" onclick=DeleteMarker(' + marker.id + ')>Delete</button>' + '</div>' + '</div>');
}

function LoadCurrentValues(marker) {
    // Find which main category of crime that marker.crimeTime belongs to
    const foundMappingEdit = crimeTypeMappings.find(x => x.options.includes(marker.crimeType));

    if (foundMappingEdit) { // Selected crime type from dropdowns
        // Enable dropdown fields
        document.getElementById('Edit_Crime_Type').removeAttribute('disabled');
        document.getElementById('Edit_Crime_Type_sub').removeAttribute('disabled');

        // Set main category of crime dropdown to found value
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

    document.getElementById('Edit_Crime_Type_sub').value = marker.crimeType;
    document.getElementById('Edit_Date').value = marker.crimeDate;
    document.getElementById('Edit_Time').value = convert_crimeTime(marker.crimeTime);
    document.getElementById('Edit_Description').value = marker.description;
}

function EditMarker(id) {
    const MarkerToEdit = MarkerArray.find(marker => marker.id === id); // Find marker to edit by ID
    MarkerToEdit.info.close(); // Close marker's info window (as the information it holds may change)
    var modal = $('#modal_edit');

    // Determine and display the current values for the marker
    LoadCurrentValues(MarkerToEdit);

    // Now show the modal with this information
    modal.modal('show');

    // Set up smaller map in 'Edit Crime' window
    var EditMapOptions = {
        center: MarkerToEdit.position,
        zoom: 12,
        disableDefaultUI: true, // Remove all controls but street view
        streetViewControl: true,
    };

    var edit_map = new google.maps.Map(document.getElementById("edit_map"), EditMapOptions);

    var Draggable_marker_edit = new google.maps.Marker({ // Add a single draggable marker to smaller map
        position: MarkerToEdit.position,
        draggable: true,
        map: edit_map
    });

    var Edit_SmallMarkerMoved = false;
    var FirstLocation = MarkerToEdit.position;
    var Latitude = FirstLocation.lat();
    var Longitude = FirstLocation.lng();

    google.maps.event.addListener(Draggable_marker_edit, 'dragend', function (evt) {
        SecondLocation = evt.latLng;
        Latitude = SecondLocation.lat(); // Information to be sent
        Longitude = SecondLocation.lng();
        Edit_SmallMarkerMoved = true;
    });

    // Edit modal confirmation
    document.getElementById('edit_submit_form').addEventListener("submit", function (e) {
        e.preventDefault();

        const description_edit = document.getElementById('Edit_Description').value;
        const validDescription = description_edit.length <= 500;

        if (validDescription) {
            // Also send to database
            var formData = $('#edit_submit_form').serialize();

            var Vars = { id: id, Latitude: Latitude, Longitude: Longitude };
            var varsData = $.param(Vars);

            var data = formData + '&' + varsData;

            ShowLoading();

            $.ajax({
                url: 'EditMarkers.php',
                type: 'POST',
                data: data,
                success: function (result) {
                    var dropdown = document.getElementById('Edit_Crime_Type_sub');

                    // Update values locally (the marker's properties)
                    MarkerToEdit.crimeDate = document.getElementById('Edit_Date').value;
                    MarkerToEdit.crimeTime = document.getElementById('Edit_Time').value;
                    MarkerToEdit.crimeType = dropdown.options[dropdown.selectedIndex].value;
                    MarkerToEdit.description = document.getElementById('Edit_Description').value;

                    if (Edit_SmallMarkerMoved == true) { // If adjustment made on smaller map
                        MarkerToEdit.position = SecondLocation;
                        Edit_SmallMarkerMoved = false;
                    }

                    MarkerToEdit.setPosition(MarkerToEdit.position);
                    UpdateMarkerInfo(MarkerToEdit);
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

    const MarkerToDelete = MarkerArray.find(marker => marker.id === id);
    const MarkerToDelete_index = MarkerArray.findIndex(marker => marker.id === id);

    if (MarkerToDelete) { // Object found
        if (typeof (MarkerToDelete.info) !== "undefined") {
            if (MarkerToDelete.info.getMap() != null) {
                MarkerToDelete.info.close();
            }
        }
    }

    $.ajax({
        url: 'DeleteMarker.php',  // Database
        type: 'POST',
        data: { id: id },
        success: function (data) {
            MarkerToDelete.setVisible(false); // View
            if (MarkerToDelete_index) {
                if (MarkerToDelete_index !== -1) { // Index found
                    MarkerArray.splice(MarkerToDelete_index, 1); // Array
                }

            }
        }
    });

    HideLoading();
}

/*
|-----------------------------------------------------------------------------------------------------------
| Deleting multiple markers
|-----------------------------------------------------------------------------------------------------------
*/

// TODO Refactor this module to handle multi-user interaction

document.getElementById('Delete_Filtered_Markers').addEventListener("click", () => {
    if ($('#progress_delete').hasClass('progress-bar bg-danger progress-bar-striped progress-bar-animated')) {
        document.getElementById('progress_delete').setAttribute('class', 'progress-bar progress-bar-striped progress-bar-animated');
    }
    document.getElementById('progress_delete').style.width = "0%";

    const visibleMarkers = MarkerArray.filter(marker => marker.getVisible()); // Array of visible markers
    const visibleMarkers_IDs = visibleMarkers.map(marker => marker.id); // Array of ids for these visible markers

    var num_markers = visibleMarkers_IDs.length;
    var within_marker_capacity = num_markers > 0 && num_markers < 50000;

    if (within_marker_capacity) { // If manageable amount of markers to delete
        $('#modal_filter').modal('hide');
        ShowProgressAlert();

        var Filter_Modal_Delete_Top = document.getElementById('modal_filter_content').offsetTop;
        var Filter_Modal_Delete_Left = document.getElementById('modal_filter_content').offsetLeft;
        var Filter_Modal_Delete_Height = document.getElementById('modal_filter_content').height;
        var Filter_Modal_Delete_Width = document.getElementById('modal_filter_content').width;

        /* Alert Position (top) */
        var Filter_Alert_Progress_Top = Filter_Modal_Delete_Top + (Filter_Modal_Delete_Height / 2);

        /* Set position of alert */
        $("#Alert_Progress").css({ top: Filter_Alert_Progress_Top, left: Filter_Modal_Delete_Left, width: Filter_Modal_Delete_Width });

        var t2 = setInterval(CheckDeleteProgressFile, 1000);

        $.ajax({
            url: 'DeleteMarker.php',
            type: 'POST',
            data: { visibleMarkers_IDs: visibleMarkers_IDs }, // Send ids
            success: function (data) {
                //
            }
        });
    }
    else {
        HideProgressAlert();
        var filter_delete_string = "";
        if (num_markers == 0) {
            filter_delete_string = "There are no visible or filtered markers to delete<br>";
        }
        else {
            filter_delete_string = "Only 50,000 markers can be deleted at once, refine the filter to select fewer markers<br>";
        }
        ShowErrorAlert(filter_delete_string, document.getElementById('modal_filter_content'));
    }

    var delete_data_hold = -10;
    var Delete_FinishCheckCounter = 0;
    var Delete_NoChangeCounter = 0;
    var Delete_TimeoutCounter = 0;
    var Delete_Timed_Out = 0;

    var delete_counter_value = 0;
    if (num_markers < 5000) {
        delete_counter_value = 3;
    }
    else if (num_markers < 10000) {
        delete_counter_value = 5;
    }
    else if (num_markers < 25000) {
        delete_counter_value = 7;
    }
    else {
        delete_counter_value = 10;
    }

    function CheckDeleteProgressFile() {
        $.ajax({
            url: "/delete_progress.txt",
            cache: false,
            async: false,
            dataType: "text",
            success: function (data, textStatus, jqXHR) {
                Delete_TimeoutCounter += 1;
                var delete_percentage = data;

                if (delete_percentage == 0) {
                    Delete_NoChangeCounter += 1;
                }
                if (delete_percentage != 0) {
                    Delete_NoChangeCounter = 0;
                }

                if (Delete_NoChangeCounter == 10) {
                    clearInterval(t2);
                    document.getElementById('progress_delete').setAttribute('class', 'progress-bar bg-danger progress-bar-striped progress-bar-animated');
                    $("#progress_delete").css("width", "100%").text("Delete (Failed)");
                    Delete_Timed_Out = 1;
                }

                if (Delete_Timed_Out == 0 && delete_percentage != 0) {
                    if (delete_percentage == 100) {
                        Delete_FinishCheckCounter += 1;
                    }
                    if (delete_percentage != delete_data_hold) {
                        Delete_FinishCheckCounter = 0;
                    }

                    if (Delete_FinishCheckCounter == delete_counter_value) {
                        $("#progress_delete").css("width", "100%").text("Delete (Complete)");
                    }

                    if (Delete_FinishCheckCounter == (delete_counter_value + 2)) {
                        clearInterval(t2);
                        ShowLoading();
                        location.reload();
                    }

                    delete_data_hold = delete_percentage;

                    if (Delete_FinishCheckCounter < delete_counter_value) {
                        const progressPrevious = Delete_TimeoutCounter < 5 && delete_percentage > 90;
                        if (!progressPrevious) {
                            const progress_delete = document.getElementById('progress_delete');
                            progress_delete.style.width = Math.round(delete_percentage) + "%";
                            progress_delete.innerHTML = "Delete (" + Math.round(delete_percentage) + "%)";
                        }
                    }

                }

            }
        });
    }

});

/*
|-----------------------------------------------------------------------------------------------------------
| Google Map Setup
|-----------------------------------------------------------------------------------------------------------
*/

function initMap() {
    var ContextMenu = null;
    var menuDisplayed = false;
    var Latitude = 0;
    var Longitude = 0;

    var initial_location = { lat: 51.454266, lng: -0.978130 };
    var map = new google.maps.Map(document.getElementById("map"), { zoom: 8, center: initial_location });

    /*
    |-----------------------------------------------------------------------------------------------------------
    | Location Search Bar Implementation
    |-----------------------------------------------------------------------------------------------------------
    */

    var input = document.getElementById('pac-input'); // Create a text input
    var searchBox = new google.maps.places.SearchBox(input); // Link it to search bar element

    searchBox.addListener('places_changed', function () { // Selecting a prediction from the list
        var places = searchBox.getPlaces(); // Can be more than one place if using text-based geographic search

        if (places.length == 0) {
            return;
        }

        var bounds = new google.maps.LatLngBounds();
        places.forEach(function (place) {
            if (!place.geometry) {
                return;
            }

            if (place.geometry.viewport) {
                bounds.union(place.geometry.viewport); // Only geocodes have viewport.
            } else {
                bounds.extend(place.geometry.location);
            }
        });
        map.fitBounds(bounds); // Move map to place location
    });

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
    var clusterStyles = [
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

    var mcOptions = {
        gridSize: 50,
        styles: clusterStyles,
        maxZoom: 15
    };

    var markerCluster = new MarkerClusterer(null, MarkerArray, mcOptions);
    markerCluster.setIgnoreHidden(true);

    google.maps.event.addListener(markerCluster, 'clusteringstart', ShowLoading);
    google.maps.event.addListener(markerCluster, 'clusteringend', HideLoading);

    // Invocation
    var Cluster_Active = false; // Clusterer flagged as off to begin with
    const btn_analyse = document.getElementById("btn_analyse");
    btn_analyse.addEventListener('click', event => {
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

    // TODO Filter by ID
    // TODO Filter by most recently added crimes (e.g last 10 crimes added to the mapper)

    function FilterMarkers(center_loc, distance) {
        var invalidInput = false;
        var filter_err_string = "";

        // Distance / Location
        var AllDistanceSelected = false;
        if (distance == null || distance == "[ALL]") {
            distance = "[ALL]";
            var AllDistanceSelected = true;
        }

        // Main Crime Type
        var main_dropdown = document.getElementById("Filter_Crime_Type");
        var Main_Crime_Type = main_dropdown.options[main_dropdown.selectedIndex].value;
        // If field is empty or the all option is implicitly selected, set flag to true
        const AllCrimes = (Main_Crime_Type == null) || (Main_Crime_Type == "[ALL]");

        // Sub Crime Type
        var sub_dropdown = document.getElementById("Filter_Crime_Type_sub");
        var Sub_Crime_Type = sub_dropdown.options[sub_dropdown.selectedIndex].value;
        const AllSubCrimes = (Sub_Crime_Type == null) || (Sub_Crime_Type == "[ALL]");

        // Date
        var min_Date = document.getElementById("Filter_minDate").value;
        var max_Date = document.getElementById("Filter_maxDate").value;

        const min_Date_Entered = min_Date.length > 0;
        const max_Date_Entered = max_Date.length > 0;
        const both_Dates_Entered = min_Date_Entered && max_Date_Entered;

        if (min_Date_Entered) {
            min_Date = new Date(min_Date);
        }

        if (max_Date_Entered) {
            max_Date = new Date(max_Date);
        }
        
        if (both_Dates_Entered) {
            if (min_Date > max_Date) { // Validation
                filter_err_string += "The 'Minimum Date' field can't be a date after the 'Maximum Date' field<br>";
                invalidInput = true;
            }
        }

        // Time
        var min_Time = document.getElementById("Filter_minTime").value;
        var max_Time = document.getElementById("Filter_maxTime").value;

        const both_Times_Entered = min_Time.length > 0 && max_Time.length > 0;
        const single_Time_Entered = min_Time.length > 0 || max_Time.length > 0;

        if (both_Times_Entered) {
            if (min_Time > max_Time) {
                filter_err_string += "The 'Minimum Time' can't be a time after the 'Maximum Time' field <br>";
                invalidInput = true;
            }
        }
        else {
            if (single_Time_Entered) {
                filter_err_string += "Both the 'Minimum Time' and 'Maximum Time' fields are required<br>";
                invalidInput = true;
            }
        }

        // Actual filtering
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

        if (!invalidInput) {
            // Remove any previous filters
            MarkerArray.forEach(marker => 
                marker.setVisible(true)
            );

            // Filter the markers which need to be hidden
            const Filtered_MarkerArray = MarkerArray.filter(marker => {
                // Filter by crime type first
                if(!AllCrimes) {
                    if(!AllSubCrimes) { // One specific crime
                        if (marker.crimeType != Sub_Crime_Type) {
                            return true; // Then this marker should be filtered and hidden
                            // Return because regardless of the remaming properties, the full criteria has not been met
                            // This therefore means the following checks are not made (they aren't required)
                            // TODO Optimisation of filtering order
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

                if(min_Date_Entered) {
                    if(new Date(marker.crimeDate) < min_Date) {
                        return true;
                    }
                }

                if(max_Date_Entered) {
                    if(new Date(marker.crimeDate) > max_Date) {
                        return true;
                    }
                }

                if(both_Times_Entered) {
                    if(marker.crimeTime < min_Time || marker.crimeTime > max_Time) {
                        return true;
                    }
                }

            });

            // Hide the markers identified
            Filtered_MarkerArray.forEach(marker =>
                HideMarker(marker)
            );

            $('#modal_filter').modal('hide');
            HideErrorAlert();
        }
        else {
            ShowErrorAlert(filter_err_string, document.getElementById('modal_filter_content'));
        }

    }

    /* By location */
    var filter_marker_hold = [];
    var UK_center = new google.maps.LatLng(52.636879, -1.139759);

    var filter_marker = new google.maps.Marker({
        position: UK_center,
        map: null
    });
    filter_marker_hold.push(filter_marker);

    $('#modal_filter').on('shown.bs.modal', function () {
        document.getElementById('Filter_Location').setAttribute("selectedIndex", 0);
        document.getElementById('Filter_Location').setAttribute('disabled', true);

        var FilterMapOptions = {
            center: UK_center,
            zoom: 6,
            disableDefaultUI: true, // Remove all controls but street view
            streetViewControl: true,
        };

        var filter_map = new google.maps.Map(document.getElementById("filter_map"), FilterMapOptions); // Show smaller map

        var marker_placed = false;

        google.maps.event.addListener(filter_map, 'click', function (event) {
            if (marker_placed == false) {
                var filter_marker = new google.maps.Marker({
                    position: event.latLng,
                    map: filter_map
                });
                filter_marker_hold[0] = filter_marker;

                marker_placed = true;
                document.getElementById('Filter_Location').removeAttribute('disabled');
            }
            else {
                filter_marker_hold[0].setPosition(event.latLng);
            }
        });

        var circle_placed = false;
        var circle_hidden = false;
        var circle_hold = [];
        var distance_val;

        document.getElementById('Filter_Location').addEventListener("change", (event) => {
            distance_val = event.target.value;

            if (distance_val == "[ALL]") {
                document.getElementById('Filter_Location').setAttribute('disabled', true);
                if (circle_placed == true) {
                    circle_hold[0].setMap(null);
                    circle_hidden = true;
                }
                if (marker_placed == true) {
                    filter_marker_hold[0].setMap(null);
                    marker_placed = false;
                }
            }
            else {
                if (circle_hidden == true) {
                    circle_hold[0].setMap(filter_map);
                }

                var f_marker = filter_marker_hold[0];

                if (circle_placed == false) {
                    var circle = new google.maps.Circle({
                        map: filter_map,
                        radius: 1,    // 10 miles in metres
                        fillColor: '#AA0000'
                    });

                    circle_hold.push(circle);
                    circle_placed = true;
                }

                circle_hold[0].bindTo('center', f_marker, 'position');
                circle_hold[0].setRadius(distance_val * 1609); // Convert miles to metres
            }


        });

        document.getElementById('btn_filter_confirm').addEventListener("click", () => {
            ShowLoading();

            var f_marker = filter_marker_hold[0];
            var center_lat = f_marker.getPosition().lat();
            var center_lng = f_marker.getPosition().lng();
            var center_loc = new google.maps.LatLng(center_lat, center_lng);

            FilterMarkers(center_loc, distance_val);
            HideLoading();
        });

    });

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

    map.addListener('rightclick', function (e) {
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
                    // Position context menu one pixel to the right and below location of click (so that hover styling is not seen immediately)
                    ContextMenu.style.left = (left + 1) + "px";
                    ContextMenu.style.top = (top - 1) + "px";
                    ContextMenu.style.display = "block";
                    menuDisplayed = true;
                }
            }
        }
    });

    map.addListener("click", function (e) { // Left click away from it
        if (menuDisplayed == true) {
            hideContextMenu();
        }
    });

    map.addListener("drag", function (e) { // Drag away from it
        if (menuDisplayed == true) {
            hideContextMenu();
        }
    });

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

    const add_btn = document.getElementById("btn_add"); // 'Add crime' button
    add_btn.addEventListener('click', event => {
        hideContextMenu();

        var modal = $('#modal_add');
        modal.modal('show');

        var CurrentZoom = map.getZoom(); // Get zoom level when add button was clicked
        var RefinedZoom = CurrentZoom + 1; // Enhance zoom level by one level

        var AddMapOptions = {
            center: FirstLocation,
            zoom: RefinedZoom,
            disableDefaultUI: true, // Remove all controls but street view
            streetViewControl: true,
        };

        var add_map = new google.maps.Map(document.getElementById("add_map"), AddMapOptions); // Show smaller map

        var Draggable_marker_add = new google.maps.Marker({ // Add a single draggable marker to smaller map
            position: FirstLocation,
            draggable: true,
            map: add_map
        });

        google.maps.event.addListener(Draggable_marker_add, 'dragend', function (evt) {
            SecondLocation = evt.latLng; // To be used to place static marker on main map
            Latitude = SecondLocation.lat(); // Information to be sent
            Longitude = SecondLocation.lng();
            SmallMarkerMoved = true;
        });

        // TODO Implement 3D View (adding markers in street view)
    });

    document.getElementById('add_submit_form').addEventListener("submit", function (e) {
        e.preventDefault();

        var dropdown = document.getElementById("Add_Crime_Type");
        var sub_dropdown = document.getElementById("Add_Crime_Type_sub"); // Initial step of getting crime type

        // Take values locally
        var crimeDate = document.getElementById("Add_Date").value;
        var crimeTime = document.getElementById("Add_Time").value;
        var crimeType = sub_dropdown.options[sub_dropdown.selectedIndex].value;
        var description = document.getElementById("Add_Description").value;

        /* Check Crime Type(s) are specified */
        var crimeTypes_Chosen = false;

        var Crime_Category = dropdown.options[dropdown.selectedIndex].value;
        if (Crime_Category == "" || crimeType == "") {
            crimeTypes_Chosen = false;
        }
        else {
            crimeTypes_Chosen = true;
        }

        if (description.length <= 500 && MarkerArray.length < 250000 && crimeTypes_Chosen == true) {
            /* Also send to database */
            var formData = $('#add_submit_form').serialize();

            var Vars = { Latitude: Latitude, Longitude: Longitude };
            var varsData = $.param(Vars);

            var data = formData + '&' + varsData;

            ShowLoading();

            $.ajax({
                url: 'SaveMarkers.php',
                type: 'POST',
                data: data,
                success: function (id) {
                    if (SmallMarkerMoved == false) {
                        placeMarker({ id: parseInt(id), crimeType, crimeDate, crimeTime, description }, FirstLocation, map); // Place a static marker on the main map				    
                    }
                    else {
                        placeMarker({ id: parseInt(id), crimeType, crimeDate, crimeTime, description }, SecondLocation, map); // Place a static marker on the main map
                    }

                    SmallMarkerMoved = false;
                    HideLoading();
                    $('#modal_add').modal('hide');
                    HideErrorAlert();
                },
                fail: function () {
                    HideLoading();
                }
            });
        }
        else {
            var add_err_string = "";
            if (description.length > 500) {
                add_err_string += "The 'description' field can only be a maximum of 500 characters<br>";
            }
            if (MarkerArray.length > 250000) {
                add_err_string += "The mapper is at its capacity of displaying 250,000 crimes<br>";
            }
            if (crimeTypes_Chosen == false) {
                if (Crime_Category == "") {
                    add_err_string += "The 'Crime Type - Main Category' field is a required field<br>";
                }
                if (crimeType == "") {
                    add_err_string += "The 'Crime Type - Subcategory' field is a required field<br>";
                }
            }
            ShowErrorAlert(add_err_string, document.getElementById('modal_add_content'));
        }

    });

    /*
    |-----------------------------------------------------------------------------------------------------------
    | Importing crimes
    |-----------------------------------------------------------------------------------------------------------
    */

    $('#modal_import').on('shown.bs.modal', function () {
        if ($('#progress_file_upload').hasClass('progress-bar bg-danger progress-bar-striped progress-bar-animated')) { // If class was to changed to class used for showing errors
            document.getElementById('progress_file_upload').setAttribute('class', 'progress-bar progress-bar-striped progress-bar-animated'); // Change it back to default
        }
        document.getElementById('progress_file_upload').style.width = "0%";

        if ($('#progress_insert_upload').hasClass('progress-bar bg-danger progress-bar-striped progress-bar-animated')) {
            document.getElementById('progress_insert_upload').setAttribute('class', 'progress-bar progress-bar-striped progress-bar-animated');
        }
        document.getElementById('progress_insert_upload').style.width = "0%";
    });

    var isFileSelected = false;
    var isCSV = false;

    document.getElementById('Import_Input').addEventListener("change", () => {
        isFileSelected = false;
        isCSV = false;

        files = this.files;

        if (files.length >= 1) { // If input has a file selected
            isFileSelected = true; // Toggle presence of file
            var fileName = files[0].name;
            var ext = fileName.substring(fileName.lastIndexOf('.') + 1).toLowerCase();

            if (ext == 'csv') {
                isCSV = true; // Toggle CSV check
            }

            document.getElementById('import_lbl').text(fileName); // Change label of input 
        }
        else {
            isFileSelected = false;
            document.getElementById('import_lbl').text("Choose file");
        }

    });

    document.getElementById('btn_import_confirm').addEventListener('click', () => { // Sending selected file to PHP file (to be handled)
        HideErrorAlert();
        HideWarningAlert();

        document.getElementById('btn_import_confirm').setAttribute('disabled', true); // Disable import button
        document.getElementById('close_import').setAttribute('disabled', true); // Disable close button

        if ($('#Import_Input').prop('files').length > 0 && (isCSV === true)) {
            file = $('#Import_Input').prop('files')[0];

            var reader = new FileReader();
            reader.readAsText(file);

            reader.onload = function (event) {
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
                var Accepted_description_headers = ["Context", "context", "description", "description", "Notes", "notes"];
                var Accepted_Time_headers = ["Time", "time", "Timestamp", "timestamp"];

                // Read file locally
                var csv = event.target.result;
                var rows = csv.split('\n');

                // Check headers
                headers = rows[0].split(','); // The first row split by commas give the headers

                for (var i = 0; i < headers.length; i++) {
                    headers[i] = $.trim(headers[i].replace(/[\t\n]+/g, ' ')); // Remove any whitespace (e.g before first header or after last header)
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
                    if (Accepted_description_headers.indexOf(headers[i]) !== -1) {
                        Description_index = i;
                    }
                    if (Accepted_Time_headers.indexOf(headers[i]) !== -1) {
                        Time_index = i;
                    }
                }

                var validFile = true;
                var import_err_str = "";

                var FileWarning = false;
                var import_warning_str = "";

                var Reached_Limit = false;

                if (Date_index === -1) {
                    import_warning_str += "Missing 'Date' column in file (the current date will be used)<br>";
                    FileWarning = true;
                }
                if (Latitude_index === -1) {
                    import_err_str += "Missing 'Latitude' column in file<br>";
                    validFile = false;
                }
                if (Longitude_index === -1) {
                    import_err_str += "Missing 'Longitude' column in file<br>";
                    validFile = false;
                }
                if (CrimeType_index === -1) {
                    import_warning_str += "Missing 'Crime Type' column in file (the crime type 'Unknown' will be used)<br>";
                    FileWarning = true;
                }
                if (Description_index === -1) {
                    import_warning_str += "Missing 'Description' column in file (no description will be used)<br>";
                    FileWarning = true;
                }
                if (Time_index === -1) {
                    import_warning_str += "Missing 'Time' column in file (the current time will be used)<br>";
                    FileWarning = true;
                }

                // Check number of rows
                var num_rows = rows.length;
                var num_records = num_rows - 1;

                if (num_records <= 0) {
                    validFile = false;
                }

                if ((MarkerArray.length + num_records) > 250000) {
                    Reached_Limit = true;
                }

                if (num_records > 50000) {
                    import_err_str += "Only 50000 records can be imported at any one time<br>(The selected file has " + num_records + " records)<br>";
                    validFile = false;
                }

                if (validFile == true && Reached_Limit == false) {
                    if (FileWarning == true) {
                        ShowWarningAlert(import_warning_str, document.getElementById('modal_import_content'));
                    }

                    var progress_file_upload = document.getElementById('progress_file_upload');
                    progress_file_upload.style.width = "100%";
                    progress_file_upload.innerHTML = "Ready";
                    formdata = new FormData();
                    formdata.append("fileToUpload", file);

                    $.ajax({
                        // File upload progress
                        xhr: function () {
                            var xhr = new window.XMLHttpRequest();
                            xhr.upload.addEventListener("progress", function (evt) {
                                if (evt.lengthComputable) {
                                    var percentComplete = evt.loaded / evt.total;
                                    var upload_percentage = percentComplete * 100;
                                    var progress_file_upload = document.getElementById('progress_file_upload');
                                    if (upload_percentage = 100) {
                                        progress_file_upload.style.width = "100%";
                                        progress_file_upload.innerHTML = "File Upload (Processing)";
                                    }
                                    else {
                                        progress_file_upload.style.width = Math.round(upload_percentage) + "%";
                                        progress_file_upload.innerHTML = "File Upload (" + upload_percentage + "%)";
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
                        success: function () {
                            progress_file_upload.style.width = "100%";
                            progress_file_upload.innerHTML = "File Upload (Complete)";
                        },
                        fail: function () {
                            document.getElementById('progress_file_upload').setAttribute('class', 'progress-bar bg-danger progress-bar-striped progress-bar-animated');
                            progress_file_upload.style.width = "100%";
                            progress_file_upload.innerHTML = "File Upload (Failed)";
                        },
                        error: function () {
                            document.getElementById('progress_file_upload').setAttribute('class', 'progress-bar bg-danger progress-bar-striped progress-bar-animated');
                            progress_file_upload.style.width = "100%";
                            progress_file_upload.innerHTML = "File Upload (Error)";
                        }
                    });

                    var NoChangeCounter = 0;
                    var FinishCheckCounter = 0;
                    var TimeoutCounter = 0;
                    var Timed_Out = 0;
                    var data_hold = -10;

                    var counter_value = 0;
                    var TimeoutLimit = 0;

                    if (num_records < 7500) {
                        counter_value = 5
                        TimeoutLimit = 60;
                    }
                    else if (num_records < 25000) {
                        counter_value = 7;
                        TimeoutLimit = 180;
                    }
                    else {
                        counter_value = 9;
                        TimeoutLimit = 300;
                    }

                    // TODO Refactor this module to handle multi-user interaction

                    var t = setInterval(CheckProgressFile, 1000); // Run below function every second

                    function CheckProgressFile() {
                        $.ajax({
                            url: "/counts.txt",
                            cache: false,
                            async: false,
                            dataType: "text",
                            success: function (data, textStatus, jqXHR) {
                                TimeoutCounter += 1;
                                var import_percentage = data;

                                if (import_percentage == 0) {
                                    NoChangeCounter += 1;
                                }
                                if (import_percentage != 0) {
                                    NoChangeCounter = 0;
                                }

                                if (TimeoutCounter == TimeoutLimit || import_percentage == "-1000" || NoChangeCounter == 10) { // Timeout, file upload error or no update
                                    clearInterval(t);
                                    // Show full width red progress bar
                                    document.getElementById('progress_insert_upload').setAttribute('class', 'progress-bar bg-danger progress-bar-striped progress-bar-animated');

                                    const progress_insert_upload = docuemt.getElementById('progress_insert_upload');

                                    progress_insert_upload.style.width = "100%";
                                    progress_insert_upload.innerHTML = "Import (Failed)";

                                    Timed_Out = 1;
                                    document.getElementById('btn_import_confirm').removeAttribute('disabled');
                                    document.getElementById('close_import').removeAttribute('disabled');
                                }

                                if (Timed_Out == 0 && import_percentage != 0) {
                                    if (import_percentage == 100) {
                                        FinishCheckCounter += 1;
                                    }
                                    if (import_percentage != data_hold) {
                                        FinishCheckCounter = 0;
                                    }

                                    if (FinishCheckCounter == counter_value) {
                                        progress_insert_upload.style.width = "100%";
                                        progress_insert_upload.innerHTML = "Import (Complete)";
                                    }

                                    if (FinishCheckCounter == (counter_value + 2)) {
                                        clearInterval(t);
                                        ShowLoading();
                                        location.reload();
                                    }

                                    data_hold = import_percentage;

                                    if (FinishCheckCounter < counter_value) {
                                        var isPreviousProgress = (TimeoutCounter <= 7 && import_percentage > 90);
                                        if (!isPreviousProgress) {
                                            progress_insert_upload.style.width = Math.round(import_percentage) + "%";
                                            progress_insert_upload.innerHTML = "Import (" + Math.round(import_percentage) + "%)";
                                        }
                                    }
                                }

                            }
                        });
                    }

                }
                else {
                    if (num_records <= 0) {
                        import_err_str += "No records found in the file<br>";
                    }
                    if (Reached_Limit == true) {
                        import_err_str += "Importing this file would exceed the limit of 250,000 markers<br>";
                    }

                    if (FileWarning == true) {
                        ShowWarningAlert(import_warning_str, document.getElementById('modal_import_content'));
                        ShowErrorAlert(import_err_str, document.getElementById('Alert_Warning'));
                    }
                    else {
                        ShowErrorAlert(import_err_str, document.getElementById('modal_import_content'));
                    }

                    document.getElementById('btn_import_confirm').removeAttribute('disabled');
                    document.getElementById('close_import').removeAttribute('disabled');
                }

            }
        }
        else {
            selectfile_err_string = "";
            if (isCSV === false) { // File of input is not a .csv file
                if (isFileSelected === false) { // And no file has been added
                    selectfile_err_string += "No file has been selected for import";
                    ShowErrorAlert(selectfile_err_string, document.getElementById('modal_import_content'));
                }
                else {
                    selectfile_err_string += "The file is not a .csv file";
                    ShowErrorAlert(selectfile_err_string, document.getElementById('modal_import_content'));
                }
            }

            document.getElementById('btn_import_confirm').removeAttribute('disabled');
            document.getElementById('close_import').removeAttribute('disabled');
        }
    });
}