<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1><i class="fas fa-search"></i> Search User Connection</h1>
          <small class="text-muted">Search user connection history from RADIUS accounting</small>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Search User Connection</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      

      
      <!-- Search Form -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-filter"></i> Search Filters
          </h3>
        </div>
        <div class="card-body">
          <form method="post" action="<?php echo base_url('Network_controller/searchUserDetails'); ?>" id="searchList">
            <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="record_type">Record Type</label>
                    <select class="form-control" id="record_type" name="record_type">
                      <option value="0" <?php echo (isset($filters['record_type']) && $filters['record_type'] == '0') ? 'selected' : ''; ?>>All Records</option>
                      <option value="1" <?php echo (isset($filters['record_type']) && $filters['record_type'] == '1') ? 'selected' : ''; ?>>Offline</option>
                      <option value="2" <?php echo (isset($filters['record_type']) && $filters['record_type'] == '2') ? 'selected' : ''; ?>>Online</option>
                      <option value="3" <?php echo (isset($filters['record_type']) && $filters['record_type'] == '3') ? 'selected' : ''; ?>>Distinct All</option>
                      <option value="4" <?php echo (isset($filters['record_type']) && $filters['record_type'] == '4') ? 'selected' : ''; ?>>Distinct Offline</option>
                      <option value="5" <?php echo (isset($filters['record_type']) && $filters['record_type'] == '5') ? 'selected' : ''; ?>>Distinct Online</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                 <div class="form-group">
                   <label for="username">Username</label>
                   <select class="form-control" id="username" name="username">
                     <?php if (isset($filters['username']) && !empty($filters['username'])): ?>
                       <option value="<?php echo htmlspecialchars($filters['username']); ?>" selected>
                         <?php echo htmlspecialchars($filters['username']); ?>
                       </option>
                     <?php endif; ?>
                   </select>
                 </div>
               </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="owner">Owner</label>
                  <select class="form-control" id="owner" name="owner">
                    <option value="">All Owners</option>
                    <?php if (!empty($managers)): ?>
                      <?php foreach ($managers as $manager): ?>
                        <option value="<?php echo htmlspecialchars($manager->managername); ?>" 
                                <?php echo (isset($filters['owner']) && $filters['owner'] == $manager->managername) ? 'selected' : ''; ?>>
                          <?php echo htmlspecialchars($manager->managername); ?>
                          <?php if (!empty($manager->firstname) || !empty($manager->lastname)): ?>
                            (<?php echo htmlspecialchars($manager->firstname . ' ' . $manager->lastname); ?>)
                          <?php endif; ?>
                        </option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="address">Address</label>
                  <input type="text" class="form-control" id="address" name="address" 
                         placeholder="Search by address" 
                         value="<?php echo isset($filters['address']) ? htmlspecialchars($filters['address']) : ''; ?>">
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label for="expiration_from">Expiration From</label>
                  <input type="date" class="form-control" id="expiration_from" name="expiration_from" 
                         value="<?php echo isset($filters['expiration_from']) ? htmlspecialchars($filters['expiration_from']) : ''; ?>">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="expiration_to">Expiration To</label>
                  <input type="date" class="form-control" id="expiration_to" name="expiration_to" 
                         value="<?php echo isset($filters['expiration_to']) ? htmlspecialchars($filters['expiration_to']) : ''; ?>">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="mobile">Mobile</label>
                  <input type="text" class="form-control" id="mobile" name="mobile" 
                         placeholder="Search by mobile" 
                         value="<?php echo isset($filters['mobile']) ? htmlspecialchars($filters['mobile']) : ''; ?>">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="taxid">ID Number</label>
                  <input type="text" class="form-control" id="taxid" name="taxid" 
                         placeholder="Search by ID number" 
                         value="<?php echo isset($filters['taxid']) ? htmlspecialchars($filters['taxid']) : ''; ?>">
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-12">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-search"></i> Search
                </button>
                <a href="<?php echo base_url('Network_controller/clearSearch'); ?>" class="btn btn-secondary">
                  <i class="fas fa-times"></i> Clear
                </a>
              </div>
            </div>
          </form>
        </div>
      </div>

                         <!-- Search Results -->
       <?php if (isset($searchResults) && !empty($searchResults)): ?>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-list"></i> Search Results
              <span class="badge badge-info"><?php echo count($searchResults); ?> records found</span>
            </h3>
          </div>
          <div class="card-body table-responsive p-0">
            <table class="table table-striped table-valign-middle">
              <thead>
                <tr>
                  <th>Username</th>
                  <th>Name</th>
                  <th>Owner</th>
                  <th>Expiration</th>
                  <th>NAS</th>
                  <th>Start Time</th>
                  <th>Stop Time</th>
                  <th>Session Time</th>
                  <th>IP Address</th>
                  <th>Cause</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($searchResults)): ?>
                  <?php foreach ($searchResults as $result): ?>
                    <tr>
                      <td>
                        <strong><?php echo htmlspecialchars($result->username); ?></strong>
                      </td>
                      <td>
                        <?php echo htmlspecialchars($result->firstname . ' ' . $result->lastname); ?>
                      </td>
                      <td>
                        <span class="badge badge-info">
                          <?php echo htmlspecialchars($result->owner); ?>
                        </span>
                      </td>
                     
                      <td>
                        <?php 
                        $expiration_date = new DateTime($result->expiration);
                        $current_date = new DateTime();
                        $is_expired = $expiration_date < $current_date;
                        ?>
                        <span class="badge <?php echo $is_expired ? 'badge-danger' : 'badge-success'; ?>">
                          <?php echo date('Y-m-d', strtotime($result->expiration)); ?>
                        </span>
                      </td>
                      <td>
                        <?php echo htmlspecialchars($result->nas_shortname ? $result->nas_shortname : $result->nasipaddress); ?>
                      </td>
                      <td>
                        <?php echo date('Y-m-d H:i:s', strtotime($result->acctstarttime)); ?>
                      </td>
                      <td>
                        <?php if ($result->acctstoptime): ?>
                          <?php echo date('Y-m-d H:i:s', strtotime($result->acctstoptime)); ?>
                        <?php else: ?>
                          <span class="badge badge-success">Online</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if ($result->acctsessiontime): ?>
                          <?php 
                          $hours = floor($result->acctsessiontime / 3600);
                          $minutes = floor(($result->acctsessiontime % 3600) / 60);
                          $seconds = $result->acctsessiontime % 60;
                          echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                          ?>
                        <?php else: ?>
                          <span class="text-muted">-</span>
                        <?php endif; ?>
                      </td>
                     
                      <td>
                        <?php echo htmlspecialchars($result->framedipaddress); ?>
                      </td>

                      <td>
                        <?php echo htmlspecialchars($result->acctterminatecause); ?>
                      </td>
                      

                      <td class="text-center">
                      <div class="btn-group">
                        <a class="btn btn-sm btn-primary mr-1" href="<?= base_url().'Userslist/userDashBoard/'.$result->username; ?>" title="Recharge">
                          <i class="fas fa-user"></i>
                        </a>
                        <a class="btn btn-sm btn-info mr-1" href="<?= base_url().'recharge/'.$result->username; ?>" title="View">
                          <i class="fas fa-exchange-alt"></i>
                        </a>

                        <a class="btn btn-sm btn-danger disconnect" href="#" data-username="<?php echo $result->username; ?>" title="Disconnect"><i class="fas fa-unlink"></i></a>
                      </div>
                    </td>

                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="13" class="text-center">No records found</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          
            <!-- Pagination -->
            <div class="card-footer clearfix">
                <?php if (!empty($pagination)): ?>
                    <div class="float-left">
                        <p class="text-muted">Showing results from search</p>
                    </div>
                    <div class="float-right">
                        <?php echo $pagination; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
       <?php elseif (empty($searchResults) || count($searchResults) == 0): ?>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-info-circle"></i> Welcome to User Details Search
            </h3>
          </div>
          <div class="card-body">
            <div class="alert alert-info">
              <h5><i class="icon fas fa-info"></i> How to use this search:</h5>
              <ul>
                <li><strong>Username:</strong> Search by username, first name, or last name (autocomplete available)</li>
                <li><strong>Owner:</strong> Filter by specific manager/owner</li>
                <li><strong>Expiration Date Range:</strong> Search users within specific expiration date range</li>
                <li><strong>Address:</strong> Search by any part of the address (uses LIKE query)</li>
                <li><strong>Mobile:</strong> Search by mobile number</li>
                <li><strong>ID Number:</strong> Search by tax ID number</li>
              </ul>
              <p class="mb-0">Click the <strong>Search</strong> button to find user connection history from the RADIUS accounting table.</p>
            </div>
          </div>
        </div>
      
      <?php endif; ?>

    </div>
  </section>
</div>

<!-- Select2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
/* Normalize height of Select2 and form controls */
.select2-container .select2-selection--single {
    height: 38px !important;
    padding: 0.375rem 0.75rem;
    border: 1px solid #ced4da;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 24px;
    padding-left: 0;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
}

/* Match Select2 border color with Bootstrap form-control */
.select2-container--default .select2-selection--single {
    border-color: #ced4da;
}

/* Consistent focus state */
.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}
</style>

 <script>
 $(document).ready(function() {

    jQuery('ul.pagination li a').click(function (e) {
        e.preventDefault();            
        var link = jQuery(this).get(0).href;            
        var value = link.substring(link.lastIndexOf('/') + 1);
        
        // Remove any existing page input
        jQuery("#searchList input[name='page']").remove();
        
        // Create a hidden input for the page number
        jQuery("#searchList").append('<input type="hidden" name="page" value="' + value + '">');
        jQuery("#searchList").attr("action", baseURL + "searchUserDetails/" + value);
        jQuery("#searchList").submit();
    });

    jQuery('.disconnect').click(function(e) {
            e.preventDefault();
            var username = $(this).data('username');
            if(confirm('Are you sure you want to disconnect this user?')) {
                jQuery.ajax({
                    //url: baseURL + 'Reports_controller/DisconnectUser/' + encodeURIComponent(username),

                    url: '<?php echo base_url("Network_controller/disconnectExpiredUser/"); ?>' + username,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if(response.status) {
                            alert('User ' + username + ' has been disconnected.');
                            // Optionally, remove the row or refresh the table
                            jQuery('tr#' + username).fadeOut();
                        } else {
                            alert('Failed to disconnect user ' + username + '.');
                        }
                    },
                    error: function() {
                        alert('Error disconnecting user ' + username + '.');
                    }
                });
            }
        });

     // Initialize Select2 for username field
     $('#username').select2({
         placeholder: 'Search username or name...',
         allowClear: true,
         ajax: {
             url: '<?php echo base_url("Network_controller/searchUsernamesAjax"); ?>',
             dataType: 'json',
             delay: 250,
             data: function(params) {
                 return {
                     q: params.term
                 };
             },
             processResults: function(data) {
                 return {
                     results: data.results
                 };
             },
             cache: true
         },
         minimumInputLength: 2
     });

     // Initialize Select2 for record_type field
     $('#record_type').select2({
         placeholder: 'Select record type...',
         minimumResultsForSearch: -1 // Disable search
     });

     // Initialize Select2 for owner field
     $('#owner').select2({
         placeholder: 'Select owner...',
         allowClear: true
     });

     // Set the selected value for username if it exists
     <?php if (isset($filters['username']) && !empty($filters['username'])): ?>
     var usernameValue = '<?php echo htmlspecialchars($filters['username']); ?>';
     var usernameOption = new Option(usernameValue, usernameValue, true, true);
     $('#username').append(usernameOption).trigger('change');
     <?php endif; ?>

     // Auto-submit form when filters change (optional)
     $('#owner, #expiration_from, #expiration_to').on('change', function() {
         // Uncomment the line below if you want auto-submit on filter change
         // $('form').submit();
     });
 });
</script> 