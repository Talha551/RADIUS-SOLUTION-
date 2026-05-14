<!-- DataTables CSS -->
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css'); ?>">

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>WhatsApp Incoming Logs</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
              <li class="breadcrumb-item active">WhatsApp Logs</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">All Incoming Messages</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-warning btn-sm" id="clearAllMessages">Clear All Messages</button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="logsTable" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Mobile</th>
                                    <th>Username</th>
                                    <th>Manager</th>
                                    <th>Session ID</th>
                                    <th>Status</th>
                                    <th>Received At</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(!empty($logs)): ?>
                                    <?php foreach($logs as $log): ?>
                                    <tr>
                                        <td><?php echo $log->id; ?></td>
                                        <td><?php echo $log->mobile; ?></td>
                                        <td><?php echo $log->username; ?></td>
                                        <td><?php echo $log->managername; ?></td>
                                        <td><?php echo $log->waha_session_id; ?></td>
                                        <td><span class="badge badge-<?php echo $log->status ? 'success' : 'danger'; ?>"><?php echo $log->status ? 'Active' : 'Inactive'; ?></span></td>
                                        <td><?php echo $log->created_at; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm view-messages" 
                                                    data-log-id="<?php echo $log->id; ?>" 
                                                    data-messages="<?php echo htmlspecialchars($log->messages); ?>"
                                                    <?php echo (empty($log->messages) || trim($log->messages) === '') ? 'disabled' : ''; ?>>
                                                <?php echo (empty($log->messages) || trim($log->messages) === '') ? 'No Messages' : 'View Messages'; ?>
                                            </button>
                                            <a href="<?php echo base_url('whatsapp_controller/delete_log/'.$log->id); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this log?');">Delete</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
</div>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="messageModalLabel">Conversation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="direct-chat-messages" id="message-container" style="height: 400px; overflow-y: auto; padding: 10px;">
            <!-- Messages will be injected here -->
        </div>
      </div>
    </div>
  </div>
</div>

<!-- DataTables JS -->
<script src="<?php echo base_url('assets/plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js'); ?>"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#logsTable').DataTable({
        "responsive": true,
        "autoWidth": false,
        "pageLength": 25,
        "order": [[ 6, "desc" ]] // Sort by received date (column 6) in descending order
    });

    // Handle view messages button click
    $(document).on('click', '.view-messages', function() {
        // Don't proceed if button is disabled
        if ($(this).prop('disabled')) {
            return;
        }
        
        var logId = $(this).data('log-id');
        var messages = $(this).data('messages');
        var button = $(this);
        console.log('Messages data:', messages); // Debug log
        
        var messageContainer = $('#message-container');
        messageContainer.empty();

        if (messages && messages.trim() !== '') {
            var messageArray = messages.split('\n--------------------\n');
            console.log('Message array:', messageArray); // Debug log

            messageArray.forEach(function(message, index) {
                if (message.trim() !== '') {
                    var chatClass = index % 2 === 0 ? 'direct-chat-msg' : 'direct-chat-msg right';
                    var nameClass = index % 2 === 0 ? 'left' : 'right';

                    var messageHtml = `
                        <div class="${chatClass}">
                          <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name float-${nameClass}">User</span>
                          </div>
                          <div class="direct-chat-text">
                            ${message.replace(/\n/g, '<br>')}
                          </div>
                        </div>`;
                    messageContainer.append(messageHtml);
                }
            });
        } else {
            messageContainer.html('<p class="text-muted">No text messages available</p><p class="text-info"><small><i class="fas fa-info-circle"></i> Media messages (images, videos, etc.) are not stored in the conversation log.</small></p>');
        }

        // Show the modal
        $('#messageModal').modal('show');
        
        // Auto-scroll to bottom after modal is shown
        $('#messageModal').on('shown.bs.modal', function() {
            var messageContainer = $('#message-container');
            messageContainer.scrollTop(messageContainer[0].scrollHeight);
        });

        // Mark messages as read via AJAX
        $.ajax({
            url: '<?php echo base_url("whatsapp_controller/mark_messages_read"); ?>',
            type: 'POST',
            data: {log_id: logId},
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    // Get the mobile number from the current row
                    var mobileNumber = button.closest('tr').find('td:eq(1)').text().trim();
                    
                    // Disable all buttons with the same mobile number
                    $('#logsTable tbody tr').each(function() {
                        var rowMobile = $(this).find('td:eq(1)').text().trim();
                        if (rowMobile === mobileNumber) {
                            var rowButton = $(this).find('.view-messages');
                            rowButton.prop('disabled', true);
                            rowButton.text('No Messages');
                        }
                    });
                    
                    console.log('Messages marked as read successfully for mobile: ' + mobileNumber);
                } else {
                    console.error('Failed to mark messages as read:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
            }
        });
    });

    // Handle Clear All Messages button click
    $('#clearAllMessages').on('click', function() {
        if (confirm('Are you sure you want to clear ALL messages for ALL users? This action cannot be undone.')) {
            $.ajax({
                url: '<?php echo base_url("whatsapp_controller/clear_all_messages"); ?>',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        // Disable all View Messages buttons and change text
                        $('.view-messages').each(function() {
                            $(this).prop('disabled', true);
                            $(this).text('No Messages');
                        });
                        
                        // Show success message
                        alert('Successfully cleared ' + response.affected_rows + ' message records.');
                        console.log('All messages cleared successfully');
                    } else {
                        alert('Error: ' + response.message);
                        console.error('Failed to clear messages:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error occurred while clearing messages. Please try again.');
                    console.error('AJAX error:', error);
                }
            });
        }
    });
});
</script> 