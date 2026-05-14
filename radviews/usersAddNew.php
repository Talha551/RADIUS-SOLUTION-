<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><i class="fas fa-users"></i> New User <small>Add / Edit User</small></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
              <li class="breadcrumb-item active">New User</li>
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
                <h3 class="card-title">New User Details</h3>
              </div><!-- /.card-header -->
              <!-- form start -->
              <?php $this->load->helper("form"); ?>
              <form role="form" id="saveNewUser" action="<?php echo base_url() ?>saveNewUser" method="post" role="form">
                <div class="card-body">
                  <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" value="1" id="status" checked>
                    <label class="form-check-label" for="status">Enabled</label>
                  </div>

                  <div class="row">
                    <div class="col-md-6">                                
                      <div class="form-group">
                        <label for="user">User Name</label>
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
                        <label for="service">Packages</label>
                        <select class="form-control required" id="service" name="service">
                          <option value="0">Select Package</option>
                          <?php
                            if(!empty($packages))
                            {
                              foreach ($packages as $rl)
                              {
                                ?>
                                <option value="<?php echo $rl->srvid ?>" <?php if($rl->srvid == set_value('srvid')) {echo "selected=selected";} ?>><?php echo $rl->srvname ?></option>
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

                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control required" id="address" value="<?php echo set_value('address'); ?>" name="address" maxlength="100">
                      </div>
                    </div>
                  </div>

                  <div class="row">

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="city">Region/Area</label>
                        <select class="form-control required" id="region" name="region">
                          <option value="0">Select Region</option>
                          <?php
                            if(!empty($regions))
                            {
                              foreach ($regions as $rl)
                              {
                                ?>
                                <option value="<?php echo $rl->segmentid ?>" <?php if($rl->segmentid == set_value('region')) {echo "selected=selected";} ?>><?php echo $rl->segmentname ?></option>
                                <?php
                              }
                            }
                          ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="state">Location</label>
                        <select class="form-control required" id="parameter" name="parameter">
                          <option value="0">Select Area</option>
                        </select>
                      </div>
                    </div>

                  </div>

                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="mobile">Contact (Format 923331234567)</label>
                        <input type="text" class="form-control required" id="mobile" value="<?php echo set_value('mobile'); ?>" name="mobile" maxlength="13">
                      </div>
                    </div>

                    <div class="col-md-4">
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
                </div><!-- /.card-body -->

                <div class="card-footer">
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
      </div><!-- /.container-fluid -->
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
<script>
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
                }
            });
        } else {
            $('#parameter').html('<option value="0">Select Area</option>');
        }
    });
});
</script>