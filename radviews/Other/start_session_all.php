<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Start Sessions on All Servers</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
</head>
<body class="hold-transition">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center">Start Sessions on All Servers</h2>
                
                <!-- Start Sessions Form -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Start New Sessions</h3>
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
                            Start Sessions on All Servers
                        </button>
                    </div>
                </div>

                <!-- Results Section -->
                <div id="results" class="box box-info" style="display: none;">
                    <div class="box-header with-border">
                        <h3 class="box-title">Results</h3>
                    </div>
                    <div class="box-body">
                        <div id="resultsContent"></div>
                    </div>
                </div>

                <!-- Schedule Cron Job Section -->
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">Schedule Recurring Sessions</h3>
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

                            <button type="button" class="btn btn-warning" id="scheduleCronBtn" onclick="scheduleCron()" disabled>
                                Schedule Sessions
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Cron Jobs List Section -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Scheduled Tasks</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" onclick="refreshCronList()">
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

        function isValidIP(ip) {
            if (!ip || ip.trim() === '') {
                return false;
            }
            const ipRegex = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
            return ipRegex.test(ip.trim());
        }

        function startSessions() {
            var ip = $('#newIp').val();
            var duration = $('#duration').val();
            var pin = $('#pin').val();
            
            // Validation
            if (!ip || !isValidIP(ip)) {
                alert('Please enter a valid IP address');
                $('#newIp').focus();
                return;
            }
            
            if (!duration || duration < 1 || duration > 3600) {
                alert('Duration must be between 1 and 3600 seconds');
                $('#duration').focus();
                return;
            }

            if (pin !== '654321') {
                alert('Invalid PIN');
                $('#pin').focus();
                return;
            }

            // Disable button and show loading state
            $('#startBtn').prop('disabled', true).text('Starting Sessions...');

            $.post('<?php echo base_url(); ?>ZeroTest/start_session_all', {
                ip: ip.trim(),
                duration: duration,
                pin: pin
            }, function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        displayResults(result.results);
                    } else {
                        alert('Error: ' + (result.message || 'Failed to start sessions'));
                    }
                } catch (e) {
                    alert('Error: Failed to process server response');
                }
                $('#startBtn').prop('disabled', false).text('Start Sessions on All Servers');
            }).fail(function() {
                alert('Error: Failed to communicate with server');
                $('#startBtn').prop('disabled', false).text('Start Sessions on All Servers');
            });
        }

        function displayResults(results) {
            let html = '<div class="table-responsive"><table class="table table-bordered">';
            html += '<thead><tr><th>Server</th><th>Status</th><th>Message</th></tr></thead><tbody>';
            
            for (const server in results) {
                const result = results[server];
                const statusClass = result.status === 'success' ? 'success' : 'danger';
                html += `<tr class="${statusClass}">`;
                html += `<td>${server}</td>`;
                html += `<td>${result.status}</td>`;
                html += `<td>${result.message}</td>`;
                html += '</tr>';
            }
            
            html += '</tbody></table></div>';
            
            $('#resultsContent').html(html);
            $('#results').show();
        }

        // Add to existing script section
        $('#scheduleType').change(function() {
            if ($(this).val() === 'custom') {
                $('#customIntervalDiv').show();
            } else {
                $('#customIntervalDiv').hide();
            }
        });

        // Add PIN validation for cron form
        $('#cronPin').on('input', function() {
            var pinValue = $(this).val();
            $('#scheduleCronBtn').prop('disabled', pinValue !== '654321');
        });

        function scheduleCron() {
            var ip = $('#cronIp').val();
            var duration = $('#cronDuration').val();
            var scheduleType = $('#scheduleType').val();
            var startTime = $('#startTime').val();
            var pin = $('#cronPin').val();
            var customInterval = $('#customInterval').val();
            
            // Validation
            if (!ip || !isValidIP(ip)) {
                alert('Please enter a valid IP address');
                $('#cronIp').focus();
                return;
            }
            
            if (!duration || duration < 1 || duration > 3600) {
                alert('Duration must be between 1 and 3600 seconds');
                $('#cronDuration').focus();
                return;
            }

            if (scheduleType === 'custom' && (!customInterval || customInterval < 1 || customInterval > 1440)) {
                alert('Custom interval must be between 1 and 1440 minutes');
                $('#customInterval').focus();
                return;
            }

            if (!startTime) {
                alert('Please select a start time');
                $('#startTime').focus();
                return;
            }

            // Disable button and show loading state
            $('#scheduleCronBtn').prop('disabled', true).text('Scheduling...');

            $.post('<?php echo base_url(); ?>ZeroTest/schedule_cron', {
                ip: ip.trim(),
                duration: duration,
                schedule_type: scheduleType,
                start_time: startTime,
                custom_interval: customInterval,
                pin: pin
            }, function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        alert('Cron jobs scheduled successfully!');
                        refreshCronList();
                    } else {
                        alert('Error: ' + (result.message || 'Failed to schedule cron jobs'));
                    }
                } catch (e) {
                    alert('Error: Failed to process server response');
                }
                $('#scheduleCronBtn').prop('disabled', false).text('Schedule Sessions');
            }).fail(function() {
                alert('Error: Failed to communicate with server');
                $('#scheduleCronBtn').prop('disabled', false).text('Schedule Sessions');
            });
        }

        function refreshCronList() {
            $('#cronList').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
            
            $.get('<?php echo base_url(); ?>ZeroTest/get_cron_list')
                .done(function(response) {
                    try {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        
                        if (response.status === 'success') {
                            displayCronList(response.cron_jobs);
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
                    $('#cronList').html('<div class="alert alert-danger">Failed to communicate with server: ' + 
                        textStatus + '</div>');
                });
        }

        function displayCronList(cronJobs) {
            if (!cronJobs || cronJobs.length === 0) {
                $('#cronList').html('<div class="alert alert-info">No scheduled tasks found</div>');
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-bordered">';
            html += '<thead><tr><th>Server</th><th>Target IP</th><th>Schedule</th><th>Duration</th><th>Next Run</th><th>Actions</th></tr></thead><tbody>';
            
            cronJobs.forEach(job => {
                html += `<tr>
                    <td>${job.server || 'Unknown'}</td>
                    <td>${job.ip}</td>
                    <td>${job.schedule}</td>
                    <td>${job.duration} seconds</td>
                    <td>${job.next_run}</td>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="deleteCronJob('${job.id}', '${job.server}')">
                            Delete
                        </button>
                    </td>
                </tr>`;
            });
            
            html += '</tbody></table></div>';
            $('#cronList').html(html);
        }

        function deleteCronJob(jobId, serverName) {
            if (!confirm('Are you sure you want to delete this scheduled task?')) {
                return;
            }

            const pin = $('#cronPin').val() || $('#pin').val();
            if (!pin) {
                alert('Please enter PIN first');
                return;
            }

            $.post('<?php echo base_url(); ?>ZeroTest/delete_cron', {
                job_id: jobId,
                server: serverName,
                pin: pin
            }, function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        refreshCronList();
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
            refreshCronList();
        });
    </script>
</body>
</html> 