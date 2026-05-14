<?php $this->load->helper('url'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fab fa-whatsapp"></i> WhatsApp Sessions</h1>
                </div>

                <?php if($this->session->userdata('name') == 'admin'): ?>
                    <div class="col-sm-6 text-right">
                        <a href="<?php echo site_url('Whatsapp_controller/add'); ?>" class="btn btn-success"><i class="fa fa-plus"></i> Add New Session</a>
                    </div>
                <?php endif; ?>
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
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Session List</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Session Name</th>
                                <th>Manager</th>
                                <th>Status</th>
                                <th>API</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sessionsTableBody">
                        <?php if (!empty($sessions)): ?>
                            <?php foreach ($sessions as $i => $session): ?>
                                <tr>
                                    <td><?php echo $i+1; ?></td>
                                    <td><?php echo htmlspecialchars($session->session_name); ?></td>
                                    <td><?php echo htmlspecialchars($session->managername); ?></td>
                                    <td><span class="badge badge-<?php
                                        $status = strtoupper($session->waha_status);
                                        if ($status === 'CONNECTED' || $status === 'WORKING') {
                                            echo 'success';
                                        } elseif ($status === 'STARTING') {
                                            echo 'warning';
                                        } elseif (in_array($status, ['SCAN_QR_CODE', 'SCAN QR CODE', 'SCAN_QR'])) {
                                            echo 'info';
                                        } else {
                                            echo 'secondary';
                                        }
                                    ?>"><?php echo ucfirst(strtolower($session->waha_status)); ?></span></td>
                                    <td>
                                        <?php
                                            $status = strtoupper($session->waha_status);
                                            if ($status === 'CONNECTED' || $status === 'WORKING'):
                                        ?>
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                <i class="fa fa-check"></i> Connected
                                            </button>
                                        <?php elseif ($status === 'STOPPED'): ?>
                                            <button class="btn btn-success btn-sm waha-action" data-action="start" data-name="<?php echo htmlspecialchars($session->session_name); ?>">
                                                <i class="fa fa-play"></i> Start
                                            </button>
                                        <?php elseif (in_array($status, ['SCAN_QR_CODE', 'SCAN QR CODE', 'SCAN_QR'])): ?>
                                            <button class="btn btn-info btn-sm show-qr-btn" data-id="<?php echo htmlspecialchars($session->session_name); ?>">
                                                <i class="fa fa-qrcode"></i> Show QR
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-warning btn-sm" disabled>
                                                <i class="fa fa-spinner fa-spin"></i> <?php echo ucfirst(strtolower($session->waha_status)); ?>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-primary btn-sm waha-action" data-action="start" data-name="<?php echo htmlspecialchars($session->session_name); ?>" title="Start"><i class="fa fa-play"></i></button>
                                        <button class="btn btn-outline-secondary btn-sm waha-action" data-action="stop" data-name="<?php echo htmlspecialchars($session->session_name); ?>" title="Stop"><i class="fa fa-stop"></i></button>
                                        <button class="btn btn-outline-info btn-sm waha-action" data-action="restart" data-name="<?php echo htmlspecialchars($session->session_name); ?>" title="Restart"><i class="fa fa-sync"></i></button>
                                        <button class="btn btn-outline-warning btn-sm waha-action" data-action="logout" data-name="<?php echo htmlspecialchars($session->session_name); ?>" title="Logout"><i class="fa fa-sign-out-alt"></i></button>
                                        <button class="btn btn-outline-success btn-sm waha-action" data-action="screenshot" data-name="<?php echo htmlspecialchars($session->session_name); ?>" title="Screenshot"><i class="fa fa-camera"></i></button>
                                        <?php if($this->session->userdata('name') == 'admin'): ?>
                                            <button class="btn btn-outline-danger btn-sm waha-action" data-action="delete" data-name="<?php echo htmlspecialchars($session->session_name); ?>" title="Delete"><i class="fa fa-trash"></i></button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">No sessions found.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- QR Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="qrModalLabel">Scan QR / Pairing Code</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center" id="qrModalBody">
        <div id="qrLoading"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>
        <img id="qrImage" src="" alt="QR Code" style="max-width: 100%; height: auto; display:none; border: 2px solid #28a745; padding: 8px; background: #fff;">
        <div id="qrStatus" class="mt-2"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
let qrInterval = null;
let currentSessionName = null;

function fetchQrAndStatus(sessionName) {
    $.get('<?php echo site_url('Whatsapp_controller/get_qr_and_status/'); ?>' + encodeURIComponent(sessionName), function(response) {
        var status = (response.status || '').toUpperCase();

        if (status === 'CONNECTED' || status === 'WORKING') {
            $('#qrImage').hide();
            $('#qrStatus').html('<span class="badge badge-success">Device Connected</span>');
            $('#qrLoading').hide();
            if (qrInterval) clearInterval(qrInterval);
            
            // Sync session status to database when connected
            $.post('<?php echo site_url('Whatsapp_controller/sync_sessions_status'); ?>', function(syncResponse) {
                console.log('Session status synced after connection:', syncResponse);
                // Refresh the sessions table to show updated status
                refreshSessionsTable();
            });
        } else if (
            (status === 'SCAN QR CODE' || status === 'SCAN_QR_CODE' || status === 'SCAN_QR') &&
            response.qr && response.qr.indexOf('data:image') === 0
        ) {
            $('#qrImage').attr('src', response.qr).show();
            $('#qrStatus').html('<span class="badge badge-info">Scan QR Code</span>');
            $('#qrLoading').hide();
        } else {
            $('#qrImage').hide();
            $('#qrStatus').html('<span class="badge badge-warning">No QR code available or status: ' + response.status + '</span>');
            $('#qrLoading').hide();
            if (qrInterval) clearInterval(qrInterval);
        }
    });
}

function refreshSessionsTable() {
    // First sync session status from WAHA API to database
    $.post('<?php echo site_url('Whatsapp_controller/sync_sessions_status'); ?>', function(syncResponse) {
        console.log('Session sync result:', syncResponse);
        
        // Then refresh the table
        $.get('<?php echo site_url('Whatsapp_controller/ajax_sessions_table'); ?>', function(data) {
            $('#sessionsTableBody').html(data);
        });
    }).fail(function() {
        console.log('Session sync failed, refreshing table anyway');
        // If sync fails, still refresh the table
        $.get('<?php echo site_url('Whatsapp_controller/ajax_sessions_table'); ?>', function(data) {
            $('#sessionsTableBody').html(data);
        });
    });
}

$(document).ready(function() {
    setInterval(refreshSessionsTable, 5000);

    // Re-bind QR modal logic after table refresh
    $(document).on('click', '.show-qr-btn', function() {
        currentSessionName = $(this).data('id');
        $('#qrModalBody #qrLoading').show();
        $('#qrModalBody #qrImage').hide();
        $('#qrModalBody #qrStatus').html('');
        $('#qrModal').modal('show');
        fetchQrAndStatus(currentSessionName);

        if (qrInterval) clearInterval(qrInterval);
        qrInterval = setInterval(function() {
            fetchQrAndStatus(currentSessionName);
        }, 5000);
    });

    // WAHA action buttons
    $(document).on('click', '.waha-action', function() {
        var action = $(this).data('action');
        var name = $(this).data('name');
        var url = '';
        var method = 'POST';

        switch(action) {
            case 'start':
                url = '<?php echo site_url('Whatsapp_controller/start_session/'); ?>' + encodeURIComponent(name);
                break;
            case 'stop':
                url = '<?php echo site_url('Whatsapp_controller/stop_session/'); ?>' + encodeURIComponent(name);
                break;
            case 'restart':
                url = '<?php echo site_url('Whatsapp_controller/restart_session/'); ?>' + encodeURIComponent(name);
                break;
            case 'logout':
                url = '<?php echo site_url('Whatsapp_controller/logout_session/'); ?>' + encodeURIComponent(name);
                break;
            case 'screenshot':
                url = '<?php echo site_url('Whatsapp_controller/get_screenshot/'); ?>' + encodeURIComponent(name);
                method = 'GET';
                break;
            case 'delete':
                if (!confirm('Are you sure you want to delete this session?')) return;
                url = '<?php echo site_url('Whatsapp_controller/delete_session/'); ?>' + encodeURIComponent(name);
                method = 'POST';
                break;
        }

        $.ajax({
            url: url,
            type: method,
            success: function(response) {
                refreshSessionsTable();
            },
            error: function() {
                alert('Action failed!');
            }
        });
    });

    $('#qrModal').on('hidden.bs.modal', function () {
        if (qrInterval) clearInterval(qrInterval);
    });
});
</script> 