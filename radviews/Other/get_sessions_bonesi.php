<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>IP Sessions Manager</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
</head>
<body class="hold-transition">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center">IP Sessions Manager</h2>
                
                <!-- Start New Session Form -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Start New Session</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <select id="serverSelect" class="form-control" required>
                                <option value="">Select Server</option>
                                <?php foreach ($server_list as $name => $info): ?>
                                <option value="<?php echo htmlspecialchars($name); ?>">
                                    <?php echo htmlspecialchars($name); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" id="newIp" class="form-control" 
                                   placeholder="Enter IP Address (e.g. 192.168.1.1)"
                                   pattern="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$"
                                   title="Please enter a valid IP address">
                        </div>
                        <div class="form-group">
                            <input type="number" id="duration" class="form-control" 
                                   placeholder="Enter Duration (in seconds)"
                                   min="1" max="3600"
                                   title="Duration must be between 1 and 3600 seconds">
                        </div>
                        <div class="form-group">
                            <input type="password" id="pin" class="form-control" 
                                   placeholder="Enter PIN"
                                   maxlength="6">
                        </div>
                        <button class="btn btn-primary" id="startBtn" onclick="startSession()" disabled>Start</button>
                    </div>
                </div>

                <!-- Active Sessions Table -->
                <?php foreach ($all_sessions as $server_name => $server_data): ?>
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Server: <?php echo htmlspecialchars($server_name); ?></h3>
                        </div>
                        <div class="box-body">
                            <div class="traffic-section">
                                <button class="btn btn-info check-traffic-btn" 
                                        onclick="checkTraffic('<?php echo htmlspecialchars($server_name); ?>', this)">
                                    Check Traffic
                                </button>
                                <div class="traffic-info alert alert-info" style="display: none; margin-top: 10px;">
                                    Loading...
                                </div>
                            </div>
                            <?php if (empty($server_data['sessions'])): ?>
                                <div class="alert alert-warning">No active sessions</div>
                            <?php else: ?>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Target IP:Port</th>
                                            <th>PID</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($server_data['sessions'] as $session): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($session['ip']); ?></td>
                                                <td><?php echo htmlspecialchars($session['pid']); ?></td>
                                                <td>
                                                    <button class="btn btn-danger btn-sm" 
                                                            onclick="killSession('<?php echo htmlspecialchars($session['pid']); ?>', '<?php echo htmlspecialchars($server_name); ?>')">
                                                        Kill
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    
    <script>
        // Add PIN validation
        $('#pin').on('input', function() {
            var pinValue = $(this).val();
            $('#startBtn').prop('disabled', pinValue !== '654321');
        });

        function killSession(pid, server) {
            var pin = $('#pin').val();
            if (!pin) {
                alert('Please enter PIN first');
                $('#pin').focus();
                return;
            }
            
            if (confirm('Are you sure you want to stop this session?')) {
                $.post('<?php echo base_url(); ?>ZeroTest/kill_session', {
                    pid: pid,
                    server: server,
                    pin: pin
                }, function(response) {
                    var result = JSON.parse(response);
                    if (result.status === 'success') {
                        location.reload();
                    } else {
                        alert('Error: ' + (result.message || 'Failed to stop session'));
                    }
                });
            }
        }

        function isValidIP(ip) {
            if (!ip || ip.trim() === '') {
                return false;
            }
            const ipRegex = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
            return ipRegex.test(ip.trim());
        }

        function startSession() {
            var ip = $('#newIp').val();
            var duration = $('#duration').val();
            var server = $('#serverSelect').val();
            var pin = $('#pin').val();
            
            // Server validation
            if (!server) {
                alert('Please select a server');
                $('#serverSelect').focus();
                return;
            }
            
            // IP validation
            if (!ip || ip.trim() === '') {
                alert('IP address cannot be empty');
                $('#newIp').focus();
                return;
            }
            
            if (!isValidIP(ip)) {
                alert('Please enter a valid IP address (e.g. 192.168.1.1)');
                $('#newIp').focus();
                return;
            }

            // Duration validation
            if (!duration || duration.trim() === '') {
                duration = 1;
                $('#duration').val(1);
            } else {
                duration = parseInt(duration);
                if (duration > 3600) {
                    alert('Duration must not exceed 3600 seconds');
                    $('#duration').focus();
                    return;
                }
                if (duration < 1) {
                    duration = 1;
                    $('#duration').val(1);
                }
            }

            // PIN validation (double-check)
            if (pin !== '654321') {
                alert('Invalid PIN');
                $('#pin').focus();
                return;
            }

            $.post('<?php echo base_url(); ?>ZeroTest/start_session', {
                ip: ip.trim(),
                duration: duration,
                server: server,
                pin: pin
            }, function(response) {
                var result = JSON.parse(response);
                if (result.status === 'success') {
                    location.reload();
                } else {
                    alert('Error: ' + (result.message || 'Failed to start session'));
                }
            }).fail(function() {
                alert('Error: Failed to communicate with server');
            });
        }

        function checkTraffic(server, button) {
            const $button = $(button);
            const $trafficInfo = $button.siblings('.traffic-info');
            
            // Disable button and show loading
            $button.prop('disabled', true).text('Checking...');
            $trafficInfo.show();
            
            $.post('<?php echo base_url(); ?>ZeroTest/get_traffic', {
                server: server
            }, function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        $trafficInfo.html(result.traffic);
                    } else {
                        $trafficInfo.html('Error: ' + (result.message || 'Failed to get traffic info'));
                    }
                } catch (e) {
                    $trafficInfo.html('Error: Failed to process server response');
                }
                
                // Re-enable button
                $button.prop('disabled', false).text('Check Traffic');
            }).fail(function() {
                $trafficInfo.html('Error: Failed to communicate with server');
                $button.prop('disabled', false).text('Check Traffic');
            });
        }
    </script>
</body>
</html> 