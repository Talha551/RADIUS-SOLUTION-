

<!-- DataTables CSS/JS (add in <head> or before </body>) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

<!-- Select2 -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<!-- Bootstrap4 Duallistbox -->
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>

</div>
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1><i class="fas fa-users"></i> Subscribers List</h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active">Subscribers</li>
					</ol>
				</div>
			</div>
		</div>
	</section>
	<section class="content">
		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header d-flex justify-content-between align-items-center">
					<h3 class="card-title"><i class="fas fa-list"></i> Subscribers</h3>
					<div class="card-tools">
						<a href="<?php echo base_url('usersAddNew'); ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add PPPoE User</a>
					</div>
				</div>
				<div class="card-body">
					<form class="mb-3">
						<div class="form-row">
							<div class="form-group col-md-3">
								<label>Filter By Status</label>
								<select class="form-control select2bs4" style="width: 100%;" id="searchText1" name="state">
									<optgroup label="User Status">
										<option value="0" selected>Active Users</option>
										<option value="1">Paid</option>
										<option value="2">Expired</option>
										<option value="3">Blocked</option>
										<option value="4">All</option>
										<option value="5">Online</option>
										<option value="6">Offline-Active</option>
										<option value="7">Offline-Inactive</option>
									</optgroup>
									<optgroup label="Expiring">
										<option value="8"> in Next 3 Days</option>
										<option value="9"> in Next 1 Day</option>
										<option value="10"> in Next 7 Days</option>
										<option value="11"> in Next 14 Days</option>
										<option value="12"> in Next 30 Days</option>
									</optgroup>
									<optgroup label="Expired">
										<option value="13"> in 3 Days</option>
										<option value="14"> in 1 Day</option>
										<option value="15"> in 7 Days</option>
										<option value="16"> in 14 Days</option>
										<option value="17"> in 30 Days</option>
									</optgroup>
								</select>
							</div>
							<div class="form-group col-md-3">
								<label>User Type</label>
								<select name="type" id="type" class="form-control select2bs4" style="width: 100%;">
									<option value="0" selected>PPPoE-Prepaid</option>
									<option value="1">PPPoE-Postpaid</option>
									<option value="2">Hotspot-Voucher</option>
								</select>
							</div>
							<div class="form-group col-md-3">
								<label>Ownership</label>
								<select class="form-control select2bs4" style="width: 100%;" id="manager" name="manager">
									<?php if (!empty($managers)) { ?>
										<option value="<?php echo $this->session->userdata('name'); ?>">Select Owner</option>
										<?php foreach ($managers as $rl) { ?>
											<option value="<?php echo $rl->managername ?>" <?php if ($rl->managername == set_value('managername')) { echo "selected=selected"; } ?>><?php echo $rl->managername ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div>
							<div class="form-group col-md-3">
								<label>Service Profile</label>
								<select class="form-control select2bs4" style="width: 100%;" id="srvid" name="srvid">
									<?php if (!empty($services)) { ?>
										<option value="0">Select Service Profile</option>
										<?php foreach ($services as $rl) { ?>
											<option value="<?php echo $rl->srvid ?>" <?php if ($rl->srvname == set_value('srvid')) { echo "selected=selected"; } ?>><?php echo $rl->srvname."(".$rl->managername.")" ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-row">
							
						</div>
						
						<!-- Add/Remove Columns section: DO NOT REMOVE OR CHANGE -->
						<div class="form-row">

							<div class="form-group col-12">
								<label>Add/Remove Columns</label>
								<select class="select2" multiple="multiple" data-placeholder="Select a Column" data-dropdown-css-class="select2-purple" style="width: 100%;" id="columnToggle">
									<optgroup label="User Profile">
										<option value="0" selected>USERNAME</option>
										<option value="1" selected>F-NAME</option>
										<option value="2">L-NAME</option>
									</optgroup>
									<optgroup label="Information">
										<option value="3" selected>OWNER</option>
										<option value="4">ADDRESS</option>
										<option value="5">CITY</option>
									</optgroup>
									<optgroup label="DETAILS">
										<option value="6">MOBILE</option>
										<option value="7" selected>PACKAGE</option>
										<option value="8" selected>STARTED</option>
										<option value="9" selected>EXPIRY</option>
										<option value="10" selected>ACTION</option>
									</optgroup>
								</select>
							</div>
						</div>
					</form>
					<div class="table-responsive">
						<table id="subscribersTable" class="table table-bordered table-hover table-striped">
							<thead>
								<tr>
									<th>Username</th>
									<th>First Name</th>
									<th>Last Name</th>
									<th>Owner</th>
									<th>Address</th>
									<th>City</th>
									<th>Mobile</th>
									<th>Package</th>
									<th>Started</th>
									<th>Expiry</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<script>
	$(document).ready(function() {

		var table = $('#subscribersTable').DataTable({
			responsive: true,
			scrollCollapse: false,
			autoWidth: false,
			"searchDelay": 3000,
			"processing": true,
			"language": {
				"processing": "<i class='fa fa-spinner fa-spin'></i> Loading, please wait..."
			},
			"serverSide": true,
			"ajax": {
				"url": "<?php echo site_url('subscribers_controller/getSubscribersList') ?>",
				"type": "POST",
				"data": function(d) {
					d.searchText1 = $('#searchText1').val(); // First Filter for Different Types of Users
					d.type = $('#type').val(); // Type of User
					d.owner = $('#manager').val();
					d.srvid = $('#srvid').val();
					d.expirationFilter3 = $('#expirationFilter3').val();
				},
				"error": function(xhr, error, thrown) {
					console.log("Error occurred: ", thrown);
					alert("An error occurred: " + thrown);
				}
				//,
				//success: function(data) {
				//    console.log(data);
				// Assuming you have a modal with an ID of actionModal and a body class of modal-body
				//    $('#actionModal .modal-body').html(data);
				//    $('#actionModal').modal('show');
				//}
			},
			"dom": '<"top"Bfl> rt<"bottom"pi><"clear">', // This option is used to initialize the Buttons extension
			"buttons": [
				'copy', 'csv', 'excel', 'pdf' // Define the buttons you want
			],
			"lengthMenu": [
				[10, 25, 50, 100, 200, 500, 1000],
				['10', '25', '50', '100', '200', '500', '1000']
			],
			"pageLength": 10,
			"columns": [{
					"data": "username"
				}, // Adjust according to your data fields
				{
					"data": "firstname"
				},
				{
					"data": "lastname",
					"visible": false
				},
				{
					"data": "owner"
				},
				{
					"data": "address",
					"visible": false
				},
				{
					"data": "city",
					"visible": false
				},
				{
					"data": "mobile",
					"visible": false
				},
				{
					"data": "servicename"
				},
				{
					"data": "createdon"
				},
				{
					"data": "expiration"
				},
				{
					"data": "action",
					"render": function(data, type, row) {
						let menu = '';
						menu += `<button class='btn btn-sm btn-primary recharge' data-id='${row.username}' style='margin-right:2px;'>Recharge</button>`;
						menu += `<button class='btn btn-sm btn-info view' data-id='${row.username}' style='margin-right:2px;'>View</button>`;
						menu += `<button class='btn btn-sm btn-warning manage' data-id='${row.username}' style='margin-right:2px;'>Manage</button>`;
						let monitorId = row.device_type == 0 ? 'pppoe-' + row.username : row.username;
						menu += `<button class='btn btn-sm btn-success monitor' data-id='${monitorId}' style='margin-right:2px;'>Monitor</button>`;
						return menu;
					},
					"initComplete": function(settings, json) {
						$(settings.nTableWrapper).find('.dataTables_length select').addClass('form-control');
					}
				}
				// Add or remove columns as needed
			]
		});

		// Handle column visibility toggle
		$("#columnToggle").on("change", function() {
			var selectedOptions = $(this).val(); // Get the selected values from the multi-select box

			// Loop through all columns to set visibility
			table.columns().every(function() {
				var column = this;
				var columnIndex = column.index(); // Get the index of the column
				var toggleVisibility = selectedOptions.includes(String(columnIndex)); // Check if the column index is in the selected options

				// Toggle visibility based on the selected values
				column.visible(toggleVisibility);
			});
		});

		$('#searchText1, #type, #expirationFilter3, #manager, #srvid').change(function() {
			table.ajax.reload();
		});

		$('#subscribersTable tbody').on('click', '.btn.recharge', function() {
			var username = $(this).data('id'); // Get the username from the data attribute

			// Redirect to the subscriberProfile method with the username as a parameter
			window.location.href = "<?php echo site_url('recharge'); ?>/" + username;
		});

		$('#subscribersTable tbody').on('click', '.btn.view', function() {
			var username = $(this).data('id'); // Get the username from the data attribute

			// Redirect to the subscriberProfile method with the username as a parameter
			window.location.href = "<?php echo site_url('usersDashboard'); ?>/" + username;
		});

		$('#subscribersTable tbody').on('click', '.btn.manage', function() {
			var username = $(this).data('id'); // Get the username from the data attribute

			// Redirect to the subscriberProfile method with the username as a parameter
			window.location.href = "<?php echo site_url('usersEdit'); ?>/" + username;
		});

		$('#subscribersTable tbody').on('click', '.btn.monitor', function() {
			var username = $(this).data('id'); // Get the username from the data attribute

			// Redirect to the subscriberProfile method with the username as a parameter
			window.location.href = "<?php echo site_url('snmp_controller/get_usertraffic_mbps'); ?>/" + username;
		});

		// After table draw, check monitoring for each row
		$('#subscribersTable').on('draw.dt', function() {
			$('#subscribersTable tbody tr').each(function() {
				var $row = $(this);
				var username = $row.find('td').eq(0).text().trim(); // Username is first column
				// Only add if not already present
				if ($row.find('.btn.monitor').length === 0 && username) {
					$.get('<?php echo site_url('snmp_controller/ajax_check_monitoring'); ?>', {username: username}, function(data) {
						if (data.monitor) {
							var $menu = $row.find('.dropdown-menu');
							if ($menu.length) {
								$menu.append(`<a class='dropdown-item monitor' data-id='${username}'>Monitor</a>`);
							}
						}
					}, 'json');
				}
			});
		});

		$('#subscribersTable tbody').on('click', '.dropdown-item.monitor1', function() {
			var username = $(this).data('id');
			window.location.href = "<?php echo site_url('snmp_controller/get_usertraffic_mbps'); ?>/" + username;
		});

	});

</script>

<!-- Before </body> -->
<script src="<?php echo base_url(); ?>assets/plugins/select2/js/select2.full.min.js"></script>
<!-- Bootstrap4 Duallistbox -->
<script src="<?php echo base_url(); ?>/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<script>
$(function () {
    //Initialize Select2 Elements
    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })

	    //Bootstrap Duallistbox
		$('.duallistbox').bootstrapDualListbox()
});
</script>

<style>
/* Style for Select2 selected choices (tags) */
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #007bff !important;   /* Bootstrap primary blue */
    color: #fff !important;                 /* White text */
    border: 1px solid #007bff !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #fff !important;                 /* White 'x' */
    margin-right: 4px;
}
</style>