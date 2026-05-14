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
$manager = $userInfo->owner;

?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><i class="fas fa-users"></i> User Management <small>User Details / Edit User</small></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
              <li class="breadcrumb-item active">Edit User</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    
    <section class="content">
    
        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
              <!-- general form elements -->
                
                
                
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Update User Details</h3>
                    </div><!-- /.card-header -->
                    <!-- form start -->
                    
                    <form role="form" action="<?php echo base_url() ?>updateUser" method="post" id="updateUser" role="form">
                        <div class="card-body">

                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" value="1" id="status" name="status" checked>
                                <label class="form-check-label" for="status">Enabled</label>
                            </div>

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
                                <div class="col-md-6"><h6>Leave empty if do not want to change password.</h6></div>
                            </div>


                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password" class="text-danger">Password</label>
                                        <input type="password" class="form-control" id="password" placeholder="Password" name="password" maxlength="20">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cpassword" class="text-danger">Confirm Password</label>
                                        <input type="password" class="form-control" id="cpassword" placeholder="Confirm Password" name="cpassword" maxlength="20">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="service">Packages</label>
                                        <select class="form-control" id="service" name="service" disabled>
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

                            <?php 
                            
                            $sessionManagerName = $this->session->userdata ( 'name' ); 
                            ?>

                            <?php if($this->ismaster > 0 || $sessionManagerName == 'admin'){ ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="manager">Manager</label>
                                        <select class="form-control" id="manager" name="manager">
                                            <option value="">Select Manager</option>
                                            <?php
                                                if(!empty($managers))
                                                {
                                                    foreach ($managers as $rl)
                                                    {
                                                        ?>
                                                            <option value="<?php echo $rl->managername; ?>" <?php if($rl->managername == $manager) {echo "selected=selected";} ?>><?php echo $rl->managername ?></option>
                                                        <?php
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php } else { ?>


                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="manager">Manager Name</label>
                                        <input type="text" class="form-control required" id="manager" value="<?php echo $sessionManagerName ?>" name="manager" maxlength="20" autocomplete="off" readonly>
                                    </div>
                                </div>
                            </div>

                        <?php } ?>

                            <div class="row">
                                <div class="col-md-6">                                
                                    <div class="form-group">
                                        <label for="firstname">First Name</label>
                                        <input type="text" class="form-control" id="fname" placeholder="First Name" name="fname" value="<?php echo $fname; ?>" maxlength="20">
                                    </div>
                                </div>
                                <div class="col-md-6">                                
                                    <div class="form-group">
                                        <label for="lastname">Last Name</label>
                                        <input type="text" class="form-control" id="lname" placeholder="Last Name" name="lname" value="<?php echo $lname; ?>" maxlength="20">
                                    </div>
                                    
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <input type="text" class="form-control" id="address" placeholder="Address" name="address" value="<?php echo $address; ?>" maxlength="100">
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="region">Region/Area</label>
                                        <select class="form-control required" id="region" name="region">
                                            <option value="0">Select Region</option>
                                            <?php
                                            if(!empty($regions))
                                            {
                                                foreach ($regions as $rl)
                                                {
                                                    ?>
                                                        <option value="<?php echo $rl->segmentid; ?>" <?php echo ($rl->segmentid == $userDocsInfo->segmentid) ? 'selected' : ''; ?>><?php echo $rl->segmentname; ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="parameter">Location</label>
                                        <select class="form-control required" id="parameter" name="parameter">
                                            <option value="0">Select Area</option>
                                            <?php
                                            if(isset($parameters) && !empty($parameters)) {
                                                foreach($parameters as $param) {
                                                    ?>
                                                    <option value="<?php echo $param->parameter; ?>" <?php if(isset($userDocsInfo->parameter) && $param->parameter == $userDocsInfo->parameter) {echo "selected=selected";} ?>><?php echo $param->parameter; ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="email">Email address</label>
                                        <input type="email" class="form-control" id="email" placeholder="Enter email" name="email" value="<?php echo $email; ?>" maxlength="128">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="mobile">Mobile Number</label>
                                        <input type="text" class="form-control" id="mobile" placeholder="Mobile Number" name="mobile" value="<?php echo $mobile; ?>" maxlength="13">
                                    </div>
                                </div>
                                <div class="col-md-4">                                
                                    <div class="form-group">
                                        <label for="firstname">CNIC</label>
                                        <input type="text" class="form-control" id="cnic" placeholder="C.N.I.C No." name="cnic" value="<?php echo $cnic; ?>" maxlength="16">
                                        <input type="hidden" value="<?php echo $cnic; ?>" name="cnic" id="cnic" />    
                                    </div>
                                </div>
                                  
                            </div>

                        </div><!-- /.card-body -->

                        <div class="card-footer">
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
    </section>
</div>

<script src="<?php echo base_url(); ?>assets/js/editUser.js" type="text/javascript"></script>
<script>
    $('#status').on('change', function(){
        //$('#status').val(this.checked ? 1 : 2);
        //$(this).val($(this).attr('checked') ? '1' : '0');
        var custom=$("#status").is(':checked')?1:0;
    });

    var currentParameter = "<?php echo isset($userDocsInfo->parameter) ? $userDocsInfo->parameter : ''; ?>";
    $(document).ready(function() {
        $('#region').change(function() {
            var segmentid = $(this).val();
            if(segmentid != '' && segmentid != '0') {
                $.ajax({
                    url: '<?php echo base_url(); ?>Userslist/getSegmentDetails',
                    type: 'POST',
                    data: {segmentid: segmentid},
                    success: function(data) {
                        $('#parameter').html(data);
                        // Set the selected parameter if editing
                        if(currentParameter) {
                            $('#parameter').val(currentParameter);
                        }
                    }
                });
            } else {
                $('#parameter').html('<option value="0">Select Area</option>');
            }
        });

        // On page load, if region is already selected, trigger change to load parameters
        var selectedRegion = $('#region').val();
        if(selectedRegion && selectedRegion != '0') {
            $('#region').trigger('change');
        }
    });
</script>