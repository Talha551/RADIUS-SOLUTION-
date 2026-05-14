<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-user-edit"></i> Edit Profile
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>Reseller_controller/profileListing">Profile Management</a></li>
                        <li class="breadcrumb-item active">Edit Profile</li>
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

                        <form role="form" id="editProfile" action="<?php echo base_url() ?>Reseller_controller/updateProfile" method="post" role="form">
                            <div class="card-body">
                                <input type="hidden" name="userId" value="<?php echo $profileInfo->userId; ?>" />

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="profileid">Profile ID</label>
                                            <input type="text" class="form-control" id="profileid" name="profileid" value="<?php echo $profileInfo->profileid; ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $profileInfo->name; ?>" placeholder="Enter Full Name" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password (leave blank to keep current)">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cpassword">Confirm Password</label>
                                            <input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="Confirm Password">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mobile">Mobile Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="mobile" name="mobile" value="<?php echo $profileInfo->mobile; ?>" placeholder="Enter Mobile Number" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="roleId">Role <span class="text-danger">*</span></label>
                                            <select class="form-control" id="roleId" name="roleId" required>
                                                <option value="">Select Role</option>
                                                <?php
                                                if (!empty($roles)) {
                                                    foreach ($roles as $role) {
                                                ?>
                                                        <option value="<?php echo $role->roleId; ?>" <?php echo ($profileInfo->roleId == $role->roleId) ? 'selected' : ''; ?>><?php echo $role->role; ?></option>
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
                                                if (!empty($managers)) {
                                                    foreach ($managers as $manager) {
                                                ?>
                                                        <option value="<?php echo $manager->managername; ?>" <?php echo ($profileInfo->managername == $manager->managername) ? 'selected' : ''; ?>><?php echo $manager->managername; ?></option>
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
                                            <label>Created Date</label>
                                            <input type="text" class="form-control" value="<?php echo date('d-m-Y H:i', strtotime($profileInfo->createdDtm)); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Last Updated</label>
                                            <input type="text" class="form-control" value="<?php echo ($profileInfo->updatedDtm) ? date('d-m-Y H:i', strtotime($profileInfo->updatedDtm)) : 'Never'; ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <input type="submit" class="btn btn-primary" value="Update" />
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
    jQuery(document).ready(function() {
        jQuery('#editProfile').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 128
                },
                cpassword: {
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
                name: {
                    required: "Please enter Full Name",
                    minlength: "Name must be at least 2 characters long",
                    maxlength: "Name cannot exceed 128 characters"
                },
                cpassword: {
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