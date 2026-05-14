<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-network-wired"></i> Live User Traffic</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                        <li class="breadcrumb-item active"> Live User Traffic</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <!-- Info boxes (AdminLTE 3 style) -->
            <div class="row">
              <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-info elevation-1"><i class="fa fa-server"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Radius Status</span>
                    <span class="info-box-number" id="radius-status">-</span>
                  </div>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                  <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-plug"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Last Disconnect</span>
                    <span class="info-box-number" id="radius-lastdisconnect">-</span>
                  </div>
                </div>
              </div>
              <div class="clearfix hidden-md-up"></div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                  <span class="info-box-icon bg-success elevation-1"><i class="fas fa-signal"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Rx/Tx Pon Signal</span>
                    <span class="info-box-number" id="pon-signal">-</span>
                  </div>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                  <span class="info-box-icon bg-warning elevation-1"><i class="fa fa-clock"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Last Inform</span>
                    <span class="info-box-number" id="pon-lastinform">-</span>
                  </div>
                </div>
              </div>
            </div>
            <!-- End Info boxes -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Live Traffic for User: <b><?php echo htmlspecialchars($username); ?></b></h5>
                    <button class="btn btn-sm btn-info" onclick="location.reload();"><i class="fas fa-sync"></i> Refresh</button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Left: Graph -->
                        <div class="col-md-9">
                            <div class="mb-2 text-center">
                                <strong>SNMP Live User Traffic</strong>
                                <div id="graph-loading-message" class="blinking mt-2">Please wait, loading graph...</div>
                            </div>
                            <div id="interactive" style="height: 300px; width: 100%;"></div>
                        </div>
                        <!-- Right: Progress Bars -->
                        <div class="col-md-3">
                            <p class="text-center"><strong>SNMP Traffic Totals</strong></p>
                            <div class="progress-group mb-3">
                                Total Download (Mbps)
                                <span class="float-end"><b id="total-download-mbps">0</b> Mbps</span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-primary" id="download-mbps-bar" style="width: 100%"></div>
                                </div>
                            </div>
                            <div class="progress-group mb-3">
                                Total Upload (Mbps)
                                <span class="float-end"><b id="total-upload-mbps">0</b> Mbps</span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-success" id="upload-mbps-bar" style="width: 100%"></div>
                                </div>
                            </div>
                            <div class="progress-group mb-3">
                                Total Download (Gbps)
                                <span class="float-end"><b id="total-download-gbps">0</b> Gbps</span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-info" id="download-gbps-bar" style="width: 100%"></div>
                                </div>
                            </div>
                            <div class="progress-group mb-3">
                                Total Upload (Gbps)
                                <span class="float-end"><b id="total-upload-gbps">0</b> Gbps</span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-warning" id="upload-gbps-bar" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-3 col-6 text-center border-end">
                            <span class="text-success"><i class="bi bi-caret-up-fill"></i></span>
                            <h5 class="fw-bold mb-0" id="current-download-mbps">0</h5>
                            <span class="text-uppercase">Current Download</span>
                        </div>
                        <div class="col-md-3 col-6 text-center border-end">
                            <span class="text-info"><i class="bi bi-caret-left-fill"></i></span>
                            <h5 class="fw-bold mb-0" id="current-upload-mbps">0</h5>
                            <span class="text-uppercase">Current Upload</span>
                        </div>
                        <div class="col-md-3 col-6 text-center border-end">
                            <span class="text-success"><i class="bi bi-caret-up-fill"></i></span>
                            <h5 class="fw-bold mb-0" id="max-download-mbps-footer">0</h5>
                            <span class="text-uppercase">Max Download</span>
                        </div>
                        <div class="col-md-3 col-6 text-center">
                            <span class="text-danger"><i class="bi bi-caret-down-fill"></i></span>
                            <h5 class="fw-bold mb-0" id="max-upload-mbps-footer">0</h5>
                            <span class="text-uppercase">Max Upload</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let lastDownload = null, lastUpload = null, lastTime = null;
let lastHtml = 'Loading live traffic...';
let polling = false;
let pollInterval = 3000; // now 3s
let pollTimer = null;
let deviceType = null;
let lastNonZeroDownloadMbps = null;
let lastNonZeroUploadMbps = null;
let lastUpdateTime = null;
let secondsSinceLastChange = 0;
let lastChangeTimestamp = null;
let hasSeenIncrement = false;
let lastIncrementTimestamp = null;
let netelasticFirstIncrementSeen = false;
let maxDownloadMbps = 0;
let maxUploadMbps = 0;
let graphStarted = false;

function pollTraffic() {
    if (polling) return;
    polling = true;
    $.ajax({
        url: '/snmp_controller/get_user_counters?username=<?php echo rawurlencode($username); ?>',
        dataType: 'json',
        timeout: 8000,
        success: function(data) {
            let html = '';
            let now = data.timestamp;
            let isNetElastic = (data.device_type !== undefined && data.device_type !== 0);
            let deviceTypeLabel = isNetElastic ? 'NetElastic' : 'Mikrotik';
            let userLabel = data.username || 'unknown';
            let deviceTypeValue = data.device_type;
            if (data.error) {
                html = '<div class="alert alert-danger">' + data.error + '</div>';
            } else {
                let download = data.download_bytes;
                let upload = data.upload_bytes;
                let downloadMbps = 0, uploadMbps = 0;
                if (lastDownload !== null && lastTime !== null) {
                    let dDownload = download - lastDownload;
                    let dUpload = upload - lastUpload;
                    if (isNetElastic) {
                        // Only update Mbps and graph when increment is detected
                        if (dDownload > 0 || dUpload > 0) {
                            if (!netelasticFirstIncrementSeen) {
                                // First increment: just set the timestamp, do not plot or calculate Mbps
                                netelasticFirstIncrementSeen = true;
                                lastIncrementTimestamp = now;
                            } else {
                                // Second and subsequent increments: calculate and plot
                                hasSeenIncrement = true;
                                let dt = lastIncrementTimestamp !== null ? (now - lastIncrementTimestamp) : (now - lastTime);
                                // Debug log for NetElastic
                                console.log(`[${deviceTypeLabel}] (type=${deviceTypeValue}) User: ${userLabel} | Previous: ${lastDownload}, Current: ${download}, dDownload: ${dDownload}, Previous TS: ${(lastIncrementTimestamp !== null ? lastIncrementTimestamp : lastTime)}, Current TS: ${now}, Elapsed: ${dt}s`);
                                if (dDownload > 0 && dt > 0) {
                                    downloadMbps = (dDownload / dt) / 125000;
                                    lastNonZeroDownloadMbps = downloadMbps;
                                    lastUpdateTime = new Date().toLocaleString();
                                    console.log(`[${deviceTypeLabel}] (type=${deviceTypeValue}) User: ${userLabel} | Download Mbps: ${downloadMbps}`);
                                }
                                if (dUpload > 0 && dt > 0) {
                                    uploadMbps = (dUpload / dt) / 125000;
                                    lastNonZeroUploadMbps = uploadMbps;
                                    lastUpdateTime = new Date().toLocaleString();
                                    console.log(`[${deviceTypeLabel}] (type=${deviceTypeValue}) User: ${userLabel} | Upload Mbps: ${uploadMbps}`);
                                }
                                lastIncrementTimestamp = now;
                                pushToChart(lastNonZeroDownloadMbps, lastNonZeroUploadMbps);
                            }
                        } // Do not push to chart if no increment for NetElastic
                    } else {
                        // Mikrotik: Always update Mbps and graph using actual elapsed time between polls
                        let dt = now - lastTime;
                        // Debug log for Mikrotik
                        console.log(`[${deviceTypeLabel}] (type=${deviceTypeValue}) User: ${userLabel} | Previous: ${lastDownload}, Current: ${download}, dDownload: ${dDownload}, Previous TS: ${lastTime}, Current TS: ${now}, Elapsed: ${dt}s`);
                        if (dDownload > 0 && dt > 0) {
                            downloadMbps = (dDownload * 8) / (dt * 1000000);
                            lastNonZeroDownloadMbps = downloadMbps;
                            lastUpdateTime = new Date().toLocaleString();
                            console.log(`[${deviceTypeLabel}] (type=${deviceTypeValue}) User: ${userLabel} | Download Mbps: ${downloadMbps}`);
                        }
                        if (dUpload > 0 && dt > 0) {
                            uploadMbps = (dUpload * 8) / (dt * 1000000);
                            lastNonZeroUploadMbps = uploadMbps;
                            lastUpdateTime = new Date().toLocaleString();
                            console.log(`[${deviceTypeLabel}] (type=${deviceTypeValue}) User: ${userLabel} | Upload Mbps: ${uploadMbps}`);
                        }
                        hasSeenIncrement = true;
                        pushToChart(lastNonZeroDownloadMbps, lastNonZeroUploadMbps);
                    }
                    if (hasSeenIncrement) {
                        html += '<b>Download:</b> ' + (lastNonZeroDownloadMbps !== null ? lastNonZeroDownloadMbps.toFixed(3) : 'Waiting...') + ' Mbps<br>';
                        html += '<b>Upload:</b> ' + (lastNonZeroUploadMbps !== null ? lastNonZeroUploadMbps.toFixed(3) : 'Waiting...') + ' Mbps<br>';
                        if (lastUpdateTime) {
                            html += '<br><span class="text-muted">Last updated: ' + lastUpdateTime + '</span>';
                        }
                        // Update max values if new max is reached
                        if (lastNonZeroDownloadMbps !== null && lastNonZeroDownloadMbps > maxDownloadMbps) {
                            maxDownloadMbps = lastNonZeroDownloadMbps;
                            $('#max-download-mbps-footer').text(maxDownloadMbps.toFixed(3));
                        }
                        if (lastNonZeroUploadMbps !== null && lastNonZeroUploadMbps > maxUploadMbps) {
                            maxUploadMbps = lastNonZeroUploadMbps;
                            $('#max-upload-mbps-footer').text(maxUploadMbps.toFixed(3));
                        }
                    } else {
                        html += 'Waiting for next poll...<br>';
                    }
                } else {
                    hasSeenIncrement = false;
                    lastIncrementTimestamp = null;
                    html += 'Waiting for next poll...<br>';
                }
                if (isNetElastic) {
                    html += '<small>Total Download: ' + download + ' bytes (' + (download / 125000).toFixed(3) + ' Mbps, ' + (download / 125000000).toFixed(3) + ' Gbps)<br>';
                    html += 'Total Upload: ' + upload + ' bytes (' + (upload / 125000).toFixed(3) + ' Mbps, ' + (upload / 125000000).toFixed(3) + ' Gbps)</small>';
                } else {
                    html += '<small>Total Download: ' + download + ' bytes (' + ((download * 8) / 1000000).toFixed(3) + ' Mbps, ' + ((download * 8) / 1000000000).toFixed(3) + ' Gbps)<br>';
                    html += 'Total Upload: ' + upload + ' bytes (' + ((upload * 8) / 1000000).toFixed(3) + ' Mbps, ' + ((upload * 8) / 1000000000).toFixed(3) + ' Gbps)</small>';
                }
                // Calculate totals for Mbps and Gbps
                let totalDownloadMbps = (download * 8) / 1000000;
                let totalUploadMbps = (upload * 8) / 1000000;
                let totalDownloadGbps = (download * 8) / 1000000000;
                let totalUploadGbps = (upload * 8) / 1000000000;
                $('#total-download-mbps').text(totalDownloadMbps.toFixed(3));
                $('#total-upload-mbps').text(totalUploadMbps.toFixed(3));
                $('#total-download-gbps').text(totalDownloadGbps.toFixed(3));
                $('#total-upload-gbps').text(totalUploadGbps.toFixed(3));
                $('#download-mbps-bar').css('width', Math.min(100, (totalDownloadMbps / 1000) * 100) + '%');
                $('#upload-mbps-bar').css('width', Math.min(100, (totalUploadMbps / 1000) * 100) + '%');
                $('#download-gbps-bar').css('width', Math.min(100, (totalDownloadGbps / 10) * 100) + '%');
                $('#upload-gbps-bar').css('width', Math.min(100, (totalUploadGbps / 10) * 100) + '%');
                // Update footer stats
                $('#current-download-mbps').text(lastNonZeroDownloadMbps ? lastNonZeroDownloadMbps.toFixed(3) : '0');
                $('#current-upload-mbps').text(lastNonZeroUploadMbps ? lastNonZeroUploadMbps.toFixed(3) : '0');
            }
            lastDownload = data.download_bytes;
            lastUpload = data.upload_bytes;
            lastTime = now;
            lastHtml = html;
            $('#traffic-live').html(html);
        },
        error: function() {
            $('#traffic-live').html(lastHtml + '<br><span style="color:red;">(Polling error or timeout)</span>');
        },
        complete: function() {
            polling = false;
        }
    });
}

// Initial call
pollTraffic();
pollTimer = setInterval(pollTraffic, pollInterval);

function updatePonSignalInfo() {
    console.log('Fetching PON signal info for username: <?php echo htmlspecialchars($username); ?>');
    
    $.getJSON('<?php echo site_url('snmp_controller/get_pon_signal_info'); ?>', {
        username: '<?php echo htmlspecialchars($username); ?>'
    })
    .done(function(data) {
        console.log('PON signal data received:', data);
        
        // More robust data validation
        let rx = 'N/A';
        let tx = 'N/A';
        let lastinform = 'N/A';
        
        if (data && typeof data === 'object') {
            rx = (data.rx !== undefined && data.rx !== null && data.rx !== '' && data.rx !== 'N/A') ? data.rx : 'N/A';
            tx = (data.tx !== undefined && data.tx !== null && data.tx !== '' && data.tx !== 'N/A') ? data.tx : 'N/A';
            lastinform = (data.lastinform !== undefined && data.lastinform !== null && data.lastinform !== '' && data.lastinform !== 'N/A') ? data.lastinform : 'N/A';
        }
        
        let ponSignalText = (rx === 'N/A' || tx === 'N/A') ? 'N/A' : (rx + ' / ' + tx);
        
        console.log('Processed PON signal:', { rx, tx, lastinform, ponSignalText });
        
        $('#pon-signal').text(ponSignalText);
        $('#pon-lastinform').text(lastinform);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('Failed to fetch PON signal info:', textStatus, errorThrown);
        console.log('Response:', jqXHR.responseText);
        
        $('#pon-signal').text('Error');
        $('#pon-lastinform').text('Error');
    });
}

// Initial call
updatePonSignalInfo();
// Poll every 3 minutes (180000 ms)
setInterval(updatePonSignalInfo, 180000);

function updateRadiusStatus() {
    // Remove pppoe- prefix if present
    let username = '<?php echo htmlspecialchars($username); ?>';
    if (username.startsWith('pppoe-')) {
        username = username.substring(6); // Remove "pppoe-" (6 characters)
    }
    
    $.getJSON('<?php echo site_url('Network_controller/get_radius_status'); ?>', {username: username}, function(data) {
        let statusText = '-';
        let sinceText = '';
        let lastDisconnect = 'N/A';
        if (data && (data.acctstarttime || data.acctstoptime)) {
            let now = new Date();
            if (data.online) {
                statusText = 'Online';
                if (data.acctstarttime) {
                    let start = new Date(data.acctstarttime.replace(' ', 'T'));
                    let diffMs = now - start;
                    let diffHrs = Math.floor(diffMs / (1000 * 60 * 60));
                    sinceText = ' since ' + diffHrs + ' hour' + (diffHrs !== 1 ? 's' : '');
                }
                lastDisconnect = 'N/A';
            } else {
                statusText = 'Offline';
                if (data.acctstoptime) {
                    let stop = new Date(data.acctstoptime.replace(' ', 'T'));
                    let diffMs = now - stop;
                    let diffHrs = Math.floor(diffMs / (1000 * 60 * 60));
                    sinceText = ' since ' + diffHrs + ' hour' + (diffHrs !== 1 ? 's' : '');
                }
                lastDisconnect = data.acctterminatecause ? data.acctterminatecause : 'N/A';
            }
        } else {
            statusText = 'Offline';
            sinceText = '';
            lastDisconnect = 'N/A';
        }
        $('#radius-status').html(statusText + '<small>' + sinceText + '</small>');
        $('#radius-lastdisconnect').text(lastDisconnect);
    });
}

// Initial call
updateRadiusStatus();
// Poll every 3 minutes (180000 ms)
setInterval(updateRadiusStatus, 180000);
</script>
<!-- Flot Charts JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.time.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.resize.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.tooltip.min.js"></script>
<script>
// Chart data arrays
let chartDownload = [];
let chartUpload = [];
let chartMaxPoints = 40; // Show last 40 points (2 minutes at 3s interval)
let chartRealtime = true;

function updateChart() {
  let data = [
    { label: "Download (Mbps)", data: chartDownload, color: "#007bff", lines: { fill: true } },
    { label: "Upload (Mbps)", data: chartUpload, color: "#28a745", lines: { fill: true } }
  ];
  $.plot("#interactive", data, {
    grid: { borderColor: "#f3f3f3", borderWidth: 1, tickColor: "#f3f3f3" },
    series: { shadowSize: 0, lines: { show: true, fill: true, fillColor: { colors: [{ opacity: 0.2 }, { opacity: 0.1 }] } } },
    yaxis: { min: 0 },
    xaxis: { mode: "time", timeformat: "%H:%M:%S", timezone: "browser" },
    legend: { position: "nw" },
    tooltip: true,
    tooltipOpts: { content: "%s: %y Mbps at %x", shifts: { x: 20, y: 0 }, defaultTheme: false }
  });
}

// Real time toggle
$('#realtime button').on('click', function() {
  $('#realtime button').removeClass('active');
  $(this).addClass('active');
  chartRealtime = $(this).data('toggle') === 'on';
});

// Hook into pollTraffic to push new values to chart
function pushToChart(downloadMbps, uploadMbps) {
  let now = new Date().getTime();
  if (!graphStarted && (typeof downloadMbps === 'number' || typeof uploadMbps === 'number')) {
    $('#graph-loading-message').hide();
    graphStarted = true;
  }
  if (typeof downloadMbps === 'number') {
    chartDownload.push([now, downloadMbps]);
    if (chartDownload.length > chartMaxPoints) chartDownload.shift();
  }
  if (typeof uploadMbps === 'number') {
    chartUpload.push([now, uploadMbps]);
    if (chartUpload.length > chartMaxPoints) chartUpload.shift();
  }
  if (chartRealtime) updateChart();
}

// Patch pollTraffic to call pushToChart when new Mbps values are available
let origPollTraffic = pollTraffic;
pollTraffic = function() {
  if (polling) return;
  polling = true;
  $.ajax({
    url: '/snmp_controller/get_user_counters?username=<?php echo rawurlencode($username); ?>',
    dataType: 'json',
    timeout: 8000,
    success: function(data) {
      let html = '';
      let now = data.timestamp;
      let isNetElastic = (data.device_type !== undefined && data.device_type !== 0);
      let deviceTypeLabel = isNetElastic ? 'NetElastic' : 'Mikrotik';
      let userLabel = data.username || 'unknown';
      let deviceTypeValue = data.device_type;
      if (data.error) {
        html = '<div class="alert alert-danger">' + data.error + '</div>';
      } else {
        let download = data.download_bytes;
        let upload = data.upload_bytes;
        let downloadMbps = 0, uploadMbps = 0;
        if (lastDownload !== null && lastTime !== null) {
          let dDownload = download - lastDownload;
          let dUpload = upload - lastUpload;
          if (isNetElastic) {
            // Only update Mbps and graph when increment is detected
            if (dDownload > 0 || dUpload > 0) {
                if (!netelasticFirstIncrementSeen) {
                    // First increment: just set the timestamp, do not plot or calculate Mbps
                    netelasticFirstIncrementSeen = true;
                    lastIncrementTimestamp = now;
                } else {
                    // Second and subsequent increments: calculate and plot
                    hasSeenIncrement = true;
                    let dt = lastIncrementTimestamp !== null ? (now - lastIncrementTimestamp) : (now - lastTime);
                    // Debug log for NetElastic
                    console.log(`[${deviceTypeLabel}] (type=${deviceTypeValue}) User: ${userLabel} | Previous: ${lastDownload}, Current: ${download}, dDownload: ${dDownload}, Previous TS: ${(lastIncrementTimestamp !== null ? lastIncrementTimestamp : lastTime)}, Current TS: ${now}, Elapsed: ${dt}s`);
                    if (dDownload > 0 && dt > 0) {
                        downloadMbps = (dDownload / dt) / 125000;
                        lastNonZeroDownloadMbps = downloadMbps;
                        lastUpdateTime = new Date().toLocaleString();
                        console.log(`[${deviceTypeLabel}] (type=${deviceTypeValue}) User: ${userLabel} | Download Mbps: ${downloadMbps}`);
                    }
                    if (dUpload > 0 && dt > 0) {
                        uploadMbps = (dUpload / dt) / 125000;
                        lastNonZeroUploadMbps = uploadMbps;
                        lastUpdateTime = new Date().toLocaleString();
                        console.log(`[${deviceTypeLabel}] (type=${deviceTypeValue}) User: ${userLabel} | Upload Mbps: ${uploadMbps}`);
                    }
                    lastIncrementTimestamp = now;
                    pushToChart(lastNonZeroDownloadMbps, lastNonZeroUploadMbps);
                }
            } // Do not push to chart if no increment for NetElastic
          } else {
            // Mikrotik: Always update Mbps and graph using actual elapsed time between polls
            let dt = now - lastTime;
            // Debug log for Mikrotik
            console.log(`[${deviceTypeLabel}] (type=${deviceTypeValue}) User: ${userLabel} | Previous: ${lastDownload}, Current: ${download}, dDownload: ${dDownload}, Previous TS: ${lastTime}, Current TS: ${now}, Elapsed: ${dt}s`);
            if (dDownload > 0 && dt > 0) {
                downloadMbps = (dDownload * 8) / (dt * 1000000);
                lastNonZeroDownloadMbps = downloadMbps;
                lastUpdateTime = new Date().toLocaleString();
                console.log(`[${deviceTypeLabel}] (type=${deviceTypeValue}) User: ${userLabel} | Download Mbps: ${downloadMbps}`);
            }
            if (dUpload > 0 && dt > 0) {
                uploadMbps = (dUpload * 8) / (dt * 1000000);
                lastNonZeroUploadMbps = uploadMbps;
                lastUpdateTime = new Date().toLocaleString();
                console.log(`[${deviceTypeLabel}] (type=${deviceTypeValue}) User: ${userLabel} | Upload Mbps: ${uploadMbps}`);
            }
            hasSeenIncrement = true;
            pushToChart(lastNonZeroDownloadMbps, lastNonZeroUploadMbps);
          }
          if (hasSeenIncrement) {
            html += '<b>Download:</b> ' + (lastNonZeroDownloadMbps !== null ? lastNonZeroDownloadMbps.toFixed(3) : 'Waiting...') + ' Mbps<br>';
            html += '<b>Upload:</b> ' + (lastNonZeroUploadMbps !== null ? lastNonZeroUploadMbps.toFixed(3) : 'Waiting...') + ' Mbps<br>';
            if (lastUpdateTime) {
                html += '<br><span class="text-muted">Last updated: ' + lastUpdateTime + '</span>';
            }
            // Update max values if new max is reached
            if (lastNonZeroDownloadMbps !== null && lastNonZeroDownloadMbps > maxDownloadMbps) {
                maxDownloadMbps = lastNonZeroDownloadMbps;
                $('#max-download-mbps-footer').text(maxDownloadMbps.toFixed(3));
            }
            if (lastNonZeroUploadMbps !== null && lastNonZeroUploadMbps > maxUploadMbps) {
                maxUploadMbps = lastNonZeroUploadMbps;
                $('#max-upload-mbps-footer').text(maxUploadMbps.toFixed(3));
            }
          } else {
            html += 'Waiting for next poll...<br>';
          }
        } else {
          hasSeenIncrement = false;
          lastIncrementTimestamp = null;
          html += 'Waiting for next poll...<br>';
        }
        if (isNetElastic) {
          html += '<small>Total Download: ' + download + ' bytes (' + (download / 125000).toFixed(3) + ' Mbps, ' + (download / 125000000).toFixed(3) + ' Gbps)<br>';
          html += 'Total Upload: ' + upload + ' bytes (' + (upload / 125000).toFixed(3) + ' Mbps, ' + (upload / 125000000).toFixed(3) + ' Gbps)</small>';
        } else {
          html += '<small>Total Download: ' + download + ' bytes (' + ((download * 8) / 1000000).toFixed(3) + ' Mbps, ' + ((download * 8) / 1000000000).toFixed(3) + ' Gbps)<br>';
          html += 'Total Upload: ' + upload + ' bytes (' + ((upload * 8) / 1000000).toFixed(3) + ' Mbps, ' + ((upload * 8) / 1000000000).toFixed(3) + ' Gbps)</small>';
        }
        // Calculate totals for Mbps and Gbps
        let totalDownloadMbps = (download * 8) / 1000000;
        let totalUploadMbps = (upload * 8) / 1000000;
        let totalDownloadGbps = (download * 8) / 1000000000;
        let totalUploadGbps = (upload * 8) / 1000000000;
        $('#total-download-mbps').text(totalDownloadMbps.toFixed(3));
        $('#total-upload-mbps').text(totalUploadMbps.toFixed(3));
        $('#total-download-gbps').text(totalDownloadGbps.toFixed(3));
        $('#total-upload-gbps').text(totalUploadGbps.toFixed(3));
        $('#download-mbps-bar').css('width', Math.min(100, (totalDownloadMbps / 1000) * 100) + '%');
        $('#upload-mbps-bar').css('width', Math.min(100, (totalUploadMbps / 1000) * 100) + '%');
        $('#download-gbps-bar').css('width', Math.min(100, (totalDownloadGbps / 10) * 100) + '%');
        $('#upload-gbps-bar').css('width', Math.min(100, (totalUploadGbps / 10) * 100) + '%');
        // Update footer stats
        $('#current-download-mbps').text(lastNonZeroDownloadMbps ? lastNonZeroDownloadMbps.toFixed(3) : '0');
        $('#current-upload-mbps').text(lastNonZeroUploadMbps ? lastNonZeroUploadMbps.toFixed(3) : '0');
      }
      lastDownload = data.download_bytes;
      lastUpload = data.upload_bytes;
      lastTime = now;
      lastHtml = html;
      $('#traffic-live').html(html);
    },
    error: function() {
      $('#traffic-live').html(lastHtml + '<br><span style="color:red;">(Polling error or timeout)</span>');
    },
    complete: function() {
      polling = false;
    }
  });
};

// Initial call
pollTraffic();
pollTimer = setInterval(pollTraffic, pollInterval);
</script>
<style>
.blinking {
  animation: blinker 1s linear infinite;
  color: #007bff;
  font-weight: bold;
}
@keyframes blinker {
  50% { opacity: 0; }
}
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> 