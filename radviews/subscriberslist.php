

<head>
    <title>Users List</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    
    <!-- Above this Testing   -->


    
    
    <!-- Include other extensions you might need -->
</head>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <small>Users List</small>
      </h1>
    </section>

    <section class="content">

        

        <div class="row">
            <div class="box-header">
                <h3 class="box-title">Filter Users List</h3>
            </div>
        </div>

        <?php //$searchText1 = $type; ?>

        <div class="row">
            <div class="col-md-3">                                
                <div class="form-group">
                    <label for="searchText1">User Status</label>
                    <select name="searchText1" id="searchText1" class="selectpicker pull-right" style="width: 150px; height: 30px;">
                        <option value="0" "selected=selected">Active Users</option>
                        <option value="1">Paid</option>
                        <option value="2">Expired</option>
                        <option value="3">Blocked</option>
                        <option value="4">All</option>
                        <option value="5">Online</option>
                        <option value="6">Offline-Active</option>
                        <option value="7">Offline-Inactive</option>
                        <option value="8">Expiring in 3 Days</option>
                        <option value="9">Expiring in 1 Days</option>
                        <option value="10">Expired last 1 Day</option>
                        <option value="11">Expired last 3 Days</option>
                        <option value="12">Expired last 7 Days</option>
                    </select>
                </div>
            </div>

            <div class="col-md-3">                                
                <div class="form-group">
                    <label for="type">User Type</label>
                    <select name="type" id="type" class="selectpicker pull-right" style="width: 150px; height: 30px;">
                        <option value="0" "selected=selected">PPPoE</option>
                        <option value="2">Hot Spot</option>                        
                    </select>
                    
                </div>
            </div>
            
        </div>

        <div class="row">
            <div class="col-md-10">                                
                <div class="form-group">
                    <label for="columnToggle">Hide/Unhide Columns</label>
                    <div id="columnToggle">
                        <label><input type="checkbox" class="toggle-vis" data-column="0" checked> Username</label>
                        <label><input type="checkbox" class="toggle-vis" data-column="1" checked> First Name</label>
                        <label><input type="checkbox" class="toggle-vis" data-column="2" checked> Last Name</label>
                        <label><input type="checkbox" class="toggle-vis" data-column="3" checked> Owner</label>
                        <label><input type="checkbox" class="toggle-vis" data-column="4" unchecked> Address</label>
                        <label><input type="checkbox" class="toggle-vis" data-column="5" unchecked> City</label>
                        <label><input type="checkbox" class="toggle-vis" data-column="6" unchecked> Mobile</label>
                        <label><input type="checkbox" class="toggle-vis" data-column="7" checked> Package</label>
                        <label><input type="checkbox" class="toggle-vis" data-column="8" checked> Started</label>
                        <label><input type="checkbox" class="toggle-vis" data-column="9" checked> Expiry</label>
                        <label><input type="checkbox" class="toggle-vis" data-column="10" checked> Action</label>
                        <!-- Repeat for other columns as needed -->
                    </div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table id="usersTable" class="display" style="width:100%">
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
                        <!-- Add or remove columns as needed -->
                    </tr>
                </thead>
            </table>
        </div>

        <!-- Bootstrap Modal -->
        <div class="modal fade" id="actionModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Action</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Modal content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
        </div>

    <script>

    $(document).ready(function() {

        var table = $('#usersTable').DataTable({
            responsive: true,
            scroller: true,
            "searchDelay": 3000,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?php echo site_url('subscribers/getSubscribersList') ?>",
                "type": "POST",
                "data": function(d) {
                    d.searchText1 = $('#searchText1').val();
                    d.type = $('#type').val();
                    d.expirationFilter3 = $('#expirationFilter3').val();
                },
                "error": function (xhr, error, thrown) {
                    console.log("Error occurred: ", thrown);
                    alert("An error occurred: " + thrown);
                }
            },
            "dom": '<"top"Bif> rt<"bottom"lp><"clear">', // This option is used to initialize the Buttons extension
            "buttons": [
                'copy', 'csv', 'excel', 'pdf' // Define the buttons you want
            ],
            "lengthMenu": [ 
                [10, 25, 50, 100, 200, 500, 1000], ['10','25', '50', '100', '200', '500', '1000'] 
            ],
            "columns": [
                { "data": "username" }, // Adjust according to your data fields
                { "data": "firstname" },
                { "data": "lastname" },
                { "data": "owner" },
                { "data": "address", "visible": false },
                { "data": "city", "visible": false },
                { "data": "mobile", "visible": false },
                { "data": "servicename" },
                { "data": "createdon" },
                { "data": "expiration" },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return "<button class='btn btn-success recharge' data-id='" + row.username + "'><i class='fa fa-dollar'></i> Recharge</button> " +
                            "<button class='btn btn-primary view' data-id='" + row.username + "'><i class='fa fa-eye'></i> View</button> " +
                            "<button class='btn btn-info change' data-id='" + row.username + "'><i class='fa fa-exchange'></i> Change</button>";
                },
                "initComplete": function(settings, json) {
                    $(settings.nTableWrapper).find('.dataTables_length select').addClass('form-control');
                }
            }
                // Add or remove columns as needed
            ]
        });

        // Handle column visibility toggle
        $('input.toggle-vis').on('change', function(e) {
            // Get the column API object
            var column = table.column($(this).attr('data-column'));

            // Toggle the visibility
            column.visible(!column.visible());
        });

        $('#searchText1, #type, #expirationFilter3').change(function() {
            table.ajax.reload();
        });


        // Use event delegation to handle clicks on dynamically generated Recharge buttons
        $('#usersTable tbody').on('click', '.recharge', function() {
            var userId = $(this).data('id'); // Retrieve the user ID from the data-id attribute

            // Perform the AJAX request to load the Recharge form into the modal
            $.ajax({
                url: "<?php echo site_url('Invoices/invoiceAddNew'); ?>",
                type: "POST",
                data: { userId: userId },
                success: function(data) {
                    // Assuming you have a modal with an ID of actionModal and a body class of modal-body
                    $('#actionModal .modal-body').html(data);
                    $('#actionModal').modal('show');
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });


    });

    </script>
    </section>
</div>
