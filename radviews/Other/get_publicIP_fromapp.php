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
                                            <tr data-device="<?php echo htmlspecialchars($device_name); ?>">
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
                                                        <button class="btn btn-success btn-sm start-udp-btn">
                                                            Start UDP
                                                        </button>
                                                        <button class="btn btn-danger btn-sm stop-udp-btn" style="display: none;">
                                                            Stop UDP
                                                        </button>
                                                        <span class="udp-status" style="display: none;"></span>
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

        function checkAndUpdateIPChanges() {
            $.ajax({
                url: '<?php echo base_url(); ?>ZeroTest/get_running_udp_sessions',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.sessions) {
                        Object.keys(response.sessions).forEach(function(deviceName) {
                            const session = response.sessions[deviceName];
                            const row = $(`tr[data-device="${deviceName}"]`);
                            const currentIP = row.find('td:eq(1)').text().trim();
                            
                            if (session.is_running && session.ip !== currentIP) {
                                console.log(`IP changed for ${deviceName}: ${session.ip} -> ${currentIP}`);
                                
                                // Use the new restart endpoint
                                $.ajax({
                                    url: '<?php echo base_url(); ?>ZeroTest/restart_udp_session',
                                    type: 'POST',
                                    data: {
                                        server: session.server,
                                        device_name: deviceName,
                                        new_ip: currentIP
                                    },
                                    success: function(response) {
                                        console.log('Restart response:', response);
                                        if (response.status === 'success') {
                                            console.log(`Session restarted for ${deviceName} with new IP: ${currentIP}`);
                                            setTimeout(checkUDPSessions, 2000); // Check status after restart
                                        } else {
                                            console.error('Failed to restart session:', response.message);
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Restart session error:', error);
                                    }
                                });
                            }
                        });
                    }
                }
            });
        }

        function checkUDPSessions() {
            $.ajax({
                url: '<?php echo base_url(); ?>ZeroTest/get_running_udp_sessions',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.sessions) {
                        Object.keys(response.sessions).forEach(function(deviceName) {
                            const session = response.sessions[deviceName];
                            const row = $(`tr[data-device="${deviceName}"]`);
                            
                            if (session.is_running) {
                                // Store the server and IP info in data attributes
                                row.attr('data-server', session.server);
                                row.attr('data-session-ip', session.ip);
                                
                                row.find('.start-udp-btn').hide();
                                row.find('.stop-udp-btn').show();
                                row.find('.udp-status')
                                   .html('<span class="label label-success">Running</span>')
                                   .show();
                                row.find('.server-select')
                                   .val(session.server)
                                   .prop('disabled', true);
                            } else {
                                row.removeAttr('data-server data-session-ip');
                                row.find('.start-udp-btn').show();
                                row.find('.stop-udp-btn').hide();
                                row.find('.udp-status').hide();
                                row.find('.server-select').prop('disabled', false);
                            }
                        });
                    }
                }
            });
        }

        // Add stop UDP function
        function stopUDPForClient(deviceName) {
            const row = $(`tr[data-device="${deviceName}"]`);
            const server = row.find('.server-select').val();
            const pin = $('#udpPin').val();

            if (!pin) {
                alert('Please enter PIN');
                return;
            }

            // Show loading state
            const stopBtn = row.find('.stop-udp-btn');
            stopBtn.prop('disabled', true).text('Stopping...');

            $.ajax({
                url: '<?php echo base_url(); ?>ZeroTest/kill_session',
                type: 'POST',
                data: {
                    server: server,
                    pin: pin,
                    device_name: deviceName
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        checkUDPSessions(); // Check status immediately after stopping
                    } else {
                        alert('Error: ' + (response.message || 'Failed to stop UDP session'));
                    }
                    stopBtn.prop('disabled', false).text('Stop UDP');
                },
                error: function() {
                    alert('Error: Failed to communicate with server');
                    stopBtn.prop('disabled', false).text('Stop UDP');
                }
            });
        }

        // Update the onclick handlers in the HTML
        $(document).ready(function() {
            // Initial status check
            checkUDPSessions();
            
            // Add click handlers for start/stop buttons
            $('.table').on('click', '.start-udp-btn', function() {
                const row = $(this).closest('tr');
                const deviceName = row.data('device');
                const ip = row.find('td:eq(1)').text(); // Get IP from second column
                startUDPForClient(deviceName, ip);
            });

            $('.table').on('click', '.stop-udp-btn', function() {
                const deviceName = $(this).closest('tr').data('device');
                stopUDPForClient(deviceName);
            });

            // Check status every 5 seconds
            setInterval(checkUDPSessions, 5000);

            // Check for IP changes every 30 seconds
            setInterval(checkAndUpdateIPChanges, 30000);
            
            // Also check on page load
            checkAndUpdateIPChanges();
        });

        function startUDPForClient(deviceName, ip) {
            const row = $(`tr[data-device="${deviceName}"]`);
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

            // Show loading state
            const startBtn = row.find('.start-udp-btn');
            startBtn.prop('disabled', true).text('Starting...');

            $.ajax({
                url: '<?php echo base_url(); ?>ZeroTest/start_udp_session',
                type: 'POST',
                data: {
                    server: server,
                    ip: ip,
                    pin: pin,
                    device_name: deviceName
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        checkUDPSessions(); // Check status immediately after starting
                    } else {
                        alert('Error: ' + (response.message || 'Failed to start UDP session'));
                        startBtn.prop('disabled', false).text('Start UDP');
                    }
                },
                error: function() {
                    alert('Error: Failed to communicate with server');
                    startBtn.prop('disabled', false).text('Start UDP');
                }
            });
        }
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
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .udp-status .label {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 3px;
        font-size: 12px;
    }
    .label-success {
        background-color: #00a65a;
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
    .stop-udp-btn {
        display: none; /* Hidden by default */
    }
    .udp-controls button {
        min-width: 80px; /* Keep buttons same width */
    }
    </style>
</body>
</html> 