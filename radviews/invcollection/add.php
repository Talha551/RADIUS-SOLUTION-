<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- Add jQuery and other required libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-money-check-alt"></i> Invoice Collection <small>Add New</small></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Add Invoice Collection</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Add Invoice Collection</h3>
                        </div>
                        <form id="invcollectionForm" action="" method="post">
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
                                        Invoice collection added successfully.
                                    </div>
                                <?php } ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="walletid">Wallet</label>
                                            <select class="form-control required" id="walletid" name="walletid">
                                                <option value="">Select Wallet</option>
                                                <?php if (!empty($wallets)) {
                                                    foreach ($wallets as $wallet) { ?>
                                                        <option value="<?php echo $wallet->walletid; ?>"><?php echo htmlspecialchars($wallet->walletname); ?></option>
                                                <?php }} ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="paydate">Pay Date</label>
                                            <input type="date" class="form-control required" id="paydate" name="paydate" value="<?php echo set_value('paydate'); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="remarks">Remarks</label>
                                            <textarea class="form-control" id="remarks" name="remarks" rows="2"><?php echo set_value('remarks'); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">Save Collection</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card card-secondary mt-12">
                        <div class="card-header">
                            <h3 class="card-title">Invoice Selection</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <button class="btn btn-outline-info btn-sm" id="btnUnpaid">Unpaid</button>
                                <button class="btn btn-outline-success btn-sm" id="btnRecent">Recent</button>
                                <button class="btn btn-outline-secondary btn-sm" id="btnCustom">Custom</button>
                                <span id="customDateRange" style="display:none;">
                                    <input type="date" id="fromDate" class="form-control form-control-sm d-inline-block" style="width:130px;">
                                    <input type="date" id="toDate" class="form-control form-control-sm d-inline-block" style="width:130px;">
                                    <button class="btn btn-sm btn-primary" id="btnFetchCustom">Fetch</button>
                                </span>
                                <button class="btn btn-outline-info btn-sm ml-2" id="btnShowPayments">Show EasyPaisa Payments</button>
                                <label for="invoiceLimit" class="ml-2 mb-0">Show</label>
                                <select id="invoiceLimit" class="form-control form-control-sm d-inline-block" style="width:80px;">
                                    <option value="10">10</option>
                                    <option value="25" selected>25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="200">200</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="all">All</option>
                                </select>
                                <span class="ml-1">invoices</span>
                            </div>
                            <div id="invoiceGrid">
                                <!-- Invoice grid will be loaded here via AJAX -->
                                <div class="alert alert-info">No invoices loaded. Use the buttons above to load invoices.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- Optionally, show summary or help -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Collection Summary</h3>
                        </div>
                        <div class="card-body">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text">Selected Invoices</span>
                                    <span class="info-box-number" id="selectedInvoiceCount">0</span>
                                </div>
                            </div>
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Amount</span>
                                    <span class="info-box-number" id="selectedTotal">0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script type="text/javascript">
$(document).ready(function(){
    // Global variables to track current filter state
    var currentFilterType = 'unpaid';
    var currentFromDate = '';
    var currentToDate = '';
    
    // jQuery validation
    $('#invcollectionForm').validate({
        rules: {
            walletid: { required: true },
            paydate: { required: true }
        },
        messages: {
            walletid: { required: 'Please select a wallet.' },
            paydate: { required: 'Please select a pay date.' }
        },
        submitHandler: function(form) {
            // Override the default form submission
            createInvoiceCollection();
            return false;
        }
    });

    // Show/hide custom date range
    $('#btnCustom').click(function(){
        $('#customDateRange').toggle();
    });
    
    // AJAX functions for loading invoices
    function loadInvoices(filterType, fromDate, toDate) {
        // Update global filter state
        currentFilterType = filterType;
        currentFromDate = fromDate;
        currentToDate = toDate;
        
        $('#invoiceGrid').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
        var limit = $('#invoiceLimit').val();
        $.ajax({
            url: '<?php echo base_url("Invoices/invcollection_get_invoices"); ?>',
            type: 'POST',
            data: {
                filterType: filterType,
                fromDate: fromDate,
                toDate: toDate,
                limit: limit
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderInvoiceGrid(response.data);
                } else {
                    $('#invoiceGrid').html('<div class="alert alert-danger">Failed to load invoices.</div>');
                }
            },
            error: function() {
                $('#invoiceGrid').html('<div class="alert alert-danger">Error connecting to server.</div>');
            }
        });
    }
    
    function renderInvoiceGrid(invoices) {
        if (invoices.length === 0) {
            $('#invoiceGrid').html('<div class="alert alert-info">No invoices found matching the criteria.</div>');
            return;
        }
        
        let html = '<div class="table-responsive">';
        html += '<table class="table table-striped table-bordered table-hover">';
        html += '<thead><tr class="bg-info">';
        html += '<th><input type="checkbox" id="selectAll"></th>';
        html += '<th>Invoice #</th>';
        html += '<th>Date</th>';
        html += '<th>Manager</th>';
        html += '<th>Customer</th>';
        html += '<th>Amount</th>';
        html += '<th>Paid Amount</th>';
        html += '</tr></thead><tbody>';
        
        let totalAmount = 0;
        
        $.each(invoices, function(i, invoice) {
            const isPaid = invoice.paid > 0;
            const isSelectable = !isPaid;
            
            html += '<tr>';
            html += '<td>';
            if (isSelectable) {
                html += '<input type="checkbox" class="invoice-select" data-id="' + invoice.invid + '" data-amount="' + Math.abs(invoice.amount) + '">';
            }
            html += '</td>';
            html += '<td>' + invoice.invid + '</td>';
            html += '<td>' + invoice.invdate + '</td>';
            html += '<td>' + invoice.managername + '</td>';
            html += '<td>' + invoice.username + '</td>';
            html += '<td class="text-right">' + Math.abs(parseFloat(invoice.amount)).toFixed(2) + '</td>';
            
            // Show paid amount as editable input
            html += '<td class="text-right">';
            html += '<input type="number" class="form-control form-control-sm paid-amount" ' + 
                   'data-invid="' + invoice.invid + '" ' +
                   'value="' + (parseFloat(invoice.paid) || 0).toFixed(2) + '" ' +
                   'min="0" max="' + Math.abs(invoice.amount).toFixed(2) + '" ' +
                   'step="0.01" ' +
                   (isPaid ? 'readonly' : '') + '>';
            html += '</td>';
            html += '</tr>';
            
            if (isSelectable) {
                totalAmount += Math.abs(parseFloat(invoice.amount));
            }
        });
        
        html += '</tbody>';
        html += '<tfoot><tr class="bg-light">';
        html += '<td colspan="5" class="text-right"><strong>Total:</strong></td>';
        html += '<td class="text-right"><strong>' + totalAmount.toFixed(2) + '</strong></td>';
        html += '<td></td>';
        html += '</tr></tfoot>';
        html += '</table></div>';
        
        html += '<div class="mt-2">';
        html += '<button id="btnAddSelected" class="btn btn-success">Add Selected Invoices</button>';
        html += '</div>';
        
        $('#invoiceGrid').html(html);
        
        // Handle select all checkbox - only select unpaid invoices
        $('#selectAll').change(function() {
            $('.invoice-select').prop('checked', $(this).prop('checked'));
            // For each checked row, set paid amount if 0
            $('.invoice-select:checked').each(function() {
                var $checkbox = $(this);
                var $row = $checkbox.closest('tr');
                var $paidInput = $row.find('.paid-amount');
                var dueAmount = parseFloat($checkbox.data('amount'));
                var currentPaid = parseFloat($paidInput.val()) || 0;
                if (currentPaid === 0) {
                    $paidInput.val(dueAmount.toFixed(2));
                }
            });
            updateTotals();
        });
        
        // Handle individual checkbox changes
        $('.invoice-select').change(function() {
            var $checkbox = $(this);
            var $row = $checkbox.closest('tr');
            var $paidInput = $row.find('.paid-amount');
            var dueAmount = parseFloat($checkbox.data('amount'));
            var currentPaid = parseFloat($paidInput.val()) || 0;

            if ($checkbox.is(':checked')) {
                // Only set if current paid is 0
                if (currentPaid === 0) {
                    $paidInput.val(dueAmount.toFixed(2));
                }
            }
            updateTotals();
        });
        
        // Handle paid amount changes
        $('.paid-amount').change(function() {
            const invid = $(this).data('invid');
            const paidAmount = parseFloat($(this).val());
            const maxAmount = parseFloat($(this).attr('max'));
            
            if (paidAmount > maxAmount) {
                $(this).val(maxAmount.toFixed(2));
                alert('Paid amount cannot exceed invoice amount');
            }
            
            // Update the checkbox state based on paid amount
            const checkbox = $(this).closest('tr').find('.invoice-select');
            if (paidAmount > 0) {
                checkbox.prop('checked', false);
            }
            
            updateTotals();
        });
        
        // Handle add selected button
        $('#btnAddSelected').click(function() {
            createInvoiceCollection();
        });
        
        updateTotals();
    }
    
    function updateTotals() {
        let selectedTotal = 0;
        let selectedCount = 0;
        
        $('.invoice-select:checked').each(function() {
            selectedTotal += parseFloat($(this).data('amount'));
            selectedCount++;
        });
        
        $('#selectedTotal').text(selectedTotal.toFixed(2));
        $('#selectedInvoiceCount').text(selectedCount);
    }
    
    function createInvoiceCollection() {
        let selectedInvoices = [];
        let paidAmounts = {};
        
        // Collect both invoice IDs and their paid amounts
        $('.invoice-select:checked').each(function() {
            const invid = $(this).data('id');
            const paidAmount = parseFloat($(this).closest('tr').find('.paid-amount').val()) || 0;
            selectedInvoices.push(invid);
            paidAmounts[invid] = paidAmount;
        });
        
        if (selectedInvoices.length === 0) {
            alert('Please select at least one invoice');
            return;
        }
        
        // Check if form is valid first
        if (!$('#invcollectionForm').valid()) {
            alert('Please fill in all required fields in the form first');
            return;
        }
        
        // Get collection details
        const walletId = $('#walletid').val();
        const payDate = $('#paydate').val();
        const remarks = $('#remarks').val();
        
        // First submit the form to create the collection
        $.ajax({
            url: '<?php echo base_url("Invoices/invcollection_add"); ?>',
            type: 'POST',
            data: {
                walletid: walletId,
                paydate: payDate,
                remarks: remarks
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Now add invoices to the created collection with paid amounts
                    $.ajax({
                        url: '<?php echo base_url("Invoices/invcollection_add_invoices"); ?>',
                        type: 'POST',
                        data: {
                            invoiceIds: selectedInvoices,
                            collectionId: response.collectionId,
                            paidAmounts: paidAmounts
                        },
                        dataType: 'json',
                        success: function(result) {
                            if (result.success) {
                                alert('Successfully added ' + result.count + ' invoices to the collection');
                                // Redirect to collections list
                                window.location.href = '<?php echo base_url("Invoices/invcollection_list"); ?>';
                            } else {
                                alert('Error: ' + result.message);
                            }
                        },
                        error: function() {
                            alert('Error connecting to server when adding invoices');
                        }
                    });
                } else {
                    alert('Error creating collection: ' + (response.message || 'Unknown error'));
                }
            },
            error: function() {
                alert('Error connecting to server when creating collection');
            }
        });
    }
    
    // Button click handlers
    $('#btnUnpaid').click(function() {
        loadInvoices('unpaid', '', '');
    });
    
    $('#btnRecent').click(function() {
        loadInvoices('recent', '', '');
    });
    
    $('#btnFetchCustom').click(function() {
        const fromDate = $('#fromDate').val();
        const toDate = $('#toDate').val();
        
        if (!fromDate || !toDate) {
            alert('Please select both From and To dates');
            return;
        }
        
        loadInvoices('custom', fromDate, toDate);
    });
    
    // Show EasyPaisa Payments for visible invoices
    $('#btnShowPayments').on('click', function() {
        // Remove any previous payment rows
        $('.payment-row').remove();
        // Collect all visible usernames from the invoice grid
        var usernames = [];
        $('#invoiceGrid .invoice-select').each(function() {
            var $row = $(this).closest('tr');
            var username = $row.find('td').eq(4).text().trim(); // 5th column is Customer
            if (username) usernames.push(username);
        });
        if (usernames.length === 0) return;

        // Use current filter state for date range
        var fromDate = currentFromDate;
        var toDate = currentToDate;
        
        // Prepare data for AJAX request
        var postData = {usernames: usernames};
        if (fromDate && toDate) {
            postData.fromDate = fromDate;
            postData.toDate = toDate;
        }

        $.post('<?php echo base_url('Invoices/ajaxMapPayments'); ?>', postData, function(resp) {
            if (resp.success && resp.data) {
                $('#invoiceGrid .invoice-select').each(function() {
                    var $row = $(this).closest('tr');
                    var username = $row.find('td').eq(4).text().trim();
                    if (resp.data[username]) {
                        var dateRangeText = '';
                        if (fromDate && toDate) {
                            dateRangeText = ' (Date Range: ' + fromDate + ' to ' + toDate + ')';
                        } else {
                            dateRangeText = ' (Current Month)';
                        }
                        var html = '<tr class="payment-row"><td colspan="100"><b>Recent Payments:' + dateRangeText + '</b><ul style="margin-bottom:0;">';
                        $.each(resp.data[username], function(i, pay) {
                            html += '<li>Consumer: ' + pay.consumer_number + ', Amount: ' + pay.amount_paid + ', Date: ' + pay.transaction_date + ', Import: ' + pay.importdate + '</li>';
                        });
                        html += '</ul></td></tr>';
                        $row.after(html);
                        // Auto-select the row
                        $(this).prop('checked', true);
                        // Set paid amount to due amount if not already set
                        var $paidInput = $row.find('.paid-amount');
                        var dueAmount = parseFloat($(this).data('amount'));
                        if ($paidInput.length && (!parseFloat($paidInput.val()) || parseFloat($paidInput.val()) === 0)) {
                            $paidInput.val(dueAmount.toFixed(0));
                        }
                    }
                });
                // Update summary after auto-selecting
                updateTotals();
            }
        }, 'json');
    });
    
    // Trigger reload on limit change
    $('#invoiceLimit').change(function() {
        // Re-trigger the last used filter (default to unpaid)
        $('#btnUnpaid').trigger('click');
    });
    
    // Initialize with Unpaid invoices by default
    $('#btnUnpaid').trigger('click');
});
</script>
<style>
.payment-row { background: #e9f7ef; }
.payment-row ul { margin-bottom: 0; }
</style> 