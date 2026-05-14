<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Mapped Devices</h1>
    </section>
    <section class="content">
        <div class="container-fluid">
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-12">
                    <a href="<?php echo site_url('Network_controller/syncRxTxForMappedDevices'); ?>" class="btn btn-success mb-2">Sync RX/TX Values</a>
                    <a href="<?php echo site_url('Network_controller/refreshAllPonQuality'); ?>" class="btn btn-info mb-2 ml-2" onclick="return confirm('Refresh PON Quality Monitor for all devices? This may take a while.');">Refresh All PON Quality</a>
                    <div class="card">
                        <div class="card-header">
                            <form method="get" class="form-inline">
                                <div class="form-group mb-2">
                                    <input type="text" name="search" class="form-control" placeholder="Search by Device/User/Manager" value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
                                </div>
                                <button type="submit" class="btn btn-primary ml-2 mb-2">Search</button>
                            </form>
                        </div>
                        <div class="card-body table-responsive">
                            <style>
                                .lastinform-warning {
                                    background-color: orange !important;
                                    color: #fff !important;
                                    animation: blink 1s linear infinite;
                                }
                                @keyframes blink {
                                    50% { opacity: 0.5; }
                                }
                            </style>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Device Name</th>
                                        <th>Type</th>
                                        <th>Username</th>
                                        <th>Manager</th>
                                        <th>RX</th>
                                        <th>TX</th>
                                        <th>Master ID</th>
                                        <th>Status</th>
                                        <th>Inform On</th>
                                        <th>Last Seen</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($mappedDevices)): ?>
                                        <?php foreach ($mappedDevices as $device): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($device->devicename); ?></td>
                                                <td><?php echo htmlspecialchars($device->devicetype_label); ?></td>
                                                <td><?php echo htmlspecialchars($device->username); ?></td>
                                                <td><?php echo htmlspecialchars($device->managername); ?></td>
                                                <td><?php echo htmlspecialchars($device->rx); ?></td>
                                                <td><?php echo htmlspecialchars($device->tx); ?></td>
                                                <td><?php echo htmlspecialchars($device->masterdeviceid_label); ?></td>
                                                <td><?php echo htmlspecialchars($device->isactive_label); ?></td>
                                                <?php
                                                    $lastInform = $device->lastInform;
                                                    $dateClass = '';
                                                    $timeClass = '';
                                                    $dateStr = '';
                                                    $timeStr = '';
                                                    if ($lastInform) {
                                                        $dt = new DateTime($lastInform);
                                                        $dateStr = $dt->format('Y-m-d');
                                                        $timeStr = $dt->format('H:i:s');
                                                        $now = new DateTime();
                                                        // Date check: orange+blink if lastInform date < today
                                                        if ($dateStr < $now->format('Y-m-d')) {
                                                            $dateClass = 'lastinform-warning';
                                                        }
                                                        // Time check: orange+blink if more than 15 min ago and date is today
                                                        if ($dateStr == $now->format('Y-m-d')) {
                                                            $diff = $now->getTimestamp() - $dt->getTimestamp();
                                                            if ($diff > 900) { // 900 seconds = 15 minutes
                                                                $timeClass = 'lastinform-warning';
                                                            }
                                                        }
                                                    }
                                                ?>
                                                <td class="<?php echo $dateClass; ?>"><?php echo htmlspecialchars($dateStr); ?></td>
                                                <td class="<?php echo $timeClass; ?>"><?php echo htmlspecialchars($timeStr); ?></td>
                                                <td>
                                                    <?php if (isset($device->deviceid)): ?>
                                                        <a href="<?php echo site_url('Network_controller/editMappedDevice/' . $device->deviceid); ?>" class="btn btn-xs btn-warning">Edit</a>
                                                        <a href="<?php echo site_url('Network_controller/deleteMappedDevice/' . $device->deviceid); ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this mapped device?');">Delete</a>
                                                        <a href="<?php echo site_url('Network_controller/refreshDeviceParameters/' . $device->deviceid); ?>" class="btn btn-xs btn-info" onclick="return confirm('Refresh parameters for this device? This may take a few seconds.');">Refresh</a>
                                                    <?php else: ?>
                                                        <span class="text-muted">No Actions</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="10" class="text-center">No mapped devices found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    function syncAndReload() {
        fetch("<?php echo site_url('Network_controller/syncRxTxForMappedDevices'); ?>")
            .then(() => location.reload());
    }
    setInterval(syncAndReload, 300000); // 5 minutes
</script> 