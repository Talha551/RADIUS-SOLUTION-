<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>API Servers & Client Apps</title>
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
                    <li><a href="<?php echo base_url('ZeroTest/view_client_apps'); ?>">Client Apps</a></li>
                    <li class="active"><a href="<?php echo base_url('ZeroTest/view_client_apps_api'); ?>">API Client Apps</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">API Servers Configuration</h3>
                    </div>
                    <div class="box-body">
                        <?php if (!empty($servers)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>API Name</th>
                                            <th>API URL</th>
                                            <th>Type</th>
                                            <th>Method</th>
                                            <th>PPS</th>
                                            <th>Port</th>
                                            <th>Subnet Mode</th>
                                            <th>Concurrents</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($servers as $name => $server): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($name); ?></td>
                                                <td><?php echo "https://test.muttasilat.ae/"; ?></td>
                                                <td><?php echo htmlspecialchars($server['type']); ?></td>
                                                <td><?php echo htmlspecialchars($server['method']); ?></td>
                                                <td><?php echo htmlspecialchars($server['pps']); ?></td>
                                                <td><?php echo htmlspecialchars($server['port']); ?></td>
                                                <td><?php echo htmlspecialchars($server['subnet_mode']); ?></td>
                                                <td><?php echo htmlspecialchars($server['concurrents']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <p>No API server configurations found. Please add API server information to the configuration file.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Client Applications</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-success btn-sm" id="addClientBtn">
                                <i class="fa fa-plus"></i> Add New Client
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if (!empty($clients)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Device Name</th>
                                            <th>IP Address</th>
                                            <th>First Seen</th>
                                            <th>Last Updated</th>
                                            <th>Scheduled</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($clients as $device_name => $client): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($device_name); ?></td>
                                                <td><?php echo htmlspecialchars($client['ip']); ?></td>
                                                <td><?php echo htmlspecialchars($client['first_seen']); ?></td>
                                                <td><?php echo htmlspecialchars($client['last_updated']); ?></td>
                                                <td>
                                                    <?php if (!empty($client['loop_enabled']) && $client['loop_enabled']): ?>
                                                        <span class="label label-success">Yes</span>
                                                        <small class="text-muted">(<?php echo htmlspecialchars($client['interval']); ?> sec)</small>
                                                    <?php else: ?>
                                                        <span class="label label-default">No</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-xs btn-primary start-api-session" 
                                                            data-ip="<?php echo htmlspecialchars($client['ip']); ?>" 
                                                            data-device="<?php echo htmlspecialchars($device_name); ?>">
                                                        Start API Session
                                                    </button>
                                                    <button class="btn btn-xs btn-danger delete-client" 
                                                            data-device="<?php echo htmlspecialchars($device_name); ?>">
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <p>No client applications found.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- API Session Modal -->
    <div class="modal fade" id="apiSessionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Start API Session</h4>
                </div>
                <div class="modal-body">
                    <form id="apiSessionForm">
                        <input type="hidden" id="client_ip" name="ip">
                        <input type="hidden" id="device_name" name="device_name">
                        
                        <div class="form-group">
                            <label for="port">Port:</label>
                            <input type="text" class="form-control" id="port" name="port" value="80">
                        </div>
                        
                        <div class="form-group">
                            <label for="duration">Duration (seconds):</label>
                            <input type="number" class="form-control" id="duration" name="duration" value="60" min="10" max="300">
                        </div>
                        
                        <div class="form-group">
                            <label for="pin">PIN:</label>
                            <input type="password" class="form-control" id="pin" name="pin" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="startApiSessionBtn">Start Session</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Client Modal -->
    <div class="modal fade" id="addClientModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add New Client</h4>
                </div>
                <div class="modal-body">
                    <form id="addClientForm">
                        <div class="form-group">
                            <label for="new_device_name">Device Name:</label>
                            <input type="text" class="form-control" id="new_device_name" name="device_name" required placeholder="Enter device name">
                            <small class="text-muted">Use a unique name to identify this client</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_client_ip">IP Address:</label>
                            <input type="text" class="form-control" id="new_client_ip" name="client_ip" required placeholder="e.g., 192.168.1.1">
                            <small class="text-muted">Enter the client's public IP address</small>
                        </div>

                        <div class="form-group">
                            <label for="api_server">API Server:</label>
                            <select class="form-control" id="api_server" name="api_server" required>
                                <option value="">Select API Server</option>
                                <?php 
                                    // Read and parse the servers_api.json file
                                    $servers_json = file_get_contents(FCPATH . 'server_config/servers_api.json');
                                    
                                    $servers = json_decode($servers_json, true);

                                    if ($servers) {
                                        foreach ($servers as $name => $server) {
                                            echo '<option value="' . htmlspecialchars($name) . '">' . htmlspecialchars($name) . '</option>';
                                        }
                                    }
                                ?>
                            </select>
                            <small class="text-muted">Select the API server for this client</small>
                        </div>

                        <div class="form-group">
                            <label for="admin_pin">Admin PIN:</label>
                            <input type="password" class="form-control" id="admin_pin" name="admin_pin" required>
                            <small class="text-muted">Enter admin PIN to authorize this action</small>
                        </div>
                        
                        <hr>
                        <h4>Scheduling Options</h4>
                        
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="loop_enabled" name="loop_enabled"> Enable Automatic Scheduling
                                </label>
                                <small class="text-muted">This will create a cron job to periodically run this task</small>
                            </div>
                        </div>
                        
                        <div class="form-group" id="interval_group" style="display:none;">
                            <label for="interval">Interval (seconds):</label>
                            <input type="number" class="form-control" id="interval" name="interval" value="60" min="30">
                            <small class="text-muted">How often the task should run (minimum 30 seconds)</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="saveClientBtn">Save Client</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteClientModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Confirm Delete</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this client?</p>
                    <form id="deleteClientForm">
                        <input type="hidden" id="delete_device_name" name="device_name">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        // Open API session modal
        $('.start-api-session').click(function() {
            var ip = $(this).data('ip');
            var device = $(this).data('device');
            $('#client_ip').val(ip);
            $('#device_name').val(device);
            $('#apiSessionModal').modal('show');
        });
        
        // Start API session
        $('#startApiSessionBtn').click(function() {
            var formData = $('#apiSessionForm').serialize();
            
            $.ajax({
                url: '<?php echo base_url('ZeroTest/start_session_api_now'); ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    alert(response.status === 'success' ? 
                          'API session started successfully!' : 
                          'Error: ' + response.message);
                    $('#apiSessionModal').modal('hide');
                },
                error: function() {
                    alert('Error communicating with the server');
                }
            });
        });
        
        // Open add client modal
        $('#addClientBtn').click(function() {
            $('#addClientModal').modal('show');
        });
        
        // Show/hide interval field based on loop checkbox
        $('#loop_enabled').change(function() {
            if($(this).is(':checked')) {
                $('#interval_group').slideDown();
            } else {
                $('#interval_group').slideUp();
            }
        });
        
        // Save new client with loop and interval options
        $('#saveClientBtn').click(function() {
            var deviceName = $('#new_device_name').val();
            var clientIP = $('#new_client_ip').val();
            var adminPin = $('#admin_pin').val();
            var loopEnabled = $('#loop_enabled').is(':checked');
            var interval = $('#interval').val();
            var api_server = $('#api_server').val();
            
            // Basic validation
            if (!deviceName || !clientIP || !adminPin) {
                alert('Device name, IP address, and PIN are required');
                return;
            }
            
            // IP address or domain validation
            var ipPattern = /^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/;
            var domainPattern = /^([a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9]\.)+[a-zA-Z]{2,}$/;

            if (!ipPattern.test(clientIP) && !domainPattern.test(clientIP)) {
                alert('Please enter a valid IP address or domain name');
                return;
            }
            
            $.ajax({
                url: '<?php echo base_url('ZeroTest/add_client_app_api'); ?>',
                type: 'POST',
                data: { 
                    device_name: deviceName,
                    client_ip: clientIP,
                    admin_pin: adminPin,
                    loop_enabled: loopEnabled,
                    interval: interval,
                    api_server: api_server
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Client added successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                    $('#addClientModal').modal('hide');
                },
                error: function() {
                    alert('Error communicating with the server');
                }
            });
        });
        
        // Open delete confirmation modal
        $('.delete-client').click(function() {
            var device = $(this).data('device');
            $('#delete_device_name').val(device);
            $('#deleteClientModal').modal('show');
        });
        
        // Confirm delete client
        $('#confirmDeleteBtn').click(function() {
            var deviceName = $('#delete_device_name').val();
            
            $.ajax({
                url: '<?php echo base_url('ZeroTest/delete_client_app_api'); ?>',
                type: 'POST',
                data: { device_name: deviceName },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Client deleted successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                    $('#deleteClientModal').modal('hide');
                },
                error: function() {
                    alert('Error communicating with the server');
                }
            });
        });
    });
    </script>
</body>
</html> 