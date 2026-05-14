<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$segmentTypeMap = array(
    0 => 'Region',
    1 => 'Zone',
    2 => 'Area',
    3 => 'Device',
    4 => 'Optical Unit',
    5 => 'Server',
    6 => 'Other'
);
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-server"></i> NAS/Segments for Manager: <?php echo htmlspecialchars($managername); ?></h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Segments & Parameters</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                    <table class="table table-striped projects">
                        <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 25%">Segment Name</th>
                                <th style="width: 15%">Type</th>
                                <th style="width: 55%">Parameters (Online/Offline)</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $row_count = 1; foreach($segmentList as $segment): ?>
                            <tr>
                                <td><?php echo $row_count; ?></td>
                                <td><?php echo htmlspecialchars($segment->segmentname); ?></td>
                                <td><?php $stype = isset($segment->segmenttype) ? trim($segment->segmenttype) : ''; echo isset($segmentTypeMap[$stype]) ? $segmentTypeMap[$stype] : $stype; ?></td>
                                <td>
                                    <?php if (!empty($segment->parameters)): ?>
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead><tr><th>Parameter</th><th>Online</th><th>Offline</th></tr></thead>
                                        <tbody>
                                        <?php foreach($segment->parameters as $param): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($param->parameter); ?></td>
                                                <td><span class="badge badge-success"><?php echo isset($param->online) ? $param->online : '-'; ?></span></td>
                                                <td><span class="badge badge-secondary"><?php echo isset($param->offline) ? $param->offline : '-'; ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <?php else: ?>
                                        <span class="text-muted">No parameters</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php $row_count++; endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> 