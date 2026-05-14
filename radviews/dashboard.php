<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1><i class="fas fa-tachometer-alt"></i> Customers and Expiration</h1>
          <small id="dashboardLastUpdated" class="text-muted d-block" style="font-size:13px;margin-top:2px;"></small>
        </div>
        <div class="col-sm-6">
          <div class="d-flex justify-content-end align-items-center">
            <button id="refreshDashboardBtn" class="btn btn-info btn-sm mr-3"><i class="fas fa-sync-alt"></i> Refresh Dashboard</button>
            <button id="abortAjaxBtn" class="btn btn-danger btn-sm mr-3"><i class="fas fa-ban"></i> Abort Requests</button>
            <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
          </div>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <?php

  //header("Refresh: 300");
  /*$totalusers = $customerInfo->users;
  $createdusers = $customerInfo->created;
  $expiredusers = $customerInfo->expired;
  $blockedusers = $customerInfo->blocked;
  $expin30day = $customerInfo->expin30day;
  $online_customers = $customerOnlineInfo;

  $expin1day = $customerInfo->expin1day;
  $expin3day = $customerInfo->expin3day;
  $exp1day = $customerInfo->exp1day;
  $exp3day = $customerInfo->exp3day;

  $accountBalance = $balanaceInfo->amount;
  $rechargeBalance = $balanaceInfo->recharge;
  $refundBalance = $balanaceInfo->refund;

  $costOfSales = $costOfSales->costprice;
  $paymentInfo = $paymentInfo->amount_paid;*/

  //print_r($salesalert);

  ?>

  <section class="content">
    <div class="container-fluid position-relative" id="dashboardStatsContainer">
      <div id="dashboardRefreshSpinner" style="display:none;position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.7);z-index:1000;display:flex;align-items:center;justify-content:center;font-size:2em;">
        <span><i class="fas fa-spinner fa-spin"></i> Refreshing...</span>
      </div>

            <!-- Alert messages -->
            <div class="row">
          <div class="col-md-12">
            <?php
              $this->load->helper('form');
              $error = $this->session->flashdata('error');
              if($error)
              {
            ?>
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              <?php echo $this->session->flashdata('error'); ?>                    
            </div>
            <?php } ?>
            <?php  
              $success = $this->session->flashdata('success');
              if($success)
              {
            ?>
            <div class="alert alert-success alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              <?php echo $this->session->flashdata('success'); ?>
            </div>
            <?php } ?>
            
            <div class="row">
              <div class="col-md-12">
                <?php echo validation_errors('<div class="alert alert-danger alert-dismissible">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
              </div>
            </div>
          </div>
        </div>


      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-info">
            <div class="inner">
              <h3 id="totalusers"><i class="fas fa-spinner fa-spin"></i></h3>
              <p>Total Active</p>
            </div>
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
            <a href="<?php echo base_url(); ?>usersListing" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-success">
            <div class="inner">
              <h3 id="online_customers"><i class="fas fa-spinner fa-spin"></i></h3>
              <p>Online</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="<?php echo base_url(); ?>onlineusers" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-warning">
            <div class="inner">
              <h3 id="expin30day"><i class="fas fa-spinner fa-spin"></i></h3>
              <p>Expiring in next 30days</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
            <a href="<?php echo base_url(); ?>usersListing" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-danger">
            <div class="inner">
              <h3 id="expiredusers"><i class="fas fa-spinner fa-spin"></i></h3>
              <p>Disconnected Last 30 Days</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
      </div>
      <!-- /.row -->

      <div class="row">
        <div class="col-md-3 col-sm-6 col-12">
          <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-money-bill"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Balance</span>
              <span class="info-box-number" id="accountBalance"><i class="fas fa-spinner fa-spin"></i></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <div class="col-md-3 col-sm-6 col-12">
          <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-shopping-cart"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Recharge</span>
              <span class="info-box-number" id="rechargeBalance"><i class="fas fa-spinner fa-spin"></i></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <div class="col-md-3 col-sm-6 col-12">
          <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fas fa-dollar-sign"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Collection <?php echo date('F, Y'); ?></span>
              <span class="info-box-number">Rs. <span id="paymentInfo"><i class="fas fa-spinner fa-spin"></i></span></span>
            </div>
          </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-money-bill-alt"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Refunds <?php echo date('F, Y'); ?></span>
              <span class="info-box-number">Rs. <span id="refundBalance"><i class="fas fa-spinner fa-spin"></i></span></span>
            </div>
          </div>
        </div>
      </div>

      <!-- For Expiration of Users -->
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <button type="button" class="btn btn-danger" style="width: 100%; height: 65px;font-size: 14px;" onclick="window.location.href='<?php echo base_url() ?>userQuickBlockFilter/0/9/1000';">
            Expiring in 1day <span class="badge badge-light" style="font-size: 14px;" id="expin1day"><i class="fas fa-spinner fa-spin"></i></span>
          </button>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <button type="button" class="btn btn-warning" style="width: 100%; height: 65px;font-size: 14px;" onclick="window.location.href='<?php echo base_url() ?>userQuickBlockFilter/0/8/1000';">
            Expiring in 3-Days  <span class="badge badge-light" style="font-size: 14px;" id="expin3day"><i class="fas fa-spinner fa-spin"></i></span>
          </button>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <button type="button" class="btn btn-info" style="width: 100%; height: 65px;font-size: 14px;" onclick="window.location.href='<?php echo base_url() ?>userQuickBlockFilter/0/10/1000';">
            Expired in last One Day  <span class="badge badge-light" style="font-size: 14px;" id="exp1day"><i class="fas fa-spinner fa-spin"></i></span>
          </button>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <button type="button" class="btn btn-primary" style="width: 100%; height: 65px;font-size: 14px;" onclick="window.location.href='<?php echo base_url() ?>userQuickBlockFilter/0/11/1000';">
            Expired in last 3 Days  <span class="badge badge-light" style="font-size: 14px;" id="exp3day"><i class="fas fa-spinner fa-spin"></i></span>
          </button>
        </div>
      </div><br>     

      <div class="row">

      <div class="col-md-6">
          <div class="card">
            <div class="card-header border-0">
              <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Top 5 Service Plans</h3><br>
                <div class="card-tools">
                  <a href="#" class="btn btn-tool btn-sm">
                    <i class="fas fa-download"></i>
                  </a>
                  <a href="#" class="btn btn-tool btn-sm">
                    <i class="fas fa-bars"></i>
                  </a>
                </div>
              </div>
            </div>
            <div class="card-body table-responsive p-0">
              <table class="table table-striped table-valign-middle">
                <thead>
                <tr>
                  <th>Service</th>
                  <th>Price</th>
                  <th>Sales</th>
                </tr>
                </thead>
                <tbody id="productsTableBody">
                  <tr><td colspan="4" class="text-center">Loading...</td></tr>
                </tbody>
              </table>
            </div>
          </div>
          <!-- /.card -->
        </div>
        
        <div class="col-lg-6">
          <div class="card">
            <div class="card-header border-0">
              <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Sales (Last 12 Months)</h3>
                <div class="card-tools">
                  <a href="#" id="refreshOnlineOfflineChart1" class="btn btn-tool btn-sm" title="Load Graph">
                    <i class="fas fa-download"></i>
                  </a>
                </div>
              </div>
            </div>
            <div class="card-body">
              <canvas id="salesChart" height="140"></canvas>
            </div>
          </div>
        </div>
      </div>


      <div class="row">

      
        <div class="col-md-6">
          <div class="card">
            <div class="card-header border-0">
              <h3 class="card-title">Fair Use Alert</h3>
              <div class="card-tools">
                <a href="#" id="refreshFairUseTable" class="btn btn-tool btn-sm" title="Load Table">
                  <i class="fas fa-download"></i>
                </a>
                <a href="#" class="btn btn-tool btn-sm">
                  <i class="fas fa-bars"></i>
                </a>
              </div>
            </div>
            <div class="card-body table-responsive p-0 position-relative">
              
              <div id="fairuseLoading" style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.8);z-index:10;display:flex;align-items:center;justify-content:center;text-align:center;font-size:1.2em;display:none;">
                <span><i class="fas fa-spinner fa-spin"></i> Loading...</span>
              </div>
              <table class="table table-striped table-valign-middle mb-0">
                <thead>
                <tr>
                  <th>User</th>
                  <th>Name</th>
                  <th>Upload (GB)</th>
                  <th>Download (GB)</th>
                </tr>
                </thead>
                <tbody id="fairuseTableBody">
                  <tr><td colspan="6" class="text-center">&nbsp;</td></tr>
                </tbody>
              </table>
            </div>
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->

        <div class="col-lg-6">
          <div class="card">
            <div class="card-header border-0">
              <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Online/Offline Users (Last 24h)</h3>
                <div class="card-tools">
                  <a href="#" id="refreshOnlineOfflineChart" class="btn btn-tool btn-sm" title="Load Graph">
                    <i class="fas fa-download"></i>
                  </a>
                </div>
              </div>
            </div>
            <div class="card-body">
              <canvas id="onlineOfflineChart" height="140"></canvas>
            </div>
          </div>
        </div>
        
        <!-- /.col -->
      </div>
      <!-- /.row -->


    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
  if (!empty($salesalert)) {
    $row_count = 0;
    $comma = "";
    $data = "";
    $month = "";
    foreach ($salesalert as $record) {
      if ($row_count > 0) {
        $comma = ",";
      } else {
        $comma = "";
      }

      $sales = round((ABS($record->recharge + $record->refund) / 1000000), 2);

      $data = $data . $comma . strval($sales);
      $month = $month . $comma . strval($record->salesmonth);
      $row_count = $row_count + 1;
    }
  }
?>

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
    if (forceRefresh) showDashboardSpinner(true);
    let url = "<?php echo base_url('User/ajaxDashboardInfo'); ?>";
    if (forceRefresh) url += "?refresh=1";
    
    // Use tracked AJAX request
    createTrackedAjaxRequest(url, {
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            // Populate stat boxes
            $('#totalusers').text(data.customerInfo.users || 0);
            $('#online_customers').text(data.customerOnlineInfo || 0);
            $('#expiredusers').text(data.customerInfo.expired || 0);
            $('#expin30day').text(data.customerInfo.expin30day || 0);
            $('#accountBalance').text(data.balanaceInfo.amount || 0);
            $('#rechargeBalance').text(data.balanaceInfo.recharge || 0);
            $('#refundBalance').text(data.balanaceInfo.refund || 0);
            $('#paymentInfo').text(data.paymentInfo.amount_paid || 0);
            $('#expin1day').text((data.customerInfo.expin1day || 0) + ' users');
            $('#expin3day').text((data.customerInfo.expin3day || 0) + ' users');
            $('#exp1day').text((data.customerInfo.exp1day || 0) + ' users');
            $('#exp3day').text((data.customerInfo.exp3day || 0) + ' users');
            if (data.last_updated) {
                $('#dashboardLastUpdated').html('Last updated: ' + data.last_updated + ' <span class="text-info">(Note: press refresh button to update dashboard data.)</span>');
            } else {
                $('#dashboardLastUpdated').html('');
            }
            showDashboardSpinner(false);
            // Also refresh graphs if needed
            refreshDashboardGraphs(forceRefresh);
        },
        error: function() {
            $('#totalusers, #online_customers, #expiredusers, #expin30day, #accountBalance, #rechargeBalance, #refundBalance, #paymentInfo, #expin1day, #expin3day, #exp1day, #exp3day').text('Error');
            showDashboardSpinner(false);
            refreshDashboardGraphs(forceRefresh);
        }
    });
}

function refreshDashboardGraphs(forceRefresh) {
    // Sales and Paid Chart
    let salesUrl = "<?php echo base_url('ajax/sales_and_paid_by_month'); ?>";
    if (forceRefresh) salesUrl += "?refresh=1";
    $('#salesChartLoading').show();
    
    createTrackedAjaxRequest(salesUrl, {
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            $("#salesChartLoading").hide();
            const labels = Object.keys(data);
            const sales = Object.values(data).map(d => d.sales);
            const paid = Object.values(data).map(d => d.paid);
            new Chart(document.getElementById('salesChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Sales', data: sales, backgroundColor: 'blue' },
                        { label: 'Paid', data: paid, backgroundColor: 'grey' }
                    ]
                },
                options: { responsive: true }
            });
        },
        error: function() {
            $("#salesChartLoading").text('Failed to load data');
        }
    });

    // Top 5 Service Plans
    let top5Url = "<?php echo base_url('ajax/top5_service_plans_last_month'); ?>";
    if (forceRefresh) top5Url += "?refresh=1";
    
    createTrackedAjaxRequest(top5Url, {
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            var html = '';
            if (data.length > 0) {
                data.forEach(function(product) {
                    html += '<tr>' +
                        '<td>' + product.srvname + '</td>' +
                        '<td>' + parseFloat(product.saleprice).toLocaleString() + '</td>' +
                        '<td>' + parseFloat(product.sales).toLocaleString() + ' Sold</td>' +
                        '</tr>';
                });
            } else {
                html = '<tr><td colspan="4" class="text-center">No data available</td></tr>';
            }
            $('#productsTableBody').html(html);
        },
        error: function() {
            $('#productsTableBody').html('<tr><td colspan="4" class="text-center text-danger">Failed to load data</td></tr>');
        }
    });

    // Fair Use Alert
    let fairuseUrl = "<?php echo base_url('ajax/dashboard_fairuse_alert'); ?>";
    if (forceRefresh) fairuseUrl += "?refresh=1";
    // Only refresh if table is visible or loaded
    if ($('#fairuseTableBody').length) {
        createTrackedAjaxRequest(fairuseUrl, {
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                var html = '';
                if (data && data.length > 0) {
                    $.each(data, function(i, user) {
                        html += '<tr>' +
                            '<td>' + (user.username ? user.username : '-') + '</td>' +
                            '<td>' + (user.firstname ? user.firstname : '') + ' ' + (user.lastname ? user.lastname : '') + '</td>' +
                            '<td>' + (user.upload ? parseFloat(user.upload).toFixed(2) : '0.00') + '</td>' +
                            '<td>' + (user.download ? parseFloat(user.download).toFixed(2) : '0.00') + '</td>' +
                        '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center">No data available</td></tr>';
                }
                $('#fairuseTableBody').html(html);
            },
            error: function() {
                $('#fairuseTableBody').html('<tr><td colspan="6" class="text-center text-danger">Failed to load data</td></tr>');
            }
        });
    }

    // Online/Offline Users (Last 24h)
    let onlineOfflineUrl = "<?php echo base_url('ajax/online_offline_24h'); ?>";
    if (forceRefresh) onlineOfflineUrl += "?refresh=1";
    // Only refresh if chart is visible or loaded
    if ($('#onlineOfflineChart').length) {
        createTrackedAjaxRequest(onlineOfflineUrl, {
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                // Dynamically set canvas height to 140px before drawing chart
                $('#onlineOfflineChart').attr('height', 250);
                const keys = Object.keys(data).sort();
                const labels = keys.map(h => h.substr(11, 5));
                const online = keys.map(h => data[h].online);
                const offline = keys.map(h => data[h].offline);
                new Chart(document.getElementById('onlineOfflineChart').getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            { label: 'Online', data: online, borderColor: 'blue', fill: false },
                            { label: 'Offline', data: offline, borderColor: 'gray', fill: false }
                        ]
                    },
                    options: { responsive: true }
                });
            }
        });
    }
}

$(document).ready(function() {
    loadDashboardInfo(false); // Initial load (from cache if available)

    // Manual refresh
    $('#refreshDashboardBtn').on('click', function() {
        loadDashboardInfo(true); // force refresh
    });

    // Abort all AJAX requests
    $('#abortAjaxBtn').on('click', function() {
        activeAjaxRequests.forEach(function(req) {
            if (req && typeof req.abort === 'function') req.abort();
        });
        activeAjaxRequests = [];
        showDashboardSpinner(false);
        $('#dashboardLastUpdated').html('<span class="text-danger">All AJAX requests aborted. You can now refresh or navigate.</span>');
    });

    // Auto-refresh every 90 seconds
    setInterval(function() {
        loadDashboardInfo(false);
    }, 1000000);
});
</script>

<script>
  const xValues = [<?php echo $month ?>];
  const yValues = [<?php echo $data ?>];
  const barColors = ["red", "green", "blue", "orange", "brown"];

  new Chart("myChart", {
    type: "bar",
    data: {
      labels: xValues,
      datasets: [{
        backgroundColor: barColors,
        data: yValues
      }]
    },
    options: {
      legend: {
        display: false
      },
      title: {
        display: true,
        text: "Sales By Month (Million)"
      }
    }
  });
</script>

<script>
  const xValues2 = ["Total", "New", "Expired", "Blocked", "Online"];
  const yValues2 = [<?php echo $totalusers . "," . $createdusers . "," . $expiredusers . "," . $blockedusers . "," . $online_customers ?>];
  const barColors2 = [
    "#b91d47",
    "#00aba9",
    "#2b5797",
    "#e8c3b9",
    "#1e7145"
  ];

  new Chart("myChartPie", {
    type: "pie",
    data: {
      labels: xValues2,
      datasets: [{
        backgroundColor: barColors2,
        data: yValues2
      }]
    },
    options: {
      title: {
        display: true,
        text: "Users Status"
      }
    }
  });
</script>

<script>
$(document).ready(function() {
    // Prepare parent for relative positioning and canvas for full size
    $('#onlineOfflineChart').parent().css('position', 'relative');
    $('#onlineOfflineChart').css({'display': 'block', 'width': '100%'});
    // Insert centered loading overlay (hidden by default)
    $('#onlineOfflineChart').parent().append(
      '<div id="onlineOfflineLoading" style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.8);z-index:10;display:flex;align-items:center;justify-content:center;text-align:center;font-size:1.2em;color:#333;display:none;">Loading graph...</div>'
    );

    // Only load the Online/Offline chart when the button is clicked
    $('#refreshOnlineOfflineChart').on('click', function(e) {
        e.preventDefault();
        $("#onlineOfflineHint").hide();
        $("#onlineOfflineLoading").show();
        $.getJSON("<?php echo base_url('ajax/online_offline_24h'); ?>", function(data) {
            $("#onlineOfflineLoading").hide();
            // Dynamically set canvas height to 140px before drawing chart
            $('#onlineOfflineChart').attr('height', 250);
            const keys = Object.keys(data).sort();
            const labels = keys.map(h => h.substr(11, 5));
            const online = keys.map(h => data[h].online);
            const offline = keys.map(h => data[h].offline);
            new Chart(document.getElementById('onlineOfflineChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Online', data: online, borderColor: 'blue', fill: false },
                        { label: 'Offline', data: offline, borderColor: 'gray', fill: false }
                    ]
                },
                options: { responsive: true }
            });
        }).fail(function(jqXHR, textStatus, errorThrown) {
            $("#onlineOfflineLoading").text('Failed to load data');
        });
    });

    // Sales and Paid Chart (auto-load as before)
    $('#salesChart').parent().css('position', 'relative').append('<div id="salesChartLoading" style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.8);z-index:10;display:flex;align-items:center;justify-content:center;font-size:1.2em;color:#333;">Loading graph...</div>');
    $.getJSON("<?php echo base_url('ajax/sales_and_paid_by_month'); ?>", function(data) {
        $("#salesChartLoading").hide();
        const labels = Object.keys(data);
        const sales = Object.values(data).map(d => d.sales);
        const paid = Object.values(data).map(d => d.paid);
        new Chart(document.getElementById('salesChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Sales', data: sales, backgroundColor: 'blue' },
                    { label: 'Paid', data: paid, backgroundColor: 'grey' }
                ]
            },
            options: { responsive: true }
        });
    }).fail(function(jqXHR, textStatus, errorThrown) {
        $("#salesChartLoading").text('Failed to load data');
    });
});
</script>

<script>
$(document).ready(function() {
    $.getJSON("<?php echo base_url('ajax/top5_service_plans_last_month'); ?>", function(data) {
        var html = '';
        if (data.length > 0) {
            data.forEach(function(product) {
                html += '<tr>' +
                    '<td>' +
                    product.srvname + '</td>' +
                    '<td>' + parseFloat(product.saleprice).toLocaleString() + '</td>' +
                    '<td>' + parseFloat(product.sales).toLocaleString() + ' Sold</td>' +
                    '</tr>';
            });
        } else {
            html = '<tr><td colspan="4" class="text-center">No data available</td></tr>';
        }
        $('#productsTableBody').html(html);
    }).fail(function() {
        $('#productsTableBody').html('<tr><td colspan="4" class="text-center text-danger">Failed to load data</td></tr>');
    });
});
</script>

<script>
$(document).ready(function() {
    // Fair Use Alert: Only load table on button click
    $('#refreshFairUseTable').on('click', function(e) {
        e.preventDefault();
        $('#fairuseHint').hide();
        $('#fairuseLoading').show();
        $.getJSON("<?php echo base_url('ajax/dashboard_fairuse_alert'); ?>", function(data) {
            var html = '';
            if (data && data.length > 0) {
                $.each(data, function(i, user) {
                    html += '<tr>' +
                        '<td>' + (user.username ? user.username : '-') + '</td>' +
                        '<td>' + (user.firstname ? user.firstname : '') + ' ' + (user.lastname ? user.lastname : '') + '</td>' +
                        '<td>' + (user.upload ? parseFloat(user.upload).toFixed(2) : '0.00') + '</td>' +
                        '<td>' + (user.download ? parseFloat(user.download).toFixed(2) : '0.00') + '</td>' +
                    '</tr>';
                });
            } else {
                html = '<tr><td colspan="6" class="text-center">No data available</td></tr>';
            }
            $('#fairuseTableBody').html(html);
            $('#fairuseLoading').hide();
        }).fail(function() {
            $('#fairuseTableBody').html('<tr><td colspan="6" class="text-center text-danger">Failed to load data</td></tr>');
            $('#fairuseLoading').hide();
        });
    });
});
</script>