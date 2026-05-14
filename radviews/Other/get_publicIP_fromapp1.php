<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Client App IPs</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
</head>
<body class="hold-transition">
    <!-- Add Navigation Menu -->
    <nav class="navbar navbar-default">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-menu">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">API Management</a>
            </div>

            <div class="collapse navbar-collapse" id="navbar-menu">
                <ul class="nav navbar-nav">
                    <li><a href="<?php echo base_url('ZeroTest/server_information'); ?>">Server Information</a></li>
                    <li><a href="<?php echo base_url('zerotest'); ?>">Zero Test</a></li>
                    <li><a href="<?php echo base_url('ZeroTest/start_session_all'); ?>">Start All Sessions</a></li>
                    <li><a href="<?php echo base_url('ZeroTest/start_session_api'); ?>">Start API Session</a></li>
                    <li class="active"><a href="<?php echo base_url('ZeroTest/view_client_apps'); ?>">Client Apps</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Client App IPs</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" onclick="refreshData()">
                                <i class="fa fa-refresh"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Device Name</th>
                                        <th>IP Address</th>
                                        <th>First Seen</th>
                                        <th>Last Updated</th>
                                        <th>
                                            UDP Control
                                            <div class="pin-input">
                                                <input type="password" id="udpPin" class="form-control input-sm" 
                                                       placeholder="Enter PIN" maxlength="6" 
                                                       style="width: 100px; display: inline-block; margin-left: 10px;">
                                            </div>
                                        </th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($clients)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No client data available</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($clients as $device_name => $info): ?>
                                            <tr id="row_<?php echo htmlspecialchars($device_name); ?>">
                                                <td><?php echo htmlspecialchars($device_name); ?></td>
                                                <td><?php echo htmlspecialchars($info['ip']); ?></td>
                                                <td><?php echo htmlspecialchars($info['first_seen']); ?></td>
                                                <td><?php echo htmlspecialchars($info['last_updated']); ?></td>
                                                <td>
                                                    <div class="udp-controls">
                                                        <select class="form-control server-select" style="width: auto; display: inline-block; margin-right: 5px;">
                                                            <option value="">Select Server</option>
                                                            <?php if (!empty($servers)): ?>
                                                                <?php foreach ($servers as $server_name => $server_info): ?>
                                                                    <option value="<?php echo htmlspecialchars($server_name); ?>">
                                                                        <?php echo htmlspecialchars($server_name); ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </select>
                                                        <button class="btn btn-success btn-sm start-udp" 
                                                                onclick="startUDPForClient('<?php echo htmlspecialchars($device_name); ?>', '<?php echo htmlspecialchars($info['ip']); ?>')">
                                                            Start UDP
                                                        </button>
                                                        <button class="btn btn-danger btn-sm stop-udp" 
                                                                onclick="stopUDPForClient('<?php echo htmlspecialchars($device_name); ?>')">
                                                            Stop UDP
                                                        </button>
                                                        <span class="status-indicator"></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <button class="btn btn-danger btn-sm" 
                                                            onclick="deleteClient('<?php echo htmlspecialchars($device_name); ?>')"
                                                            title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    
    <script>
        function refreshData() {
            location.reload();
        }

        // Auto refresh every 60 seconds
        setInterval(refreshData, 60000);

        function deleteClient(deviceName) {
            if (confirm('Are you sure you want to delete this device?')) {
                $.ajax({
                    url: '<?php echo base_url("ZeroTest/delete_client_app"); ?>',
                    type: 'POST',
                    data: {
                        device_name: deviceName
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert('Device deleted successfully');
                            location.reload();
                        } else {
                            alert('Error: ' + (response.message || 'Failed to delete device'));
                        }
                    },
                    error: function() {
                        alert('Error: Failed to communicate with server');
                    }
                });
            }
        }

        // Move these functions outside of deleteClient
        function startUDPForClient(deviceName, ip) {
            console.log('Starting UDP for:', {deviceName, ip}); // Debug log
            
            const row = $(`#row_${deviceName}`);
            const server = row.find('.server-select').val();
            const pin = $('#udpPin').val();

            console.log('Selected server and PIN:', {server, hasPin: !!pin}); // Debug log

            if (!server) {
                alert('Please select a server');
                return;
            }

            if (!pin) {
                alert('Please enter PIN');
                return;
            }

            // Show loading state
            row.find('.start-udp').prop('disabled', true).text('Starting...');
            
            const data = {
                server: server,
                ip: ip,
                pin: pin,
                device_name: deviceName
            };
            
            console.log('Sending data:', data); // Debug log
            
            $.ajax({
                url: '<?php echo base_url("ZeroTest/start_udp_session"); ?>',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    console.log("UDP Session tried to start");
                    console.log('Server response (Results):', response); // Debug log
                    
                    if (response.status === 'success') {
                        // Immediately update UI
                        row.find('.status-indicator').html('<span class="label label-success">Running</span>');
                        row.find('.start-udp').hide();
                        row.find('.stop-udp').show();
                        row.find('.server-select').prop('disabled', true);
                        
                        // Then check all sessions
                        updateSessionStatuses();
                    } else {
                        alert('Error: ' + (response.message || 'Unknown error occurred'));
                        row.find('.start-udp').prop('disabled', false).text('Start UDP');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {xhr, status, error});
                    console.error('Response Text:', xhr.responseText);
                    alert('Error: Failed to communicate with server. Please check console for details.');
                    row.find('.start-udp').prop('disabled', false).text('Start UDP');
                }
            });
        }

        function stopUDPForClient(deviceName) {
            const row = $(`#row_${deviceName}`);
            const server = row.find('.server-select').val();
            const pin = $('#udpPin').val();

            if (!server) {
                alert('Please select a server');
                return;
            }

            if (!pin) {
                alert('Please enter PIN');
                return;
            }

            $.post('<?php echo base_url("ZeroTest/kill_session"); ?>', {
                server: server,
                pin: pin,
                device_name: deviceName
            }, function(response) {
                if (response.status === 'success') {
                    updateSessionStatuses();
                    alert('UDP session stopped successfully');
                } else {
                    alert('Error: ' + response.message);
                }
            });
        }

        function checkIPChanges() {
            $.get('<?php echo base_url("ZeroTest/check_ip_changes"); ?>', function(response) {
                if (response.status === 'success' && response.changes.length > 0) {
                    response.changes.forEach(function(change) {
                        const row = $(`#row_${change.device}`);
                        if (row.find('.status-indicator').text().includes('Running')) {
                            stopUDPForClient(change.device);
                            setTimeout(() => {
                                startUDPForClient(change.device, change.new_ip);
                            }, 2000);
                        }
                    });
                }
            });
        }

        // Keep your existing refresh and interval code
        setInterval(refreshData, 60000);
        setInterval(checkIPChanges, 60000);

        // Add this function to update session statuses
        function updateSessionStatuses() {
            $.get('<?php echo base_url("ZeroTest/get_running_udp_sessions"); ?>', function(response) {
                if (response.status === 'success') {
                    // Reset all status indicators first
                    $('.status-indicator').empty();
                    $('.start-udp').show();
                    $('.stop-udp').hide();
                    $('.server-select').prop('disabled', false);

                    // Update UI for running sessions
                    Object.entries(response.sessions || {}).forEach(([device, session]) => {
                        const row = $(`#row_${device}`);
                        if (row.length && session.is_running) {
                            row.find('.status-indicator').html('<span class="label label-success">Running</span>');
                            row.find('.start-udp').hide();
                            row.find('.stop-udp').show();
                            row.find('.server-select').val(session.server).prop('disabled', true);
                        }
                    });
                }
            });
        }

        // Update the document ready handler
        $(document).ready(function() {
            // Initialize any necessary components
            $('.server-select').on('change', function() {
                const row = $(this).closest('tr');
                row.find('.start-udp').prop('disabled', !$(this).val());
            });

            // Hide stop buttons initially
            $('.stop-udp').hide();

            // Initial status check
            updateSessionStatuses();

            // Check session status every 10 seconds
            setInterval(updateSessionStatuses, 10000);
        });
    </script>

    <!-- Add this CSS -->
    <style>
    .navbar {
        margin-bottom: 20px;
        border-radius: 0;
    }
    .navbar-brand {
        font-weight: bold;
    }
    .nav > li > a {
        padding: 15px 20px;
    }
    .box {
        border-top: 3px solid #3c8dbc;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    }
    .table-responsive {
        border: none;
    }
    .box-header {
        color: #444;
        display: block;
        padding: 10px;
        position: relative;
    }
    .btn-box-tool {
        padding: 5px;
        font-size: 12px;
        background: transparent;
        color: #97a0b3;
    }
    .btn-box-tool:hover {
        color: #606c84;
    }
    .btn-danger {
        background-color: #dd4b39;
        border-color: #d73925;
        color: #fff;
    }
    .btn-danger:hover {
        background-color: #d73925;
        border-color: #d73925;
    }
    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
        line-height: 1.5;
        border-radius: 3px;
    }
    .udp-controls {
        white-space: nowrap;
    }
    .status-indicator {
        margin-left: 5px;
    }
    .label-success {
        background-color: #00a65a;
        padding: 2px 5px;
        border-radius: 3px;
        color: white;
    }
    .pin-input {
        display: inline-block;
        vertical-align: middle;
    }
    .pin-input input {
        height: 25px;
        padding: 2px 8px;
    }
    th {
        vertical-align: middle !important;
    }
    </style>
</body>
</html> 