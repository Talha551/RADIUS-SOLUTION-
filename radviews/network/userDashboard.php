<div class="content-wrapper">
  

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1><i class="fas fa-users"></i> ISP Connectivity Status</h1>
          <small class="text-muted">Online/Offline Statistics</small>
          <small id="dashboardLastUpdated" class="text-muted d-block" style="font-size:13px;margin-top:2px;">
            Last updated: 
            <?php 
            // Get the last refresh timestamp from temp_dashboard_activity table
            $last_refresh = null;
            if ($this->db->table_exists('temp_dashboard_activity')) {
                $result = $this->db->query("SELECT MAX(created_at) as last_refresh FROM temp_dashboard_activity");
                if ($result && $result->num_rows() > 0) {
                    $last_refresh = $result->row()->last_refresh;
                }
            }
            if ($last_refresh) {
                echo '<span class="text-info">' . date('M d, Y H:i:s', strtotime($last_refresh)) . '</span>';
            } else {
                echo '<span class="text-info">Click refresh button to update dashboard data</span>';
            }
            ?>
          </small>
        </div>
        <div class="col-sm-6">
          <div class="d-flex justify-content-end align-items-center">
            <button id="refreshDashboardBtn" class="btn btn-info btn-sm mr-3"><i class="fas fa-sync-alt"></i> Refresh Dashboard</button>
            <button id="abortAjaxBtn" class="btn btn-danger btn-sm mr-3"><i class="fas fa-ban"></i> Abort Requests</button>
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">User Dashboard</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid position-relative" id="dashboardStatsContainer">
      <div id="dashboardRefreshSpinner" style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.7);z-index:1000;display:none;align-items:center;justify-content:center;font-size:2em;">
        <span><i class="fas fa-spinner fa-spin"></i> Refreshing...</span>
      </div>
      
      <?php if ($this->session->userdata('name') == 'admin'){ ?>
      <!-- Overall Statistics -->
      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3><?php echo isset($overall_stats->total_users) ? $overall_stats->total_users : 0; ?></h3>
              <p>Total Users</p>
            </div>
            <div class="icon">
              <i class="fas fa-users"></i>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3><?php echo isset($overall_stats->total_online_users) ? $overall_stats->total_online_users : 0; ?></h3>
              <p>Online Users</p>
            </div>
            <div class="icon">
              <i class="fas fa-wifi"></i>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3><?php echo isset($overall_stats->total_active_users) ? $overall_stats->total_active_users : 0; ?></h3>
              <p>Active Users</p>
            </div>
            <div class="icon">
              <i class="fas fa-check-circle"></i>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-6">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3><?php echo isset($overall_stats->total_expired_users) ? $overall_stats->total_expired_users : 0; ?></h3>
              <p>Expired Users</p>
            </div>
            <div class="icon">
              <i class="fas fa-times-circle"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Detailed Statistics -->
      <div class="row">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-chart-pie"></i> Active Users Status
              </h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-wifi"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Online</span>
                      <span class="info-box-number"><?php echo isset($overall_stats->active_online_users) ? $overall_stats->active_online_users : 0; ?></span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="info-box bg-secondary">
                    <span class="info-box-icon"><i class="fas fa-power-off"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Offline</span>
                      <span class="info-box-number"><?php echo isset($overall_stats->active_offline_users) ? $overall_stats->active_offline_users : 0; ?></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-chart-pie"></i> Expired Users Status
              </h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-wifi"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Online</span>
                      <span class="info-box-number"><?php echo isset($overall_stats->expired_online_users) ? $overall_stats->expired_online_users : 0; ?></span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-power-off"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Offline</span>
                      <span class="info-box-number"><?php echo isset($overall_stats->expired_offline_users) ? $overall_stats->expired_offline_users : 0; ?></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php } ?>

      <!-- Owner Statistics -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-chart-bar"></i> User Statistics by Owner
              </h3>
            </div>
            <div class="card-body table-responsive p-0">
              <table class="table table-striped table-valign-middle" id="ownerStatsTable">
                <thead>
                  <tr>
                    <th>Owner</th>
                    <th>Active Users</th>
                    <th>Expired Users</th>
                    <th>Online Users</th>
                    <th>Offline Users</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($owner_stats)): ?>
                    <?php 
                    $total_owners = count($owner_stats);
                    $initial_display = 10;
                    $counter = 0;
                    ?>
                    <?php foreach ($owner_stats as $owner): ?>
                      <?php $counter++; ?>
                      <tr class="owner-row <?php echo $counter > $initial_display ? 'hidden-row' : ''; ?>">
                        <td>
                          <strong><?php echo htmlspecialchars($owner['owner']); ?></strong>
                        </td>
                        <td>
                          <span class="badge badge-success">
                            <?php echo $owner['total_active']; ?>
                          </span>
                        </td>
                        <td>
                          <span class="badge badge-danger">
                            <?php echo $owner['total_expired']; ?>
                          </span>
                        </td>
                        <td>
                          <span class="badge badge-info">
                            <?php echo $owner['total_online']; ?>
                          </span>
                        </td>
                        <td>
                          <span class="badge badge-secondary">
                            <?php echo $owner['total_offline']; ?>
                          </span>
                        </td>
                        <td>
                          <strong><?php echo $owner['total_active'] + $owner['total_expired']; ?></strong>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="6" class="text-center">No data available</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
              
              <?php if (!empty($owner_stats) && $total_owners > $initial_display): ?>
                <div class="text-center p-3">
                  <button type="button" class="btn btn-primary" id="showMoreOwners">
                    <i class="fas fa-plus"></i> Show More (<?php echo $total_owners - $initial_display; ?> more)
                  </button>
                  <button type="button" class="btn btn-secondary" id="showLessOwners" style="display: none;">
                    <i class="fas fa-minus"></i> Show Less
                  </button>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>


      <?php if ($this->session->userdata('name') == 'admin'){ ?>

      <!-- NAS Statistics -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-network-wired"></i> Online Users by NAS
              </h3>
            </div>
            <div class="card-body table-responsive p-0">
              <table class="table table-striped table-valign-middle" id="nasStatsTable">
                <thead>
                  <tr>
                    <th>NAS Name</th>
                    <th>Total Online</th>
                    <th>Active Online</th>
                    <th>Expired Online</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($nas_stats)): ?>
                    <?php 
                    $total_nas = count($nas_stats);
                    $initial_display_nas = 5;
                    $counter_nas = 0;
                    ?>
                    <?php foreach ($nas_stats as $nas): ?>
                      <?php $counter_nas++; ?>
                      <tr class="nas-row <?php echo $counter_nas > $initial_display_nas ? 'hidden-row' : ''; ?>">
                        <td>
                          <strong><?php echo htmlspecialchars($nas->shortname ? $nas->shortname : $nas->nasname); ?></strong>
                          <br>
                          <small class="text-muted"><?php echo htmlspecialchars($nas->nasname); ?></small>
                        </td>
                        <td>
                          <span class="badge badge-primary">
                            <?php echo $nas->online_users; ?>
                          </span>
                        </td>
                        <td>
                          <span class="badge badge-success">
                            <?php echo $nas->active_online_users; ?>
                          </span>
                        </td>
                        <td>
                          <span class="badge badge-warning">
                            <?php echo $nas->expired_online_users; ?>
                          </span>
                        </td>
                        <td>
                          <?php if ($nas->expired_online_users > 0): ?>
                            <a href="<?php echo base_url('Network_controller/expiredOnlineUsers/'.$nas->nasname); ?>" 
                               class="btn btn-sm btn-danger">
                              <i class="fas fa-power-off"></i> Force Disconnect
                            </a>
                          <?php else: ?>
                            <span class="text-muted">-</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="5" class="text-center">No online users found</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
              
              <?php if (!empty($nas_stats) && $total_nas > $initial_display_nas): ?>
                <div class="text-center p-3">
                  <button type="button" class="btn btn-primary" id="showMoreNas">
                    <i class="fas fa-plus"></i> Show More (<?php echo $total_nas - $initial_display_nas; ?> more)
                  </button>
                  <button type="button" class="btn btn-secondary" id="showLessNas" style="display: none;">
                    <i class="fas fa-minus"></i> Show Less
                  </button>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <?php } ?>

      <!-- Recent Activity -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-history"></i> Recent Activity (Last 5 Hours)
              </h3>
            </div>
            <div class="card-body table-responsive p-0">
              <table class="table table-striped table-valign-middle" id="recentActivityTable">
                <thead>
                  <tr>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Owner</th>
                    <th>NAS</th>
                    <th>Status</th>
                    <th>Start Time</th>
                    <th>Stop Time</th>
                    <th>Termination Cause</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($recent_activity)): ?>
                    <?php 
                    $total_activities = count($recent_activity);
                    $initial_display_activity = 10;
                    $counter_activity = 0;
                    ?>
                    <?php foreach ($recent_activity as $activity): ?>
                      <?php $counter_activity++; ?>
                      <tr class="activity-row <?php echo $counter_activity > $initial_display_activity ? 'hidden-row' : ''; ?>">
                        <td>
                          <strong><?php echo htmlspecialchars($activity->username); ?></strong>
                        </td>
                        <td>
                          <?php echo htmlspecialchars($activity->firstname . ' ' . $activity->lastname); ?>
                        </td>
                        <td>
                          <span class="badge badge-info">
                            <?php echo htmlspecialchars($activity->owner); ?>
                          </span>
                        </td>
                        <td>
                          <?php echo htmlspecialchars($activity->nas_shortname ? $activity->nas_shortname : $activity->nasipaddress); ?>
                        </td>
                        <td>
                          <?php if ($activity->status === 'Online'): ?>
                            <span class="badge badge-success">Online</span>
                          <?php else: ?>
                            <span class="badge badge-secondary">Offline</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php echo date('M d, Y H:i:s', strtotime($activity->acctstarttime)); ?>
                        </td>
                        <td>
                          <?php if ($activity->acctstoptime): ?>
                            <?php echo date('M d, Y H:i:s', strtotime($activity->acctstoptime)); ?>
                          <?php else: ?>
                            <span class="text-muted">-</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if ($activity->acctterminatecause): ?>
                            <span class="badge badge-warning">
                              <?php echo htmlspecialchars($activity->acctterminatecause); ?>
                            </span>
                          <?php else: ?>
                            <span class="text-muted">-</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="8" class="text-center">No recent activity found</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
              
              <?php if (!empty($recent_activity) && $total_activities > $initial_display_activity): ?>
                <div class="text-center p-3">
                  <button type="button" class="btn btn-primary" id="showMoreActivities">
                    <i class="fas fa-plus"></i> Show More (<?php echo $total_activities - $initial_display_activity; ?> more)
                  </button>
                  <button type="button" class="btn btn-secondary" id="showLessActivities" style="display: none;">
                    <i class="fas fa-minus"></i> Show Less
                  </button>
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
// Add global variables for tracking AJAX requests and dashboard state
var activeAjaxRequests = [];
var dashboardForceRefresh = false;

// Helper function to create and track AJAX requests
function createTrackedAjaxRequest(url, options) {
    var req = $.ajax(url, options);
    activeAjaxRequests.push(req);
    req.always(function() {
        var idx = activeAjaxRequests.indexOf(req);
        if (idx > -1) activeAjaxRequests.splice(idx, 1);
    });
    return req;
}

function showDashboardSpinner(show) {
    if (show) {
        $('#dashboardRefreshSpinner').show();
    } else {
        $('#dashboardRefreshSpinner').hide();
    }
}

function loadDashboardInfo(forceRefresh = false) {
    dashboardForceRefresh = forceRefresh;
    if (forceRefresh) {
        showDashboardSpinner(true);
        showRefreshInProgress();
    }
    let url = "<?php echo base_url('Other/createUserDashboardCache'); ?>";
    if (forceRefresh) url += "?refresh=1";
    
    // Use tracked AJAX request
    createTrackedAjaxRequest(url, {
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.status === 'success') {
                // Update last updated timestamp
                if (data.timestamp) {
                    $('#dashboardLastUpdated').html('Last updated: <span class="text-info">' + data.timestamp + '</span>');
                } else {
                    $('#dashboardLastUpdated').html('Last updated: <span class="text-info">Click refresh button to update dashboard data</span>');
                }

                showDashboardSpinner(false);
                hideRefreshInProgress();
                // Refresh the page to show updated data
                if (forceRefresh) {
                    location.reload();
                } else {
                    // For silent refresh, just update the timestamp
                    console.log('Silent refresh completed');
                }
            } else {
                showDashboardSpinner(false);
                hideRefreshInProgress();
                console.log('Dashboard refresh failed:', data.message);
            }
        },
        error: function() {
            showDashboardSpinner(false);
            hideRefreshInProgress();
            console.log('Dashboard refresh failed');
        }
    });
}

// Show refresh in progress message in tables
function showRefreshInProgress() {
    // Add blinking CSS with better contrast
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .refresh-in-progress {
                animation: blink 1.5s infinite;
                color: #ffffff !important;
                font-weight: bold;
                text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
            }
            .refresh-in-progress-small {
                animation: blink 1.5s infinite;
                color: #ffffff !important;
                font-weight: bold;
                font-size: 0.8em;
                text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
            }
            @keyframes blink {
                0% { opacity: 1; }
                50% { opacity: 0.7; }
                100% { opacity: 1; }
            }
        `)
        .appendTo('head');
    
    // Replace summary card values with spinners only if they are 0 (no data)
    if ($('.small-box').length) {
        $('.small-box .inner h3').each(function() {
            var currentValue = $(this).text().trim();
            // Only show refreshing if the value is 0 or empty
            if (currentValue === '0' || currentValue === '' || currentValue === 'null') {
                $(this).html('<span class="refresh-in-progress-small"><i class="fas fa-sync-alt fa-spin"></i> Refreshing...</span>');
            }
        });
        
        // Also update info-box numbers only if they are 0
        $('.info-box .info-box-number').each(function() {
            var currentValue = $(this).text().trim();
            // Only show refreshing if the value is 0 or empty
            if (currentValue === '0' || currentValue === '' || currentValue === 'null') {
                $(this).html('<span class="refresh-in-progress-small"><i class="fas fa-sync-alt fa-spin"></i> Refreshing...</span>');
            }
        });
    }
    
    // Replace table content with refresh message
    $('#ownerStatsTable tbody').html('<tr><td colspan="6" class="text-center refresh-in-progress"><i class="fas fa-sync-alt fa-spin"></i> Data refresh in progress...</td></tr>');
    $('#recentActivityTable tbody').html('<tr><td colspan="8" class="text-center refresh-in-progress"><i class="fas fa-sync-alt fa-spin"></i> Data refresh in progress...</td></tr>');
    
    // For admin users, also update NAS table
    if ($('#nasStatsTable').length) {
        $('#nasStatsTable tbody').html('<tr><td colspan="5" class="text-center refresh-in-progress"><i class="fas fa-sync-alt fa-spin"></i> Data refresh in progress...</td></tr>');
    }
    
    // Hide pagination buttons during refresh
    $('.text-center button').hide();
}

// Hide refresh in progress message
function hideRefreshInProgress() {
    // Remove the blinking CSS
    $('style').each(function() {
        if ($(this).html().indexOf('refresh-in-progress') !== -1) {
            $(this).remove();
        }
    });
    
    // Show pagination buttons again
    $('.text-center button').show();
    
    // Note: The summary card values will be restored when the page reloads
    // after the refresh completes, so we don't need to manually restore them here
}

// Check if refresh is in progress when page loads
function checkRefreshStatusOnLoad() {
    // Check if there's a recent cache refresh in progress
    $.ajax({
        url: "<?php echo base_url('Other/checkDataStatus'); ?>",
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            // If data is not ready, show refresh in progress
            if (data.status === 'not_ready') {
                showRefreshInProgress();
                $('#dashboardLastUpdated').html('Last updated: <span class="text-warning">Data refresh in progress...</span>');
                
                // Start monitoring for completion
                startRefreshMonitoring();
            }
        },
        error: function() {
            // Silently fail on error
        }
    });
}

// Monitor refresh completion
function startRefreshMonitoring() {
    let checkCount = 0;
    const maxChecks = 60; // Check for 10 minutes (60 * 10 seconds)
    
    const checkStatus = function() {
        checkCount++;
        
        $.ajax({
            url: "<?php echo base_url('Other/checkDataStatus'); ?>",
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.status === 'ready') {
                    // Refresh completed, reload page to show new data
                    hideRefreshInProgress();
                    location.reload();
                    return; // Stop monitoring
                }
                
                // Continue monitoring if not ready and haven't exceeded max checks
                if (checkCount < maxChecks) {
                    setTimeout(checkStatus, 10000); // Check every 10 seconds
                } else {
                    hideRefreshInProgress();
                    $('#dashboardLastUpdated').html('Last updated: <span class="text-danger">Refresh timeout - please try again</span>');
                }
            },
            error: function() {
                // Continue monitoring on error
                if (checkCount < maxChecks) {
                    setTimeout(checkStatus, 10000);
                }
            }
        });
    };
    
    // Start monitoring after 5 seconds
    setTimeout(checkStatus, 5000);
}



$(document).ready(function() {
    // Hidden row styles
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .hidden-row { display: none !important; }
        `)
        .appendTo('head');

    // Check if refresh is in progress on page load
    checkRefreshStatusOnLoad();
    
    // Manual refresh
    $('#refreshDashboardBtn').on('click', function() {
        loadDashboardInfo(true); // force refresh
    });
    
    // Auto refresh every 30 seconds - simple page reload
    setInterval(function() {
        // Only auto-refresh if no manual refresh is in progress
        if (!dashboardForceRefresh) {
            location.reload(); // Simple page reload like F5
        }
    }, 30000); // 30 seconds

    // Abort all AJAX requests
    $('#abortAjaxBtn').on('click', function() {
        activeAjaxRequests.forEach(function(req) {
            if (req && typeof req.abort === 'function') req.abort();
        });
        activeAjaxRequests = [];
        showDashboardSpinner(false);
        $('#dashboardLastUpdated').html('<span class="text-danger">All AJAX requests aborted. You can now refresh or navigate.</span>');
    });
    
    // Add some interactive features
    $('.info-box').hover(
        function() {
            $(this).addClass('shadow');
        },
        function() {
            $(this).removeClass('shadow');
        }
    );





    // Show/Hide owner statistics
    var $tableBody = $('#ownerStatsTable tbody');
    var $showMoreBtn = $('#showMoreOwners');
    var $showLessBtn = $('#showLessOwners');

    $showMoreBtn.on('click', function() {
        $tableBody.find('.owner-row.hidden-row').removeClass('hidden-row');
        $showMoreBtn.hide();
        $showLessBtn.show();
    });

    $showLessBtn.on('click', function() {
        $tableBody.find('.owner-row').each(function(index) {
            if (index >= 10) {
                $(this).addClass('hidden-row');
            }
        });
        $showMoreBtn.show();
        $showLessBtn.hide();
    });

    // Show/Hide recent activity
    var $activityTableBody = $('#recentActivityTable tbody');
    var $showMoreActivitiesBtn = $('#showMoreActivities');
    var $showLessActivitiesBtn = $('#showLessActivities');

    $showMoreActivitiesBtn.on('click', function() {
        $activityTableBody.find('.activity-row.hidden-row').removeClass('hidden-row');
        $showMoreActivitiesBtn.hide();
        $showLessActivitiesBtn.show();
    });

    $showLessActivitiesBtn.on('click', function() {
        $activityTableBody.find('.activity-row').each(function(index) {
            if (index >= 10) {
                $(this).addClass('hidden-row');
            }
        });
        $showMoreActivitiesBtn.show();
        $showLessActivitiesBtn.hide();
    });

    // Show/Hide NAS statistics
    var $nasTableBody = $('#nasStatsTable tbody');
    var $showMoreNasBtn = $('#showMoreNas');
    var $showLessNasBtn = $('#showLessNas');

    $showMoreNasBtn.on('click', function() {
        $nasTableBody.find('.nas-row.hidden-row').removeClass('hidden-row');
        $showMoreNasBtn.hide();
        $showLessNasBtn.show();
    });

    $showLessNasBtn.on('click', function() {
        $nasTableBody.find('.nas-row').each(function(index) {
            if (index >= 5) {
                $(this).addClass('hidden-row');
            }
        });
        $showMoreNasBtn.show();
        $showLessNasBtn.hide();
    });
});
</script> </script> 
