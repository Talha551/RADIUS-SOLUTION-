<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-users"></i> New Reseller
                        <small>Add / Edit User</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>resellerListing">Resellers</a></li>
                        <li class="breadcrumb-item active">New Reseller</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-8">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">New Reseller Details</h3>
                        </div><!-- /.card-header -->
                        
                        <!-- form start -->
                        <?php $this->load->helper("form"); ?>
                        <form role="form" id="saveNewReseller" action="<?php echo base_url() ?>saveNewReseller" method="post" role="form">
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" value="1" id="status" name="status" checked>
                                        <label class="custom-control-label" for="status">Enabled</label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="user">Reseller ID</label>
                                            <input type="text" class="form-control required" value="<?php echo set_value('user'); ?>" id="user" name="user" maxlength="50">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control required" id="password" name="password" maxlength="20">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cpassword">Confirm Password</label>
                                            <input type="password" class="form-control required equalTo" id="cpassword" name="cpassword" maxlength="20">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="master">Master</label>
                                            <?php $session_manager = $this->session->userdata ( 'name' ); ?>
                                            <select class="form-control required" id="master" name="master" <?php if($session_manager <> "admin") echo "readonly"; ?>>
                                                <option value="NONE">Select Master</option>
                                                <?php
                                                    if(!empty($resellers))
                                                    {
                                                        foreach ($resellers as $rl)
                                                        {
                                                            ?>
                                                            <option value="<?php echo $rl->managername ?>" <?php if($rl->managername == $session_manager) {echo "selected=selected";} ?>><?php echo $rl->managername ?></option>
                                                            <?php
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fname">First Name</label>
                                            <input type="text" class="form-control required" id="fname" value="<?php echo set_value('fname'); ?>" name="fname" maxlength="20">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lname">Last Name</label>
                                            <input type="text" class="form-control required" id="lname" value="<?php echo set_value('lname'); ?>" name="lname" maxlength="20">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <input type="text" class="form-control required" id="address" value="<?php echo set_value('address'); ?>" name="address" maxlength="100">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="city">City</label>
                                            <input type="text" class="form-control required" id="city" value="<?php echo set_value('city'); ?>" name="city" maxlength="100">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="mobile">Mobile Number</label>
                                            <input type="text" class="form-control required" id="mobile" value="<?php echo set_value('mobile'); ?>" name="mobile" maxlength="13">
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="email">Email address</label>
                                            <input type="text" class="form-control required email" id="email" value="<?php echo set_value('email'); ?>" name="email" maxlength="128">
                                        </div>
                                    </div>                               
                                
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="cnic">CNIC No.</label>
                                            <input type="text" class="form-control required" id="cnic" value="<?php echo set_value('cnic'); ?>" name="cnic" maxlength="16">
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.card-body -->
        
                            
                    </div>

                    <!-- Tabs Card -->
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#permissions" data-toggle="tab">Permissions</a></li>
                                <li class="nav-item"><a class="nav-link" href="#profiles" data-toggle="tab">Profiles</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="permissions">
                                    <div class="row">
                                        <!-- Users Permissions -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_listusers" name="perm_listusers" value="1" checked>
                                                    <label class="custom-control-label" for="perm_listusers">List users</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_createusers" name="perm_createusers" value="1" checked>
                                                    <label class="custom-control-label" for="perm_createusers">Register users</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_editusers" name="perm_editusers" value="1" checked>
                                                    <label class="custom-control-label" for="perm_editusers">Edit users</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_edituserspriv" name="perm_edituserspriv" value="1">
                                                    <label class="custom-control-label" for="perm_edituserspriv">Edit privileged user data</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_deleteusers" name="perm_deleteusers" value="1">
                                                    <label class="custom-control-label" for="perm_deleteusers">Delete users</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Managers Permissions -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_listmanagers" name="perm_listmanagers" value="1">
                                                    <label class="custom-control-label" for="perm_listmanagers">List managers</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_createmanagers" name="perm_createmanagers" value="1">
                                                    <label class="custom-control-label" for="perm_createmanagers">Register managers</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_editmanagers" name="perm_editmanagers" value="1">
                                                    <label class="custom-control-label" for="perm_editmanagers">Edit managers</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_deletemanagers" name="perm_deletemanagers" value="1">
                                                    <label class="custom-control-label" for="perm_deletemanagers">Delete managers</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Services Permissions -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_listservices" name="perm_listservices" value="1" checked>
                                                    <label class="custom-control-label" for="perm_listservices">List services</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_createservices" name="perm_createservices" value="1">
                                                    <label class="custom-control-label" for="perm_createservices">Register services</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_editservices" name="perm_editservices" value="1">
                                                    <label class="custom-control-label" for="perm_editservices">Edit services</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_deleteservices" name="perm_deleteservices" value="1">
                                                    <label class="custom-control-label" for="perm_deleteservices">Delete services</label>
                                                </div>
                                            </div>
                                        </div>
                                    
                                    </div>
                                    <div class="row mt-4">
                                        <!-- Billing Functions -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_addcredits" name="perm_addcredits" value="1">
                                                    <label class="custom-control-label" for="perm_addcredits">Billing functions</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_negbalance" name="perm_negbalance" value="1">
                                                    <label class="custom-control-label" for="perm_negbalance">Allow negative balance</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_allowdiscount" name="perm_allowdiscount" value="1">
                                                    <label class="custom-control-label" for="perm_allowdiscount">Allow discount prices</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_enwriteoff" name="perm_enwriteoff" value="1">
                                                    <label class="custom-control-label" for="perm_enwriteoff">Enable canceling invoices</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reports & Access -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_listinvoices" name="perm_listinvoices" value="1" checked>
                                                    <label class="custom-control-label" for="perm_listinvoices">Access invoices</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_listallinvoices" name="perm_listallinvoices" value="1">
                                                    <label class="custom-control-label" for="perm_listallinvoices">Access all invoices</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_editinvoice" name="perm_editinvoice" value="1">
                                                    <label class="custom-control-label" for="perm_editinvoice">Edit invoices</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_showinvtotals" name="perm_showinvtotals" value="1">
                                                    <label class="custom-control-label" for="perm_showinvtotals">Accounting summary</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- System Functions -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_cardsys" name="perm_cardsys" value="1">
                                                    <label class="custom-control-label" for="perm_cardsys">Card system & IAS</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_accessap" name="perm_accessap" value="1">
                                                    <label class="custom-control-label" for="perm_accessap">Maintain APs</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_trafficreport" name="perm_trafficreport" value="1">
                                                    <label class="custom-control-label" for="perm_trafficreport">Overall traffic report</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_cts" name="perm_cts" value="1">
                                                    <label class="custom-control-label" for="perm_cts">Connection report</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Additional Permissions -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_listonlineusers" name="perm_listonlineusers" value="1" checked>
                                                    <label class="custom-control-label" for="perm_listonlineusers">List online users</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_allusers" name="perm_allusers" value="1">
                                                    <label class="custom-control-label" for="perm_allusers">Access all users</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_logout" name="perm_logout" value="1" checked>
                                                    <label class="custom-control-label" for="perm_logout">Disconnect users</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- New Permissions (aligned in a row) -->
                                    
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_forceprofile" name="perm_forceprofile" value="1">
                                                    <label class="custom-control-label" for="perm_forceprofile">Force profile</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_macbinding" name="perm_macbinding" value="1">
                                                    <label class="custom-control-label" for="perm_macbinding">MAC binding</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_hostspotrandomcheck" name="perm_hostspotrandomcheck" value="1">
                                                    <label class="custom-control-label" for="perm_hostspotrandomcheck">Hotspot random check</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_forceactivation" name="perm_forceactivation" value="1">
                                                    <label class="custom-control-label" for="perm_forceactivation">Force activation</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_allowaccounts" name="perm_allowaccounts" value="1">
                                                    <label class="custom-control-label" for="perm_allowaccounts">Allow accounts</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_allowaccountssync" name="perm_allowaccountssync" value="1">
                                                    <label class="custom-control-label" for="perm_allowaccountssync">Allow accounts sync</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_fullrefund" name="perm_fullrefund" value="1">
                                                    <label class="custom-control-label" for="perm_fullrefund">Full refund</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_allowaccountsadd" name="perm_allowaccountsadd" value="1">
                                                    <label class="custom-control-label" for="perm_allowaccountsadd">Allow accounts add</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_allowdowngrade" name="perm_allowdowngrade" value="1">
                                                    <label class="custom-control-label" for="perm_allowdowngrade">Allow Upgrade/Downgrade</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_networkmanager" name="perm_networkmanager" value="1">
                                                    <label class="custom-control-label" for="perm_networkmanager">Network Manager</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_areaaccess" name="perm_areaaccess" value="1">
                                                    <label class="custom-control-label" for="perm_areaaccess">All Areas Access</label>
                                                </div>
                                            </div>
                                        </div>
                                    
                                    </div>
                                </div>
                                <!-- END of Permissions tab-pane -->
                                <div class="tab-pane" id="profiles">
                                    <!-- Profile content will be added later -->
                                    <p>Profile settings will be added here later.</p>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                                <button type="submit" class="btn btn-primary" id="btn-submit">Submit</button>
                                <button type="reset" class="btn btn-default">Reset</button>
                            </div>
                        </form>

                    </div>
                </div>
                
                <div class="col-md-4">
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
        </div>
    </section>
</div>

<script src="<?php echo base_url(); ?>assets/js/usersAddNew.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        // Function to update permissions based on selected reseller
        function updatePermissions() {
            var selectedReseller = $('#master').val();
            var resellerPermissions = <?php echo json_encode($resellers); ?>;
            
            console.log('Selected Reseller:', selectedReseller);
            console.log('Available Resellers:', resellerPermissions);
            
            // If no reseller selected, enable all permissions and keep their current state
            if (!selectedReseller || selectedReseller === 'NONE') {
                console.log('No reseller selected, enabling all permissions');
                $('.permission-checkbox').prop('disabled', false);
                return;
            }
            
            // Find the selected reseller's data
            var selectedResellerData = resellerPermissions.find(function(reseller) {
                return reseller.managername === selectedReseller;
            });

            console.log('Selected Reseller Data:', selectedResellerData);

            // If reseller not found, enable all permissions and keep their current state
            if (!selectedResellerData) {
                console.log('No reseller data found, enabling all permissions');
                $('.permission-checkbox').prop('disabled', false);
                return;
            }

            // Update each permission checkbox based on the selected reseller's permissions
            $('.permission-checkbox').each(function() {
                var permissionName = $(this).attr('name');
                var hasPermission = selectedResellerData[permissionName] == 1;
                
                console.log('Permission:', permissionName, 'Value:', hasPermission);
                
                // Set both checked state and disabled state based on permission
                $(this).prop('checked', hasPermission);
                $(this).prop('disabled', !hasPermission);
            });
        }

        // Add class to all permission checkboxes
        $('input[type="checkbox"][name^="perm_"]').addClass('permission-checkbox');

        // Update permissions when master selection changes
        $('#master').on('change', function() {
            var selectedMaster = $(this).val();
            var resellerPermissions = <?php echo json_encode($resellers); ?>;
            
            console.log('Master Changed:', selectedMaster);
            
            // If no master selected, enable all permissions and keep their current state
            if (!selectedMaster || selectedMaster === 'NONE') {
                console.log('No master selected, enabling all permissions');
                $('.permission-checkbox').prop('disabled', false);
                return;
            }
            
            // Find the selected master's data
            var masterData = resellerPermissions.find(function(reseller) {
                return reseller.managername === selectedMaster;
            });

            console.log('Master Data:', masterData);

            // Update permissions based on master's permissions
            if (masterData) {
                $('.permission-checkbox').each(function() {
                    var permissionName = $(this).attr('name');
                    var hasPermission = masterData[permissionName] == 1;
                    
                    console.log('Master Permission:', permissionName, 'Value:', hasPermission);
                    
                    // Set both checked state and disabled state based on permission
                    $(this).prop('checked', hasPermission);
                    $(this).prop('disabled', !hasPermission);
                });
            } else {
                console.log('No master data found, enabling all permissions');
                // If no master data found, enable all permissions and keep their current state
                $('.permission-checkbox').prop('disabled', false);
            }
        });

        // Update permissions on page load
        updatePermissions();

        // Handle form submission
        $("#saveNewReseller").submit(function (e) {
            $("#btn-submit").attr("disabled", true);
            return true;
        });
    });
</script>