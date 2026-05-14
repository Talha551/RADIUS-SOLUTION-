<?php 
    $user = $resellerInfo->managername;
    $mastername = $resellerInfo->mastername;
    $fname = $resellerInfo->firstname;
    $lname = $resellerInfo->lastname;
    $enablemanager = $resellerInfo->enablemanager;
    $address = $resellerInfo->address;
    $city = $resellerInfo->city;
    $mobile = $resellerInfo->mobile;
    $email = $resellerInfo->email;
    $cnic = $resellerInfo->vatid;
?>

<link href="<?php echo base_url(); ?>assets/plugins/select2/css/select2.min.css" rel="stylesheet" />
<link href="<?php echo base_url(); ?>assets/plugins/select2/css/select2-bootstrap4.min.css" rel="stylesheet" />

<style>
.select2-container {
    width: 100% !important;
}
.tab-content {
    padding: 20px 0;
}
.tab-pane {
    padding: 15px;
}
#settingsTable {
    margin-top: 20px;
}
.text-danger {
    color: #dc3545 !important;
}
.loading {
    opacity: 0.5;
    pointer-events: none;
}
</style>

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
                        <li class="breadcrumb-item active">Edit Reseller</li>
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
                            <h3 class="card-title">Edit Reseller Details</h3>
                        </div><!-- /.card-header -->
                        <!-- form start -->
                        <?php $this->load->helper("form"); ?>
                        <form role="form" id="updateCurrentReseller" action="<?php echo base_url() ?>updateCurrentReseller" method="post" role="form">
                            <div class="card-body">

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" checked>
                                        <label class="custom-control-label" for="status">Enabled</label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="user">Reseller ID</label>
                                            <input type="text" class="form-control required" value="<?php echo $user; ?>" id="user_display" name="user_display" maxlength="50" disabled>
                                            <input type="hidden" value="<?php echo $user; ?>" name="user" id="user" />
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
                                                                <option value="<?php echo $rl->managername ?>" <?php if($rl->managername == $mastername) {echo "selected=selected";} ?>><?php echo $rl->managername ?></option>
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
                                            <input type="text" class="form-control required" id="fname" value="<?php echo $fname; ?>" name="fname" maxlength="20">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lname">Last Name</label>
                                            <input type="text" class="form-control required" id="lname" value="<?php echo $lname; ?>" name="lname" maxlength="20">
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <input type="text" class="form-control required" id="address" value="<?php echo $address; ?>" name="address" maxlength="100">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="city">City</label>
                                            <input type="text" class="form-control required" id="city" value="<?php echo $city; ?>" name="city" maxlength="100">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="mobile">Mobile Number</label>
                                            <input type="text" class="form-control required" id="mobile" value="<?php echo $mobile; ?>" name="mobile" maxlength="13">
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="email">Email address</label>
                                            <input type="text" class="form-control required email" id="email" value="<?php echo $email; ?>" name="email" maxlength="128">
                                        </div>
                                    </div>                               
                                
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="mobile">CNIC No.</label>
                                            <input type="text" class="form-control required" id="cnic" value="<?php echo $cnic; ?>" name="cnic" maxlength="16">
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
                                <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
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
                                                    <input type="checkbox" class="custom-control-input" id="perm_listusers" name="perm_listusers" value="1" <?php echo ($resellerInfo->perm_listusers == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_listusers">List users</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_createusers" name="perm_createusers" value="1" <?php echo ($resellerInfo->perm_createusers == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_createusers">Register users</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_editusers" name="perm_editusers" value="1" <?php echo ($resellerInfo->perm_editusers == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_editusers">Edit users</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_edituserspriv" name="perm_edituserspriv" value="1" <?php echo ($resellerInfo->perm_edituserspriv == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_edituserspriv">Edit privileged user data</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_deleteusers" name="perm_deleteusers" value="1" <?php echo ($resellerInfo->perm_deleteusers == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_deleteusers">Delete users</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Managers Permissions -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_listmanagers" name="perm_listmanagers" value="1" <?php echo ($resellerInfo->perm_listmanagers == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_listmanagers">List managers</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_createmanagers" name="perm_createmanagers" value="1" <?php echo ($resellerInfo->perm_createmanagers == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_createmanagers">Register managers</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_editmanagers" name="perm_editmanagers" value="1" <?php echo ($resellerInfo->perm_editmanagers == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_editmanagers">Edit managers</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_deletemanagers" name="perm_deletemanagers" value="1" <?php echo ($resellerInfo->perm_deletemanagers == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_deletemanagers">Delete managers</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Services Permissions -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_listservices" name="perm_listservices" value="1" <?php echo ($resellerInfo->perm_listservices == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_listservices">List services</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_createservices" name="perm_createservices" value="1" <?php echo ($resellerInfo->perm_createservices == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_createservices">Register services</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_editservices" name="perm_editservices" value="1" <?php echo ($resellerInfo->perm_editservices == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_editservices">Edit services</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_deleteservices" name="perm_deleteservices" value="1" <?php echo ($resellerInfo->perm_deleteservices == 1) ? 'checked' : ''; ?>>
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
                                                    <input type="checkbox" class="custom-control-input" id="perm_addcredits" name="perm_addcredits" value="1" <?php echo ($resellerInfo->perm_addcredits == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_addcredits">Billing functions</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_negbalance" name="perm_negbalance" value="1" <?php echo ($resellerInfo->perm_negbalance == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_negbalance">Allow negative balance</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_allowdiscount" name="perm_allowdiscount" value="1" <?php echo ($resellerInfo->perm_allowdiscount == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_allowdiscount">Allow discount prices</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_enwriteoff" name="perm_enwriteoff" value="1" <?php echo ($resellerInfo->perm_enwriteoff == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_enwriteoff">Enable canceling invoices</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reports & Access -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_listinvoices" name="perm_listinvoices" value="1" <?php echo ($resellerInfo->perm_listinvoices == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_listinvoices">Access invoices</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_listallinvoices" name="perm_listallinvoices" value="1" <?php echo ($resellerInfo->perm_listallinvoices == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_listallinvoices">Access all invoices</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_editinvoice" name="perm_editinvoice" value="1" <?php echo ($resellerInfo->perm_editinvoice == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_editinvoice">Edit invoices</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_showinvtotals" name="perm_showinvtotals" value="1" <?php echo ($resellerInfo->perm_showinvtotals == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_showinvtotals">Accounting summary</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- System Functions -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_cardsys" name="perm_cardsys" value="1" <?php echo ($resellerInfo->perm_cardsys == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_cardsys">Card system & IAS</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_accessap" name="perm_accessap" value="1" <?php echo ($resellerInfo->perm_accessap == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_accessap">Maintain APs</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_trafficreport" name="perm_trafficreport" value="1" <?php echo ($resellerInfo->perm_trafficreport == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_trafficreport">Overall traffic report</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_cts" name="perm_cts" value="1" <?php echo ($resellerInfo->perm_cts == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_cts">Connection report</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <!-- Additional Permissions -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_listonlineusers" name="perm_listonlineusers" value="1" <?php echo ($resellerInfo->perm_listonlineusers == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_listonlineusers">List online users</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_allusers" name="perm_allusers" value="1" <?php echo ($resellerInfo->perm_allusers == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_allusers">Access all users</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_logout" name="perm_logout" value="1" <?php echo ($resellerInfo->perm_logout == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_logout">Disconnect users</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <!-- New Permissions -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_forceprofile" name="perm_forceprofile" value="1" <?php echo ($resellerInfo->perm_forceprofile == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_forceprofile">Force Profile</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_macbinding" name="perm_macbinding" value="1" <?php echo ($resellerInfo->perm_macbinding == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_macbinding">MAC Binding</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_hostspotrandomcheck" name="perm_hostspotrandomcheck" value="1" <?php echo ($resellerInfo->perm_hostspotrandomcheck == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_hostspotrandomcheck">Hotspot Random Check</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_forceactivation" name="perm_forceactivation" value="1" <?php echo ($resellerInfo->perm_forceactivation == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_forceactivation">Force Activation</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_allowaccounts" name="perm_allowaccounts" value="1" <?php echo ($resellerInfo->perm_allowaccounts == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_allowaccounts">Allow Accounts</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_allowaccountssync" name="perm_allowaccountssync" value="1" <?php echo ($resellerInfo->perm_allowaccountssync == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_allowaccountssync">Allow Accounts Sync</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_fullrefund" name="perm_fullrefund" value="1" <?php echo ($resellerInfo->perm_fullrefund == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_fullrefund">Full Refund</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_allowaccountsadd" name="perm_allowaccountsadd" value="1" <?php echo ($resellerInfo->perm_allowaccountsadd == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_allowaccountsadd">Allow Accounts Add</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_allowdowngrade" name="perm_allowdowngrade" value="1" <?php echo ($resellerInfo->perm_allowdowngrade == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_allowdowngrade">Allow Upgrade/Downgrade</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_networkmanager" name="perm_networkmanager" value="1" <?php echo ($resellerInfo->perm_networkmanager == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_networkmanager">Network Manager</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_areaaccess" name="perm_areaaccess" value="1" <?php echo ($resellerInfo->perm_areaaccess == 1) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="perm_areaaccess">Area Access</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="profiles">
                                    <!-- Profile content will be added later -->
                                    <p>Profile settings will be added here later.</p>
                                </div>

                                <div class="tab-pane" id="settings">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="stgtype">Setting Type</label>
                                                <select class="form-control required" id="stgtype" name="stgtype">
                                                    <option value="0">Select Type</option>
                                                    <option value="FORCE-INSTALL">Force Installation before recharge</option>
                                                    <option value="GRACE-DAYS-OLD">Grace Days for Active Customer</option>
                                                    <option value="GRACE-DAYS-NEW">Grace Days New Customer Billing Adjustment </option>
                                                    <option value="INACTIVE-DAYS-BILL">Inactive Days Limit</option>
                                                    <option value="POSTPAID-MANAGER">Post Paid Credit Limit</option>
                                                    <option value="ALLOW-ACCOUNTS">Allow Accounts Module to Manager</option>
                                                    <option value="ALLOW-SMS">Allow SMS</option>
                                                    <option value="ALLOW-BULK-SMS">Allow BULK SMS</option>
                                                    <option value="TAX-GST">TAX GST Rate</option>
                                                    <option value="TAX-AIT">TAX AIT Rate</option>
                                                    <option value="DIV-RATIO">DIV Ratio</option>
                                                    <option value="CHECK-MAC">Hotspot Random Mac Check</option>
                                                    <option value="BIND-MAC">BIND USER MAC</option>
                                                    <option value="DEFAULT-ACCOUNT">Default Account Cash</option>
                                                    <option value="DEFAULT-ACCOUNT-SALES">Default Account Sales</option>
                                                    <option value="DEFAULT-ACCOUNT-PURCHASE">Default Account Purchase</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="stgvalue">Setting Value</label>
                                                <input type="text" class="form-control required digits" id="stgvalue" name="stgvalue" maxlength="10">
                                                <small id="settingHelp" class="form-text text-muted">Select a setting type to see value format</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button type="button" class="btn btn-primary" id="addSetting">Add Setting</button>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <table class="table table-bordered" id="settingsTable">
                                                <thead>
                                                    <tr>
                                                        <th>Setting Type</th>
                                                        <th>Value</th>
                                                        <th>Setting Name</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Settings will be loaded here -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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
<!-- At the bottom of the page, just before closing </body> tag -->
<!-- Load jQuery first -->
<script src="<?php echo base_url(); ?>assets/plugins/jquery/jquery.min.js"></script>
<!-- Load Bootstrap JS -->
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Load Select2 -->
<script src="<?php echo base_url(); ?>assets/plugins/select2/js/select2.full.min.js"></script>


<script src="<?php echo base_url(); ?>assets/js/editService.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/js/billing.js" type="text/javascript"></script> 

<!-- Load your custom script -->
<script type="text/javascript">
    var baseURL = "<?php echo base_url(); ?>";
    
    // Handle status checkbox
    $('#status').on('change', function(){
        var custom = $(this).is(':checked') ? 1 : 0;
        $(this).val(custom);
    });

    $(document).ready(function () {
        // Initialize select2 for setting type with error handling
        if($.fn.select2) {
            try {
                $("#stgtype").select2({
                    placeholder: "Select Setting Type",
                    allowClear: true,
                    width: '100%',
                    theme: 'bootstrap4'
                });
            } catch(e) {
                console.error('Select2 initialization failed:', e);
            }
        } else {
            console.error('Select2 plugin not loaded');
        }

        // Handle setting type change
        $('#stgtype').change(function() {
            var selectedType = $(this).val();
            var helpText = '';
            
            switch(selectedType) {
                case 'ALLOW-SMS':
                    helpText = 'Value will be SMS Balance';
                    break;
                case 'POSTPAID-MANAGER':
                    helpText = 'Value will be Credit Limit';
                    break;
                case 'TAX-GST':
                    helpText = 'TAX Percentage';
                    break;
                case 'TAX-AIT':
                    helpText = 'Advance Tax %age';
                    break;
                case 'DIV-RATIO':
                    helpText = 'Division Ratio %age';
                    break;
                case 'GRACE-DAYS-OLD':
                    helpText = 'Allow Activation for Grace Days';
                    break;
                case 'GRACE-DAYS-NEW':
                    helpText = 'Allow New Installation Adjustment for Days';
                    break;
                case 'DEFAULT-ACCOUNT':
                    helpText = 'Default Account for Cash';
                    break;
                case 'DEFAULT-ACCOUNT-BANK':
                    helpText = 'Default Account for Bank';
                    break;
                case 'DEFAULT-ACCOUNT-SALES':
                    helpText = 'Default Account for Sales';
                    break;
                case 'DEFAULT-ACCOUNT-PURCHASE':
                    helpText = 'Default Account for Purchases';
                    break;
                case 'ALLOW-ACCOUNTS':
                    helpText = 'rue=1, False=0 (Must select default accounts to enable accounting module work smoothly)';
                    break;
                case 'ALLOW-BULK-SMS':
                case 'CHECK-MAC':
                case 'FORCE-INSTALL':
                case 'BIND-MAC':
                    helpText = 'True=1, False=0';
                    break;
                default:
                    helpText = 'Select a setting type to see value format';
            }
            
            $('#settingHelp').text(helpText);
        });

        // Load existing settings for the manager
        loadManagerSettings();

        // Add setting button click handler
        $("#addSetting").click(function() {
            var stgtype = $("#stgtype").val();
            var stgvalue = $("#stgvalue").val();
            var managername = $("#user").val();
            var stgname = $("#stgtype option:selected").text(); // Get the selected option's text

            // Validate inputs
            if (!stgtype || stgtype === "0") {
                alert("Please select a setting type");
                return;
            }
            if (!stgvalue) {
                alert("Please enter a setting value");
                return;
            }

            // Show loading state
            var $btn = $(this);
            $btn.prop('disabled', true).addClass('loading');
            
            $.ajax({
                url: baseURL + 'User/settingAddNewManager',
                type: 'POST',
                dataType: 'json',
                data: {
                    stgtype: stgtype,
                    stgvalue: stgvalue,
                    managername: managername,
                    stgname: stgname // Add the setting name to the request
                },
                success: function(response) {
                    if (response.status) {
                        loadManagerSettings();
                        $("#stgtype").val('0').trigger('change');
                        $("#stgvalue").val('');
                    } else {
                        alert(response.message || 'Error adding setting');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    alert('Error occurred while adding setting');
                },
                complete: function() {
                    $btn.prop('disabled', false).removeClass('loading');
                }
            });
        });

        // Delete setting handler with confirmation
        $(document).on('click', '.deleteSetting', function() {
            var $btn = $(this);
            if (confirm("Are you sure you want to delete this setting?")) {
                var stgid = $btn.data('stgid');
                
                // Show loading state
                $btn.prop('disabled', true).addClass('loading');
                
                $.ajax({
                    url: baseURL + 'user/deleteManagerSetting',
                    type: 'POST',
                    dataType: 'json',
                    data: { stgid: stgid },
                    success: function(response) {
                        if (response.status) {
                            loadManagerSettings();
                        } else {
                            alert(response.message || 'Error deleting setting');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        alert('Error occurred while deleting setting');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).removeClass('loading');
                    }
                });
            }
        });

        // Function to load manager settings with improved error handling
        function loadManagerSettings() {
            var managername = $("#user").val();
            var $tbody = $("#settingsTable tbody");
            
            // Show loading state
            $tbody.html('<tr><td colspan="3" class="text-center">Loading settings...</td></tr>');
            
            $.ajax({
                url: baseURL + 'User/getManagerSettings',
                type: 'POST',
                dataType: 'json',
                data: { managername: managername },
                success: function(response) {
                    $tbody.empty();
                    
                    if (response && response.status === true && Array.isArray(response.settings)) {
                        if (response.settings.length > 0) {
                            response.settings.forEach(function(setting) {
                                $tbody.append(
                                    '<tr>' +
                                    '<td>' + $('<div>').text(setting.stgtype).html() + '</td>' +
                                    '<td>' + $('<div>').text(setting.stgvalue).html() + '</td>' +
                                    '<td>' + $('<div>').text(setting.stgname).html() + '</td>' +
                                    '<td>' +
                                    '<button type="button" class="btn btn-sm btn-danger deleteSetting" data-stgid="' + setting.stgid + '">' +
                                    '<i class="fas fa-trash"></i>' +
                                    '</button>' +
                                    '</td>' +
                                    '</tr>'
                                );
                            });
                        } else {
                            $tbody.html('<tr><td colspan="3" class="text-center">No settings found</td></tr>');
                        }
                    } else {
                        $tbody.html('<tr><td colspan="3" class="text-center text-danger">Invalid response format</td></tr>');
                        console.error('Invalid response format:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.log('Response Text:', xhr.responseText);
                    try {
                        var response = JSON.parse(xhr.responseText);
                        $tbody.html('<tr><td colspan="3" class="text-center text-danger">' + (response.message || 'Error loading settings') + '</td></tr>');
                    } catch(e) {
                        $tbody.html('<tr><td colspan="3" class="text-center text-danger">Error loading settings</td></tr>');
                    }
                }
            });
        }

        // Function to update permissions based on selected reseller
        function updatePermissions() {
            var selectedReseller = $('#master').val();
            var resellerPermissions = <?php echo json_encode($resellers); ?>;
            var excludedMasterPropagate = [
                'perm_createservices',
                'perm_editservices',
                'perm_deleteservices',
                'perm_listmanagers',
                'perm_createmanagers',
                'perm_editmanagers',
                'perm_deletemanagers',
                'perm_edituserspriv',
                'perm_deleteusers',
                'perm_addcredits',
                'perm_negbalance',
                'perm_cardsys',
                'perm_editinvoice',
                'perm_allusers',
                'perm_allowdiscount',
                'perm_allowaccounts',
                'perm_allowaccountssync',
                'perm_listallinvoices',
                'perm_networkmanager',
                'perm_areaaccess',
                'perm_fullrefund'
            ];
            
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
                // Skip auto-propagation/locking for excluded permissions
                if (excludedMasterPropagate.indexOf(permissionName) !== -1) {
                    $(this).prop('disabled', false);
                    return; 
                }
                var hasPermission = selectedResellerData[permissionName] == 1;
                
                console.log('Permission:', permissionName, 'Value:', hasPermission);
                
                // Set both checked state and disabled state based on permission
                //$(this).prop('checked', hasPermission);
                $(this).prop('disabled', !hasPermission);
            });
        }

        // Add class to all permission checkboxes
        $('input[type="checkbox"][name^="perm_"]').addClass('permission-checkbox');

        // Update permissions on page load
        updatePermissions();

        // Update permissions when master selection changes
        $('#master').on('change', function() {
            var selectedMaster = $(this).val();
            var resellerPermissions = <?php echo json_encode($resellers); ?>;
            var excludedMasterPropagate = [
                'perm_createservices',
                'perm_editservices',
                'perm_deleteservices',
                'perm_listmanagers',
                'perm_createmanagers',
                'perm_editmanagers',
                'perm_deletemanagers',
                'perm_edituserspriv',
                'perm_deleteusers',
                'perm_addcredits',
                'perm_negbalance',
                'perm_cardsys',
                'perm_editinvoice',
                'perm_allusers',
                'perm_allowdiscount',
                'perm_allowaccounts',
                'perm_allowaccountssync',
                'perm_listallinvoices',
                'perm_networkmanager',
                'perm_areaaccess',
                'perm_fullrefund'
            ];
            
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
                    // Skip auto-assign and locking for excluded permissions
                    if (excludedMasterPropagate.indexOf(permissionName) !== -1) {
                        $(this).prop('disabled', false);
                        return; 
                    }
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

        // Handle form submission
        $("#updateCurrentReseller").submit(function (e) {
            $("#btn-submit").attr("disabled", true);
            return true;
        });
    });
</script>