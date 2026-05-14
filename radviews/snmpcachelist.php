<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$deviceTypeMap = array(
    '' => 'All',
    0 => 'Mikrotik',
    5 => 'NetElastic/Other'
);
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>SNMP Cache List</h1>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <form method="get" class="form-inline mb-2">
                        <div class="form-group mb-2 mr-2">
                            <input type="text" name="search" class="form-control" placeholder="Search by Username/IP/NAS" value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
                        </div>
                        <div class="form-group mb-2 mr-2">
                            <select name="device_type" class="form-control">
                                <?php foreach ($deviceTypeMap as $k => $v): ?>
                                    <option value="<?php echo $k; ?>" <?php if ((string)$deviceType === (string)$k) echo 'selected'; ?>><?php echo $v; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group mb-2 mr-2">
                            <select name="nas_ip" class="form-control">
                                <option value="">All NAS</option>
                                <?php foreach ($nasList as $nas): ?>
                                    <option value="<?php echo htmlspecialchars($nas->nasname); ?>" <?php if ($nasIp === $nas->nasname) echo 'selected'; ?>><?php echo htmlspecialchars($nas->shortname); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary ml-2 mb-2">Filter</button>
                    </form>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">SNMP Cache Entries</h3>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Session Index</th>
                                        <th>IP Address</th>
                                        <th>NAS</th>
                                        <th>Device Type</th>
                                        <th>Last Updated</th>
                                        <th>Monitor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($snmpCacheList)): ?>
                                        <?php foreach ($snmpCacheList as $row): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row->username); ?></td>
                                                <td><?php echo htmlspecialchars($row->session_index); ?></td>
                                                <td><?php echo htmlspecialchars($row->ipaddress); ?></td>
                                                <td><?php echo htmlspecialchars($row->nas_shortname ? $row->nas_shortname : $row->nas_ip); ?></td>
                                                <td><?php echo isset($deviceTypeMap[$row->device_type]) ? $deviceTypeMap[$row->device_type] : $row->device_type; ?></td>
                                                <td><?php echo htmlspecialchars($row->last_updated); ?></td>
                                                <td>
                                                    <a href="<?php echo site_url('snmp_controller/get_usertraffic_mbps/' . urlencode($row->username)); ?>" class="btn btn-info" target="_blank">Monitor</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="7" class="text-center">No SNMP cache entries found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <div class="card-footer clearfix">
                                <?php
                                if (isset($pagination_links)) {
                                    echo str_replace('pagination-sm', 'pagination-lg', $pagination_links);
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div> 