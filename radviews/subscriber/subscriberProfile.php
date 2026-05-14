<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-user mr-2"></i>Subscriber Information
                        </h4>
                        <h2 class="font-weight-bold mt-2 text-capitalize">
                            <?php echo $userInfo->firstname." ".$userInfo->lastname; ?>
                        </h2>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <p class="mb-2">
                                    <?php
                                        $address = (!empty($userInfo->address) || $userInfo->address !== "") ? $userInfo->address : "Address not available";
                                        $city = (!empty($userInfo->city) || $userInfo->city !== "") ? $userInfo->city : "N/A";
                                        $country = (!empty($userInfo->country) || $userInfo->country !== "") ? $userInfo->country : "N/A";
                                        $state = (!empty($userInfo->state) || $userInfo->state !== "") ? $userInfo->state : "N/A";
                                        echo $address.", city ".$city."<br> country ".$country." state ".$state;
                                    ?>
                                </p>
                                <p class="mb-0">
                                    <?php echo "Personal ID: ".$userInfo->mobile; ?>
                                    <?php echo "   Contact: ".$userInfo->mobile; ?><br>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light mb-2">
                                    <div class="card-header p-2">
                                        <strong>
                                            <?php echo ($userInfo->acctype == 0) ? "PPPoE " : "HotSpot "; ?>
                                            User Name: <?php echo $userInfo->username; ?>
                                        </strong>
                                    </div>
                                    <div class="card-body p-2">
                                        <?php
                                            $currentDate = date('Y-m-d H:i:s');
                                            $expirationDate = $userInfo->expiration;
                                            $isExpired = ($expirationDate <= $currentDate);
                                            $class = $isExpired ? 'list-group-item-danger' : 'list-group-item-success';
                                        ?>
                                        <ul class="list-group">
                                            <li class="list-group-item <?php echo $class; ?> d-flex justify-content-between align-items-center">
                                                <span><i class="fas fa-box-open mr-1"></i>Package Name</span>
                                                <span><?php echo $userInfo->srvname; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span><i class="far fa-calendar-alt mr-1"></i>Expiration</span>
                                                <span><?php echo $userInfo->expiration; ?></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Stats Row: AdminLTE 3 Info-Box Widgets -->
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12 mb-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-arrow-down"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Download Speed</span>
                        <span class="info-box-number"><?php echo $userInfo->trafficunitdl; ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12 mb-4">
                <div class="info-box">
                    <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-arrow-up"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Upload Speed</span>
                        <span class="info-box-number"><?php echo $userInfo->trafficunitul; ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12 mb-4">
                <div class="info-box">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-download"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Monthly Download</span>
                        <span class="info-box-number"><?php echo $userInfo->downlimit; ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12 mb-4">
                <div class="info-box">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-upload"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Monthly Upload</span>
                        <span class="info-box-number"><?php echo $userInfo->uplimit; ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row d-flex align-items-stretch">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-light font-weight-bold">
                        Subscriber Information & Settings
                    </div>

                    <div class="card-body">
                        <!-- AdminLTE 3 Nav Tabs -->
                        <ul class="nav nav-tabs" id="profileTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="setting-tab" data-toggle="tab" href="#setting1" role="tab" aria-controls="setting1" aria-selected="true">Setting</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile1" role="tab" aria-controls="profile1" aria-selected="false">Profile</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="access-tab" data-toggle="tab" href="#setting2" role="tab" aria-controls="setting2" aria-selected="false">Access</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="details-tab" data-toggle="tab" href="#docs" role="tab" aria-controls="docs" aria-selected="false">Details</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="installation-tab" data-toggle="tab" href="#installation" role="tab" aria-controls="installation" aria-selected="false">Installation</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="network-tab" data-toggle="tab" href="#network" role="tab" aria-controls="network" aria-selected="false">Network</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="complaint-tab" data-toggle="tab" href="#complaint" role="tab" aria-controls="complaint" aria-selected="false">Complaints</a>
                            </li>
                        </ul>
                        <div class="tab-content pt-3" id="profileTabContent">
                            <div class="tab-pane fade show active" id="setting1" role="tabpanel" aria-labelledby="setting-tab">
                                <form action="<?php echo base_url() ?>Subscribers/update_subscriberProfile" method="post" id="subscriber_profile" role="form" enctype="multipart/form-data">
                                    <div class="profile-setting">
                                        <ul class="profile-edit-list row">
                                            <li class="weight-500 col-md-6">
                                                <input hidden type="text" class="form-control required" id="username" value="<?php echo $userInfo->username; ?>" name="username" maxlength="20">
                                                <div class="custom-control custom-checkbox mb-5">
                                                    <input
                                                        type="checkbox"
                                                        class="custom-control-input"
                                                        id="enableuser"
                                                        name="enableuser"
                                                        value="1"
                                                        <?php  if($userInfo->enableuser == 1){ echo "checked"; } ?>
                                                    />
                                                    <label class="custom-control-label" for="enableuser">Enable User Internet Access</label>
                                                </div>
                                                <div class="custom-control custom-checkbox mb-5">
                                                    <input
                                                        type="checkbox"
                                                        class="custom-control-input"
                                                        id="verified"
                                                        name="verified"
                                                        value="0"
                                                        <?php  if($userInfo->verified == 1){ echo "checked"; } ?>
                                                    />
                                                    <label class="custom-control-label" for="verified">User Account Verified</label>
                                                </div><br>
                                                <h4 class="text-blue h5 mb-20">
                                                    Change Login Password
                                                </h4>
                                                <div class="form-group row">
                                                    <div class="col-md-10 col-sm-6">
                                                        <input class="form-control" type="password" id="password1" name="password1" placeholder="password" autocomplete="off" value="" />
                                                        <span style="position: absolute;right:35px;top:7px;" id="toggle-password">
                                                            <i id="slash" class="fa fa-eye-slash"></i>
                                                            <i id="eye" class="fa fa-eye" style="display: none;"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-10 col-sm-6">
                                                        <input class="form-control" type="password" id="password2" name="password2" placeholder="confirm" value="" autocomplete="nope" />
                                                    </div>
                                                </div>
                                                <h5 class="text-blue h5 mb-20">
                                                    Change Authentication Password
                                                </h5>
                                                <div class="form-group row">
                                                    <div class="col-md-10 col-sm-6">
                                                        <input class="form-control" type="password" id="apassword3" name="password3" placeholder="password" autocomplete="nope" />
                                                        <span style="position: absolute;right:35px;top:7px;" id="atoggle-password">
                                                            <i id="aslash" class="fa fa-eye-slash"></i>
                                                            <i id="aeye" class="fa fa-eye" style="display: none;"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-10 col-sm-6">
                                                        <input class="form-control" type="password" id="apassword4" name="password4" placeholder="confirm" autocomplete="nope" />
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="weight-500 col-md-6">
                                                <div class="custom-control custom-checkbox mb-5">
                                                    <input
                                                        type="checkbox"
                                                        class="custom-control-input"
                                                        id="alertemail"
                                                        name="alertemail"
                                                        value="0"
                                                        <?php  if($userInfo->alertemail == 1){ echo "checked"; } ?>
                                                    />
                                                    <label class="custom-control-label" for="alertemail">Email Alert</label>
                                                </div>
                                                <div class="custom-control custom-checkbox mb-5">
                                                    <input
                                                        type="checkbox"
                                                        class="custom-control-input"
                                                        id="alertsms"
                                                        name="alertsms"
                                                        value="0"
                                                        <?php  if($userInfo->alertsms == 1){ echo "checked"; } ?>
                                                    />
                                                    <label class="custom-control-label" for="alertsms">Alert SMS/Whatsapp</label>
                                                </div><br>
                                                <h5 class="text-blue h5 mb-20">
                                                    Download & Upload Limits (Bytes)
                                                </h5>
                                                <div class="form-group">
                                                    <input
                                                        id="tot_download"
                                                        type="text"
                                                        value="<?php echo $userInfo->downlimit; ?>"
                                                        name="tot_download"
                                                        placeholder="password"
                                                    />
                                                    <input
                                                        id="tot_upload"
                                                        type="text"
                                                        value="<?php echo $userInfo->uplimit; ?>"
                                                        name="tot_upload"
                                                    />
                                                </div>
                                                <h5 class="text-blue h5 mb-20">
                                                    Combined Limit (Bytes)
                                                </h5>
                                                <div class="form-group">
                                                    <input
                                                        id="tot_combined"
                                                        type="text"
                                                        value="<?php echo $userInfo->comblimit; ?>"
                                                        name="tot_combined"
                                                    />
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="profile1" role="tabpanel" aria-labelledby="profile-tab">
                                <div class="pd-20">
                                    <div class="profile-setting">
                                        <ul class="profile-edit-list row">
                                            <li class="weight-500 col-md-6">
                                                <div class="row"><div class="col-md-10">
                                                    <div class="form-group">
                                                        <label>Account Owner</label>
                                                        <select class="custom-select2 form-control" id="owner" name="owner" style="width: 100%; height: 38px">
                                                            <?php
                                                            if (!empty($managers)) {
                                                            ?> <option value="">Select Account</option>
                                                                <?php
                                                                foreach ($managers as $rl) {
                                                                ?>
                                                                    <option value="<?php echo $rl->managername ?>" <?php if ($rl->managername == $userInfo->owner) {
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
                                                    <div class="form-group">
                                                        <label>First Name</label>
                                                        <input type="text" class="form-control required" id="firstname" value="<?php echo $userInfo->firstname; ?>" name="firstname" maxlength="20">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Last Name</label>
                                                        <input type="text" class="form-control required" id="lastname" value="<?php echo $userInfo->lastname; ?>" name="lastname" maxlength="20">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Company Name</label>
                                                        <input type="text" class="form-control required" id="company" value="<?php echo $userInfo->company; ?>" name="company" maxlength="20">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Comment</label>
                                                        <input type="text" class="form-control required" id="comment" value="<?php echo $userInfo->comment; ?>" name="comment" maxlength="20">
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="weight-500 col-md-6">
                                                <div class="form-group">
                                                    <label>Phone</label>
                                                    <input type="text" class="form-control required" id="phone" value="<?php echo $userInfo->phone; ?>" name="phone" maxlength="20">
                                                </div>
                                                <div class="form-group">
                                                    <label>Mobile/Whatsapp</label>
                                                    <input type="text" class="form-control required" id="mobile" value="<?php echo $userInfo->mobile; ?>" name="mobile" maxlength="20">
                                                </div>
                                                <div class="form-group">
                                                    <label>Address</label>
                                                    <input type="text" class="form-control required" id="address" value="<?php echo $userInfo->address; ?>" name="address" maxlength="100">
                                                </div>
                                                <div class="form-group">
                                                    <label>City</label>
                                                    <input type="text" class="form-control required" id="city" value="<?php echo $userInfo->city; ?>" name="city" maxlength="20">
                                                </div>
                                                <div class="form-group">
                                                    <label>Zip Code</label>
                                                    <input type="text" class="form-control required" id="zip" value="<?php echo $userInfo->zip; ?>" name="zip" maxlength="20">
                                                </div>
                                                <div class="form-group">
                                                    <label>Country</label>
                                                    <input type="text" class="form-control required" id="country" value="<?php echo $userInfo->country; ?>" name="country" maxlength="20">
                                                </div>
                                                <div class="form-group">
                                                    <label>State</label>
                                                    <input type="text" class="form-control required" id="state" value="<?php echo $userInfo->state; ?>" name="state" maxlength="20">
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="setting2" role="tabpanel" aria-labelledby="access-tab">
                                <div class="profile-setting">
                                    <ul class="profile-edit-list row">
                                        <li class="weight-500 col-md-6">
                                            <h4 class="text-blue h5 mb-20">
                                                Billing & Expiration
                                            </h4>
                                            <div class="form-group">
                                                <label>Expiration Date</label>
                                                <input
                                                    id="expiration"
                                                    name="expiration"
                                                    class="form-control date-picker"
                                                    type="text" 
                                                    value="<?php echo date('d F Y', strtotime($userInfo->expiration)); ?>"
                                                />
                                                <small class="form-text text-muted" id="gdays_msg">You can adjust the time only.</small>
                                            </div>
                                            <div class="form-group">
                                                <label>Expiration Time</label>
                                                <input
                                                    class="form-control time-picker"
                                                    id="expiration_time"
                                                    name="expiration_time"
                                                    placeholder="Select time"
                                                    type="text"
                                                    value="<?php echo date('H:i:s', strtotime($userInfo->expiration)); ?>"
                                                />
                                            </div>
                                            <div class="form-group">
                                                <label>Creation Date</label>
                                                <input
                                                    id="createdon"
                                                    name="createdon"
                                                    class="form-control date-picker"
                                                    value="<?php echo date('d F Y', strtotime($userInfo->createdon)); ?>"
                                                    type="text" />
                                            </div>
                                        </li>
                                        <li class="weight-500 col-md-6">
                                            <h4 class="text-blue h5 mb-20">
                                                Authentication
                                            </h4>
                                            <div class="form-group row">
                                                <label>MAC address CPE</label>
                                                <input type="text" class="form-control required" id="mac" value="<?php echo $userInfo->mac; ?>" name="remarks" maccpe="XX:XX:XX:XX:XX:XX" maxlength="20">
                                            </div>
                                            <div class="custom-control custom-checkbox mb-5">
                                                <input
                                                    type="checkbox"
                                                    class="custom-control-input"
                                                    id="usemacauth"
                                                    name="usemacauth"
                                                    value="1"
                                                    <?php  if($userInfo->usemacauth == 1){ echo "checked"; } ?>
                                                />
                                                <label class="custom-control-label" for="usemacauth">Allow MAC Authentication</label>
                                            </div>
                                            <label class="weight-600">IP Address CPE</label>
                                            <div class="custom-control custom-radio mb-5">
                                                <input
                                                    type="radio"
                                                    id="customRadio1"
                                                    name="ipmodecpe"
                                                    class="custom-control-input"
                                                    value="0"
                                                    checked
                                                />
                                                <label class="custom-control-label" for="customRadio1"
                                                    >NAS Pool or DHCP</label
                                                >
                                            </div>
                                            <div class="custom-control custom-radio mb-5">
                                                <input
                                                    type="radio"
                                                    id="customRadio2"
                                                    name="ipmodecpe"
                                                    class="custom-control-input"
                                                    value="1"
                                                />
                                                <label class="custom-control-label" for="customRadio2"
                                                    >IP Pool</label
                                                >
                                            </div>
                                            <div class="form-group">
                                                <select name="ippool" id="ippool" class="custom-select form-control" style="width: 100%; height: 38px" disabled>
                                                    <option value="0" selected>IP Pool</option>
                                                </select>
                                            </div>
                                            <div class="custom-control custom-radio mb-5">
                                                <input
                                                    type="radio"
                                                    id="customRadio3"
                                                    name="ipmodecpe"
                                                    class="custom-control-input"
                                                    value="2"
                                                />
                                                <label class="custom-control-label" for="customRadio3"
                                                    >Static IP</label
                                                >
                                            </div>                                                
                                            <div class="form-group row">
                                                <input type="text" class="form-control required" id="staticip" value="<?php echo $userInfo->staticipcpe; ?>" placeholder="static ip" name="staticip" maxlength="20">
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-8 col-md-8 col-form-label">Simultaneous Sessions </label>
                                                <div class="col-sm-4 col-md-4">
                                                    <input id="simsession" class="form-control" value="<?php echo $radcheck->value; ?>" type="number" />
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="docs" role="tabpanel" aria-labelledby="details-tab">
                                <div class="profile-setting">
                                    <ul class="profile-edit-list row">
                                        <li class="weight-500 col-md-6">
                                            <div class="form-group">
                                                <label>Payment Show Name</label>
                                                <input type="text" class="form-control required" id="payname" value="<?php if(!empty($docsInfo->payname)){  echo $docsInfo->payname; }else{ echo $userInfo->firstname." ".$userInfo->lastname;} ?>" name="payname" maxlength="20">
                                            </div>
                                            <div class="form-group">
                                                <label>ID Number</label>
                                                <input type="text" class="form-control required" id="cnicno" value="<?php if(!empty($docsInfo->cnicno)){  echo $docsInfo->cnicno; }else{ echo $userInfo->taxid;} ?>" name="cnicno" maxlength="20">
                                            </div>
                                            <div class="form-group">
                                                <label>Payment ID</label>
                                                <input type="text" class="form-control required" id="payid" value="<?php if(!empty($docsInfo->payid)){  echo $docsInfo->payid; }else{ echo "";} ?>" name="payid" maxlength="20">
                                            </div>
                                            <div class="form-group">
                                                <button type="button" class="btn btn-outline-primary">
                                                    Generate ID
                                                </button>
                                            </div>
                                            <div class="form-group">
                                                <label>Discount</label>
                                                <input class="form-control" id="discount" value="<?php if(!empty($docsInfo->discount)){  echo $docsInfo->discount; }else{ echo "";} ?>" name="discount" maxlength="20" type="number">
                                            </div>
                                            <div class="form-group">
                                                <label>Adjustment</label>
                                                <input id="adjamount" name="adjamount" class="form-control" value="<?php if(!empty($docsInfo->adjamount)){  echo $docsInfo->adjamount; }else{ echo "";} ?>" type="number" />
                                            </div>
                                        </li>
                                        <li class="weight-500 col-md-6">
                                            <div class="form-group">
                                                <label>ID Dcoument One</label>
                                                <input
                                                    type="file" id="cnic_file1" name="cnic_file1" value="<?php if(!empty($docsInfo->cnic_file1)){  echo $docsInfo->cnic_file1; }else{ echo "";} ?>"
                                                    class="form-control-file form-control height-auto"
                                                />
                                                <small class="form-text text-muted">
                                                    <?php if(!empty($docsInfo->cnic_file1)){  echo "Current File: ".$docsInfo->cnic_file1; }else{ echo "For example ID front side";} ?>
                                                </small>
                                            </div>
                                            <div class="form-group">
                                                <label>ID Document Two</label>
                                                <input
                                                    type="file" id="cnic_file2" name="cnic_file2" value="<?php if(!empty($docsInfo->cnic_file2)){  echo $docsInfo->cnic_file2; }else{ echo "";} ?>"
                                                    class="form-control-file form-control height-auto"
                                                />
                                                <small class="form-text text-muted">
                                                    <?php if(!empty($docsInfo->cnic_file2)){  echo "Current File: ".$docsInfo->cnic_file2; }else{ echo "For example ID back side";} ?>
                                                </small>
                                            </div>
                                            <div class="form-group">
                                                <label>Contract Document</label>
                                                <input
                                                    type="file" id="agreement_file1" name="agreement_file1" value="<?php if(!empty($docsInfo->agreement_file1)){  echo $docsInfo->agreement_file1; }else{ echo "";} ?>"
                                                    class="form-control-file form-control height-auto"
                                                />
                                                <small class="form-text text-muted">
                                                    <?php if(!empty($docsInfo->agreement_file1)){  echo "Current File: ".$docsInfo->agreement_file1; }else{ echo "For example your document front page";} ?>
                                                </small>
                                            </div>
                                            <div class="form-group">
                                                <label>Service Agreement</label>
                                                <input
                                                    type="file" id="agreement_file2" name="agreement_file2" value="<?php if(!empty($docsInfo->agreement_file2)){  echo $docsInfo->agreement_file2; }else{ echo "";} ?>"
                                                    class="form-control-file form-control height-auto"
                                                />
                                                <small class="form-text text-muted">
                                                    <?php if(!empty($docsInfo->agreement_file2)){  echo "Current File: ".$docsInfo->agreement_file2; }else{ echo "For example your document back page";} ?>
                                                </small>
                                            </div>
                                            <small class="form-text text-muted">
                                                NOTE: <br>Maxiumum file size 4MB. You can change in settings.
                                                <br>Any new file upload will add files.
                                            </small>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="installation" role="tabpanel" aria-labelledby="installation-tab">
                                <div class="pd-20">
                                    <div class="profile-setting">
                                        <ul class="profile-edit-list row">
                                            <li class="weight-500 col-md-6">
                                                <div class="form-group">
                                                    <label>Installation Name</label>
                                                    <input type="text" class="form-control required" id="inst_name" value="<?php if(!empty($docsInfo->inst_name)){  echo $docsInfo->inst_name; }else{ echo "";} ?>" name="inst_name" placeholder="Installation Reference" maxlength="20">
                                                </div>
                                                <div class="form-group">
                                                    <label>Optical Equipment</label>
                                                    <input type="text" class="form-control required" id="inst_box" value="<?php if(!empty($docsInfo->inst_box)){  echo $docsInfo->inst_box; }else{ echo "";} ?>" name="inst_box" placeholder="Make/Model/Type etc" maxlength="20">
                                                </div>
                                                <div class="form-group">
                                                    <label>ONT Serial Number</label>
                                                    <input type="text" class="form-control required" id="inst_box_sn" value="<?php if(!empty($docsInfo->inst_box_sn)){  echo $docsInfo->inst_box_sn; }else{ echo "";} ?>" name="inst_box_sn" placeholder="Huawei Serial No. for auto authorization" maxlength="20">
                                                </div>
                                                <div class="form-group">
                                                    <label>Wireless Equipment</label>
                                                    <input type="text" class="form-control required" id="inst_wifi" value="<?php if(!empty($docsInfo->inst_wifi)){  echo $docsInfo->inst_wifi; }else{ echo "";} ?>" name="inst_wifi" placeholder="Make/Model/Type etc" maxlength="20">
                                                </div>
                                            </li>
                                            <li class="weight-500 col-md-6">
                                                <div class="form-group">
                                                    <label>Wiring & Cable Details</label>
                                                    <input type="text" class="form-control required" id="inst_fiber" value="<?php if(!empty($docsInfo->inst_fiber)){  echo $docsInfo->inst_fiber; }else{ echo "";} ?>" name="inst_fiber" placeholder="Make/Model/Type etc" maxlength="20">
                                                </div>
                                                <div class="form-group">
                                                    <label>Drop Cable Length (Meter)</label>
                                                    <input class="form-control" id="inst_meter" value="<?php if(!empty($docsInfo->inst_meter)){  echo $docsInfo->inst_meter; }else{ echo "";} ?>" name="inst_meter" maxlength="20" type="number">
                                                </div>
                                                <div class="form-group">
                                                    <label>Installation Charges</label>
                                                    <input class="form-control" id="inst_chrg" value="<?php if(!empty($docsInfo->inst_chrg)){  echo $docsInfo->inst_chrg; }else{ echo "";} ?>" name="inst_chrg" maxlength="20" type="number">
                                                </div>
                                                <div class="form-group">
                                                    <label>Installation Charges</label>
                                                    <input class="form-control" id="inst_cost" value="<?php if(!empty($docsInfo->inst_cost)){  echo $docsInfo->inst_cost; }else{ echo "";} ?>" name="inst_cost" maxlength="20" type="number">
                                                </div>
                                                <div class="form-group">
                                                    <label>Installation Discount</label>
                                                    <input id="inst_disc" name="inst_disc" class="form-control" value="<?php if(!empty($docsInfo->inst_disc)){  echo $docsInfo->inst_disc; }else{ echo "";} ?>" type="number" />
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="network" role="tabpanel" aria-labelledby="network-tab">
                                <div class="pd-20">
                                    <div class="profile-setting">
                                        <ul class="profile-edit-list row">
                                            <li class="weight-500 col-md-6">
                                            </li>
                                            <li class="weight-500 col-md-6">
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="complaint" role="tabpanel" aria-labelledby="complaint-tab">
                                <div class="pd-20">
                                    <div class="profile-setting">
                                        <ul class="profile-edit-list row">
                                            <li class="weight-500 col-md-6">
                                            </li>
                                            <li class="weight-500 col-md-6">
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 col-sm-6 col-12 mb-4">
                <div class="card bg-white shadow rounded p-3 h-100 d-flex flex-column">

                    <h5 class="text-center h5 mb-0 text-primary" id="accountid">Account Info</h5>
                    <br>
                    <p class="text-center text-muted font-14" id="accounttype">
                        <?php 
                            $accType = ( $userInfo->acctype == 0) ? "PPPoE-User" : "HotSpot User";
                            echo "Account Type - ".$accType;
                        ?>
                    </p><br>
                    <div class="profile-info">
                        <ul class="profile-edit-list row">
                            <li class="weight-500 col-md-6">
                                <span>Owner</span>
                                <p id="ownerid">admin</p>
                                <span>Current Balance</span>
                                <p id="balanceinfo">0</p>
                                <span>Package Name</span>
                                <p id="packagename">50 Mbps - Unlimited</p>
                                <span>Speed Profile</span>
                                <p id="packagedetails">50 Mbps</p>
                            </li>
                            <li class="weight-500 col-md-6">
                                <span>Status</span>
                                <p id="status">Active</p>
                                <span>Current Status</span>
                                <p id="status">Online</p>
                                <span>IP</span>
                                <p id="ipaddress">192.168.100.155</p>
                                <span>NAS</span>
                                <p id="NAS">Banigala</p>
                                <span>Registered On</span>
                                <p id="registeron">15-09-2024 10:10:35</p>
                                <span>Last LoggOff</span>
                                <p id="logoff">15-09-2024 10:10:35</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-box pd-20 height-100-p mb-30">
            <h4 class="mb-15 text-blue h4">Other User Actions</h4>
            <div class="btn-list">
                <button type="button" class="btn btn-outline-primary">
                    Traffic Report
                </button>
                <button type="button" class="btn btn-outline-primary">
                    Connection Report
                </button>
                <button type="button" class="btn btn-outline-primary">
                    Buy Credit
                </button>
                <button type="button" class="btn btn-outline-primary">
                    List Invoices
                </button>
                <button type="button" class="btn btn-outline-primary">
                    Change Service
                </button>
                <button type="button" class="btn btn-outline-primary">
                    Service History
                </button>
                <button type="button" class="btn btn-outline-primary">
                    Authentication Logs
                </button>
            </div>
        </div>
        <div class="card-box mb-30">
            <div class="pd-20">
                <h4 class="text-blue h4">Invoices</h4>
                <table class="data-table table stripe hover nowrap" id="invoiceTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Service</th>
                            <th>Price</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Service On</th>
                            <th>Expiration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated by DataTables via AJAX -->
                    </tbody>
                </table>
            </div>
            <div class="pb-20"></div>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/src/plugins/apexcharts/apexcharts.min.js"></script>
<script>
    $(document).ready(function() {
        $("#saveSubscriber1").on("submit", function(e) {
            e.preventDefault();
            let isValid = true;
            const firstName = $("#fname").val();
            if (firstName.length < 3) {
                isValid = false;
                alert("First Name must be at least 3 characters.");
            }
            const lastName = $("#lname").val();
            if (lastName.length < 3) {
                isValid = false;
                alert("Last Name must be at least 3 characters.");
            }
            const username = $("#user").val();
            const usernamePattern = /^[a-z\d@]{6,}$/;
            if (!usernamePattern.test(username)) {
                isValid = false;
                alert("Username must be at least 6 characters and only contain @ without spaces.");
            } else {
                $.ajax({
                    url: "<?php echo base_url(); ?>subscribers/checkUserExist",
                    type: "POST",
                    data: { userId: username },
                    async: false,
                    success: function(response) {
                        console.log(response);
                        if (response == "exists") {
                            isValid = false;
                            alert("Username already exists. Please choose another.");
                        }
                        usernameChecked = true;
                    },
                    error: function() {
                        isValid = false;
                        alert("Error checking username.");
                        usernameChecked = true;
                    }
                });
            }
            const validId = $("#validid").val();
            if (validId.length < 13 || validId.length > 20) {
                isValid = false;
                alert("Valid ID must be between 13 and 20 characters.");
            }
            const password = $("#password1").val();
            const passwordPattern = /^[A-Za-z\d]{4,}$/;
            if (!passwordPattern.test(password)) {
                isValid = false;
                alert("Password must be at least 4 characters and contain a number without spaces.");
            }
            const repeatPassword = $("#password2").val();
            console.log(password + "-");
            console.log(repeatPassword);
            if (repeatPassword !== password) {
                isValid = false;
                alert("Passwords do not match.");
            }
            const password_rad = $("#password3").val();
            const passwordPattern_rad = /^[A-Za-z\d]{4,}$/;
            if (!passwordPattern_rad.test(password_rad)) {
                isValid = false;
                alert("Password must be at least 4 characters and contain a number without spaces.");
            }
            const repeatPassword_rad = $("#password4").val();
            console.log(password_rad + "-");
            console.log(repeatPassword_rad);
            if (repeatPassword_rad !== password_rad) {
                isValid = false;
                alert("Radius Passwords do not match.");
            }
            const mobile = $("#mobile").val();
            if (mobile.length < 12 || mobile.length > 20) {
                isValid = false;
                alert("Contact number must be between 12 and 20 characters.");
            }
            if (isValid) {
                alert("New user created successfully.");
                $("#saveSubscriber").unbind('submit').submit();
            }
        });
        function hideshow(toggleId, passwordFieldId, slashIconId, eyeIconId) {
            const passwordField = $(`#${passwordFieldId}`);
            console.log('Password field type before toggle:', passwordField.attr("type"));
            
            if (passwordField.attr("type") === "password") {
                passwordField.attr("type", "text");
                $(`#${slashIconId}`).hide();
                $(`#${eyeIconId}`).show();
            } else {
                passwordField.attr("type", "password");
                $(`#${slashIconId}`).show();
                $(`#${eyeIconId}`).hide();
            }
        }
        $("#toggle-password").on("click", function() {
            hideshow('toggle-password', 'password1', 'slash', 'eye');
        });
        $("#atoggle-password").on("click", function() {
            hideshow('atoggle-password', 'apassword1', 'aslash', 'aeye');
        });
        $('#password1, #password2').on('blur', function() {
            var password1 = $('#password1').val();
            var password2 = $('#password2').val();
            if(password1 && password2) {
                if(password1 !== password2) {
                    alert('Login passwords do not match!');
                    $("#password1").val("");
                    $("#password2").val("");
                }
            }
        });
        $('#apassword1, #apassword2').on('blur', function() {
            var apassword1 = $('#apassword1').val();
            var apassword2 = $('#apassword2').val();
            if(apassword1 && apassword2) {
                if(apassword1 !== apassword2) {
                    alert('Radius passwords do not match!');
                    $("#apassword1").val("");
                    $("#apassword2").val("");
                }
            }
        });
        $('#enableuser').on('change', function() {
            if ($(this).is(':checked')) {
                console.log("Status Enabled");
                $(this).val('1');
            } else {
                console.log("Status Disabled");
                $(this).val('0');
            }
        });
        $('#verified').on('change', function() {
            if ($(this).is(':checked')) {
                console.log("Status Verified");
                $(this).val('1');
            } else {
                console.log("Status Not Verified");
                $(this).val('0');
            }
        });
        $('#alertemail').on('change', function() {
            if ($(this).is(':checked')) {
                console.log("Email Alert Enabled");
                $(this).val('1');
            } else {
                console.log("Email Alert Disabled");
                $(this).val('0');
            }
        });
        $('#alertsms').on('change', function() {
            if ($(this).is(':checked')) {
                console.log("SMS Alert Enabled");
                $(this).val('1');
            } else {
                console.log("SMS Alert Disabled");
                $(this).val('0');
            }
        });
        var username = "<?php echo $userInfo->username; ?>";
        $('#invoiceTable').DataTable({
            responsive: true,
            scrollCollapse: false,
            autoWidth: false,
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": "<?php echo base_url('Invoices/fetch_billing_transactions'); ?>",
                "type": "POST",
                "data": {
                    "username": username
                },
                "dataSrc": function(json) {
                    console.log("Full JSON response:", json);
                    return json.data;
                }
            }, 
            "dom": '<"top"Bfl> rt<"bottom"pi><"clear">',
            "buttons": [
                'copy', 'csv', 'excel', 'pdf'
            ],
            "columns": [
                { "data": "createdDtm" },
                { "data": "srvname" },
                { "data": "price" },
                { "data": "amount" },
                { "data": "invtype" },
                { "data": "srvdate" },
                { "data": "expdate" }
            ]
        });
    });
    $('#subscriber_profile').submit(function (e) {
        e.preventDefault();
        var formData = $(this).serialize() + '&expiration=' + $('#expiration').val();
        console.log("TEST.............");
        
        $.ajax({
            url: "<?php echo base_url('Subscribers/update_subscriberProfile'); ?>",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function (response) {
                console.log(response);
                if (response.status == 'success') {
                    alert(response.message);
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert('An error occurred. Please try again.');
            }
        });
    });
</script>
<script src="<?php echo base_url(); ?>assets/vendors/scripts/dashboard11.js"></script>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NXZMQSS" height="0" width="0" style="display: none; visibility: hidden"></iframe></noscript>
<script>
    $(document).ready(function() {
        $("#saveSubscriber1").on("submit", function(e) {
            e.preventDefault();
            let isValid = true;
            const firstName = $("#fname").val();
            if (firstName.length < 3) {
                isValid = false;
                alert("First Name must be at least 3 characters.");
            }
            const lastName = $("#lname").val();
            if (lastName.length < 3) {
                isValid = false;
                alert("Last Name must be at least 3 characters.");
            }
            const username = $("#user").val();
            const usernamePattern = /^[a-z\d@]{6,}$/;
            if (!usernamePattern.test(username)) {
                isValid = false;
                alert("Username must be at least 6 characters and only contain @ without spaces.");
            } else {
                $.ajax({
                    url: "<?php echo base_url(); ?>subscribers/checkUserExist",
                    type: "POST",
                    data: { userId: username },
                    async: false,
                    success: function(response) {
                        console.log(response);
                        if (response == "exists") {
                            isValid = false;
                            alert("Username already exists. Please choose another.");
                        }
                        usernameChecked = true;
                    },
                    error: function() {
                        isValid = false;
                        alert("Error checking username.");
                        usernameChecked = true;
                    }
                });
            }
            const validId = $("#validid").val();
            if (validId.length < 13 || validId.length > 20) {
                isValid = false;
                alert("Valid ID must be between 13 and 20 characters.");
            }
            const password = $("#password1").val();
            const passwordPattern = /^[A-Za-z\d]{4,}$/;
            if (!passwordPattern.test(password)) {
                isValid = false;
                alert("Password must be at least 4 characters and contain a number without spaces.");
            }
            const repeatPassword = $("#password2").val();
            console.log(password + "-");
            console.log(repeatPassword);
            if (repeatPassword !== password) {
                isValid = false;
                alert("Passwords do not match.");
            }
            const password_rad = $("#password3").val();
            const passwordPattern_rad = /^[A-Za-z\d]{4,}$/;
            if (!passwordPattern_rad.test(password_rad)) {
                isValid = false;
                alert("Password must be at least 4 characters and contain a number without spaces.");
            }
            const repeatPassword_rad = $("#password4").val();
            console.log(password_rad + "-");
            console.log(repeatPassword_rad);
            if (repeatPassword_rad !== password_rad) {
                isValid = false;
                alert("Radius Passwords do not match.");
            }
            const mobile = $("#mobile").val();
            if (mobile.length < 12 || mobile.length > 20) {
                isValid = false;
                alert("Contact number must be between 12 and 20 characters.");
            }
            if (isValid) {
                alert("New user created successfully.");
                $("#saveSubscriber").unbind('submit').submit();
            }
        });
        function hideshow(toggleId, passwordFieldId, slashIconId, eyeIconId) {
            const passwordField = $(`#${passwordFieldId}`);
            console.log('Password field type before toggle:', passwordField.attr("type"));
            
            if (passwordField.attr("type") === "password") {
                passwordField.attr("type", "text");
                $(`#${slashIconId}`).hide();
                $(`#${eyeIconId}`).show();
            } else {
                passwordField.attr("type", "password");
                $(`#${slashIconId}`).show();
                $(`#${eyeIconId}`).hide();
            }
        }
        $("#toggle-password").on("click", function() {
            hideshow('toggle-password', 'password1', 'slash', 'eye');
        });
        $("#atoggle-password").on("click", function() {
            hideshow('atoggle-password', 'apassword1', 'aslash', 'aeye');
        });
        $('#password1, #password2').on('blur', function() {
            var password1 = $('#password1').val();
            var password2 = $('#password2').val();
            if(password1 && password2) {
                if(password1 !== password2) {
                    alert('Login passwords do not match!');
                    $("#password1").val("");
                    $("#password2").val("");
                }
            }
        });
        $('#apassword1, #apassword2').on('blur', function() {
            var apassword1 = $('#apassword1').val();
            var apassword2 = $('#apassword2').val();
            if(apassword1 && apassword2) {
                if(apassword1 !== apassword2) {
                    alert('Radius passwords do not match!');
                    $("#apassword1").val("");
                    $("#apassword2").val("");
                }
            }
        });
        $('#enableuser').on('change', function() {
            if ($(this).is(':checked')) {
                console.log("Status Enabled");
                $(this).val('1');
            } else {
                console.log("Status Disabled");
                $(this).val('0');
            }
        });
        $('#verified').on('change', function() {
            if ($(this).is(':checked')) {
                console.log("Status Verified");
                $(this).val('1');
            } else {
                console.log("Status Not Verified");
                $(this).val('0');
            }
        });
        $('#alertemail').on('change', function() {
            if ($(this).is(':checked')) {
                console.log("Email Alert Enabled");
                $(this).val('1');
            } else {
                console.log("Email Alert Disabled");
                $(this).val('0');
            }
        });
        $('#alertsms').on('change', function() {
            if ($(this).is(':checked')) {
                console.log("SMS Alert Enabled");
                $(this).val('1');
            } else {
                console.log("SMS Alert Disabled");
                $(this).val('0');
            }
        });
        var username = "<?php echo $userInfo->username; ?>";
        $('#invoiceTable').DataTable({
            responsive: true,
            scrollCollapse: false,
            autoWidth: false,
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": "<?php echo base_url('Invoices/fetch_billing_transactions'); ?>",
                "type": "POST",
                "data": {
                    "username": username
                },
                "dataSrc": function(json) {
                    console.log("Full JSON response:", json);
                    return json.data;
                }
            }, 
            "dom": '<"top"Bfl> rt<"bottom"pi><"clear">',
            "buttons": [
                'copy', 'csv', 'excel', 'pdf'
            ],
            "columns": [
                { "data": "createdDtm" },
                { "data": "srvname" },
                { "data": "price" },
                { "data": "amount" },
                { "data": "invtype" },
                { "data": "srvdate" },
                { "data": "expdate" }
            ]
        });
    });
    $('#subscriber_profile').submit(function (e) {
        e.preventDefault();
        var formData = $(this).serialize() + '&expiration=' + $('#expiration').val();
        console.log("TEST.............");
        
        $.ajax({
            url: "<?php echo base_url('Subscribers/update_subscriberProfile'); ?>",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function (response) {
                console.log(response);
                if (response.status == 'success') {
                    alert(response.message);
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert('An error occurred. Please try again.');
            }
        });
    });
</script>
    