<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Invoices</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="index.html">Billing</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Invoices
                                </li>
                            </ol>
                        </nav>
                    </div>


                    <div class="col-md-6 col-sm-12 text-right">
                        <div class="dropdown">
                            <a class="btn btn-primary dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                Menu
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#">Export List</a>
                                <a class="dropdown-item" href="#">Policies</a>
                                <a class="dropdown-item" href="#">View Assets</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="faq-wrap">

                <div id="accordion">
                    <div class="card">
                        <div class="card-header">
                            <button
                                class="btn btn-block"
                                data-toggle="collapse"
                                data-target="#faq1">
                                Filter Invoices
                            </button>
                        </div>
                        <div id="faq1" class="collapse show" data-parent="#accordion">
                            <div class="card-body">

                                <form>

                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <select name="type" id="type" class="custom-select2 form-control" style="width: 100%; height: 38px">
                                                    <option value="0" selected>All Invoices</option>
                                                    <option value="1">PPPoE Invoices</option>
                                                    <option value="2">Hotspot Invoices</option>
                                                    <option value="3">Manager Invoices</option>
                                                </select>

                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <select class="custom-select2 form-control" id="manager" name="manager" style="width: 100%; height: 38px">
                                                    <?php
                                                    if (!empty($managers)) {
                                                    ?> <option value="">Select Owner</option>
                                                        <?php
                                                        foreach ($managers as $rl) {
                                                        ?>
                                                            <option value="<?php echo $rl->managername ?>" <?php if ($rl->managername == set_value('managername')) {
                                                                                                                echo "selected=selected";
                                                                                                            } ?>><?php echo $rl->managername ?></option>
                                                        <?php
                                                        }
                                                    } else {
                                                        ?>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>

                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <select class="custom-select2 form-control" name="statusid" style="width: 100%; height: 38px" id="statusid">
                                                    <option value=0 selected>Select Status</option>
                                                    <optgroup label="Invoice Status">
                                                        <option value=1>Unpaid</option>
                                                        <option value=2>Paid</option>
                                                        <option value=3>Due</option>
                                                        <option value=4>Auto Blocked</option>
                                                    </optgroup>
                                                    <optgroup label="Accounts">
                                                        <option value=5> Posted</option>
                                                        <option value=6> Adjustment</option>
                                                    </optgroup>
                                                </select>
                                            </div>
                                        </div>



                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <select class="custom-select2 form-control" id="wallet" name="wallet" style="width: 100%; height: 38px">
                                                    <?php
                                                    if (!empty($wallets)) {
                                                    ?> <option value="">Select Wallet</option>
                                                        <?php
                                                        foreach ($wallets as $rl) {
                                                        ?>
                                                            <option value="<?php echo $rl->walletid ?>" <?php if ($rl->walletname == set_value('srvid')) {
                                                                                                            echo "selected=selected";
                                                                                                        } ?>><?php echo $rl->walletname; ?></option>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>

                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <select class="custom-select2 form-control" id="userid" name="userid" style="width: 100%; height: 38px">
                                                    <?php
                                                    if (!empty($users)) {
                                                    ?> <option value="">Select User</option>
                                                        <?php
                                                        foreach ($users as $rl) {
                                                        ?>
                                                            <option value="<?php echo $rl->username ?>" <?php if ($rl->username == set_value('username')) {
                                                                                                            echo "selected=selected";
                                                                                                        } ?>><?php echo $rl->username; ?></option>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <input class="form-control date-picker" placeholder="From Date" type="text" id="fromdate" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <input class="form-control date-picker" placeholder="To Date" type="text" id="todate" readonly>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="faq-wrap">

                <div id="accordion">
                    <div class="card">
                        <div class="card-header">
                            <button
                                class="btn btn-block collapsed"
                                data-toggle="collapse"
                                data-target="#faq2">
                                Add Remove Columns
                            </button>
                        </div>
                        <div id="faq2" class="collapse" data-parent="#accordion">
                            <div class="card-body">

                                <div class="form-group">
                                    <select class="custom-select2 form-control" multiple="multiple" style="width: 100%" id="columnToggle">
                                        <optgroup label="User Profile">
                                            <option value="0" selected>USERNAME</option>
                                            <option value="1" selected>TYPE</option>
                                            <option value="2" selected>SERVICE</option>
                                            <option value="3" selected>AMOUNT</option>
                                            <option value="4" selected>SRV-DATE</option>
                                            <option value="5" selected>EXP-DATE</option>
                                            <option value="6">INV-DATE</option>
                                        </optgroup>
                                        <optgroup label="DETAILS">
                                            <option value="7" selected>WALLET</option>
                                            <option value="8" selected>STATUS</option>
                                            <option value="9" selected>OWNER</option>
                                            <option value="10">REMARKS</option>
                                            <option value="11" selected>ACTION</option>
                                        </optgroup>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="pd-20 card-box mb-30">

                <div class="pb-20">
                    <table id="subscribersTable" class="data-table table stripe hover nowrap">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Type</th>
                                <th>Service</th>
                                <th>Amount</th>
                                <th>Renew On</th>
                                <th>Expires On</th>
                                <th>Invoice On</th>
                                <th>Wallet</th>
                                <th>Owner</th>

                                <th>Status</th>
                                <th>Remarks</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="3"></th>
                                <th></th>
                                <th></th>
                                <th colspan="6"></th>
                            </tr>
                        </tfoot>
                        <tbody>

                        </tbody>
                    </table>



                    <!-- Bootstrap Modal -->
                    <div class="modal fade" id="actionModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalLabel">Invoice</h5>
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

                </div>
            </div>

        </div>
    </div>
</div>


<style>
    table.dataTable {
        table-layout: auto;
        width: 100%;
    }
</style>

<script>
    $(document).ready(function() {

        var table = $('#subscribersTable').DataTable({
            responsive: false,
            scrollCollapse: false,
            autoWidth: true,
            "searchDelay": 3000,
            "processing": false,
            "serverSide": true,
            "scrollX": true,
            "ajax": {
                "url": "<?php echo site_url('Invoices/invoiceListDataTable') ?>",
                "type": "POST",
                "data": function(d) {
                    d.statusid = $('#statusid').val();
                    d.type = $('#type').val();
                    d.owner = $('#manager').val();
                    d.userid = $('#userid').val();
                    d.wallet = $('#wallet').val();
                    d.fromdate = $('#fromdate').val();
                    d.todate = $('#todate').val();
                },
                "error": function(xhr, error, thrown) {
                    console.log("Error occurred: ", thrown);
                    alert("An error occurred: " + thrown);
                },
                "dataSrc": function(json) {
                    console.log(json.logs.status_id_value); // Log the entire JSON response for debugging

                    // Update the footer with total cost and total due
                    if (json.totals) {
                        // Combine the totals into one string with line breaks
                        var totalsHTML = 'Total Value = Rs.' + json.totals.totalCost.toFixed(2) + '<br>' +
                            'Amount Due = Rs.' + json.totals.totalDue.toFixed(2);

                        // Set the innerHTML of the first column footer to display the totals
                        $(table.column(0).footer()).html(totalsHTML);
                    }

                    return json.data;
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
                [5, 15, 50, 100, 200, 500, 1000],
                ['5', '15', '50', '100', '200', '500', '1000']
            ],
            "columnDefs": [{
                    "targets": 0,
                    "width": "100%"
                },
                {
                    "targets": 1,
                    "width": "100%"
                },
                {
                    "targets": 2,
                    "width": "100%"
                },
                {
                    "targets": 3,
                    "width": "100%"
                },
                {
                    "targets": 4,
                    "width": "100%"
                },
                {
                    "targets": 5,
                    "width": "100%"
                },
                {
                    "targets": 6,
                    "width": "100%"
                },
                {
                    "targets": 7,
                    "width": "100%"
                },
                {
                    "targets": 8,
                    "width": "100%"
                },
                {
                    "targets": 9,
                    "width": "100%"
                },
                {
                    "targets": 10,
                    "width": "100%"
                },
            ],
            "columns": [{
                    "data": "username"
                }, // Adjust according to your data fields
                {
                    "data": "invtype"
                },
                {
                    "data": "srvname"
                },
                {
                    "data": "costprice"
                },
                {
                    "data": "srvdate"
                },
                {
                    "data": "expdate"
                },
                {
                    "data": "createdDtm",
                    "visible": false
                },
                {
                    "data": "walletname"
                },

                {
                    "data": "managername",
                    "visible": true
                },

                {
                    "data": "statusid",
                    "render": function(data, type, row) {
                        // Convert the data to an integer if it's not already
                        var postedValue = parseInt(data, 10);
                        var statusText = '';
                        var statusColor = '';

                        switch (postedValue) {
                            case 0:
                                statusText = 'UNPAID';
                                statusColor = 'orange';
                                break;
                            case 1:
                                statusText = 'PAID';
                                statusColor = 'blue';
                                break;
                            case 2:
                                statusText = 'DUE';
                                statusColor = 'red';
                                break;
                            case 3:
                                statusText = 'BLOCKED';
                                statusColor = 'gray';
                                break;
                            case 4:
                                statusText = 'POSTED';
                                statusColor = 'green';
                                break;
                            case 5:
                                statusText = 'ADJUSTMENT';
                                statusColor = 'purple';
                                break;
                            default:
                                statusText = 'UNKNOWN';
                                statusColor = 'black';
                        }
                        return '<span style="color:' + statusColor + '">' + statusText + '</span>';
                    }
                },

                {
                    "data": "remarks",
                    "visible": false
                },
                {
                    "data": "action",
                    "render": function(data, type, row) {
                        return `
							<div class="dropdown">
								<button class='btn btn-link font-24 p-0 line-height-1 dropdown-toggle' data-toggle='dropdown' role='button'>
									<i class='fa fa-ellipsis-v'></i>
								</button>

								<div class='dropdown-menu'>
									<a class='dropdown-item view' data-id='${row.username}'>View</a>
									<a class='dropdown-item manage' data-id='${row.username}'>Print</a>
								</div>
							</div>
                    	`;
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

        $('#statusid, #type, #manager, #userid, #wallet').on('change', function() {
            table.ajax.reload();
        });

        $('#fromdate, #todate').datepicker({
            onSelect: function() {
                table.ajax.reload();
            }
        });

        // Use event delegation to handle clicks on dynamically generated Recharge buttons
        $('#subscribersTable tbody').on('click', '.view', function() {
            var userId = $(this).data('id'); // Retrieve the user ID from the data-id attribute

            // Perform the AJAX request to load the Recharge form into the modal
            $.ajax({
                url: "<?php echo site_url('Invoices/subscriberInvoice'); ?>",
                type: "POST",
                data: {
                    userId: userId
                },
                success: function(data) {
                    //console.log(data);
                    // Assuming you have a modal with an ID of actionModal and a body class of modal-body
                    $('#actionModal .modal-body').html(data);
                    $('#actionModal').modal('show');
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });


        // Limit Users for Selected Manager
        $('#manager').change(function() {
            var managername = $('#manager').val();

            if (managername != 0) {

                $.ajax({
                    url: "<?php echo base_url() ?>Invoices/getUsersByManager",
                    method: "POST",
                    data: {
                        owner: managername
                    },
                    dataType: 'json',
                    success: function(response) {
                        var users = response;

                        //$('#price').val(unitprice);
                        //$('#amount').val(saleprice);
                        console.log(users);

                        // Empty the target field
                        $('#userid').empty();

                        // For each chocie in the selected option
                        $('#userid').append("<option value=''>Select User</option>");
                        for (i = 0; i < users.length; i++) {
                            // Output choice in the target field
                            $('#userid').append("<option value=" + users[i].invoiceuser + ">" + users[i].invoiceuser + "-" + users[i].type + "</option>");
                        }

                    }
                });

            } else {
                alert("Operation Failed...")
            }
        });


    });
</script>