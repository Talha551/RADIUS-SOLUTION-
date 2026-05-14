<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1><i class="fas fa-exclamation-triangle"></i> Expired Online Users</h1>
          <small class="text-muted">
            <?php if ($nasip): ?>
              NAS: <?php echo htmlspecialchars($nasip); ?>
            <?php else: ?>
              All NAS Devices
            <?php endif; ?>
          </small>
        </div>
        <div class="col-sm-6">
          <div class="d-flex justify-content-end align-items-center">
            <?php if (!empty($expired_users)): ?>
              <button id="disconnectAllBtn" class="btn btn-danger btn-sm mr-3">
                <i class="fas fa-power-off"></i> Disconnect All (<?php echo count($expired_users); ?>)
              </button>
            <?php endif; ?>
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item"><a href="<?php echo base_url('userDashboard'); ?>">Dashboard</a></li>
              <li class="breadcrumb-item active">Expired Online Users</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <!-- Summary Card -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-info-circle"></i> Summary
              </h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-3">
                  <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Total Expired Online</span>
                      <span class="info-box-number"><?php echo count($expired_users); ?></span>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-network-wired"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">NAS Devices</span>
                      <span class="info-box-number">
                        <?php 
                        $nas_count = 0;
                        if (!empty($expired_users)) {
                            $nas_ips = array();
                            foreach ($expired_users as $user) {
                                if (!in_array($user->nasipaddress, $nas_ips)) {
                                    $nas_ips[] = $user->nasipaddress;
                                }
                            }
                            $nas_count = count($nas_ips);
                        }
                        echo $nas_count;
                        ?>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-user-tie"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Owners</span>
                      <span class="info-box-number">
                        <?php 
                        $owner_count = 0;
                        if (!empty($expired_users)) {
                            $owners = array();
                            foreach ($expired_users as $user) {
                                if (!in_array($user->owner, $owners)) {
                                    $owners[] = $user->owner;
                                }
                            }
                            $owner_count = count($owners);
                        }
                        echo $owner_count;
                        ?>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Oldest Expired</span>
                      <span class="info-box-number">
                        <?php 
                        $oldest_expired = 'N/A';
                        if (!empty($expired_users)) {
                            $oldest_date = null;
                            foreach ($expired_users as $user) {
                                if ($oldest_date === null || $user->expiration < $oldest_date) {
                                    $oldest_date = $user->expiration;
                                }
                            }
                            if ($oldest_date) {
                                $oldest_expired = date('M d', strtotime($oldest_date));
                            }
                        }
                        echo $oldest_expired;
                        ?>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Expired Users Table -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-list"></i> Expired Online Users List
              </h3>
            </div>
            <div class="card-body table-responsive p-0">
              <?php if (!empty($expired_users)): ?>
                <table class="table table-striped table-valign-middle">
                  <thead>
                    <tr>
                      <th>Username</th>
                      <th>Name</th>
                      <th>Owner</th>
                      <th>NAS</th>
                      <th>IP Address</th>
                      <th>Online Since</th>
                      <th>Expired Date</th>
                      <th>Days Expired</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($expired_users as $user): ?>
                      <?php 
                      $expired_date = new DateTime($user->expiration);
                      $current_date = new DateTime();
                      $days_expired = $current_date->diff($expired_date)->days;
                      ?>
                      <tr id="user-<?php echo htmlspecialchars($user->username); ?>">
                        <td>
                          <strong><?php echo htmlspecialchars($user->username); ?></strong>
                        </td>
                        <td>
                          <?php echo htmlspecialchars($user->firstname . ' ' . $user->lastname); ?>
                        </td>
                        <td>
                          <span class="badge badge-info">
                            <?php echo htmlspecialchars($user->owner); ?>
                          </span>
                        </td>
                        <td>
                          <?php echo htmlspecialchars($user->nas_shortname ? $user->nas_shortname : $user->nasipaddress); ?>
                          <br>
                          <small class="text-muted"><?php echo htmlspecialchars($user->nasipaddress); ?></small>
                        </td>
                        <td>
                          <code><?php echo htmlspecialchars($user->framedipaddress); ?></code>
                        </td>
                        <td>
                          <?php echo date('M d, Y H:i', strtotime($user->acctstarttime)); ?>
                        </td>
                        <td>
                          <span class="text-danger">
                            <?php echo date('M d, Y', strtotime($user->expiration)); ?>
                          </span>
                        </td>
                        <td>
                          <span class="badge badge-danger">
                            <?php echo $days_expired; ?> days
                          </span>
                        </td>
                        <td>
                          <button class="btn btn-sm btn-danger disconnect-user" 
                                  data-username="<?php echo htmlspecialchars($user->username); ?>">
                            <i class="fas fa-power-off"></i> Disconnect
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <div class="text-center p-5">
                  <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                  <h4>No Expired Online Users Found</h4>
                  <p class="text-muted">All expired users are currently offline.</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
$(document).ready(function() {
    // Disconnect individual user
    $('.disconnect-user').on('click', function() {
        var username = $(this).data('username');
        var $btn = $(this);
        var $row = $btn.closest('tr');
        
        if (confirm('Are you sure you want to disconnect user "' + username + '"?')) {
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Disconnecting...');
            
            $.ajax({
                url: '<?php echo base_url("Network_controller/disconnectExpiredUser/"); ?>' + username,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $row.fadeOut(function() {
                            $(this).remove();
                            // Update count
                            var remainingUsers = $('tbody tr').length;
                            if (remainingUsers === 0) {
                                location.reload(); // Reload to show "no users" message
                            } else {
                                $('#disconnectAllBtn').text('Disconnect All (' + remainingUsers + ')');
                            }
                        });
                        // Show success message
                        toastr.success('User ' + username + ' has been disconnected successfully.');
                    } else {
                        toastr.error('Failed to disconnect user: ' + response.message);
                        $btn.prop('disabled', false).html('<i class="fas fa-power-off"></i> Disconnect');
                    }
                },
                error: function() {
                    toastr.error('Error disconnecting user ' + username);
                    $btn.prop('disabled', false).html('<i class="fas fa-power-off"></i> Disconnect');
                }
            });
        }
    });
    
    // Disconnect all users
    $('#disconnectAllBtn').on('click', function() {
        var totalUsers = <?php echo count($expired_users); ?>;
        var $btn = $(this);
        
        if (confirm('Are you sure you want to disconnect all ' + totalUsers + ' expired users?')) {
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Disconnecting All...');
            
            $.ajax({
                url: '<?php echo base_url("Network_controller/disconnectAllExpiredUsers/"); ?><?php echo $nasip ? "/" . urlencode($nasip) : ""; ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success('Successfully disconnected ' + response.disconnected + ' users.');
                        location.reload(); // Reload to show updated status
                    } else {
                        toastr.error('Failed to disconnect users: ' + response.message);
                        $btn.prop('disabled', false).html('<i class="fas fa-power-off"></i> Disconnect All (' + totalUsers + ')');
                    }
                },
                error: function() {
                    toastr.error('Error disconnecting users');
                    $btn.prop('disabled', false).html('<i class="fas fa-power-off"></i> Disconnect All (' + totalUsers + ')');
                }
            });
        }
    });
});
</script> 