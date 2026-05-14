<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-users"></i> Agents Management
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Agents</li>
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
                            <h3 class="card-title">Agents User List</h3>
                            <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#globalRequestCodeModal"><i class="fas fa-barcode"></i> Request User Code</button>
                        </div><!-- /.card-header -->
                        
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap" id="apiUsersTable">
                                <thead>
                                    <tr>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Owner</th>
                                        <th>Request Code</th>
                                        <th>Registered At</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($apiUsersRecords)): ?>
                                        <?php foreach($apiUsersRecords as $record): ?>
                                        <tr id="row-<?php echo $record->id; ?>">
                                            <td><?php echo $record->email; ?></td>
                                            <td>
                                                <?php if($record->is_active): ?>
                                                    <span class="badge badge-success status-label">Active</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger status-label">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="owner-cell"><?php echo (empty($record->owner) || strtolower(trim($record->owner)) === 'pppoe') ? '<i>Unassigned (pppoe)</i>' : $record->owner; ?></td>
                                            <td class="code-cell"><?php echo empty($record->requestcode) ? '-' : $record->requestcode; ?></td>
                                            <td><?php echo date("Y-m-d H:i", strtotime($record->created_at)); ?></td>
                                            <td class="text-center">
                                                <!-- Toggle Active Button -->
                                                <button class="btn btn-sm btn-<?php echo $record->is_active ? 'warning' : 'success'; ?> toggle-btn" data-id="<?php echo $record->id; ?>" data-status="<?php echo $record->is_active ? 'false' : 'true'; ?>">
                                                    <i class="fas fa-power-off"></i> <?php echo $record->is_active ? 'Deactivate' : 'Activate'; ?>
                                                </button>

                                                <!-- Credit Button -->
                                                <button class="btn btn-sm btn-primary credit-btn" data-id="<?php echo $record->id; ?>" data-email="<?php echo $record->email; ?>">
                                                    <i class="fas fa-money-bill-wave"></i> Credit
                                                </button>

                                                <!-- Request Code Button -->
                                                <button class="btn btn-sm btn-info request-btn" data-id="<?php echo $record->id; ?>">
                                                    <i class="fas fa-barcode"></i> Request
                                                </button>

                                                <!-- Approve Owner Button -->
                                                <?php if(empty($record->owner) || strtolower(trim($record->owner)) === 'pppoe'): ?>
                                                <button class="btn btn-sm btn-success approve-btn" data-id="<?php echo $record->id; ?>">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No API Users Found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div><!-- /.card-body -->
                    </div><!-- /.card -->
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
</div>

<!-- Credit Modal -->
<div class="modal fade" id="creditModal" tabindex="-1" role="dialog" aria-labelledby="creditModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="creditModalLabel">Add/Deduct Credit for <span id="creditUserEmail"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="creditForm">
            <input type="hidden" id="creditUserId" name="id">
            <div class="form-group">
                <label for="creditAmount">Amount (Use negative for deduction)</label>
                <input type="number" step="0.01" class="form-control" id="creditAmount" name="amount" required placeholder="e.g. 100.00 or -50.00">
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveCreditBtn">Apply Credit</button>
      </div>
    </div>
  </div>
</div>

<!-- Global Request Code Modal -->
<div class="modal fade" id="globalRequestCodeModal" tabindex="-1" role="dialog" aria-labelledby="globalRequestCodeModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="globalRequestCodeModalLabel">Generate Request Code</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="globalRequestCodeForm">
            <div class="form-group">
                <label for="requestCodeEmail">Target Email Address</label>
                <input type="email" class="form-control" id="requestCodeEmail" name="email" required placeholder="Enter API user's email">
            </div>

            <?php if ($this->session->userdata('role') == 1 || strtolower($this->session->userdata('name')) === 'admin'): ?>
            <div class="form-group">
                <label for="requestCodeOwner">Assign Owner (Admin Only)</label>
                <input type="text" class="form-control" id="requestCodeOwner" name="owner" placeholder="Enter manager's username (optional)">
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="requestCodeInput">Assigned Request Code</label>
                <input type="text" class="form-control" id="requestCodeInput" name="code" required>
                <small class="form-text text-muted">Provide this exact code to the API User so they can register their account.</small>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveGlobalRequestCodeBtn">Save Code</button>
      </div>
    </div>
  </div>
</div>

<!-- jQuery / AJAX Scripting for the UI Buttons -->
<script type="text/javascript">
jQuery(document).ready(function(){
    // Toggle Active Status
    $('.toggle-btn').on('click', function(){
        var btn = $(this);
        var id = btn.data('id');
        var newStatus = btn.data('status'); // The target status ('true' or 'false')
        
        $.ajax({
            url: "<?php echo base_url(); ?>Api_apps/toggle_active",
            type: "POST",
            data: { id: id, status: newStatus },
            dataType: "json",
            success: function(response){
                if(response.status === 'success'){
                    location.reload(); // Simple reload to refresh UI colors/text
                } else {
                    alert(response.message);
                }
            }
        });
    });

    // Request Code Generator
    $('.request-btn').on('click', function(){
        var btn = $(this);
        var id = btn.data('id');
        
        $.ajax({
            url: "<?php echo base_url(); ?>Api_apps/generate_request_code",
            type: "POST",
            data: { id: id },
            dataType: "json",
            success: function(response){
                if(response.status === 'success'){
                    $('#row-' + id + ' .code-cell').text(response.code);
                    alert("Generated Code: " + response.code);
                } else {
                    alert(response.message);
                }
            }
        });
    });

    // Approve Owner Action
    $('.approve-btn').on('click', function(){
        var btn = $(this);
        var id = btn.data('id');
        
        if(!confirm("Are you sure you want to take ownership of this API account?")){
            return false;
        }

        $.ajax({
            url: "<?php echo base_url(); ?>Api_apps/approve_owner",
            type: "POST",
            data: { id: id },
            dataType: "json",
            success: function(response){
                if(response.status === 'success'){
                    location.reload();
                } else {
                    alert(response.message);
                }
            }
        });
    });

    // Open Credit Modal
    $('.credit-btn').on('click', function(){
        var id = $(this).data('id');
        var email = $(this).data('email');
        
        $('#creditUserId').val(id);
        $('#creditUserEmail').text(email);
        $('#creditAmount').val(''); // reset
        $('#creditModal').modal('show');
    });

    // Submit Credit Form
    $('#saveCreditBtn').on('click', function(){
        var id = $('#creditUserId').val();
        var amount = $('#creditAmount').val();
        
        if(!amount || amount == 0){
            alert("Please enter a valid amount (cannot be 0).");
            return false;
        }

        $.ajax({
            url: "<?php echo base_url(); ?>Api_apps/add_credit",
            type: "POST",
            data: { id: id, amount: amount },
            dataType: "json",
            success: function(response){
                if(response.status === 'success'){
                    alert("Credit transaction logged successfully!");
                    $('#creditModal').modal('hide');
                } else {
                    alert(response.message);
                }
            }
        });
    });

    // Open Global Request Code Modal & Auto-Generate Code
    $('#globalRequestCodeModal').on('show.bs.modal', function () {
        // Clear previous email
        $('#requestCodeEmail').val('');
        
        // Auto-generate random 5-digit string and put it in input
        var randomNum = Math.floor(10000 + Math.random() * 90000);
        $('#requestCodeInput').val(randomNum);
    });

    // Submit Global Request Code Form
    $('#saveGlobalRequestCodeBtn').on('click', function(){
        var email = $('#requestCodeEmail').val();
        var code = $('#requestCodeInput').val();
        var owner = $('#requestCodeOwner').length ? $('#requestCodeOwner').val() : '';
        
        if(!email || !code){
            alert("Please provide both a valid email address and a request code.");
            return false;
        }

        $.ajax({
            url: "<?php echo base_url(); ?>Api_apps/update_request_code_by_email",
            type: "POST",
            data: { email: email, code: code, owner: owner },
            dataType: "json",
            success: function(response){
                if(response.status === 'success'){
                    alert(response.message + " (Code: " + response.code + ")");
                    $('#globalRequestCodeModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.message);
                }
            }
        });
    });
});
</script>
