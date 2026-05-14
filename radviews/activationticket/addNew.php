<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-ticket-alt"></i> Activation Invoice <small>Add New</small></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active"> Activation Ticket</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 mx-auto">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Generate Activation Ticket/Invoice</h3>
                        </div>
                        <form id="activationTicketForm" action="" method="post">
                            <div class="card-body">
                                <?php if (!empty($error)) { ?>
                                    <div class="alert alert-danger alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                        <?php echo $error; ?>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($success)) { ?>
                                    <div class="alert alert-success alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                        Activation ticket created successfully.
                                    </div>
                                <?php } ?>

                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="actstatus">Status</label>
                                            <select class="form-control" id="actstatus" name="actstatus" required>
                                                <?php foreach ($actstatus as $key => $label): ?>
                                                    <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="acttype">Type</label>
                                            <select class="form-control" id="acttype" name="acttype" required>
                                                <option value="">Select Type</option>
                                                <?php foreach ($acttypes as $key => $label): ?>
                                                    <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="username">Select User</label>
                                            <select class="form-control select2" id="username" name="username" required>
                                                <option value="">Select User</option>
                                                <?php foreach ($users as $user): ?>
                                                    <option value="<?php echo $user->username; ?>"><?php echo $user->username . ' - ' . $user->firstname . ' ' . $user->lastname; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="srvdate">Service Date</label>
                                            <input type="date" class="form-control" id="srvdate" name="srvdate" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="expdate">Expiration Date</label>
                                            <input type="date" class="form-control" id="expdate" name="expdate" required>
                                        </div>
                                    </div>
                                
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="totalamount">Total Amount</label>
                                            <input type="number" class="form-control" id="totalamount" name="totalamount" step="0.01" required readonly>
                                        </div>
                                    </div>
                                    

                                </div>
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <label for="remarks">Remarks</label>
                                            <textarea class="form-control" id="remarks" name="remarks" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h5>Details</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="detailsTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Account</th>
                                                <th>Details</th>
                                                <th>Qty</th>
                                                <th>Price</th>
                                                <th>Amount</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <select name="acctid[]" class="form-control" required>
                                                        <option value="">Select Account</option>
                                                        <?php foreach ($accounts as $acc): ?>
                                                            <option value="<?php echo $acc->acctid; ?>"><?php echo $acc->accname; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td><input type="text" name="details[]" class="form-control" required></td>
                                                <td><input type="number" name="invqty[]" class="form-control" min="1" value="1" required onchange="calculateRowAmount(this)"></td>
                                                <td><input type="number" name="invprice[]" class="form-control" step="0.01" required onchange="calculateRowAmount(this)"></td>
                                                <td><input type="number" name="invamount[]" class="form-control" step="0.01" readonly></td>
                                                <td><button type="button" class="btn btn-danger btn-sm removeDetailRow"><i class="fa fa-trash"></i></button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-success btn-sm" id="addDetailRow"><i class="fa fa-plus"></i> Add Row</button>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12 text-right">
                                        <button type="submit" class="btn btn-primary">Save Ticket</button>
                                        <a href="<?php echo base_url('Invoices/activationTicket_list'); ?>" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>


<script>
    $(document).ready(function(){
        // Get grace days settings from PHP variables
        var graceDaysOld = <?php echo isset($gracedays_old[0]->stgvalue) ? $gracedays_old[0]->stgvalue : 0; ?>;
        var graceDaysNew = <?php echo isset($gracedays_new[0]->stgvalue) ? $gracedays_new[0]->stgvalue : 0; ?>;
        
        // Set min and max dates for service date
        var today = new Date();
        var minDate = new Date(today);
        var maxDate = new Date(today);
        
        // Format dates for input fields
        function formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();
            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;
            return [year, month, day].join('-');
        }
        
        

        // Trigger change event on page load to set initial values
        //$('#acttype').trigger('change');

        // Add new detail row
        $('#addDetailRow').click(function(){
            var row = $('#detailsTable tbody tr:first').clone();
            row.find('input, select').val('');
            row.find('input[type=number]').val('0.00');
            row.find("input[name='invqty[]']").val('1');
            $('#detailsTable tbody').append(row);
            calculateTotal(); // Calculate total after adding new row
        });

        // Remove detail row
        $(document).on('click', '.removeDetailRow', function(){
            if($('#detailsTable tbody tr').length > 1){
                $(this).closest('tr').remove();
                calculateTotal(); // Calculate total after removing row
            }
        });

        // Auto-calculate amount and total
        $(document).on('input', "input[name='invqty[]'], input[name='invprice[]']", function(){
            var row = $(this).closest('tr');
            var qty = parseFloat(row.find("input[name='invqty[]']").val()) || 0;
            var price = parseFloat(row.find("input[name='invprice[]']").val()) || 0;
            var amount = (qty * price).toFixed(2);
            row.find("input[name='invamount[]']").val(amount);
            calculateTotal();
        });
    });

    // Update date limitations based on activation type
    $('#acttype').change(function() {

            var graceDaysOld = <?php echo isset($gracedays_old[0]->stgvalue) ? $gracedays_old[0]->stgvalue : 0; ?>;
            var graceDaysNew = <?php echo isset($gracedays_new[0]->stgvalue) ? $gracedays_new[0]->stgvalue : 0; ?>;

            console.log("Grace Days New:"+graceDaysOld);
            
            var acttype = $(this).val();
            var today = new Date();

            // Set min and max dates for service date
            var today = new Date();
            var minDate = new Date(today);
            var maxDate = new Date(today);
            
            // Format dates for input fields
            function formatDate(date) {
                var d = new Date(date),
                    month = '' + (d.getMonth() + 1),
                    day = '' + d.getDate(),
                    year = d.getFullYear();
                if (month.length < 2) month = '0' + month;
                if (day.length < 2) day = '0' + day;
                return [year, month, day].join('-');
            }
            
            if (acttype == 1) { // Activation
                // For Activation, use graceDaysOld
                var maxExpDate = new Date(today);
                maxExpDate.setDate(today.getDate() + parseInt(graceDaysOld));
                
                // Set service date to today
                $('#srvdate').val(formatDate(today));
                $('#srvdate').attr('min', formatDate(today));
                $('#srvdate').attr('max', formatDate(today));
                
                // Set expiration date to today + graceDaysOld
                $('#expdate').val(formatDate(maxExpDate));
                $('#expdate').attr('min', formatDate(today));
                $('#expdate').attr('max', formatDate(maxExpDate));
                
            } else if (acttype == 0) { // Installation
                // For Installation, use graceDaysNew
                var maxExpDate = new Date(today);
                maxExpDate.setDate(today.getDate() + parseInt(graceDaysNew));

                console.log("Max Day:"+maxExpDate);
                
                // Set service date to today
                $('#srvdate').val(formatDate(today));
                $('#srvdate').attr('min', formatDate(today));
                $('#srvdate').attr('max', formatDate(today));
                
                // Set expiration date to today + graceDaysNew
                $('#expdate').val(formatDate(today));
                $('#expdate').attr('min', formatDate(today));
                $('#expdate').attr('max', formatDate(maxExpDate));

            } else if (acttype == 3) { // Installation
                // For Installation, use graceDaysNew
                var maxExpDate = new Date(today);
                var minSrvDate = new Date(today);
                maxExpDate.setDate(today.getDate() + parseInt(graceDaysNew));
                minSrvDate.setDate(today.getDate() - parseInt(graceDaysNew));

                console.log("Max Day:"+maxExpDate);
                
                // Set service date to today
                $('#srvdate').val(formatDate(today));
                $('#srvdate').attr('min', formatDate(minSrvDate));
                $('#srvdate').attr('max', formatDate(today));
                
                // Set expiration date to today + graceDaysNew
                $('#expdate').val(formatDate(maxExpDate));
                $('#expdate').attr('min', formatDate(today));
                //$('#expdate').attr('max', formatDate(maxExpDate));
            }else if (acttype == 2) { // Installation
                // For Installation, use graceDaysNew
                var maxExpDate = new Date(today);
                var minSrvDate = new Date(today);
                maxExpDate.setDate(today.getDate());
                minSrvDate.setDate(today.getDate());

                console.log("Max Day:"+maxExpDate);
                
                // Set service date to today
                $('#srvdate').val(formatDate(today));
                $('#srvdate').attr('min', formatDate(minSrvDate));
                $('#srvdate').attr('max', formatDate(today));
                
                // Set expiration date to today + graceDaysNew
                $('#expdate').val(formatDate(maxExpDate));
                $('#expdate').attr('min', formatDate(today));
                $('#expdate').attr('max', formatDate(maxExpDate));
            }
        });

    function calculateTotal() {
        var total = 0;
        $("input[name='invamount[]']").each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#totalamount').val(total.toFixed(2));
    }
</script> 



    <!-- Add Select2 CSS and JS files -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Custom CSS to fix Select2 height -->
    <style>
        /* Make Select2 height match other form controls */
        .select2-container .select2-selection--single {
            height: 38px !important;
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.5;
        }
        
        /* Adjust the dropdown arrow position */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        
        /* Fix the text vertical alignment */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px;
        }
        
        /* Match border radius with Bootstrap */
        .select2-container--default .select2-selection--single {
            border-radius: 4px;
            border-color: #ced4da;
        }
        
        /* Fix focus state */
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>

    <!-- Add Select2 CSS and JS files -->
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/select2/css/select2.min.css'); ?>">
    <script src="<?php echo base_url('assets/plugins/select2/js/select2.full.min.js'); ?>"></script>
    <script>
        $(function () {
            $('.select2').select2();
        });
    </script>