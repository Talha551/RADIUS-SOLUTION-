<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-users"></i> Transaction History
                        <small>Credit & Recharge</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Transaction History</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <!-- Flash messages -->
            <div class="row">
                <div class="col-md-12">
                    <?php
                        $this->load->helper('form');
                        $error = $this->session->flashdata('error');
                        if($error)
                        {
                    ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('error'); ?>                    
                    </div>
                    <?php } ?>
                    <?php  
                        $success = $this->session->flashdata('success');
                        if($success)
                        {
                    ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                    <?php } ?>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo validation_errors('<div class="alert alert-danger alert-dismissible">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-10">
                    <!-- Search Form -->
                    <form action="<?php echo base_url() ?>invoiceListing" method="POST" id="searchList">
                        <div class="row align-items-end">
                            <div class="col-md-<?php echo ($this->session->userdata('name') == 'admin') ? '3' : '3'; ?> mb-3">
                                <label for="searchText">Search Text</label>
                                <input type="text" id="searchText" name="searchText" value="<?php echo isset($searchText) ? $searchText : ''; ?>" class="form-control" placeholder="Search text..."/>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="searchUsername">User Name</label>
                                <select id="searchUsername" name="searchUsername" class="form-control select2" style="width: 100%;">
                                    <option value="">Select Username</option>
                                    <?php
                                    if(!empty($usernames)) {
                                        foreach($usernames as $user) {
                                            $selected = (isset($searchUsername) && $user->username == $searchUsername) ? 'selected' : '';
                                            echo '<option value="'.$user->username.'" '.$selected.'>'.$user->username.'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <?php if($this->session->userdata('name') == 'admin' || $this->ismaster > 0): ?>
                            <div class="col-md-2 mb-3">
                                <label for="searchManager">Manager</label>
                                <select id="searchManager" name="searchManager" class="form-control select2" style="width: 100%;">
                                    <option value="">Select Manager</option>
                                    <?php
                                    if(!empty($managerList)) {
                                        foreach($managerList as $manager) {
                                            $selected = (isset($searchManager) && $manager->managername == $searchManager) ? 'selected' : '';
                                            echo '<option value="'.$manager->managername.'" '.$selected.'>'.$manager->managername.'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php endif; ?>

                            <div class="col-md-<?php echo ($this->session->userdata('name') == 'admin') ? '2' : '2'; ?> mb-3">
                                <label for="searchRenewDate">Renew From</label>
                                <input type="date" id="searchRenewDate" name="searchRenewDate" value="<?php echo $searchRenewDate; ?>" class="form-control"/>
                            </div>
                            <div class="col-md-<?php echo ($this->session->userdata('name') == 'admin') ? '2' : '2'; ?> mb-3">
                                <label for="searchRenewDateTo">Renew To</label>
                                <input type="date" id="searchRenewDateTo" name="searchRenewDateTo" value="<?php echo $searchRenewDateTo; ?>" class="form-control"/>
                            </div>
                            <div class="col-md-2 mb-3">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                            <div class="col-md-3 mb-3">
                                <button id="map-payments-btn" class="btn btn-info ml-3">Show EasyPaisa Payments</button>
                            </div>
                        </div>
                    </form>
                </div>
                
            </div>

            <div class="row">

            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Transactions List</h3>
                            
                            <?php  
                                if(isset($grand_totals->amount) || isset($grand_totals->paid)): ?>
                            <div class="card-tools">
      
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <span class="badge badge-info">Net Amount: Rs. <?php echo number_format($grand_totals->amount, 2); ?></span>
                                        <span class="badge badge-success">Net Paid: Rs. <?php echo number_format($grand_totals->paid, 2); ?></span>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <small class="text-muted">
                                            <strong>Breakdown by Type:</strong> 
                                            <?php if($grand_totals->activation_amount != 0): ?>
                                                <span class="badge badge-secondary">Activation: Rs. <?php echo number_format($grand_totals->activation_amount, 2); ?></span>
                                            <?php endif; ?>
                                            <?php if($grand_totals->credit_amount != 0): ?>
                                                <span class="badge badge-primary">Credit: Rs. <?php echo number_format($grand_totals->credit_amount, 2); ?></span>
                                            <?php endif; ?>
                                            <?php if($grand_totals->debit_amount != 0): ?>
                                                <span class="badge badge-danger">Debit: Rs. <?php echo number_format($grand_totals->debit_amount, 2); ?></span>
                                            <?php endif; ?>
                                            <?php if($grand_totals->gracedays_amount != 0): ?>
                                                <span class="badge badge-warning">Gracedays: Rs. <?php echo number_format($grand_totals->gracedays_amount, 2); ?></span>
                                            <?php endif; ?>
                                            <?php if($grand_totals->recharge_amount != 0): ?>
                                                <span class="badge badge-info">Recharge: Rs. <?php echo number_format($grand_totals->recharge_amount, 2); ?></span>
                                            <?php endif; ?>
                                            <?php if($grand_totals->refund_amount != 0): ?>
                                                <span class="badge badge-dark">Refund: Rs. <?php echo number_format($grand_totals->refund_amount, 2); ?></span>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Table -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>User Name</th>
                                        <?php if($this->session->userdata('name') == 'admin'): ?>
                                            <th>Manager Name</th>
                                        <?php endif; ?>
                                        <th>Type</th>
                                        <th>Service</th>
                                        <th>Amount</th>
                                        <th>Paid</th>
                                        <th>Renew On</th>
                                        <th>Expires On</th>
                                        <th>Trans Date</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(!empty($invoiceRecords))
                                    {

                                        $row_count = 1;
                                        foreach($invoiceRecords as $record)
                                        {
                                    ?>
                                    <tr class="invoice-row" data-username="<?php echo htmlspecialchars($record->username); ?>">
                                        <td class="text-nowrap <?php 
                                            $invType = $record->invtype;
                                            if($invType == 'Credit')
                                                echo 'text-primary';
                                        ?>"><?php echo $record->serial_number;?>.</td>

                                        <td class="text-nowrap <?php 
                                            $invType = $record->invtype;
                                            if($invType == 'Credit')
                                                echo 'text-primary';
                                        ?>"><?php echo htmlspecialchars($record->username); ?></td>
                                        
                                        <?php 
                                        if($this->session->userdata('name') == 'admin'): ?>
                                        <td class="text-nowrap"><?php echo htmlspecialchars($record->managername); ?></td>
                                        <?php endif; ?>

                                        <td class="text-nowrap <?php 
                                            $invType = $record->invtype;
                                            if($invType == 'Credit')
                                                echo 'text-primary';
                                        ?>"><?php echo $record->invtype ?></td>

                                        <td class="text-nowrap <?php 
                                            $invType = $record->invtype;
                                            if($invType == 'Credit')
                                                echo 'text-primary';
                                        ?>"><?php if($record->srvname <> NULL){ echo $record->srvname; }else{ echo "Reseller Credit"; } ?></td>                        

                                        <td class="text-nowrap <?php 
                                            $invType = $record->invtype;
                                            if($invType == 'Credit')
                                                echo 'text-primary';
                                        ?>"><?php echo $record->amount ?></td>

                                        
                                        <td class="text-nowrap <?php 
                                            $invType = $record->invtype;
                                            if($invType == 'Credit')
                                                echo 'text-primary';
                                        ?>"><?php echo $record->paid ?></td>

                                        <td class="text-nowrap <?php 
                                            $invType = $record->invtype;
                                            if($invType == 'Credit')
                                                echo 'text-primary';
                                        ?>"><?php echo $record->srvdate ?></td>
                                        
                                        <td class="text-nowrap <?php 
                                            $invType = $record->invtype;
                                            if($invType == 'Credit')
                                                echo 'text-primary';
                                        ?>"><?php echo $record->expdate ?></td>

                                        <td class="text-nowrap <?php 
                                            $invType = $record->invtype;
                                            if($invType == 'Credit')
                                                echo 'text-primary';
                                        ?>"><?php echo $record->createdDtm ?></td>
                                       
                                        <td class="<?php 
                                            $invType = $record->invtype;
                                            if($invType == 'Credit')
                                                echo 'text-primary';
                                        ?>"><?php if($record->eppay<>NULL){ echo "(EasyPaisa Pay:".$record->eppay.") ".$record->remarks; } else { echo $record->remarks; } ?></td>
                                    </tr>
                                    <?php
                                            $row_count++;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div><!-- /.card-body -->
                        
                        <div class="card-footer clearfix">
                            <?php echo $this->pagination->create_links(); ?>
                        </div>
                    </div><!-- /.card -->
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>
<script type="text/javascript">
    jQuery(document).ready(function(){
        // Store payment state
        var paymentsVisible = false;
        
        jQuery('ul.pagination li a').click(function (e) {
            e.preventDefault();            
            var link = jQuery(this).get(0).href;            
            var value = link.substring(link.lastIndexOf('/') + 1);
            jQuery("#searchList").attr("action", baseURL + "invoiceListing/" + value);
            jQuery("#searchList").submit();
        });
        
        // Initialize Select2 for better dropdown experience
        $('.select2').select2({
            placeholder: "Select Username",
            allowClear: true
        });

        // Use event delegation to prevent interference
        $(document).on('click', '#map-payments-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $btn = $(this);
            var originalText = $btn.text();
            
            // Toggle payments visibility
            if (paymentsVisible) {
                // Hide payments
                $('.payment-row').remove();
                paymentsVisible = false;
                $btn.text('Show EasyPaisa Payments');
                $btn.removeClass('btn-warning').addClass('btn-info');
            } else {
                // Show payments
                $btn.text('Loading...').prop('disabled', true);
                
                // Remove any previous payment rows first
                $('.payment-row').remove();
                
                // Collect all visible usernames
                var usernames = [];
                $('.invoice-row').each(function() {
                    var username = $(this).data('username');
                    if (username && usernames.indexOf(username) === -1) {
                        usernames.push(username);
                    }
                });
                
                if (usernames.length === 0) {
                    alert('No usernames found to map payments.');
                    $btn.text(originalText).prop('disabled', false);
                    return;
                }
                
                $.post('<?php echo base_url('Invoices/ajaxMapPayments'); ?>', 
                    {usernames: usernames}, 
                    function(resp) {
                        if (resp.success && resp.data) {
                            $('.invoice-row').each(function() {
                                var username = $(this).data('username');
                                if (resp.data[username] && resp.data[username].length > 0) {
                                    var html = '<tr class="payment-row" style="background-color: #f8f9fa; border-left: 4px solid #007bff;">';
                                    html += '<td colspan="100" style="padding: 10px;">';
                                    html += '<div class="d-flex justify-content-between align-items-center">';
                                    html += '<strong style="color: #007bff;">Recent EasyPaisa Payments:</strong>';
                                    html += '<small class="text-muted">' + resp.data[username].length + ' payment(s) found</small>';
                                    html += '</div>';
                                    html += '<ul style="margin: 10px 0 0 0; padding-left: 20px;">';
                                    $.each(resp.data[username], function(i, pay) {
                                        html += '<li style="margin-bottom: 5px;">';
                                        html += '<strong>Consumer:</strong> ' + pay.consumer_number + ', ';
                                        html += '<strong>Amount:</strong> ' + pay.amount_paid + ', ';
                                        html += '<strong>Date:</strong> ' + pay.transaction_date + ', ';
                                        html += '<strong>Import:</strong> ' + pay.importdate;
                                        html += '</li>';
                                    });
                                    html += '</ul></td></tr>';
                                    $(this).after(html);
                                }
                            });
                            paymentsVisible = true;
                            $btn.text('Hide EasyPaisa Payments').removeClass('btn-info').addClass('btn-warning');
                        } else {
                            alert('No payment data found or error occurred.');
                            $btn.text(originalText);
                        }
                    }, 'json'
                ).fail(function() {
                    alert('Error loading payment data. Please try again.');
                    $btn.text(originalText);
                }).always(function() {
                    $btn.prop('disabled', false);
                });
            }
        });
        
        // Prevent other event handlers from interfering with payment rows
        $(document).on('click', '.payment-row', function(e) {
            e.stopPropagation();
        });
        
        // Prevent form submission from affecting payment rows
        $('#searchList').on('submit', function() {
            // Clear payment state when form is submitted
            paymentsVisible = false;
        });
    });
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
