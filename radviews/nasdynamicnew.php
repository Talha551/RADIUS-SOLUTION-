<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> Dynamic DNS Mapping
        <small>Add / Edit User</small>
      </h1>
    </section>
    
    <section class="content">
    
        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
              <!-- general form elements -->
                
                
                
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">New Reseller Details</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <?php $this->load->helper("form"); ?>
                    <form role="form" id="saveNewReseller" action="<?php echo base_url() ?>saveNewReseller" method="post" role="form">
                        <div class="box-body">

                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" value="1" id="status" checked>
                                <label class="form-check-label" for="status">Enabled</label>
                            </div>

                            <div class="row">
                                <div class="col-md-6">                                
                                    <div class="form-group">
                                        <label for="user">reseller ID</label>
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
                                        <select class="form-control required" id="master" name="master" <?php if($session_manager <> "admin") echo "disabled"; ?>>
                                            <option value="0">Select Master</option>
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
                                        <label for="mobile">CNIC No.</label>
                                        <input type="text" class="form-control required" id="cnic" value="<?php echo set_value('cnic'); ?>" name="cnic" maxlength="16">
                                    </div>
                                </div>
                            </div>

                        </div><!-- /.box-body -->
    
                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" value="Submit" id="btn-submit" />
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
<script src="<?php echo base_url(); ?>assets/js/usersAddNew.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#saveNewUser").submit(function (e) {
            $("#btn-submit").attr("disabled", true);
            return true;
        });
    });
</script>