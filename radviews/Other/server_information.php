<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Server Information Management</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
</head>
<body class="hold-transition">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center">Server Information Management</h2>
                
                <?php if($this->session->flashdata('success')): ?>
                    <div class="alert alert-success">
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                <?php endif; ?>
                
                <?php if($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Add/Edit Server Form -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add/Edit Server</h3>
                    </div>
                    <div class="box-body">
                        <form action="<?php echo base_url('ZeroTest/server_information'); ?>" method="post">
                            <div class="form-group">
                                <label>Server Name</label>
                                <input type="text" name="server_name" class="form-control" 
                                       placeholder="e.g., Server-1" required>
                            </div>
                            <div class="form-group">
                                <label>Server IP</label>
                                <input type="text" name="server_ip" class="form-control" 
                                       placeholder="e.g., 192.168.1.1" required
                                       pattern="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$">
                            </div>
                            <div class="form-group">
                                <label>SSH Username</label>
                                <input type="text" name="ssh_user" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>SSH Password</label>
                                <input type="password" name="ssh_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Server PIN</label>
                                <input type="password" name="server_pin" class="form-control" 
                                       placeholder="Enter 6-digit PIN" required
                                       pattern="\d{6}" maxlength="6">
                            </div>
                            <button type="submit" class="btn btn-primary">Save Server</button>
                        </form>
                    </div>
                </div>

                <!-- Existing Servers Table -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Configured Servers</h3>
                    </div>
                    <div class="box-body">
                        <?php if (empty($servers)): ?>
                            <div class="alert alert-info">
                                No servers configured yet. Use the form above to add a server.
                            </div>
                        <?php else: ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Server Name</th>
                                        <th>IP Address</th>
                                        <th>SSH Username</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($servers as $name => $info): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($name); ?></td>
                                        <td><?php echo htmlspecialchars($info['ip']); ?></td>
                                        <td><?php echo htmlspecialchars($info['user']); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" 
                                                    onclick="editServer('<?php echo htmlspecialchars($name); ?>', '<?php echo htmlspecialchars($info['ip']); ?>', '<?php echo htmlspecialchars($info['user']); ?>')">
                                                Edit
                                            </button>
                                            <button class="btn btn-sm btn-success install-btn" 
                                                    onclick="verifyAndInstall('<?php echo htmlspecialchars($name); ?>')"
                                                    data-server="<?php echo htmlspecialchars($name); ?>">
                                                Install
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Installation Progress Modal -->
    <div class="modal fade" id="installModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Installing Services</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="installProgress">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped active" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                    <div id="installLog" style="margin-top: 20px; max-height: 300px; overflow-y: auto;">
                        <pre style="background-color: #f5f5f5; padding: 10px;"></pre>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Modal -->
    <div class="modal fade" id="verifyModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Verify Credentials</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>SSH Password</label>
                        <input type="password" id="verify_ssh_password" class="form-control" 
                               placeholder="Enter SSH Password">
                    </div>
                    <div class="form-group">
                        <label>Server PIN</label>
                        <input type="password" id="verify_pin" class="form-control" 
                               placeholder="Enter 6-digit PIN"
                               maxlength="6">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="startInstallation()">Verify & Install</button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    
    <script>
        let selectedServer = '';

        // Enable install buttons when document is ready
        $(document).ready(function() {
            // Enable all install buttons by default
            $('.install-btn').prop('disabled', false);

            $('form').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.status === 'success') {
                                // Enable the Install button for this server
                                $('.install-btn[data-server="' + result.server + '"]').prop('disabled', false);
                                location.reload(); // Reload to show updated server list
                            } else {
                                alert('Error: ' + result.message);
                            }
                        } catch (e) {
                            alert('Error processing response');
                        }
                    },
                    error: function() {
                        alert('Failed to save server information');
                    }
                });
            });
        });

        function editServer(name, ip, user) {
            $('input[name="server_name"]').val(name);
            $('input[name="server_ip"]').val(ip);
            $('input[name="ssh_user"]').val(user);
            // Password is not pre-filled for security reasons
            $('input[name="ssh_password"]').focus();
        }

        function verifyAndInstall(serverName) {
            selectedServer = serverName;
            $('#verify_ssh_password').val('');
            $('#verify_pin').val('');
            $('#verifyModal').modal('show');
        }

        function startInstallation() {
            const sshPassword = $('#verify_ssh_password').val();
            const pin = $('#verify_pin').val();

            if (!sshPassword || !pin) {
                alert('Both SSH Password and PIN are required');
                return;
            }

            // Verify credentials
            $.post('<?php echo base_url(); ?>ZeroTest/verify_credentials', {
                server: selectedServer,
                password: sshPassword,
                pin: pin
            }, function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        $('#verifyModal').modal('hide');
                        installServices(selectedServer, sshPassword);
                    } else {
                        alert('Invalid credentials: ' + result.message);
                    }
                } catch (e) {
                    alert('Error verifying credentials');
                }
            });
        }

        function installServices(serverName, sshPassword) {
            $('#installModal').modal('show');
            $('#installLog pre').text('Starting installation...\n');
            updateProgress(0);

            const steps = [
                { name: 'Installing dependencies', command: 'dependencies' },
                { name: 'Cloning bonesi repository', command: 'clone' },
                { name: 'Configuring bonesi', command: 'configure' },
                { name: 'Building bonesi', command: 'make' },
                { name: 'Installing bonesi', command: 'install' },
                { name: 'Setting up target script', command: 'setup_script' },
                { name: 'Setting up BoNeSi control script', command: 'setup_bonesi_script' }
            ];

            let currentStep = 0;
            processNextStep();

            function processNextStep() {
                if (currentStep >= steps.length) {
                    updateProgress(100);
                    appendLog('Installation completed successfully!');
                    return;
                }

                const step = steps[currentStep];
                const progress = (currentStep / steps.length) * 100;
                
                updateProgress(progress);
                appendLog(`\nStarting: ${step.name}...`);

                $.post('<?php echo base_url(); ?>ZeroTest/install_services', {
                    server: serverName,
                    step: step.command,
                    password: sshPassword
                }, function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.status === 'success') {
                            appendLog(result.output);
                            if (result.skip_to_script) {
                                // Skip to setup_script step
                                currentStep = steps.findIndex(s => s.command === 'setup_script');
                                if (currentStep === -1) currentStep = steps.length;
                            } else if (result.skip_remaining) {
                                // Skip all remaining steps
                                currentStep = steps.length;
                            } else {
                                currentStep++;
                            }
                            processNextStep();
                        } else {
                            appendLog(`\nError: ${result.message}`);
                        }
                    } catch (e) {
                        appendLog('\nError: Failed to process server response');
                    }
                }).fail(function() {
                    appendLog('\nError: Failed to communicate with server');
                });
            }
        }

        function updateProgress(percentage) {
            $('.progress-bar').css('width', percentage + '%');
        }

        function appendLog(text) {
            const logElement = $('#installLog pre');
            logElement.text(logElement.text() + text + '\n');
            logElement.scrollTop(logElement[0].scrollHeight);
        }
    </script>
</body>
</html> 