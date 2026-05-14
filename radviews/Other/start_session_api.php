<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Start API Sessions</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
</head>
<body class="hold-transition">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center">Start API Sessions</h2>
                
                <!-- Start Sessions Form -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Start New API Sessions</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label>Target IP Address</label>
                            <input type="text" id="newIp" class="form-control" 
                                   placeholder="Enter IP Address (e.g. 192.168.1.1)"
                                   pattern="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$"
                                   title="Please enter a valid IP address">
                        </div>
                        <div class="form-group">
                            <label>Port</label>
                            <input type="number" id="port" class="form-control" 
                                   value="<?php echo $default_port; ?>"
                                   placeholder="Enter Port (e.g. 80)"
                                   min="1" max="65535"
                                   title="Port must be between 1 and 65535">
                        </div>
                        <div class="form-group">
                            <label>Duration (seconds)</label>
                            <input type="number" id="duration" class="form-control" 
                                   placeholder="Enter Duration (in seconds)"
                                   min="1" max="3600"
                                   title="Duration must be between 1 and 3600 seconds">
                        </div>
                        <div class="form-group">
                            <label>PIN</label>
                            <input type="password" id="pin" class="form-control" 
                                   placeholder="Enter PIN"
                                   maxlength="6">
                        </div>
                        <button class="btn btn-primary" id="startBtn" onclick="startSessions()" disabled>
                            Start API Sessions
                        </button>
                    </div>
                </div>

                <!-- Schedule Cron Job Section -->
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">Schedule Recurring API Sessions</h3>
                    </div>
                    <div class="box-body">
                        <form id="cronForm">
                            <div class="form-group">
                                <label>Target IP Address</label>
                                <input type="text" id="cronIp" class="form-control" 
                                       placeholder="Enter IP Address (e.g. 192.168.1.1)"
                                       pattern="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$"
                                       title="Please enter a valid IP address">
                            </div>
                            
                            <div class="form-group">
                                <label>Port</label>
                                <input type="number" id="cronPort" class="form-control" 
                                       placeholder="Enter Port (e.g. 80)"
                                       min="1" max="65535"
                                       title="Port must be between 1 and 65535">
                            </div>

                            <div class="form-group">
                                <label>Session Duration (seconds)</label>
                                <input type="number" id="cronDuration" class="form-control" 
                                       placeholder="Enter Duration (in seconds)"
                                       min="1" max="3600"
                                       title="Duration must be between 1 and 3600 seconds">
                            </div>

                            <div class="form-group">
                                <label>Schedule Type</label>
                                <select id="scheduleType" class="form-control">
                                    <option value="daily">Daily</option>
                                    <option value="hourly">Hourly</option>
                                    <option value="custom">Custom Interval</option>
                                </select>
                            </div>

                            <div id="customIntervalDiv" style="display: none;">
                                <div class="form-group">
                                    <label>Interval (minutes)</label>
                                    <input type="number" id="customInterval" class="form-control" 
                                           placeholder="Enter interval in minutes"
                                           min="1" max="1440">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Start Time</label>
                                <input type="time" id="startTime" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>PIN</label>
                                <input type="password" id="cronPin" class="form-control" 
                                       placeholder="Enter PIN"
                                       maxlength="6">
                            </div>

                            <button type="button" class="btn btn-warning" id="scheduleCronBtn" onclick="scheduleApiCron()" disabled>
                                Schedule API Sessions
                            </button>
                        </form>
                    </div>
                </div>

                <!-- API Cron Jobs List Section -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Scheduled API Tasks</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" onclick="refreshApiCronList()">
                                <i class="fa fa-refresh"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div id="cronList">
                            <div class="text-center">Loading scheduled tasks...</div>
                        </div>
                    </div>
                </div>

                <!-- Add this section for displaying running sessions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Running Sessions</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="runningSessions">
                                <thead>
                                    <tr>
                                        <th>IP</th>
                                        <th>Port</th>
                                        <th>API</th>
                                        <th>Method</th>
                                        <th>Duration</th>
                                        <th>Remaining Time</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Sessions will be loaded here -->
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
        // Add PIN validation
        $('#pin, #cronPin').on('input', function() {
            var pinValue = $(this).val();
            if (this.id === 'pin') {
                $('#startBtn').prop('disabled', pinValue !== '654321');
            } else {
                $('#scheduleCronBtn').prop('disabled', pinValue !== '654321');
            }
        });

        $('#scheduleType').change(function() {
            if ($(this).val() === 'custom') {
                $('#customIntervalDiv').show();
            } else {
                $('#customIntervalDiv').hide();
            }
        });

        function isValidIP(ip) {
            if (!ip || ip.trim() === '') return false;
            const ipRegex = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
            return ipRegex.test(ip.trim());
        }

        function startSessions() {
            var ip = $('#newIp').val();
            var port = $('#port').val();
            var duration = $('#duration').val();
            var pin = $('#pin').val();
            
            if (!isValidIP(ip)) {
                alert('Please enter a valid IP address');
                return;
            }
            
            if (!duration || duration < 1 || duration > 3600) {
                alert('Duration must be between 1 and 3600 seconds');
                return;
            }

            $.post('<?php echo base_url(); ?>ZeroTest/start_session_api_now', {
                ip: ip.trim(),
                port: port,
                duration: duration,
                pin: pin
            }, function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        alert('API sessions started successfully!');
                        updateRunningSessions();
                    } else {
                        alert('Error: ' + (result.message || 'Failed to start API sessions'));
                    }
                } catch (e) {
                    alert('Error: Failed to process server response');
                }
            }).fail(function() {
                alert('Error: Failed to communicate with server');
            });
        }

        function scheduleApiCron() {
            if (!$('#cronIp').val()) {
                alert('Please enter IP address');
                return;
            }
            
            const port = $('#cronPort').val() || '80';
            const duration = $('#cronDuration').val();
            const scheduleType = $('#scheduleType').val();
            const startTime = $('#startTime').val();
            const pin = $('#cronPin').val();
            const customInterval = $('#customInterval').val();
            
            if (!duration) {
                alert('Please enter duration');
                return;
            }
            
            if (!pin) {
                alert('Please enter PIN');
                return;
            }
            
            // Show loading state
            $('#scheduleCronBtn').prop('disabled', true).text('Scheduling...');
            
            $.ajax({
                url: '<?php echo base_url(); ?>ZeroTest/schedule_api_cron',
                type: 'POST',
                data: {
                    ip: $('#cronIp').val(),
                    port: port,
                    duration: duration,
                    schedule_type: scheduleType,
                    start_time: startTime,
                    custom_interval: customInterval,
                    pin: pin
                },
                dataType: 'json',
                success: function(response) {
                    // Reset button state
                    $('#scheduleCronBtn').prop('disabled', false).text('Schedule API Sessions');
                    
                    if (response && response.status === 'success') {
                        // Success handling
                        alert('API sessions scheduled successfully');
                        refreshApiCronList(); // Refresh the list
                        
                        // Clear form
                        $('#cronIp').val('');
                        $('#cronPort').val('');
                        $('#cronDuration').val('');
                        $('#startTime').val('');
                        $('#customInterval').val('');
                    } else {
                        // Error handling
                        const errorMsg = (response && response.message) ? response.message : 'Failed to schedule API sessions';
                        alert('Error: ' + errorMsg);
                    }
                },
                error: function(xhr, status, error) {
                    // Handle AJAX errors
                    $('#scheduleCronBtn').prop('disabled', false).text('Schedule API Sessions');
                    console.error('AJAX Error:', xhr.responseText);
                    alert('Error: Failed to communicate with server');
                }
            });
        }

        function refreshApiCronList() {
            $('#cronList').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
            
            $.get('<?php echo base_url(); ?>ZeroTest/get_api_cron_list')
                .done(function(response) {
                    try {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        
                        if (response.status === 'success') {
                            displayApiCronList(response.cron_jobs);
                        } else {
                            $('#cronList').html('<div class="alert alert-danger">' + 
                                (response.message || 'Failed to load scheduled tasks') + '</div>');
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        $('#cronList').html('<div class="alert alert-danger">Error processing server response</div>');
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                    $('#cronList').html('<div class="alert alert-danger">Failed to communicate with server</div>');
                });
        }

        function displayApiCronList(cronJobs) {
            if (!cronJobs || cronJobs.length === 0) {
                $('#cronList').html('<div class="alert alert-info">No scheduled API tasks found</div>');
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-bordered">';
            html += '<thead><tr><th>API Server</th><th>Target IP</th><th>Schedule</th><th>Duration</th><th>Next Run</th><th>Actions</th></tr></thead><tbody>';
            
            cronJobs.forEach(job => {
                html += `<tr>
                    <td>${job.api_name}</td>
                    <td>${job.ip}</td>
                    <td>${job.schedule}</td>
                    <td>${job.duration} seconds</td>
                    <td>${job.next_run}</td>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="deleteApiCronJob('${job.id}')">
                            Delete
                        </button>
                    </td>
                </tr>`;
            });
            
            html += '</tbody></table></div>';
            $('#cronList').html(html);
        }

        function deleteApiCronJob(jobId) {
            if (!confirm('Are you sure you want to delete this scheduled API task?')) {
                return;
            }

            const pin = $('#cronPin').val() || $('#pin').val();
            if (!pin) {
                alert('Please enter PIN first');
                return;
            }

            $.post('<?php echo base_url(); ?>ZeroTest/delete_api_cron', {
                job_id: jobId,
                pin: pin
            }, function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        refreshApiCronList();
                        updateRunningSessions();
                    } else {
                        alert('Error: ' + (result.message || 'Failed to delete scheduled task'));
                    }
                } catch (e) {
                    alert('Error: Failed to process server response');
                }
            }).fail(function() {
                alert('Error: Failed to communicate with server');
            });
        }

        // Load cron list on page load
        $(document).ready(function() {
            refreshApiCronList();
        });

        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            return `${minutes}m ${remainingSeconds}s`;
        }

        function updateRunningSessions() {
            $.ajax({
                url: '<?php echo base_url("ZeroTest/get_running_sessions"); ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        const tbody = $('#runningSessions tbody');
                        tbody.empty();

                        if (response.sessions.length === 0) {
                            tbody.append('<tr><td colspan="8" class="text-center">No active sessions</td></tr>');
                            return;
                        }

                        response.sessions.forEach(function(session) {
                            const row = `
                                <tr>
                                    <td>${session.ip}</td>
                                    <td>${session.port}</td>
                                    <td>${session.api_name}</td>
                                    <td>${session.method}</td>
                                    <td>${session.duration}s</td>
                                    <td>${formatTime(session.remaining_time)}</td>
                                    <td>${session.start_time}</td>
                                    <td>${session.end_time}</td>
                                </tr>
                            `;
                            tbody.append(row);
                        });
                    } else {
                        console.error('Failed to fetch running sessions:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching running sessions:', error);
                }
            });
        }

        // Update running sessions every 1 second
        $(document).ready(function() {
            updateRunningSessions(); // Initial load
            setInterval(updateRunningSessions, 1000); // Update every second
        });
    </script>

    <!-- Add this CSS -->
    <style>
    #runningSessions {
        margin-top: 20px;
    }
    #runningSessions th, #runningSessions td {
        text-align: center;
        vertical-align: middle;
    }
    .table-responsive {
        margin-top: 15px;
    }
    </style>
</body>
</html> 