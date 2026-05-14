<head>
    <meta charset="UTF-8">
    <title>Slay | Hotspot Login Page</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/css/bootstrap.min.css">

    <!-- AdminLTE 3 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">

    <!-- Base Styles -->
    <style>
      body.hold-transition.login-page{
        background-image: url('https://www.caterermiddleeast.com/cloud/2024/12/12/SLAY.jpg') !important;
        background-position: center center !important;
        background-repeat: no-repeat !important;
        background-attachment: fixed !important;
        background-size: cover !important;
        position: relative;
        isolation: isolate;
        min-height: 100vh;
      }
      body.hold-transition.login-page::before{
        content:"";
        position: fixed; inset: 0;
        background: linear-gradient(135deg, rgba(0,0,0,.6), rgba(0,0,0,.4));
        pointer-events:none;
        z-index:-1;
      }

      .login-page { background: transparent !important; }

      .login-box {
        width: 100%;
        max-width: 400px;
        margin-top: 180px;
      }

      .login-logo { text-align: center; margin-bottom: 20px; }

      .login-logo h3 {
        color: #000; /* CHANGED: was #fff — text ko black kiya for visibility */
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        margin-bottom: 15px;
      }

      .login-logo img {
        max-width: 200px; height: auto;
        filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.3));
      }

      .login-box-body {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        padding: 30px;
      }

      .login-box-msg { color: #495057; font-weight: 600; margin-bottom: 25px; text-align: center; }

      .form-group { margin-bottom: 20px; }

      .form-control {
        border-radius: 10px;
        border: 2px solid #000000ff;
        padding: 12px 15px;
        font-size: 14px;
        transition: all 0.3s ease;
      }
      .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
      }

      .input-group-text {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        color: #050505ff;
      }

      .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none; border-radius: 10px;
        padding: 12px 20px; font-weight: 600;
        transition: all 0.3s ease;
      }
      .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
      }

      .alert { border-radius: 10px; border: none; margin-bottom: 20px; }
      .alert-danger { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white; }
      .alert-success { background: linear-gradient(135deg, #51cf66 0%, #40c057 100%); color: white; }

      .label { border-radius: 5px; padding: 5px 10px; font-size: 12px; font-weight: 600; }
      .label-info { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); }

      .device-info {
        background: #f8f9fa; border-radius: 10px; padding: 15px; margin: 20px 0; border-left: 4px solid #667eea;
      }
      .device-info label { color: #000000ff; font-weight: 600; margin-bottom: 5px; }

      .complaints-link { color: #667eea; text-decoration: none; font-weight: 600; transition: color 0.3s ease; }
      .complaints-link:hover { color: #764ba2; text-decoration: none; }

      .welcome-message {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-radius: 10px; padding: 15px; margin: 20px 0; border-left: 4px solid #2196f3;
      }
      .welcome-message .label { margin-right: 10px; }

      @media (max-width: 576px) {
        
        .login-box { margin-top: 28px; }
        .login-box-body { padding: 20px; }
      }
    </style>

    <!-- ✅ OVERRIDE STYLES (end of <head>) -->
    <style>
      /* Remove any global white backgrounds from AdminLTE wrappers */
      html, body,
      .login-page, .register-page,
      .wrapper, .content-wrapper {
        background-color: transparent !important;
        background-image: none !important;
      }

      /* Turn OFF the dark/white overlay completely */
      body.hold-transition.login-page::before { display: none !important; }

      /* Force background image on <html> so nothing covers it */
      html {
        height: 100%;
        background: url('https://www.caterermiddleeast.com/cloud/2024/12/12/SLAY.jpg')
                    no-repeat center center fixed !important;
        background-size: cover !important;
      }
      body.hold-transition.login-page {
        background: transparent !important;
        min-height: 100vh;
      }

      /* Make the box slightly glassy instead of solid white */
      .login-box-body,

      .card, .card-body, .login-card-body {
        background: rgba(255,255,255,0.15) !important; /* was 0.95 */
        border: 1px solid rgba(255,255,255,0.25) !important;
        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(2px);
        color: #00000028 !important; /* CHANGED: was #fff — card text ko black kiya */
      }

      /* Inputs readable on glass */
      .input-group-text,
      .form-control {
        background: rgba(255,255,255,0.85) !important;
        border: 1px solid rgba(0,0,0,0.06) !important;
        color: #000 !important; /* CHANGED: force input text black */
      }
      .form-control::placeholder { color: #000 !important; opacity: .6; } /* CHANGED: placeholder ko bhi black/tint */

      /* Text colors on top of image */
      .login-box-msg, #messageLabel, #deviceLabel1 {
        color: #000 !important; /* CHANGED: was #fff — headings/labels ko black kiya */
        text-shadow: 0 1px 2px rgba(0,0,0,.15); /* optional soft shadow, readable but minimal */
      }
    </style>
    <!-- /OVERRIDES -->


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Include FingerprintJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fingerprintjs/3.3.6/fingerprint.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clientjs/1.2.0/client.min.js"></script>
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        
        <div class="login-box-body">
            <p class="login-box-msg">
                <i class="fas fa-sign-in-alt"></i> Sign In (<?php echo $identity; ?>)
            </p>

            <div id="error" class="alert alert-danger" style="display: none;"></div>

            <?php $this->load->helper('form'); ?>
            <div class="row">
                <div class="col-md-12">
                    <?php echo validation_errors('<div class="alert alert-danger alert-dismissible">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                </div>
            </div>
            
            <?php
            $this->load->helper('form');
            $error = $this->session->flashdata('error');
            if ($error) {
            ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
            <?php }
            $success = $this->session->flashdata('success');
            if ($success) {
            ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php } ?>

            <form id="hotspotlogin" action="<?php echo $linkLoginOnly; ?>" method="post">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-ticket-alt"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" placeholder="Enter Mobile Number" name="username" id="username" required />
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" placeholder="Enter Your Name *" name="name" id="name" required />
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                        </div>
                        <input type="email" class="form-control" placeholder="Enter Your Email *" name="email" id="email" required />
                    </div>
                </div>
                
                <div class="form-group" id="passwordGroup" style="display: none;">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                        </div>
                        <input type="password" class="form-control" placeholder="Enter Voucher PIN" name="password" id="password" />
                    </div>
                </div>


                <label id="messageLabel">Format: 05XXXXXXXX or 0096XXXXXXXX</label>


                <input type="hidden" name="mac" value="<?php echo $mac; ?>">
                <input type="hidden" name="ip" value="<?php echo $ip; ?>">
                <input type="hidden" name="linkLogin" value="<?php echo $linkLogin; ?>">
                <input type="hidden" name="linkLoginOnly" value="<?php echo $linkLoginOnly; ?>">
                <input type="hidden" name="domain" value="">
                <input type="hidden" name="dst" value="http://www.pace-tel.com">
                <input type="hidden" name="comments" id="comments" value="">

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block btn-lg" id="signInBtn">
                            <i class="fas fa-sign-in-alt"></i> Sign In
                        </button>
                    </div>
                </div>
            </form>

            <div class="device-info">
                <label id="deviceLabel1">
                    <i class="fas fa-mobile-alt"></i> Device-ID
                </label>
            </div>

        </div>
    </div>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    
    <!-- AdminLTE 3 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#passwordGroup').hide(); // Initially hide the password field
            $('#messageLabel').css('color', 'green');

            $('#messageLabel1').text('<?php echo $error; ?>').css('color', 'green');

            let storedUsername = localStorage.getItem('storedUsername') || ''; // Retrieve stored username or empty string
            let visitorId = 'default-id-' + Math.random().toString(36).substr(2, 9);
            let clientId = 'client-' + Math.random().toString(36).substr(2, 9);

            // Function to validate device ID on page load
            function validateDeviceId(visitorId, clientId, storedUsername) {

                console.log("FingerprintJS ID: " + visitorId);
                console.log("ClientJS ID: " + clientId);
                console.log("Stored Username: " + storedUsername);

                $.ajax({
                    url: '<?php echo site_url('Other/validate_deviceid'); ?>',
                    type: 'POST',
                    data: {
                        device_id: visitorId, // Send device ID for validation
                        client_id: clientId,
                        stored_username: storedUsername
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log("Username: " + response.username);
                        console.log("Message: " + response.message);
                        if (response.status === 'success') {
                            if (response.deviceid === visitorId + '-' + clientId || storedUsername == response.username) {

                                console.log("Going to Auto Login");
                                $('#deviceLabel1').html('<i class="fas fa-check-circle text-success"></i> ' + response.message);
                                $('#signInBtn').prop('disabled', false);

                                $('#username').val(response.username);
                                    
                                if('<?php echo $hotspoterror; ?>' === '')
                                {
                                    $('#messageLabel').text('Press Sign in to login with device saved voucher').css('color', 'green');
                                    //$('#hotspotlogin').submit(); // Auto-login user
                                }else{
                                    $('#messageLabel').text('<?php echo $hotspoterror; ?>').css('color', 'red');
                                    $('#signInBtn').prop('disabled', true);
                                }

                                //$('#hotspotlogin').submit(); // Auto-login user
                                
                            }
                        } else {
                            $('#deviceLabel1').html('<i class="fas fa-exclamation-triangle text-warning"></i> New Device Found');
                            $('#messageLabel').text(response.message).css('color', 'red');
                        }
                    }
                });
            }

            // Try to initialize FingerprintJS, fallback to default if not available
            function initializeFingerprinting() {
                try {
                    if (typeof FingerprintJS !== 'undefined') {
                        const fpPromise = FingerprintJS.load();
                        fpPromise.then(fp => fp.get()).then(result => {
                            visitorId = result.visitorId;
                            if (typeof ClientJS !== 'undefined') {
                                const client = new ClientJS();
                                clientId = client.getFingerprint();
                            }
                            validateDeviceId(visitorId, clientId, storedUsername);
                        }).catch(error => {
                            console.log("FingerprintJS error, using default IDs");
                            validateDeviceId(visitorId, clientId, storedUsername);
                        });
                    } else {
                        console.log("FingerprintJS not available, using default IDs");
                        validateDeviceId(visitorId, clientId, storedUsername);
                    }
                } catch (error) {
                    console.log("Error initializing fingerprinting: " + error);
                    validateDeviceId(visitorId, clientId, storedUsername);
                }
            }

            // Initialize fingerprinting
            initializeFingerprinting();

            // Username change handler - only for validation, not API call
            $('#username').on('change keyup blur', function() {
                var username = $('#username').val();
                
                if (username && username.length > 0) {
                    console.log("Username changed: " + username);

                    // Only call validation, not the MikroTik API
                    $.ajax({
                        url: '<?php echo site_url('Other/validate_hotspotuser'); ?>',
                        type: 'POST',
                        data: {
                            username: username,
                            device_id: visitorId, // Include the device ID for validation
                            clientid: clientId,
                            storedUsername: storedUsername
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log("Response Message : " + response.message);
                            // Handle response for username validation
                            if (response.status === 'success') {
                                if (response.mode === 3) {
                                    $('#signInBtn').prop('disabled', true);
                                    $('#messageLabel').text('Voucher not allowed from this device.').css('color', 'red');
                                } else {

                                    if (response.mode === 1 || response.mode === 2) {

                                        $('#signInBtn').prop('disabled', true);
                                        if (response.mode === 1) {
                                            $('#passwordGroup').show();
                                        }

                                        $('#messageLabel').text(response.message).css('color', 'green');

                                    } else {

                                        localStorage.setItem('storedUsername', username);

                                        let get_storedUsername = localStorage.getItem('storedUsername') || '';
                                        // Make AJAX call to update device ID
                                        $.ajax({
                                            url: '<?php echo site_url('Other/update_deviceid'); ?>',
                                            type: 'POST',
                                            data: {
                                                username: username,
                                                device_id: visitorId,
                                                client_id: clientId,
                                                device_storedUsername: get_storedUsername
                                            },
                                            dataType: 'json',
                                            success: function(updateResponse) {
                                                console.log("Message: " + updateResponse.message);
                                                if (updateResponse.mode === 1) {
                                                    $('#signInBtn').prop('disabled', false);
                                                    $('#messageLabel').text('Press sign In button').css('color', 'green');
                                                    //$('#hotspotlogin').submit();
                                                } else {
                                                    $('#signInBtn').prop('disabled', true);
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
                }
            });

            // Form submission handler - API call happens here
            $('#hotspotlogin').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission
                
                var username = $('#username').val();
                var name = $('#name').val();
                var email = $('#email').val();
                
                // Validate required fields
                if (!username || username.trim() === '') {
                    $('#messageLabel').text('Please enter your mobile number').css('color', 'red');
                    return false;
                }
                
                if (!name || name.trim() === '') {
                    $('#messageLabel').text('Please enter your name').css('color', 'red');
                    $('#name').focus();
                    return false;
                }
                
                if (!email || email.trim() === '') {
                    $('#messageLabel').text('Please enter your email address').css('color', 'red');
                    $('#email').focus();
                    return false;
                }
                
                // Validate email format
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    $('#messageLabel').text('Please enter a valid email address').css('color', 'red');
                    $('#email').focus();
                    return false;
                }
                
                // Combine name and email into comments
                var comments = 'Name: ' + name.trim() + ' | Email: ' + email.trim();
               
                // Set the comments hidden field
                $('#comments').val(comments);
                
                console.log("Form submitted, calling MikroTik API for username: " + username);
                console.log("Comments: " + comments);
                
                // Show loading state
                $('#signInBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
                
                // Call the API to add user to MikroTik hotspot
                $.ajax({
                    url: '<?php echo site_url('Other/add_hotspot_user_api'); ?>',
                    type: 'POST',
                    data: {
                        username: username,
                        comments: comments,
                        domain: '192.168.195.31' // You can change this to your domain
                    },
                    dataType: 'json',
                    success: function(apiResponse) {
                        console.log("MikroTik API Response: " + apiResponse.message);
                        
                        // Restore button state
                        $('#signInBtn').prop('disabled', false).html('<i class="fas fa-sign-in-alt"></i> Sign In');
                        
                        // Continue with normal form submission
                        var form = document.getElementById('hotspotlogin');
                        form.submit();
                    },
                    error: function(xhr, status, error) {
                        console.log("MikroTik API Error: " + error);
                        
                        // Restore button state
                        $('#signInBtn').prop('disabled', false).html('<i class="fas fa-sign-in-alt"></i> Sign In');
                        
                        // Continue with normal form submission even if API fails
                        var form = document.getElementById('hotspotlogin');
                        form.submit();
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
    </script>
</body>

</html>