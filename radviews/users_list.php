<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1><i class="fas fa-users"></i> User Management <small>Add, Edit, Delete</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
            <li class="breadcrumb-item active">User Management</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <!-- Download section -->
      <div class="card card-custom">
        <div class="card-header">
          <h3 class="card-title">Download Customers List</h3>
        </div>
        <div class="card-body">
          <div class="user-list-actions">
            <a href="<?php echo base_url(); ?>usersListing/all" class="btn btn-info btn-nav">
              <i class="fas fa-list"></i> All List
            </a>
            <a href="<?php echo base_url(); ?>usersListing/active" class="btn btn-success btn-nav">
              <i class="fas fa-check-circle"></i> Active
            </a>
            <a href="<?php echo base_url(); ?>usersListing/expired" class="btn btn-danger btn-nav">
              <i class="fas fa-times-circle"></i> Expired
            </a>
            <a href="<?php echo base_url(); ?>usersListing/blocked" class="btn btn-warning btn-nav">
              <i class="fas fa-ban"></i> Blocked
            </a>
            <a href="<?php echo base_url(); ?>addNew" class="btn btn-primary btn-nav float-right">
              <i class="fas fa-plus"></i> Add New
            </a>
          </div>
        </div>
      </div>

      <!-- Users list section -->
      <div class="card card-custom">
        <div class="card-header">
          <h3 class="card-title">Users List</h3>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <div class="form-group">
                <select class="form-control" id="userStatus">
                  <option value="active">Active Users</option>
                  <option value="expired">Expired Users</option>
                  <option value="blocked">Blocked Users</option>
                  <option value="all">All Users</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group search-box">
                <input type="text" class="form-control" placeholder="Search" id="searchInput">
                <div class="input-group-append">
                  <button type="button" class="btn btn-default" id="searchBtn">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="table-responsive-custom">
            <table class="table table-custom">
              <thead>
                <tr>
                  <th style="width: 50px">S.No.</th>
                  <th>User Name<br>Online/Offline</th>
                  <th>Name</th>
                  <th>Owner</th>
                  <th>Service</th>
                  <th>PayID</th>
                  <th>Status</th>
                  <th>CNIC</th>
                  <th>Expiration</th>
                  <th>Contact</th>
                  <th>Created On</th>
                  <th style="width: 120px">Recharge | Manage</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if(!empty($userRecords))
                {
                  $count = 1;
                  foreach($userRecords as $record)
                  {
                ?>
                <tr>
                  <td><?php echo $count++; ?>.</td>
                  <td>
                    <a href="<?php echo base_url().'usersDashboard/'.$record->username; ?>" class="btn btn-info btn-sm btn-block btn-user-block">
                      <?php echo $record->username; ?>
                    </a>
                  </td>
                  <td><?php echo 'Mr '.$record->firstname; ?></td>
                  <td><?php echo $record->owner; ?></td>
                  <td>Hotspot<br>Prepaid</td>
                  <td><?php echo !empty($record->payid) ? $record->payid : 'N/A'; ?></td>
                  <td>
                    <?php if($record->status == 'Active'): ?>
                      <span class="badge badge-active"><?php echo $record->status; ?></span>
                    <?php elseif($record->status == 'Expired'): ?>
                      <span class="badge badge-expired"><?php echo $record->status; ?></span>
                    <?php elseif($record->status == 'Blocked'): ?>
                      <span class="badge badge-blocked"><?php echo $record->status; ?></span>
                    <?php else: ?>
                      <?php echo $record->status; ?>
                    <?php endif; ?>
                  </td>
                  <td class="text-center">
                    <button class="btn btn-warning btn-sm">
                      <i class="fas fa-id-card"></i>
                    </button>
                  </td>
                  <td><?php echo date('d-m-Y', strtotime($record->expiration)); ?></td>
                  <td><?php echo $record->mobile; ?></td>
                  <td><?php echo date('d-m-Y', strtotime($record->createdon)); ?></td>
                  <td>
                    <div class="btn-group">
                      <a href="<?php echo base_url().'rechargeUser/'.$record->username; ?>" class="btn btn-primary btn-sm" title="Recharge">
                        <i class="fas fa-sync-alt"></i>
                      </a>
                      <a href="<?php echo base_url().'editOld/'.$record->username; ?>" class="btn btn-info btn-sm" title="View">
                        <i class="fas fa-eye"></i>
                      </a>
                      <a href="<?php echo base_url().'deleteUser/'.$record->username; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure to delete this user?')" title="Delete">
                        <i class="fas fa-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
                <?php
                  }
                }
                else
                {
                ?>
                <tr>
                  <td colspan="12" class="text-center">No records found</td>
                </tr>
                <?php
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer clearfix">
          <ul class="pagination pagination-sm pagination-custom">
            <?php echo $this->pagination->create_links(); ?>
          </ul>
        </div>
      </div>
      <!-- /.card -->
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
$(document).ready(function() {
  // Search functionality
  $('#searchBtn').click(function() {
    var searchTerm = $('#searchInput').val();
    var status = $('#userStatus').val();
    if(searchTerm.trim() != '') {
      window.location.href = baseURL + 'usersListing/' + status + '/' + searchTerm;
    }
  });

  // Enter key for search
  $('#searchInput').keypress(function(e) {
    if(e.which == 13) {
      $('#searchBtn').click();
    }
  });
});
</script> 