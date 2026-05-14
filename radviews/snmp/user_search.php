<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-network-wired"></i> SNMP User Search</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                        <li class="breadcrumb-item active">SNMP User Search</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Search User via SNMP</h3>
                </div>
                <div class="card-body">
                    <form method="post" action="<?php echo base_url('snmp_controller/search_user_snmp'); ?>">
                        <div class="form-group row">
                            <label for="username" class="col-sm-2 col-form-label">Username</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required>
                            </div>
                            <div class="col-sm-4">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </form>
                    <?php if (isset($snmp_result)): ?>
                        <hr>
                        <?php if ($snmp_result): ?>
                            <div class="alert alert-success">
                                <strong>SNMP Details Found:</strong>
                                <pre><?php print_r($snmp_result); ?></pre>
                                <?php if (isset($snmp_result['ifIndex'])): ?>
                                    <div class="form-inline mb-2">
                                        <label class="mr-2"><input type="checkbox" id="autoRefreshToggle"> Auto Refresh</label>
                                        <label class="ml-3 mr-2">Interval (sec):</label>
                                        <input type="number" id="refreshInterval" value="10" min="2" max="120" class="form-control form-control-sm" style="width:80px;">
                                    </div>
                                    <button id="getTrafficBtn" class="btn btn-info mb-2" data-ifindex="<?php echo htmlspecialchars($snmp_result['ifIndex']); ?>">Get Traffic (Mbps)</button>
                                    <div id="trafficResult" class="mt-3"></div>
                                    <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        var ifIndex = '<?php echo htmlspecialchars($snmp_result['ifIndex']); ?>';
                                        var resultDiv = document.getElementById('trafficResult');
                                        var btn = document.getElementById('getTrafficBtn');
                                        var autoToggle = document.getElementById('autoRefreshToggle');
                                        var intervalInput = document.getElementById('refreshInterval');
                                        var intervalId = null;

                                        function fetchTraffic() {
                                            resultDiv.innerHTML = 'Measuring...';
                                            fetch('<?php echo base_url('snmp_controller/get_traffic_mbps'); ?>', {
                                                method: 'POST',
                                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                                body: 'ifIndex=' + encodeURIComponent(ifIndex)
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                resultDiv.innerHTML = '<b>Download:</b> ' + data.download_mbps + ' Mbps<br><b>Upload:</b> ' + data.upload_mbps + ' Mbps';
                                            })
                                            .catch(() => {
                                                resultDiv.innerHTML = 'Error fetching traffic data.';
                                            });
                                        }

                                        btn.addEventListener('click', function(e) {
                                            e.preventDefault();
                                            fetchTraffic();
                                        });

                                        function startAutoRefresh() {
                                            if (intervalId) clearInterval(intervalId);
                                            var interval = Math.max(2, parseInt(intervalInput.value, 10)) * 1000;
                                            fetchTraffic();
                                            intervalId = setInterval(fetchTraffic, interval);
                                        }
                                        function stopAutoRefresh() {
                                            if (intervalId) clearInterval(intervalId);
                                        }
                                        autoToggle.addEventListener('change', function() {
                                            if (autoToggle.checked) {
                                                startAutoRefresh();
                                            } else {
                                                stopAutoRefresh();
                                            }
                                        });
                                        intervalInput.addEventListener('change', function() {
                                            if (autoToggle.checked) {
                                                startAutoRefresh();
                                            }
                                        });
                                    });
                                    </script>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">No SNMP details found for this username.</div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div> 