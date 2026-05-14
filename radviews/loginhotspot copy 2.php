<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>RadSpot | Hotspot Login Page</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <!-- Include FingerprintJS -->
    <script src="<?php echo base_url(); ?>assets/js/fp.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="#">
                <h3>Rad-Spot Radius</h3>
                <img class="logo" src="../assets/images/logo.png" width="214px" />
        </div><!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">Sign In</p>

            <label id="error"><?php //echo $hotspoterror; ?></label>

            <?php $this->load->helper('form'); ?>
            <div class="row">
                <div class="col-md-12">
                    <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                </div>
            </div>
            <?php
            $this->load->helper('form');
            $error = $this->session->flashdata('error');
            if ($error) {
            ?>
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo $error; ?>
                </div>
            <?php }
            $success = $this->session->flashdata('success');
            if ($success) {
            ?>
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo $success; ?>
                </div>
            <?php } ?>

            <form id="hotspotlogin" action="<?php echo $linkLoginOnly; ?>" method="post">
                <div class="form-group has-feedback">
                    <input type="hidden" class="form-control" placeholder="Voucher Number" name="voucher" />
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>

                <div class="form-group has-feedback">
                    <input type="text" class="form-control" placeholder="Voucher Number" name="username" id="username" required />
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input class="form-control" placeholder="Voucher PIN" name="password" id="password" />
                    <span class="glyphicon glyphicon-lock form-control-feedback" id="locksign"></span>
                </div>

                    <label id="messageLabel1"></label>
                    <br>
                <span class="label label-info">Message</span>&nbsp<span class="label "><label id="messageLabel">Welcome</label></span>

                <input type="hidden" name="mac" value="<?php echo $mac; ?>">
                <input type="hidden" name="ip" value="<?php echo $ip; ?>">
                <input type="hidden" name="linkLogin" value="<?php echo $linkLogin; ?>">
                <input type="hidden" name="linkLoginOnly" value="<?php echo $linkLoginOnly; ?>">
                <input type="hidden" name="domain" value="">
                <input type="hidden" name="dst" value="http://www.pace-tel.com">

                <?php //echo "MAC ADDRESS: ".$mac." | Link Login: ".$linkLoginOnly; 
                ?>

                <div class="row">
                    <div class="col-xs-8">
                        <!-- <div class="checkbox icheck">
                <label>
                  <input type="checkbox"> Remember Me
                </label>
              </div>  -->
                    </div><!-- /.col -->
                    <div class="col-xs-4">
                        <input type="submit" class="btn btn-primary btn-block btn-flat" value="Sign In" />
                    </div><!-- /.col -->
                </div>
            </form>

            <br>
            <label id="deviceLabel1">Device-ID</label>
            <!-- <label id="deviceLabel2"></label> -->
            <br>

            <a href="<?php echo base_url() ?>forgotPassword">Click here for complaints</a><br>

        </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

    <script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <script>

        $(document).ready(function() {
            $('#password').hide(); // Initially hide the password field
            $('#locksign').hide(); // Initially hide the password field
            $('#messageLabel').css('color', 'green');

            $('#messageLabel1').text('<?php echo $error; ?>').css('color', 'green');

            // Initialize FingerprintJS
            const fpPromise = FingerprintJS.load();


            // Function to validate device ID on page load
            function validateDeviceId(visitorId) {
                console.log(visitorId);
                $.ajax({
                    url: '<?php echo site_url('Other/validate_deviceid'); ?>',
                    type: 'POST',
                    data: {
                        device_id: visitorId // Send device ID for validation
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log("Username: " + response.username);
                        console.log("Message: " + response.message);
                        if (response.status === 'success') {
                            if (response.deviceid === visitorId) {

                                console.log("Going to Auto Login");
                                $('#deviceLabel1').text(response.message).css('color', 'red');
                                //$('#deviceLabel2').text('ID: ' + visitorId).css('color', 'green');
                                $(':input[type="submit"]').prop('disabled', false);
                                $('#username').val(response.username);
                                if('<?php echo $hotspoterror; ?>' === '')
                                {
                                    //$('#messageLabel').text('Press Sign In Button. Trying Auto Login').css('color', 'green');
                                    //$('#hotspotlogin').submit(); // Auto-login user
                                }else{
                                    //$('#messageLabel').text('<?php echo $hotspoterror; ?>').css('color', 'red');
                                    //$(':input[type="submit"]').prop('disabled', true);
                                }

                                //$('#hotspotlogin').submit(); // Auto-login user
                                
                            }
                        } else {
                            $('#deviceLabel1').text('New Device Found').css('color', 'red');
                            //$('#deviceLabel2').text('ID: ' + visitorId).css('color', 'red');
                            $('#messageLabel').text(response.message).css('color', 'red');
                        }
                    }
                });
            }

            // Get the unique device ID and validate on page load
            fpPromise.then(fp => fp.get()).then(result => {
                const visitorId = result.visitorId;
                validateDeviceId(visitorId);

                $('#username').change(function() {
                    var username = $('#username').val();

                    // Make AJAX call to controller for username validation
                    $.ajax({
                        url: '<?php echo site_url('Other/validate_hotspotuser'); ?>',
                        type: 'POST',
                        data: {
                            username: username,
                            device_id: visitorId // Include the device ID for validation
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log("Response Message : " + response.message);
                            // Handle response for username validation
                            if (response.status === 'success') {
                                if (response.mode === 3) {
                                    $(':input[type="submit"]').prop('disabled', true);
                                    $('#messageLabel').text('Voucher not allowed from this device.').css('color', 'red');
                                } else {

                                    if (response.mode === 1 || response.mode === 2) {

                                        $(':input[type="submit"]').prop('disabled', true);
                                        if (response.mode === 1) {
                                            $('#password').show();
                                            $('#locksign').show();
                                        }

                                        $('#messageLabel').text(response.message).css('color', 'green');

                                    } else {

                                        console.log("User :" + username + "-");
                                        console.log("Device: " + visitorId);

                                        // Make AJAX call to update device ID
                                        $.ajax({
                                            url: '<?php echo site_url('Other/update_deviceid'); ?>',
                                            type: 'POST',
                                            data: {
                                                username: username,
                                                device_id: visitorId
                                            },
                                            dataType: 'json',
                                            success: function(updateResponse) {
                                                console.log("Message: " + updateResponse.message);
                                                if (updateResponse.mode === 1) {
                                                    $(':input[type="submit"]').prop('disabled', false);
                                                    $('#messageLabel').text('Press sign In button').css('color', 'green');
                                                    //$('#hotspotlogin').submit();
                                                } else {
                                                    $(':input[type="submit"]').prop('disabled', true);
                                                    $('#messageLabel').text(updateResponse.message).css('color', 'red');
                                                }
                                            }
                                        });

                                    }
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
                                password: $('#password').val(), // Include the password for validation
                                device_id: visitorId // Include the device ID for validation
                            },
                            dataType: 'json',
                            success: function(response) {
                                // Handle response for password validation
                                if (response.status === 'success') {
                                    $('#messageLabel').text('Validation successful.').css('color', 'green');
                                } else {
                                    $('#messageLabel').text(response.message).css('color', 'red');
                                }
                            }
                        });
                    }
                });

            });
        });


    </script>
</body>

</html>