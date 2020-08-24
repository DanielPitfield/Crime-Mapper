<?php
require 'dbConfig.php'; // Include the database configuration file
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Crime Mapper</title>
	<link rel="shortcut icon" href="#"> <!-- Website tab icon, change link to ico file -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> <!-- JQuery (Google CDN) -->
</head>

<link rel="stylesheet" href="/css/layout.css"> <!-- External styling -->

<body oncontextmenu="return false;">
	<!-- Disable the default right click context menu for the body of the page -->

	<!-- Filter Crime - 0% left (16% width)
     Import Crime - 0.25% left (16% width)
     Location Search - 0.25% left and right (35% width)
     Analyse Crime - 0.25% right (16% width)
     Predict Crime - 0% right (16% width)
-->

	<!-- Navigation Bar -->
	<nav class="navbar navbar-dark bg-dark">
		<!-- Filter Crime -->
		<button class="btn btn-outline-primary navbar-btn" id="btn_filter" role="button" data-toggle="modal" data-target="#modal_filter" disabled style="color:white;width:16%;margin-left:0%;">Filter Crime</button>
		<!-- Import Crime -->
		<button class="btn btn-outline-primary navbar-btn" id="btn_import" role="button" data-toggle="modal" data-target="#modal_import" disabled style="color:white;width:16%;margin-left:0.25%;">Import Crime</button>
		<!-- Location Search Bar -->
		<input id="pac-input" class="controls" type="text" placeholder="Location Search" disabled style="color:black;width:35%;margin-left:0.25%;margin-right:0.25%;">
		<!-- Analyse Crime -->
		<button class="btn btn-outline-primary navbar-btn" id="btn_analyse" role="button" disabled style="color:white;width:16%;margin-right:0.25%;">Analyse Crime</button>
		<!-- Predict Crime -->
		<button class="btn btn-outline-primary disabled navbar-btn" id="btn_predict" role="button" disabled style="color:white;width:16%;margin-right:0%;">Predict Crime</button>
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

	<!-- Error Alert -->
	<div class="alert alert-danger alert-dismissible fade show" role="alert" id="Alert_Error">
		<h5 class="alert-heading" style="font-weight: bold;">Error</h5>
		<div id="Alert_Error_Message" style="font-size:14px;">
			Message
		</div>
		<button type="button" class="close" id="close_alert_error">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>

	<!-- Warning Alert -->
	<div class="alert alert-warning alert-dismissible fade show" role="alert" id="Alert_Warning">
		<h5 class="alert-heading" style="font-weight: bold;">Warning</h5>
		<div id="Alert_Warning_Message" style="font-size:14px;">
			Message
		</div>
		<button type="button" class="close" id="close_alert_warning">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>

	<!-- Delete Progress Alert -->
	<div class="alert alert-dark alert-dismissible fade show" role="alert" id="Alert_Progress">
		<h5 class="alert-heading" style="font-weight:bold;">Progress</h5>
		<div class="progress" style="margin-top:10px;">
			<div id="progress_delete" class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%;" data-backdrop="static" data-keyboard="false">Progress Bar
			</div>
		</div>
		<button type="button" class="close" id="close_alert_progress">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>

	<!-- Filter modal -->
	<div class="modal fade bd-example-modal-xl" data-backdrop="false" tabindex="-1" role="dialog" id="modal_filter">
		<div class="modal-dialog modal-xl" id="modal_filter_dialog">
			<div class="modal-content" id="modal_filter_content">
				<div class="modal-header">
					<h5 class="modal-title">Filter</h5>
					<button class="btn btn-info" id="Filter_Clear" style="font-size:12px;height:20px;padding: 0px 10px 2px 10px;margin-left:10px;margin-top:5px;text-align:center;">Clear Filter</button>
					<button class="btn btn-danger" id="Delete_Filtered_Markers" style="font-size:12px;height:20px;padding: 0px 10px 2px 10px;margin-left:250px;margin-top:5px;text-align:center;">Delete Filtered (Visible) Markers</button>
					<button type="button" class="close" data-dismiss="modal" id="close_filter">
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
								<option value="[ALL]" selected disabled hidden>Search Radius (miles)</option>
							</select>
						</div>

					</div>

					<div id="modal_right">
						<div id="filter_map"></div>
					</div>

					<button id="btn_filter_confirm" class="btn btn-success" style="width:100%;">Confirm</button>

				</div>
			</div>
		</div>
	</div>

	<!-- Add crime modal -->
	<div class="modal fade bd-example-modal-xl" data-backdrop="false" tabindex="-1" role="dialog" id="modal_add">
		<div class="modal-dialog modal-xl" id="modal_add_dialog">
			<div class="modal-content" id="modal_add_content">
				<div class="modal-header">
					<h5 class="modal-title">Add Crime</h5>
					<button type="button" class="close" data-dismiss="modal" id="close_add">
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
								<input type="time" id="Add_Time" name="Time" value="00:00" required>
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
						<div id="add_map"></div>
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
			<div class="modal-content" id="modal_edit_content">
				<div class="modal-header">
					<h5 class="modal-title">Edit Crime</h5>
					<button type="button" class="close" data-dismiss="modal" id="close_edit">
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
								<input type="time" id="Edit_Time" name="Time" value="" required>
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
						<div id="edit_map"></div>
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
			<div class="modal-content" id="modal_import_content">
				<div class="modal-header">
					<h5 class="modal-title">Import</h5>
					<a href="template.csv" class="btn btn-info" role="button" style="font-size:12px;height:20px;padding: 0px 10px 2px 10px;margin-left:10px;margin-top:5px;text-align:center;">Download Template</a>

					<button type="button" class="close" id="close_import" data-dismiss="modal">
						<span>&times;</span>
					</button>
				</div>

				<div class="modal-body">
					<div class="custom-file mb-3">
						<input type="file" id="Import_Input" class="custom-file-input" name="fileToUpload" accept=".csv">
						<label class="custom-file-label" id="import_lbl" for="customFile" style="display:inline-block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Choose file</label>
					</div>

					<button type="submit" id="btn_import_confirm" class="btn btn-success" style="width:100%;margin-top:8px;">Import</button>

					<div class="progress" style="margin-top:8px;">
						<div id="progress_file_upload" class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%;">Progress Bar</div>
					</div>

					<div class="progress" style="margin-top:8px;">
						<div id="progress_insert_upload" class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%;">Progress Bar</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<script src="/resources/moment.js"></script> <!-- Moment.js library -->

	<script>
		var markers = [ // Query database (return currently stored markers)
			<?php
			$result = $db->query("SELECT * FROM markers");
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) { // TODO: HTML Encoding
					echo "{ id: {$row['ID']}, crimeType: '{$row['Crime_Type']}', crimeDate: '{$row['Crime_Date']}', crimeTime: '{$row['Crime_Time']}',
					description: '" . htmlspecialchars($row['Description']) . "', latitude: '{$row['Latitude']}', longitude: '{$row['Longitude']}'},";
				}
			}
			?>
		];
	</script>

	<!-- Bootstrap Scripts -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> <!-- JQuery (Google CDN) -->
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

	<script src="/js/data.js"></script>
	<script src="/js/setup.js"></script>
	<script src="/js/map.js"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/markerclustererplus/2.1.4/markerclusterer.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA_kjnrExSDqqcj7Wq3rpdwgt5JiG9sJec&libraries=geometry,places&callback=initMap" async defer></script> <!-- API Key, Libraries and map function -->

</body>

</html>