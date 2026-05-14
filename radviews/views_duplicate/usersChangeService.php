<?php
$user = $userInfo->username;
$password = $userInfo->password;

$fname = $userInfo->firstname;
$lname = $userInfo->lastname;
$address = $userInfo->address;
$mobile = $userInfo->mobile;
$email = $userInfo->email;
$cnic = $userInfo->taxid;
$srvid = $userInfo->srvid;

?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> User Management
        <small>User Details / Package Update</small>
      </h1>
    </section>
    
    <section class="content">
    
        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
              <!-- general form elements -->
                
                
                
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">All advance recharges will be lost & refund to reseller</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    
                    <form role="form" action="<?php echo base_url() ?>updateUserService" method="post" id="updateUser" role="form">
                        <div class="box-body">

                            <div class="row">
                                <div class="col-md-6">                                
                                    <div class="form-group">
                                    <label for="username">User Name</label>
                                        <input type="label" class="form-control" id="user" placeholder="User Name" name="user" value="<?php echo $user; ?>" maxlength="50" readonly>
                                        <input type="hidden" value="<?php echo $user; ?>" name="user" id="user" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="service">Change Package</label>
                                        <select class="form-control" id="service" name="service">
                                            <option value="0">Select Package</option>
                                            <?php
                                            if(!empty($packages))
                                            {
                                                foreach ($packages as $rl)
                                                {
                                                    ?>
                                                    <option value="<?php echo $rl->srvid; ?>" <?php if($rl->radsrvid == $srvid) {echo "selected=selected";} ?>><?php echo $rl->srvname ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div> 
                            </div>

                            <h4 class="box-title">User Details</h4>

                            <div class="row">
                                <div class="col-md-6">                                
                                    <div class="form-group">
                                        <label for="firstname">First Name</label>
                                        <input type="text" class="form-control" id="fname" placeholder="First Name" name="fname" value="<?php echo $fname; ?>" maxlength="20" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">                                
                                    <div class="form-group">
                                        <label for="lastname">Last Name</label>
                                        <input type="text" class="form-control" id="lname" placeholder="Last Name" name="lname" value="<?php echo $lname; ?>" maxlength="20" readonly>
                                    </div>
                                    
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <input type="text" class="form-control" id="address" placeholder="Address" name="address" value="<?php echo $address; ?>" maxlength="100" readonly>
                                    </div>
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="email">Email address</label>
                                        <input type="email" class="form-control" id="email" placeholder="Enter email" name="email" value="<?php echo $email; ?>" maxlength="128" readonly>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="mobile">Mobile Number</label>
                                        <input type="text" class="form-control" id="mobile" placeholder="Mobile Number" name="mobile" value="<?php echo $mobile; ?>" maxlength="13" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">                                
                                    <div class="form-group">
                                        <label for="firstname">CNIC</label>
                                        <input type="text" class="form-control" id="cnic" placeholder="C.N.I.C No." name="cnic" value="<?php echo $cnic; ?>" maxlength="16" readonly>
                                        <input type="hidden" value="<?php echo $cnic; ?>" name="cnic" id="cnic" />    
                                    </div>
                                </div>
                                  
                            </div>

                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" value="Submit" />
                            <input type="reset" class="btn btn-default" value="Reset" />
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
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo $this->session->flashdata('error'); ?>                    
                </div>
                <?php } ?>
                <?php  
                    $success = $this->session->flashdata('success');
                    if($success)
                    {
                ?>
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
                <?php } ?>
                
                <div class="row">
                    <div class="col-md-12">
                        <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                    </div>
                </div>
            </div>
        </div>    
    </section>
</div>

<script src="<?php echo base_url(); ?>assets/js/editUser.js" type="text/javascript"></script>