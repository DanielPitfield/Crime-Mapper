var MarkerArray = [];

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

        var MarkerDate_display = moment(googleMapsMarker.crimeDate).format("DD-MM-YYYY"); // Convert to UK format

        var MarkerTime_display = googleMapsMarker.crimeTime;
        if (MarkerTime_display.length == 8) { // If time is retrieved form database which includes seconds
            MarkerTime_display = MarkerTime_display.substring(0, MarkerTime_display.length - 3); // Remove the seconds for display purposes
        }

        /* Make a new InfoWindow */
        googleMapsMarker.info = new google.maps.InfoWindow({
            content: '<div id="iw-container">' + '<div class="iw-content">' +
                '<b>id: </b>' + googleMapsMarker.id + '<br> <b style="word-wrap: break-word;">Crime Type: </b>' + googleMapsMarker.crimeType + '<br> <b>Date: </b>' + MarkerDate_display +
                '<br><b>Time: </b>' + MarkerTime_display + '<br></br>' + '<i style="word-wrap: break-word;">' + googleMapsMarker.description + '</i>' +
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

var ErrorAlertOpen = false;
function ShowErrorAlert(message) {
    Error_Alert = document.getElementById("Alert_Error");
    document.getElementById('Alert_Error_Message').innerHTML = message;
    Error_Alert.style.left = "14%";
    Error_Alert.style.top = "100px";
    Error_Alert.style.display = "block";
    ErrorAlertOpen = true;
}

function HideErrorAlert() {
    Error_Alert = document.getElementById("Alert_Error");
    Error_Alert.style.left = "-500px";
    Error_Alert.style.top = "-500px";
    Error_Alert.style.display = "none";
    ErrorAlertOpen = false;
}

document.getElementById('close_alert_error').addEventListener("click", () => {
    HideErrorAlert();
});

document.getElementById('close_add').addEventListener("click", () => {
    if (ErrorAlertOpen == true) {
        HideErrorAlert();
    }
});

document.getElementById('close_filter').addEventListener("click", () => {
    if (ErrorAlertOpen == true) {
        HideErrorAlert();
    }
});

document.getElementById('close_edit').addEventListener("click", () => {
    if (ErrorAlertOpen == true) {
        HideErrorAlert();
    }
});

document.getElementById('close_import').addEventListener("click", () => {
    if (ErrorAlertOpen == true) {
        HideErrorAlert();
    }
});

var WarningAlertOpen = false;
function ShowWarningAlert(message) {
    Warning_Alert = document.getElementById("Alert_Warning");
    document.getElementById('Alert_Warning_Message').innerHTML = message;
    Warning_Alert.style.left = "34%";
    Warning_Alert.style.top = "500px";
    Warning_Alert.style.display = "block";
    WarningAlertOpen = true;
}

function HideWarningAlert() {
    Warning_Alert = document.getElementById("Alert_Warning");
    Warning_Alert.style.left = "-500px";
    Warning_Alert.style.top = "-500px";
    Warning_Alert.style.display = "none";
    WarningAlertOpen = false;
}

document.getElementById('close_alert_warning').addEventListener("click", () => {
    HideWarningAlert();
});

document.getElementById('close_import').addEventListener("click", () => {
    if (WarningAlertOpen == true) {
        HideWarningAlert();
    }
});

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

document.getElementById('close_alert_progress').addEventListener("click", () => {
    HideProgressAlert();
});

function UpdateMarkerInfo(marker) {
    marker.title = marker.crimeType; // Shown on hover

    var MarkerDate_update = moment(marker.crimeDate).format("DD-MM-YYYY"); // Convert to UK format

    var MarkerTime_update = marker.crimeTime;
    if (MarkerTime_update.length == 8) { // If time is retrieved form database which includes seconds
        MarkerTime_update = MarkerTime_update.substring(0, MarkerTime_update.length - 3); // Remove the seconds for display purposes
    }

    marker.info.setContent('<div id="iw-container">' + '<div class="iw-content">' +
        '<b>id: </b>' + marker.id + '<br> <b style="word-wrap: break-word;">Crime Type: </b>' + marker.crimeType + '<br> <b>Date: </b>' + MarkerDate_update +
        '<br><b>Time: </b>' + MarkerTime_update + '<br></br>' + '<i style="word-wrap: break-word;">' + marker.description + '</i>' +
        '<br></br> <button id="btn_edit" type="button" class="btn btn-secondary" style="width:50%;" onclick=EditMarker(' + marker.id + ')>Edit</button>' +
        '<button type="button" class="btn btn-danger" style="width:50%;" onclick=DeleteMarker(' + marker.id + ')>Delete</button>' + '</div>' + '</div>');
}

function EditMarker(id) {
    const MarkerToEdit = MarkerArray.find(marker => marker.id === id);

    MarkerToEdit.info.close(); // Close marker's info window (as the information it holds may change)

    var modal = $('#modal_edit');

    document.getElementById('Edit_Crime_Type').removeAttribute('disabled');
    document.getElementById('Edit_Crime_Type_sub').removeAttribute('disabled');

    const foundMappingEdit = crimeTypeMappings.find(x => x.options.includes(MarkerToEdit.crimeType));

    if (foundMappingEdit) {
        document.getElementById('Edit_Crime_Type').value = foundMappingEdit.value;
        var event = new Event('change');
        document.getElementById('Edit_Crime_Type').dispatchEvent(event);
    }
    else { // Imported crime type  
        document.getElementById('Edit_Crime_Type').value = 'Other';

        var opt = MarkerToEdit.crimeType;
        var el = document.createElement("option");
        el.textContent = opt;
        el.value = opt;
        var edit_sub_select = document.getElementById("Edit_Crime_Type_sub");
        edit_sub_select.appendChild(el);

        document.getElementById('Edit_Crime_Type').setAttribute('disabled', true);
        document.getElementById('Edit_Crime_Type_sub').setAttribute('disabled', true);
    }

    document.getElementById('Edit_Crime_Type_sub').value = MarkerToEdit.crimeType;
    modal.find('#Edit_Date').val(MarkerToEdit.crimeDate);
    modal.find('#Edit_Time').val(MarkerToEdit.crimeTime.substring(0, 5));
    modal.find('#Edit_Description').val(MarkerToEdit.description);

    /* Showing and setting up edit modal */

    modal.modal('show');

    var EditMapOptions = {
        center: MarkerToEdit.position,
        zoom: 15,
        disableDefaultUI: true, // Remove all controls but street view
        streetViewControl: true,
    };

    var edit_map = new google.maps.Map(document.getElementById("edit_map"), EditMapOptions); // Show smaller map

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

    /* Edit modal confirmation */

    document.getElementById('edit_submit_form').addEventListener("submit", function (e) {
        e.preventDefault();

        var edit_err_string = "";
        var description_edit = document.getElementById("Edit_Description").value;

        if (description_edit.length > 500) {
            edit_err_string += "The 'description' field can only be a maximum of 500 characters<br>";
        }

        if (description_edit.length <= 500) {
            /* Also send to database */
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
                    var dropdown = document.getElementById("Edit_Crime_Type_sub");

                    /* Update values locally (the marker's properties) */
                    MarkerToEdit.crimeDate = document.getElementById("Edit_Date").value;
                    MarkerToEdit.crimeTime = document.getElementById("Edit_Time").value;
                    MarkerToEdit.crimeType = dropdown.options[dropdown.selectedIndex].value;
                    MarkerToEdit.description = document.getElementById("Edit_Description").value;

                    if (Edit_SmallMarkerMoved == true) {
                        MarkerToEdit.position = SecondLocation;
                        Edit_SmallMarkerMoved = false;
                    }

                    MarkerToEdit.setPosition(MarkerToEdit.position);
                    UpdateMarkerInfo(MarkerToEdit);
                    HideLoading();
                   $('#modal_edit').modal('hide');

                    if (ErrorAlertOpen == true) {
                        HideErrorAlert();
                    }

                }

            });
        }
        else {
            ShowErrorAlert(edit_err_string);

            /* Modal Position */
            var Edit_Modal_Top = document.getElementById('modal_edit_content').offsetTop;
            var Edit_Modal_Left = document.getElementById('modal_edit_content').offsetLeft;
            var Edit_Modal_Height = document.getElementById('modal_edit_content').height;
            var Edit_Modal_Width = document.getElementById('modal_edit_content').width;

            /* Alert Position (top) */
            var Edit_Alert_Error_Top = 0;
            if (screen.height >= 1080) {
                Edit_Alert_Error_Top = Edit_Modal_Top + Edit_Modal_Height + 50;
            }

            /* Set position of alert */
            $("#Alert_Error").css({ top: Edit_Alert_Error_Top, left: Edit_Modal_Left, width: Edit_Modal_Width });
        }

    });

}

/* Delete single marker */
function DeleteMarker(id) {
    ShowLoading();

    const MarkerToDelete = MarkerArray.find(marker => marker.id === id);
    const MarkerToDelete_index = MarkerArray.findIndex(marker => marker.id === id);

    if (typeof (MarkerToDelete.info) !== "undefined") {
        if (MarkerToDelete.info.getMap() != null) { // And it is open
            MarkerToDelete.info.close(); // Close it
        }
    }

    var MarkerID = id; // Assign to send variable

    $.ajax({
        url: 'DeleteMarker.php',  // Database
        type: 'POST',
        data: { MarkerID: MarkerID },
        success: function (data) {
            MarkerToDelete.setVisible(false); // View
            if (MarkerToDelete_index !== -1) MarkerArray.splice(MarkerToDelete_index, 1); // Array
        }
    });

    HideLoading();

}

/* Delete multiple marker */
document.getElementById('Delete_Filtered_Markers').addEventListener("click", () => {
    if ($('#progress_delete').hasClass('progress-bar bg-danger progress-bar-striped progress-bar-animated')) {
        $("#progress_delete").attr('class', 'progress-bar progress-bar-striped progress-bar-animated');
    }
    $("#progress_delete").css("width", "0%");

    const visibleMarkers = MarkerArray.filter(marker => marker.getVisible()); // Array of visible markers
    const visibleMarkers_IDs = visibleMarkers.map(marker => marker.id); // Array of ids for these visible markers

    var num_markers = visibleMarkers_IDs.length;
    var within_marker_capacity = num_markers > 0 && num_markers < 50000;

    if (within_marker_capacity) { // If markers to delete
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
        ShowErrorAlert(filter_delete_string);

        /* Modal Position */
        var Filter_Modal_Top2 = document.getElementById('modal_filter_content').offsetTop;
        var Filter_Modal_Left2 = document.getElementById('modal_filter_content').offsetLeft;
        var Filter_Modal_Height2 = document.getElementById('modal_filter_content').height;
        var Filter_Modal_Width2 = document.getElementById('modal_filter_content').width;

        /* Alert Position (top) */
        var Filter_Alert_Error_Top2 = 0;
        if (screen.height >= 1080) {
            Filter_Alert_Error_Top2 = Filter_Modal_Top2 + Filter_Modal_Height2 + 50;
        }

        /* Set position of alert */
        $("#Alert_Error").css({ top: Filter_Alert_Error_Top2, left: Filter_Modal_Left2, width: Filter_Modal_Width2 });
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
                        if (Delete_TimeoutCounter < 5 && delete_percentage > 90) {
                            //console.log("Progress from previous file")
                        }
                        else {
                            $("#progress_delete").css("width", Math.round(delete_percentage) + "%").text("Delete (" + Math.round(delete_percentage) + "%)");
                        }
                    }

                }

            }
        });
    }

});

function initMap() {
    var ContextMenu = null;
    var menuDisplayed = false;
    var Latitude = 0;
    var Longitude = 0;

    var initial_location = { lat: 51.454266, lng: -0.978130 };
    var map = new google.maps.Map(document.getElementById("map"), { zoom: 8, center: initial_location });

    // Create a text input and link it to search bar element
    var input = document.getElementById('pac-input');
    var searchBox = new google.maps.places.SearchBox(input);

    /*
    |-----------------------------------------------------------------------------------------------------------
    | Retrieving and placing database markers
    |-----------------------------------------------------------------------------------------------------------
    */

    function LoadMarkers() {
        markers.forEach(marker =>
            // Placing the markers stored in the database
            placeMarker(marker, new google.maps.LatLng(marker.latitude, marker.longitude), map)
        );
    }

    LoadMarkers();
    HideLoading();

    /*
    |-----------------------------------------------------------------------------------------------------------
    | Marker Clustering
    |-----------------------------------------------------------------------------------------------------------
    */

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

    /*
    |-----------------------------------------------------------------------------------------------------------
    | Filtering Markers
    |-----------------------------------------------------------------------------------------------------------
    */

    function FilterMarkers(center_loc, distance) {
        var filter_err_string = "";

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
        var main_dropdown_value = main_dropdown.options[main_dropdown.selectedIndex].value;

        if ((main_dropdown_value == null) || (main_dropdown_value == "[ALL]")) {
            AllMainSelected = true;
        }
        else {
            var Main_Crime_Type = main_dropdown_value;
        }

        /* ---- Sub Crime Type ---- */
        var sub_dropdown = document.getElementById("Filter_Crime_Type_sub");
        var sub_dropdown_value = sub_dropdown.options[sub_dropdown.selectedIndex].value;

        if ((sub_dropdown_value == null) || (sub_dropdown_value == "[ALL]")) {
            AllSubSelected = true;
        }
        else {
            var Sub_Crime_Type = sub_dropdown_value;
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

        // Also by id or last x/10/100 crimes?

        /* -------- Input validation -------- */

        if (isMinDate == true && isMaxDate == true) {
            if (minDate > maxDate) {
                filter_err_string += "The 'Minimum Date' field can't be a date after the 'Maximum Date' field<br>";
                invalidInput = true;
            }
        }

        if (isMinTime == true && isMaxTime == true) {
            if (minTime > maxTime) {
                filter_err_string += "The 'Minimum Time' can't be a time after the 'Maximum Time' field <br>";
                invalidInput = true;
            }
        }

        if (isMinTime == false && isMaxTime == true || isMaxTime == false && isMinTime == true) {
            filter_err_string += "Both the 'Minimum Time' and 'Maximum Time' fields are required<br>";
            invalidInput = true;
        }

        /* -------- Filtering -------- */

        function HideMarker(marker) {
            marker.setVisible(false);

            if (typeof (marker.info) !== "undefined") {
                if (marker.info.getMap() != null) {
                    marker.info.close();
                }
            }
        }

        /* ---- Remove any previous filters ---- */
        if (invalidInput == false) {
            for (i = 0; i < MarkerArray.length; i++) {
                MarkerArray[i].setVisible(true);

                /* ---- Convert date into comparable object ---- */
                var MarkerDate = moment(MarkerArray[i].crimeDate).format("YYYY-MM-DD"); // Convert date
                MarkerDate = new Date(MarkerDate);

                var MarkerTime = MarkerArray[i].crimeTime;

                if (AllMainSelected == false) {
                    if (AllSubSelected == false) { // One specific crime
                        if (MarkerArray[i].crimeType != Sub_Crime_Type) {
                            HideMarker(MarkerArray[i]);
                        }
                    }
                    if (AllSubSelected == true) { // One main category of crime						
                        const foundMappingFilter = crimeTypeMappings.find(x => Main_Crime_Type == x.value);
                        if (foundMappingFilter) {
                            if (foundMappingFilter.options.includes(MarkerArray[i].crimeType) === false) {
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
            $('#modal_filter').modal('hide');

            if (ErrorAlertOpen == true) {
                HideErrorAlert();
            }

        }
        else {
            ShowErrorAlert(filter_err_string);

            /* Modal Position */
            var Filter_Modal_Top = document.getElementById('modal_filter_content').offsetTop;
            var Filter_Modal_Left = document.getElementById('modal_filter_content').offsetLeft;
            var Filter_Modal_Height = document.getElementById('modal_filter_content').height;
            var Filter_Modal_Width = document.getElementById('modal_filter_content').width;

            /* Alert Position (top) */
            var Filter_Alert_Error_Top = 0;
            if (screen.height >= 1080) {
                Filter_Alert_Error_Top = Filter_Modal_Top + Filter_Modal_Height + 50;
            }

            /* Set position of alert */
            $("#Alert_Error").css({ top: Filter_Alert_Error_Top, left: Filter_Modal_Left, width: Filter_Modal_Width });
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
    | 'Add crime' input window
    |-----------------------------------------------------------------------------------------------------------
    */

    $('#modal_add').on('shown.bs.modal', function () {
        document.getElementById('Add_Date').value = new Date().toISOString().split("T")[0];
        document.getElementById('Add_Time').value = "00:00";
        document.getElementById('Add_Crime_Type').value = "";
        document.querySelectorAll('Add_Crime_Type_sub option:not(:first-child)').forEach(el => el.remove());
        document.getElementById('Add_Crime_Type_sub').value = "";
        document.getElementById('Add_Description').value = "";
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

        // 3D View (adding markers in street view)
    });

    document.getElementById('add_submit_form').addEventListener("submit", function (e) {
        e.preventDefault();

        var dropdown = document.getElementById("Add_Crime_Type");
        var sub_dropdown = document.getElementById("Add_Crime_Type_sub"); // Initial step of getting crime type

        /* Take values locally */
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

                    if (ErrorAlertOpen == true) {
                        HideErrorAlert();
                    }

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
            ShowErrorAlert(add_err_string);

            /* Modal Position */
            var Add_Modal_Top = document.getElementById('modal_add_content').offsetTop;
            var Add_Modal_Left = document.getElementById('modal_add_content').offsetLeft;
            var Add_Modal_Height = document.getElementById('modal_add_content').height;
            var Add_Modal_Width = document.getElementById('modal_add_content').width;

            /* Alert Position (top) */
            var Add_Alert_Error_Top = 0;
            if (screen.height >= 1080) {
                Add_Alert_Error_Top = Add_Modal_Top + Add_Modal_Height + 50;
            }

            /* Set position of alert */
            $("#Alert_Error").css({ top: Add_Alert_Error_Top, left: Add_Modal_Left, width: Add_Modal_Width });
        }

    });

    /*
    |-----------------------------------------------------------------------------------------------------------
    | Filtering crimes
    |-----------------------------------------------------------------------------------------------------------
    */

    document.getElementById('Filter_Clear').addEventListener("click", () => {
       document.getElementById('Filter_minDate').value = "";
       document.getElementById('Filter_maxDate').value = "";
       document.getElementById('Filter_minTime').value = "";
       document.getElementById('Filter_maxTime').value = "";
       document.getElementById('Filter_Crime_Type').value = "[ALL]";
       document.querySelectorAll('Filter_Crime_Type_sub option:not(:first-child)').forEach(el => el.remove());
       document.getElementById('Filter_Crime_Type_sub').value = "[ALL]";
       document.getElementById('Filter_Location').setAttribute("selectedIndex", 0);
       document.getElementById('Filter_Location').setAttribute('disabled', true);
    });

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

    })

    /*
    |-----------------------------------------------------------------------------------------------------------
    | 'Analyse' - Crime Analysis Techniques
    |-----------------------------------------------------------------------------------------------------------
    */

    var Cluster_Active = false; // Clusterer initialised as unactive
    const btn_analyse = document.getElementById("btn_analyse"); // 'Analyse' button
    btn_analyse.addEventListener('click', event => {
        hideContextMenu();
        ShowLoading();

        if (Cluster_Active == true) { // If active and button was pressed
            markerCluster.setMap(null); // Hide clusterer
            Cluster_Active = false; // Alternate variable
        }
        else {
            markerCluster.addMarkers(MarkerArray); // Update markers to cluster
            markerCluster.setMap(map);
            markerCluster.repaint(); // Redraw and show clusterer
            Cluster_Active = true;
        }

        HideLoading();
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
        $("#progress_file_upload").css("width", "0%");

        if ($('#progress_insert_upload').hasClass('progress-bar bg-danger progress-bar-striped progress-bar-animated')) {
            document.getElementById('progress_insert_upload').setAttribute('class', 'progress-bar progress-bar-striped progress-bar-animated');
        }
        $("#progress_insert_upload").css("width", "0%");
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

        if (ErrorAlertOpen == true) {
            HideErrorAlert();
        }

        if (WarningAlertOpen == true) {
            HideWarningAlert();
        }

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
                var description_index = -1;
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
                        description_index = i;
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
                if (description_index === -1) {
                    import_warning_str += "Missing 'description' column in file (no description will be used)<br>";
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
                        ShowWarningAlert(import_warning_str);

                        /* Modal Position */
                        var Import_Modal_Warning_Only_Top = document.getElementById('modal_import_content').offsetTop;
                        var Import_Modal_Warning_Only_Left = document.getElementById('modal_import_content').offsetLeft;
                        var Import_Modal_Warning_Only_Height = document.getElementById('modal_import_content').height;
                        var Import_Modal_Warning_Only_Width = document.getElementById('modal_import_content').width;

                        /* Alert Position (top) */
                        var Import_Alert_Warning_Only_Top = 0;
                        if (screen.height >= 1080) {
                            Import_Alert_Warning_Only_Top = Import_Modal_Warning_Only_Top + Import_Modal_Warning_Only_Height + 50;
                        }

                        /* Set position of alert */
                        $("#Alert_Warning").css({ top: Import_Alert_Warning_Only_Top, left: Import_Modal_Warning_Only_Left, width: Import_Modal_Warning_Only_Width });
                    }

                    $("#progress_file_upload").css("width", "100%").text("Ready");
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
                        success: function () {
                            $("#progress_file_upload").css("width", "100%").text("File Upload (Complete)");
                        },
                        fail: function () {
                            document.getElementById('progress_file_upload').setAttribute('class', 'progress-bar bg-danger progress-bar-striped progress-bar-animated');
                            $("#progress_file_upload").css("width", "100%").text("File Upload (Failed)");
                        },
                        error: function () {
                            document.getElementById('progress_file_upload').setAttribute('class', 'progress-bar bg-danger progress-bar-striped progress-bar-animated');
                            $("#progress_file_upload").css("width", "100%").text("File Upload (Error)");
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
                                    $("#progress_insert_upload").css("width", "100%").text("Import (Failed)");
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
                                        $("#progress_insert_upload").css("width", "100%").text("Import (Complete)");
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
                                            $("#progress_insert_upload").css("width", Math.round(import_percentage) + "%").text("Import (" + Math.round(import_percentage) + "%)");
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
                        /* Warning */
                        ShowWarningAlert(import_warning_str);

                        /* Modal Position */
                        var Import_Modal_Top = document.getElementById('modal_import_content').offsetTop;
                        var Import_Modal_Left = document.getElementById('modal_import_content').offsetLeft;
                        var Import_Modal_Height = document.getElementById('modal_import_content').height;
                        var Import_Modal_Width = document.getElementById('modal_import_content').width;

                        /* Alert Position (top) */
                        var Import_Alert_Warning_Top = 0;
                        if (screen.height >= 1080) {
                            Import_Alert_Warning_Top = Import_Modal_Top + Import_Modal_Height + 50;
                        }

                        /* Set position of alert */
                        $("#Alert_Warning").css({ top: Import_Alert_Warning_Top, left: Import_Modal_Left, width: Import_Modal_Width });

                        /* Error */
                        ShowErrorAlert(import_err_str);

                        /* Warning Alert Position */
                        var Warning_Alert_Top = document.getElementById('Alert_Warning').offsetTop;
                        var Warning_Alert_Height = document.getElementById('Alert_Warning').height;

                        /* Alert Position (top) */
                        var Import_Alert_Error_Top = Warning_Alert_Top + Warning_Alert_Height + 30;

                        /* Set position of alert */
                        $("#Alert_Error").css({ top: Import_Alert_Error_Top, left: Import_Modal_Left, width: Import_Modal_Width });
                    }
                    else {
                        ShowErrorAlert(import_err_str);

                        /* Modal Position */
                        var Import_Modal_Error_Only_Top = document.getElementById('modal_import_content').offsetTop;
                        var Import_Modal_Error_Only_Left = document.getElementById('modal_import_content').offsetLeft;
                        var Import_Modal_Error_Only_Height = document.getElementById('modal_import_content').height;
                        var Import_Modal_Error_Only_Width = document.getElementById('modal_import_content').width;

                        /* Alert Position (top) */
                        var Import_Alert_Error_Only_Top = 0;
                        if (screen.height >= 1080) {
                            Import_Alert_Error_Only_Top = Import_Modal_Error_Only_Top + Import_Modal_Error_Only_Height + 50;
                        }

                        /* Set position of alert */
                        $("#Alert_Error").css({ top: Import_Alert_Error_Only_Top, left: Import_Modal_Error_Only_Left, width: Import_Modal_Error_Only_Width });
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
                    ShowErrorAlert(selectfile_err_string);

                    /* Modal Position */
                    var Import_Modal_Select_Top = document.getElementById('modal_import_content').offsetTop;
                    var Import_Modal_Select_Left = document.getElementById('modal_import_content').offsetLeft;
                    var Import_Modal_Select_Height = document.getElementById('modal_import_content').height;
                    var Import_Modal_Select_Width = document.getElementById('modal_import_content').width;

                    /* Alert Position (top) */
                    var Import_Alert_Error_Select_Top = 0;
                    if (screen.height >= 1080) {
                        Import_Alert_Error_Select_Top = Import_Modal_Select_Top + Import_Modal_Select_Height + 50;
                    }

                    /* Set position of alert */
                    $("#Alert_Error").css({ top: Import_Alert_Error_Select_Top, left: Import_Modal_Select_Left, width: Import_Modal_Select_Width });
                }
                else {
                    selectfile_err_string += "The file is not a .csv file";
                    ShowErrorAlert(selectfile_err_string);

                    /* Modal Position */
                    var Import_Modal_Select_Top = document.getElementById('modal_import_content').offsetTop;
                    var Import_Modal_Select_Left = document.getElementById('modal_import_content').offsetLeft;
                    var Import_Modal_Select_Height = document.getElementById('modal_import_content').height;
                    var Import_Modal_Select_Width = document.getElementById('modal_import_content').width;

                    /* Alert Position (top) */
                    var Import_Alert_Error_Select_Top = 0;
                    if (screen.height >= 1080) {
                        Import_Alert_Error_Select_Top = Import_Modal_Select_Top + Import_Modal_Select_Height + 50;
                    }

                    /* Set position of alert */
                    $("#Alert_Error").css({ top: Import_Alert_Error_Select_Top, left: Import_Modal_Select_Left, width: Import_Modal_Select_Width });
                }
            }

            document.getElementById('btn_import_confirm').removeAttribute('disabled');
            document.getElementById('close_import').removeAttribute('disabled');
        }
    });

    /*
    |-----------------------------------------------------------------------------------------------------------
    | Search box implementation
    |-----------------------------------------------------------------------------------------------------------
    */

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
                // Only geocodes have viewport.
                bounds.union(place.geometry.viewport);
            } else {
                bounds.extend(place.geometry.location);
            }
        });
        map.fitBounds(bounds); // Move map to place location
    });
}
