<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>RadSpot | Manage Voucher</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    
  </head>
  <body class="hold-transition login-page">
    <div class="login-box">
      <div class="login-logo">
        <a href="#"><h3>Rad-Spot Radius</h3>
        <img class="logo" src="../assets/images/logo.png" width="214px" />
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg">Session Refresh Or Mac Unbind Request</p>
        <?php $this->load->helper('form'); ?>
        <div class="row">
            <div class="col-md-12">
                <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
            </div>
        </div>
        <?php
        $this->load->helper('form');
        $error = $this->session->flashdata('error');
        if($error)
        {
            ?>
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?php echo $error; ?>                    
            </div>
        <?php }
        $success = $this->session->flashdata('success');
        if($success)
        {
            ?>
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?php echo $success; ?>                    
            </div>
        <?php } ?>

        <form id="hotspotlogin" action="<?php echo base_url() ?>Other/api_session_delete" method="post">

            <div class="form-group has-feedback">
                <input type="text" class="form-control" placeholder="Voucher Number" name="username" id="username" required />
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input class="form-control" placeholder="Voucher PIN" name="password" id="password" />
                <span  class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>

            <span class="label label-info">Message</span>&nbsp<span class="label "><label id="messageLabel">Enter Voucher Details to clear session</label></span>

           
            <?php //echo "MAC ADDRESS: ".$mac." | Link Login: ".$linkLoginOnly; ?>

          <div class="row">
            <div class="col-xs-8">    
              <!-- <div class="checkbox icheck">
                <label>
                  <input type="checkbox"> Remember Me
                </label>
              </div>  -->                       
            </div><!-- /.col -->
            <div class="col-xs-4">
              <input type="submit" class="btn btn-primary btn-block btn-flat" value="Reset Now" />
            </div><!-- /.col -->
          </div>
        </form>

        

        <a href="<?php echo base_url() ?>forgotPassword">Click here for complaints</a><br>
        
      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

    <script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
          $('#password').hide(); // Initially hide the password field
          $('#messageLabel').css('color', 'green');

          // Initially, we are checking the username
          var checkingUsername = true;

          $('#username').change(function() {
              // Make AJAX call to controller for username validation
              $.ajax({
                  url: '<?php echo site_url('Other/validate_hotspotuser'); ?>',
                  type: 'POST',
                  data: {
                      username: $('#username').val()
                  },
                  dataType: 'json',
                  success: function(response) {
                      // Handle response for username validation
                      if(response.status === 'success') {
                          console.log(response.mode);
                          if(response.mode === 1) {
                              // Password is required, show the password field
                              $('#password').show();
                              $('#messageLabel').text('Please enter your password.').css('color', 'green');
                          } else {
                              // No password required, proceed with form submission or next step
                              $('#messageLabel').text('No password required, proceeding...').css('color', 'green');
                              // Optionally submit the form or take another action here
                              $('#hotspotlogin').submit();
                          }
                      } else {
                          $('#messageLabel').text(response.message).css('color', 'red');
                      }
                  }
              });
          });

          $('#password').change(function() {
              if (!checkingUsername) { // Proceed if we're in the password checking step
                  // Make AJAX call to controller for password validation
                  $.ajax({
                      url: '<?php echo site_url('Other/validate_hotspotpassword'); ?>',
                      type: 'POST',
                      data: {
                          username: $('#username').val(), // Include the username for validation
                          password: $('#password').val() // Include the password for validation
                      },
                      dataType: 'json',
                      success: function(response) {
                          // Handle response for password validation
                          if(response.status === 'success') {
                              $('#messageLabel').text('Validation successful.').css('color', 'green');
                          } else {
                              $('#messageLabel').text(response.message).css('color', 'red');
                          }
                      }
                  });
              }
          });
      });

      </script>
  </body>
</html>