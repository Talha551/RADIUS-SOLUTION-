<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-server"></i> NAS Devices List</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="<?php echo base_url('Network_controller/addNas'); ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Add New NAS</a>
                    <button id="update-status-btn" class="btn btn-info ml-2"><i class="fas fa-sync-alt"></i> Update Status</button>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <?php if($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
            <?php endif; ?>
            <?php if($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">NAS Devices</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                    <table class="table table-striped projects">
                        <thead>
                            <tr>
                                <th style="width: 1%"></th>
                                <th style="width: 1%">#</th>
                                <th style="width: 20%">NAS Name</th>
                                <th style="width: 15%">Short Name</th>
                                <th style="width: 10%">Type</th>
                                <th style="width: 20%">User Count</th>
                                <th style="width: 8%; white-space: nowrap;" class="text-center">Status</th>
                                <th style="width: 20%">User Status</th>
                                <th style="width: 20%; white-space: nowrap;"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $totalUsers = 0;
                        foreach($nasList as $nas) {
                            $totalUsers += $nas->user_count;
                        }
                        $row_count = 1;
                        $typeMap = [
                            '0' => 'Mikrotik',
                            '1' => 'StarOS',
                            '2' => 'ChilliSpot',
                            '3' => 'Cisco',
                            '4' => 'pFSense',
                            '5' => 'Other'
                        ];
                        foreach($nasList as $nas) {
                            $percent = $totalUsers > 0 ? round(($nas->user_count / $totalUsers) * 100) : 0;
                            $rowId = 'nas-row-' . $nas->id;
                            $detailsId = 'nas-details-' . $nas->id;
                        ?>
                            <tr id="<?php echo $rowId; ?>">
                                <td style="vertical-align: middle; text-align: center;">
                                    <button class="btn btn-link btn-sm toggle-details" data-nasid="<?php echo $nas->id; ?>" data-nasip="<?php echo htmlspecialchars($nas->nasname); ?>" aria-expanded="false" style="padding:0;">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </td>
                                <td><?php echo $row_count; ?></td>
                                <td><?php echo htmlspecialchars($nas->nasname); ?></td>
                                <td><?php echo htmlspecialchars($nas->shortname); ?></td>
                                <td><?php echo isset($typeMap[$nas->type]) ? $typeMap[$nas->type] : 'Other'; ?></td>
                                <td class="project_progress" id="progress-<?php echo $nas->id; ?>">
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-green" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                                    </div>
                                    <small>
                                        <span class="progress-label"><i class="fas fa-spinner fa-spin"></i> Loading...</span>
                                    </small>
                                </td>
                                <td class="project-state" style="white-space: nowrap;">
                                    <?php if($nas->enableapi): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Disabled</span>
                                    <?php endif; ?>
                                </td>
                                <td id="userstatus-<?php echo $nas->id; ?>">
                                    <span class="badge badge-success">Online: <i class="fas fa-spinner fa-spin"></i></span>
                                    <span class="badge badge-secondary">Offline: <i class="fas fa-spinner fa-spin"></i></span>
                                </td>
                                <td class="project-actions text-right" style="white-space: nowrap;">
                                    <div class="btn-group" role="group">
                                        <a class="btn btn-info btn-sm mr-2" href="<?php echo base_url('Network_controller/editNas/'.$nas->id); ?>">
                                            <i class="fas fa-pencil-alt"></i> Edit
                                        </a>
                                        <a class="btn btn-danger btn-sm" href="<?php echo base_url('Network_controller/deleteNas/'.$nas->id); ?>" onclick="return confirm('Are you sure you want to delete this NAS?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr id="<?php echo $detailsId; ?>" class="nas-details-row" style="display:none; background:#f8f9fa;">
                                <td colspan="9">
                                    <div class="nas-details-content" id="nas-details-content-<?php echo $nas->id; ?>">
                                        <div class="text-center text-muted py-3"><i class="fas fa-spinner fa-spin"></i> Loading details...</div>
                                    </div>
                                </td>
                            </tr>
                        <?php $row_count++; } ?>
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
<script>
$(document).ready(function() {
    // On page load, fetch cached values for each NAS
    <?php foreach($nasList as $nas): ?>
    $.get('<?php echo base_url('Network_controller/getNasOnlineOfflineUserCountAjax'); ?>', {nasip: '<?php echo $nas->nasname; ?>'}, function(data) {
        let d = typeof data === 'string' ? JSON.parse(data) : data;
        let percent = (d.total > 0) ? Math.round((d.online / d.total) * 100) : 0;
        // Update progress bar
        $('#progress-<?php echo $nas->id; ?> .progress-bar').css('width', percent + '%').attr('aria-valuenow', percent);
        $('#progress-<?php echo $nas->id; ?> .progress-label').html(percent + '% Online (' + d.online + ' online / ' + d.offline + ' offline)');
        // Update user status badges
        $('#userstatus-<?php echo $nas->id; ?>').html(
            '<span class="badge badge-success">Online: ' + d.online + '</span> ' +
            '<span class="badge badge-secondary">Offline: ' + d.offline + '</span>'
        );
    });
    <?php endforeach; ?>

    // Remove the default AJAX status update on page load
    // Add handler for Update Status button
    $('#update-status-btn').click(function() {
        $(this).prop('disabled', true).html('<i class="fas fa-sync fa-spin"></i> Updating...');
        // First, refresh the cache on the server
        $.get('<?php echo base_url('Network_controller/refreshNasUserStatusCacheAjax'); ?>', function(refreshData) {
            // Now update the UI with the new cached values
            var nasCount = <?php echo count($nasList); ?>;
            var completed = 0;
            <?php foreach($nasList as $nas): ?>
            $.get('<?php echo base_url('Network_controller/getNasOnlineOfflineUserCountAjax'); ?>', {nasip: '<?php echo $nas->nasname; ?>'}, function(data) {
                let d = typeof data === 'string' ? JSON.parse(data) : data;
                let percent = (d.total > 0) ? Math.round((d.online / d.total) * 100) : 0;
                // Update progress bar
                $('#progress-<?php echo $nas->id; ?> .progress-bar').css('width', percent + '%').attr('aria-valuenow', percent);
                $('#progress-<?php echo $nas->id; ?> .progress-label').html(percent + '% Online (' + d.online + ' online / ' + d.offline + ' offline)');
                // Update user status badges
                $('#userstatus-<?php echo $nas->id; ?>').html(
                    '<span class="badge badge-success">Online: ' + d.online + '</span> ' +
                    '<span class="badge badge-secondary">Offline: ' + d.offline + '</span>'
                );
                completed++;
                if (completed === nasCount) {
                    $('#update-status-btn').prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Update Status');
                }
            });
            <?php endforeach; ?>
        });
    });

    // Expand/collapse details
    $('.toggle-details').click(function() {
        var nasid = $(this).data('nasid');
        var nasip = $(this).data('nasip');
        var $btn = $(this);
        var $icon = $btn.find('i');
        var $detailsRow = $('#nas-details-' + nasid);
        var $content = $('#nas-details-content-' + nasid);
        var expanded = $btn.attr('aria-expanded') === 'true';
        // Collapse any open details
        $('.nas-details-row').hide();
        $('.toggle-details').attr('aria-expanded', 'false').find('i').removeClass('fa-chevron-down').addClass('fa-chevron-right');
        if (!expanded) {
            $detailsRow.show();
            $btn.attr('aria-expanded', 'true');
            $icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
            $content.html('<div class="text-center text-muted py-3"><i class="fas fa-spinner fa-spin"></i> Loading details...</div>');
            $.get('<?php echo base_url('Network_controller/getNasSegmentUserStatusAjax'); ?>', {nasip: nasip}, function(data) {
                let segments = typeof data === 'string' ? JSON.parse(data) : data;
                if (!segments.length) {
                    $content.html('<div class="text-center text-muted py-3">No user segment data found for this NAS.</div>');
                    return;
                }
                let html = '<table class="table table-sm table-bordered mb-0">';
                html += '<thead><tr><th>Segment</th><th>Parameter</th><th>Online Users</th><th>Offline Users</th></tr></thead><tbody>';
                segments.forEach(function(seg) {
                    let rowspan = seg.parameters.length;
                    seg.parameters.forEach(function(param, idx) {
                        html += '<tr>';
                        if (idx === 0) {
                            html += '<td rowspan="' + rowspan + '" style="vertical-align:middle;"><strong>' + seg.segment_name + '</strong></td>';
                        }
                        html += '<td>' + param.parameter + '</td>';
                        html += '<td><span class="badge badge-success">' + param.online + '</span></td>';
                        html += '<td><span class="badge badge-secondary">' + param.offline + '</span></td>';
                        html += '</tr>';
                    });
                });
                html += '</tbody></table>';
                $content.html(html);
            });
        }
    });
});
</script> 