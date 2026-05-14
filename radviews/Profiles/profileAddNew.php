<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-user-plus"></i> Add New Profile
                    </h1>
                </div>
                <?php $this->load->helper("form"); ?>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>Reseller_controller/profileListing">Profile Management</a></li>
                        <li class="breadcrumb-item active">Add New Profile</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Profile Information</h3>
                        </div>
                        
                        <form role="form" id="addProfile" action="<?php echo base_url() ?>Reseller_controller/saveNewProfile" method="post" role="form">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="profileid">Profile ID <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="profileid" name="profileid" value="<?php echo set_value('profileid'); ?>" placeholder="Enter Profile ID" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" value="<?php echo set_value('name'); ?>" placeholder="Enter Full Name" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Password <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cpassword">Confirm Password <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="Confirm Password" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mobile">Mobile Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="mobile" name="mobile" value="<?php echo set_value('mobile'); ?>" placeholder="Enter Mobile Number" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="roleId">Role <span class="text-danger">*</span></label>
                                            <select class="form-control" id="roleId" name="roleId" required>
                                                <option value="">Select Role</option>
                                                <?php
                                                if(!empty($roles))
                                                {
                                                    foreach($roles as $role)
                                                    {
                                                ?>
                                                <option value="<?php echo $role->roleId; ?>" <?php echo set_select('roleId', $role->roleId); ?>><?php echo $role->role; ?></option>
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
                                            <label for="managername">Manager <span class="text-danger">*</span></label>
                                            <select class="form-control" id="managername" name="managername" required>
                                                <option value="">Select Manager</option>
                                                <?php
                                                if(!empty($managers))
                                                {
                                                    foreach($managers as $manager)
                                                    {
                                                ?>
                                                <option value="<?php echo $manager->managername; ?>" <?php echo set_select('managername', $manager->managername); ?>><?php echo $manager->managername; ?></option>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </select>
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
                            
                            <div class="card-footer">
                                <input type="submit" class="btn btn-primary" value="Submit" />
                                <input type="reset" class="btn btn-default" value="Reset" />
                                <a class="btn btn-secondary" href="<?php echo base_url(); ?>Reseller_controller/profileListing">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#addProfile').validate({
            rules: {
                profileid: {
                    required: true,
                    minlength: 3,
                    maxlength: 64,
                    pattern: /^[a-zA-Z0-9_-]+$/
                },
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 128
                },
                password: {
                    required: true,
                    minlength: 6,
                    maxlength: 64
                },
                cpassword: {
                    required: true,
                    equalTo: "#password"
                },
                mobile: {
                    required: true,
                    minlength: 10,
                    maxlength: 20
                },
                roleId: {
                    required: true
                }
            },
            messages: {
                profileid: {
                    required: "Please enter Profile ID",
                    minlength: "Profile ID must be at least 3 characters long",
                    maxlength: "Profile ID cannot exceed 64 characters",
                    pattern: "Profile ID can only contain letters, numbers, hyphens, and underscores"
                },
                name: {
                    required: "Please enter Full Name",
                    minlength: "Name must be at least 2 characters long",
                    maxlength: "Name cannot exceed 128 characters"
                },
                password: {
                    required: "Please enter Password",
                    minlength: "Password must be at least 6 characters long",
                    maxlength: "Password cannot exceed 64 characters"
                },
                cpassword: {
                    required: "Please confirm Password",
                    equalTo: "Password and Confirm Password must match"
                },
                mobile: {
                    required: "Please enter Mobile Number",
                    minlength: "Mobile number must be at least 10 digits",
                    maxlength: "Mobile number cannot exceed 20 digits"
                },
                roleId: {
                    required: "Please select a Role"
                }
            }
        });
    });
</script> 