<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-history"></i> Import Logs</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url('import/ImportUsers'); ?>">Import Users</a></li>
                        <li class="breadcrumb-item active">Import Logs</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0"><i class="fas fa-list-alt"></i> Import History</h3>
                            <a href="<?php echo base_url('import/ImportUsers'); ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-upload"></i> Import Users
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="importLogsTable" class="table table-bordered table-striped table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 60px;">ID</th>
                                            <th>Manager</th>
                                            <th>Import Type</th>
                                            <th>File Name</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th style="width: 110px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($logs)): ?>
                                            <?php foreach ($logs as $log): ?>
                                                <tr>
                                                    <td><?php echo $log->id; ?></td>
                                                    <td>
                                                        <span class="badge badge-info px-2 py-1"><?php echo htmlspecialchars($log->managername); ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-secondary px-2 py-1"><?php echo htmlspecialchars($log->import_type_name); ?></span>
                                                    </td>
                                                    <td>
                                                        <i class="fas fa-file-csv text-success"></i>
                                                        <span class="ml-1"><?php echo htmlspecialchars($log->importfile); ?></span>
                                                    </td>
                                                    <td>
                                                        <?php if ($log->status == 1): ?>
                                                            <span class="badge badge-success px-2 py-1"><?php echo $log->status_name; ?></span>
                                                        <?php elseif ($log->status == 0): ?>
                                                            <span class="badge badge-danger px-2 py-1"><?php echo $log->status_name; ?></span>
                                                        <?php else: ?>
                                                            <span class="badge badge-warning px-2 py-1"><?php echo $log->status_name; ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <i class="far fa-clock text-muted"></i>
                                                        <span class="ml-1"><?php echo date('M d, Y H:i', strtotime($log->created_at)); ?></span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="<?php echo base_url('import/ImportUsers/downloadCsv/' . $log->id); ?>" class="btn btn-success" title="Download CSV">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-info view-log" data-log-id="<?php echo $log->id; ?>" title="View Details">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <?php if ($isAdmin || $isMaster || $log->managername == $this->session->userdata('name')): ?>
                                                                <button type="button" class="btn btn-danger delete-log" data-log-id="<?php echo $log->id; ?>" title="Delete Log">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Log Details Modal -->
<div class="modal fade" id="logDetailsModal" tabindex="-1" role="dialog" aria-labelledby="logDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logDetailsModalLabel">Import Log Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Log ID:</strong></td>
                                <td id="modal-log-id"></td>
                            </tr>
                            <tr>
                                <td><strong>Manager:</strong></td>
                                <td id="modal-manager"></td>
                            </tr>
                            <tr>
                                <td><strong>Import Type:</strong></td>
                                <td id="modal-type"></td>
                            </tr>
                            <tr>
                                <td><strong>File Name:</strong></td>
                                <td id="modal-filename"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td id="modal-status"></td>
                            </tr>
                            <tr>
                                <td><strong>Created At:</strong></td>
                                <td id="modal-created"></td>
                            </tr>
                            <tr>
                                <td><strong>File Path:</strong></td>
                                <td id="modal-filepath"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this import log? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#importLogsTable').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"],
        "order": [[5, "desc"]], // Sort by created_at descending
        "pageLength": 25
    }).buttons().container().appendTo('#importLogsTable_wrapper .col-md-6:eq(0)');

    // View log details
    $('.view-log').on('click', function() {
        var logId = $(this).data('log-id');
        
        $.ajax({
            url: '<?php echo base_url("import/ImportUsers/getLogDetails"); ?>',
            type: 'POST',
            data: { log_id: logId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var log = response.log;
                    
                    $('#modal-log-id').text(log.id);
                    $('#modal-manager').html('<span class="badge badge-info">' + log.managername + '</span>');
                    $('#modal-type').html('<span class="badge badge-secondary">' + log.import_type_name + '</span>');
                    $('#modal-filename').html('<i class="fas fa-file-csv text-success"></i> ' + log.importfile);
                    
                    var statusClass = log.status == 1 ? 'badge-success' : (log.status == 0 ? 'badge-danger' : 'badge-warning');
                    $('#modal-status').html('<span class="badge ' + statusClass + '">' + log.status_name + '</span>');
                    
                    $('#modal-created').html('<i class="far fa-clock text-muted"></i> ' + new Date(log.created_at).toLocaleString());
                    $('#modal-filepath').text('uploads/imports/' + log.importfile);
                    
                    $('#logDetailsModal').modal('show');
                } else {
                    toastr.error(response.message || 'Failed to load log details');
                }
            },
            error: function() {
                toastr.error('An error occurred while loading log details');
            }
        });
    });

    // Delete log confirmation
    var logIdToDelete = null;
    
    $('.delete-log').on('click', function() {
        logIdToDelete = $(this).data('log-id');
        $('#deleteConfirmModal').modal('show');
    });
    
    $('#confirmDelete').on('click', function() {
        if (!logIdToDelete) return;
        
        $.ajax({
            url: '<?php echo base_url("import/ImportUsers/deleteLog"); ?>',
            type: 'POST',
            data: { log_id: logIdToDelete },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    // Reload the page to refresh the table
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(response.message || 'Failed to delete log');
                }
                $('#deleteConfirmModal').modal('hide');
            },
            error: function() {
                toastr.error('An error occurred while deleting the log');
                $('#deleteConfirmModal').modal('hide');
            }
        });
    });
});
</script> 