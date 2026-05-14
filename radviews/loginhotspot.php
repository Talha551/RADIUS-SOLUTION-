<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>RadSpot | Hotspot Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    
    <style>
        body {
            background-color: #f4f6f9; /* Light, modern background */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Source Sans Pro', sans-serif;
        }

        .login-box {
            width: 100%;
            max-width: 400px; /* Max width for better focus */
            margin: 1rem;
        }

        .card {
            background: #fff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px 30px;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .login-logo h3 {
            font-weight: 600;
            color: #343a40;
            margin-bottom: 5px;
        }

       .login-box-msg {
        color: #6c757d;
        text-align: center;
        margin-bottom: 20px;
        font-size: 1.1rem;
    }
    
    .logo {
        /* CHANGED: Increased the maximum width for a larger logo */
        max-width: 400px; 
        height: auto;
    }
        
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }
        
        .message-label {
            display: block;
            margin-top: 10px;
            font-weight: 600;
        }
        
        .message-welcome {
            font-weight: 400;
            color: #6c757d;
        }
    </style>

    <script src="<?php echo base_url(); ?>assets/js/fp.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/client.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
</head>

<body>
    <div class="login-box">
        <div class="login-logo">
            <a href="#">
                <!-- <h3>Rad-Spot Radius</h3> -->
<img class="logo" src="https://radspot.net/wp-content/uploads/2025/11/cropped-all-black.png" alt="RadSpot Radius Logo" />            </a>
        </div><div class="card">
            <div class="card-body">
                <p class="login-box-msg">Sign In to connect (<?php echo $identity; ?>)</p>

                <?php $this->load->helper('form'); ?>
                <div class="row">
                    <div class="col-12">
                        <?php echo validation_errors('<div class="alert alert-danger alert-dismissible fade show" role="alert">', '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'); ?>
                    </div>
                </div>

                <?php
                $error = $this->session->flashdata('error');
                if ($error) {
                ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php }
                $success = $this->session->flashdata('success');
                if ($success) {
                ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } ?>

                <form id="hotspotlogin" action="<?php echo $linkLoginOnly; ?>" method="post">
                    <input type="hidden" name="voucher" /> 

                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-ticket-alt"></i></span>
                            <input type="text" class="form-control" placeholder="Voucher Number" name="username" id="username" required aria-label="Voucher Number" />
                        </div>
                    </div>

                    <div class="mb-3" id="password-group" style="display: none;">
                        <div class="input-group">
                            <span class="input-group-text" id="locksign-icon"><i class="fas fa-lock"></i></span>
                            <input class="form-control" type="password" placeholder="Voucher PIN" name="password" id="password" aria-label="Voucher PIN" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <small id="messageLabel1" class="text-danger message-label"></small>
                        <p class="mb-0 mt-2">
                            <span class="badge bg-info">Status</span>
                            <span class="message-welcome ms-2"><label id="messageLabel">Welcome</label></span>
                        </p>
                    </div>

                    <input type="hidden" name="mac" value="<?php echo $mac; ?>">
                    <input type="hidden" name="ip" value="<?php echo $ip; ?>">
                    <input type="hidden" name="linkLogin" value="<?php echo $linkLogin; ?>">
                    <input type="hidden" name="linkLoginOnly" value="<?php echo $linkLoginOnly; ?>">
                    <input type="hidden" name="domain" value="">
                    <input type="hidden" name="dst" value="http://www.pace-tel.com">

                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <input type="submit" class="btn btn-primary btn-lg w-100" value="Sign In" />
                        </div>
                    </div>
                </form>
                
                <hr>

                <p class="text-muted text-center mt-3 mb-1">
                    <small>Device-ID Status: <span id="deviceLabel1" class="text-danger">Checking...</span></small>
                </p>
                
                <p class="text-center">
                    <a href="<?php echo base_url() ?>forgotPassword" class="text-secondary">Click here for complaints / help</a>
                </p>

            </div></div></div><script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

    <script>
        // Note: The original JavaScript logic is largely preserved to maintain functionality
        $(document).ready(function() {
            // Hide the password group container instead of just the input/icon for better layout control
            $('#password-group').hide();
            $('#messageLabel').css('color', 'green');

            // Initialize FingerprintJS
            const fpPromise = FingerprintJS.load();
            const client = new ClientJS(); // Initialize ClientJS

            let storedUsername = localStorage.getItem('storedUsername') || ''; // Retrieve stored username or empty string

            function updateMessage(selector, text, isError) {
                $(selector).text(text).css('color', isError ? 'red' : 'green');
            }

            // Function to validate device ID on page load
            function validateDeviceId(visitorId, clientId, storedUsername) {

                // Simplified console logging for clarity
                // console.log("FingerprintJS ID: " + visitorId);
                // console.log("ClientJS ID: " + clientId);
                // console.log("Stored Username: " + storedUsername);

                $.ajax({
                    url: '<?php echo site_url('Other/validate_deviceid'); ?>',
                    type: 'POST',
                    data: {
                        device_id: visitorId,
                        client_id: clientId,
                        stored_username: storedUsername
                    },
                    dataType: 'json',
                    success: function(response) {
                        // console.log("Username: " + response.username);
                        // console.log("Message: " + response.message);
                        
                        if (response.status === 'success') {
                            if (response.deviceid === visitorId + '-' + clientId || storedUsername == response.username) {
                                
                                // console.log("Going to Auto Login Check");
                                $('#deviceLabel1').text(response.message).removeClass('text-danger').addClass('text-success');
                                $(':input[type="submit"]').prop('disabled', false);

                                $('#username').val(response.username);
                                    
                                // Check if a hotspot error from the server exists
                                if('<?php echo $hotspoterror; ?>' === '')
                                {
                                    updateMessage('#messageLabel', 'Press Sign in to log in with device saved voucher', false);
                                    // Removed auto-submit. User action is generally better for UX.
                                } else {
                                    updateMessage('#messageLabel', '<?php echo $hotspoterror; ?>', true);
                                    $(':input[type="submit"]').prop('disabled', true);
                                }
                            }
                        } else {
                            $('#deviceLabel1').text('New Device Found').addClass('text-danger').removeClass('text-success');
                            updateMessage('#messageLabel', response.message, true);
                        }
                    },
                    error: function() {
                        $('#deviceLabel1').text('Connection Error').addClass('text-danger').removeClass('text-success');
                    }
                });
            }

            // Get the unique device ID and validate on page load
            fpPromise.then(fp => fp.get()).then(result => {
                const visitorId = result.visitorId;
                const clientId = client.getFingerprint();

                validateDeviceId(visitorId, clientId, storedUsername);

                $('#username').on('change blur', function() {
                    var username = $('#username').val();
                    $('#password-group').hide(); // Hide password field when username changes
                    $(':input[type="submit"]').prop('disabled', false); // Enable submit by default

                    // Make AJAX call to controller for username validation
                    $.ajax({
                        url: '<?php echo site_url('Other/validate_hotspotuser'); ?>',
                        type: 'POST',
                        data: {
                            username: username,
                            device_id: visitorId,
                            clientid: clientId,
                            storedUsername: storedUsername
                        },
                        dataType: 'json',
                        success: function(response) {
                            // console.log("Response Message : " + response.message);
                            
                            if (response.status === 'success') {
                                if (response.mode === 3) {
                                    $(':input[type="submit"]').prop('disabled', true);
                                    updateMessage('#messageLabel', 'Voucher not allowed from this device.', true);
                                } else {
                                    if (response.mode === 1 || response.mode === 2) {
                                        $(':input[type="submit"]').prop('disabled', true);
                                        if (response.mode === 1) {
                                            $('#password-group').show();
                                        }
                                        updateMessage('#messageLabel', response.message, false);
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
                                                // console.log("Message: " + updateResponse.message);
                                                if (updateResponse.mode === 1) {
                                                    $(':input[type="submit"]').prop('disabled', false);
                                                    updateMessage('#messageLabel', 'Press Sign In button', false);
                                                } else {
                                                    $(':input[type="submit"]').prop('disabled', true);
                                                    updateMessage('#messageLabel', updateResponse.message, true);
                                                }
                                            }
                                        });
                                    }
                                }
                            } else {
                                updateMessage('#messageLabel', response.message, true);
                                $(':input[type="submit"]').prop('disabled', true);
                            }
                        }
                    });
                });

                // Only perform password check if the field is visible (mode 1)
                $('#password').on('change blur', function() {
                    if ($('#password-group').is(':visible')) {
                        $.ajax({
                            url: '<?php echo site_url('Other/validate_hotspotpassword'); ?>',
                            type: 'POST',
                            data: {
                                username: $('#username').val(),
                                password: $('#password').val(),
                                device_id: visitorId
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {
                                    $(':input[type="submit"]').prop('disabled', false);
                                    updateMessage('#messageLabel', 'Password validated. Ready to sign in.', false);
                                } else {
                                    $(':input[type="submit"]').prop('disabled', true);
                                    updateMessage('#messageLabel', response.message, true);
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