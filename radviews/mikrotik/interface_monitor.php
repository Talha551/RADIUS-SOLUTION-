<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-network-wired"></i> MikroTik Interface Monitor
                        <small>Real-time Interface Statistics</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Interface Monitor</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <!-- Flash messages -->
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
                </div>
            </div>
            
            <!-- Router Selection and Controls -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Router Selection & Controls</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="routerSelect">Select Router:</label>
                                    <select id="routerSelect" class="form-control select2" style="width: 100%;">
                                        <option value="">Choose a router...</option>
                                        <?php
                                        if(!empty($routers)) {
                                            foreach($routers as $router) {
                                                echo '<option value="'.$router->id.'" data-nasname="'.$router->nasname.'">'.$router->shortname.' ('.$router->nasname.')</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="interfaceTypeFilter">Interface Type:</label>
                                    <select id="interfaceTypeFilter" class="form-control select2" style="width: 100%;">
                                        <option value="">All Types</option>
                                        <?php
                                        if(!empty($interfaceTypes)) {
                                            foreach($interfaceTypes as $key => $type) {
                                                echo '<option value="'.$key.'">'.$type.'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="statusFilter">Status:</label>
                                    <select id="statusFilter" class="form-control select2" style="width: 100%;">
                                        <option value="">All Status</option>
                                        <option value="running">Running</option>
                                        <option value="disabled">Disabled</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <div class="controls-wrap">
                                        <button id="refreshBtn" class="btn btn-primary btn-block" disabled>
                                            <i class="fas fa-sync-alt"></i> Refresh
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-lg-12 d-flex align-items-center flex-wrap controls-wrap">
                                    <button id="testConnectionBtn" class="btn btn-info mr-2 mb-2" disabled>
                                        <i class="fas fa-plug"></i> Test Connection
                                    </button>
                                    <button id="autoRefreshBtn" class="btn btn-success mr-2 mb-2" disabled>
                                        <i class="fas fa-play"></i> Auto Refresh
                                    </button>
                                    <div class="form-check form-check-inline mr-3 mb-2">
                                        <input class="form-check-input" type="checkbox" id="ratesOnlyToggle">
                                        <label class="form-check-label" for="ratesOnlyToggle">Rates-only refresh</label>
                                    </div>
                                    <button id="clearFiltersBtn" class="btn btn-secondary mb-2">
                                        <i class="fas fa-undo"></i> Reset Filters
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div id="connectionStatus" class="alert alert-info mb-0" style="display: none;">
                                        <i class="fas fa-info-circle"></i> <span id="statusMessage">Select a router to begin monitoring</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Interface Statistics Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Interface Statistics</h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <input type="text" id="searchInput" class="form-control float-right" placeholder="Search interfaces...">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body table-responsive p-0">
                            <table id="interfaceTable" class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Actual MTU</th>
                                        <th>L2 MTU</th>
                                        <th>Tx Bytes</th>
                                        <th>Rx Bytes</th>
                                        <th>Tx Packets</th>
                                        <th>Rx Packets</th>
                                        <th>Tx Rate</th>
                                        <th>Rx Rate</th>
                                        <th>FP Tx Bytes</th>
                                        <th>FP Rx Bytes</th>
                                        <th>FP Tx Packets</th>
                                        <th>FP Rx Packets</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="16" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> Select a router to view interface statistics
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <strong>Total Interfaces:</strong> <span id="totalInterfaces">0</span> | 
                                        <strong>Running:</strong> <span id="runningInterfaces">0</span> | 
                                        <strong>Disabled:</strong> <span id="disabledInterfaces">0</span>
                                    </small>
                                </div>
                                <div class="col-md-6 text-right">
                                    <small class="text-muted">
                                        <strong>Last Updated:</strong> <span id="lastUpdated">Never</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Select2 CSS and JS -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/plugins/select2/css/select2.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/select2/js/select2.full.min.js"></script>

<!-- DataTables CSS and JS -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

<script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/jszip/jszip.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/pdfmake/pdfmake.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/pdfmake/vfs_fonts.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

<script type="text/javascript">
    // Basic test to see if JavaScript is loading
    console.log('=== MIKROTIK INTERFACE MONITOR SCRIPT LOADING ===');
    
    // Check if jQuery is available
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded!');
        alert('ERROR: jQuery is not loaded!');
    } else {
        console.log('jQuery is available, version:', jQuery.fn.jquery);
    }
    
    // Add error handler to catch any JavaScript errors
    window.onerror = function(msg, url, lineNo, columnNo, error) {
        console.error('JavaScript Error:', msg, 'at', url, 'line', lineNo);
        alert('JavaScript Error: ' + msg + ' at line ' + lineNo);
        return false;
    };
    
    try {
        jQuery(document).ready(function(){
            console.log('=== JQUERY DOCUMENT READY STARTED ===');
            
            // Basic element test
            console.log('Testing basic elements...');
            console.log('jQuery object:', typeof $);
            console.log('Document ready element:', $('body').length);
            
            var dataTable = null;
            var autoRefreshInterval = null;
            var selectedRouterId = null;
            var expandedRows = new Set(); // Track expanded rows by interface name
            
            // Initialize Select2 with error handling
            try {
                if (typeof $.fn.select2 !== 'undefined') {
                    console.log('Select2 is available, initializing...');
                    $('.select2').select2({
                        placeholder: "Select an option",
                        minimumResultsForSearch: Infinity, // hide search box for small lists
                        allowClear: false, // no need for clear button
                        width: 'resolve'
                    });
                    console.log('Select2 initialized successfully');
                } else {
                    console.warn('Select2 not available, using regular dropdowns');
                    // Remove select2 class to use regular dropdowns
                    $('.select2').removeClass('select2');
                }
            } catch (error) {
                console.error('Error initializing Select2:', error);
                // Remove select2 class to use regular dropdowns
                $('.select2').removeClass('select2');
            }
            
            // Check if DataTables is loaded
            if (typeof $.fn.DataTable !== 'undefined') {
                console.log('DataTables loaded successfully');
            } else {
                console.error('DataTables failed to load');
            }
            
            // Debug: Check if elements exist
            console.log('Router select element exists:', $('#routerSelect').length > 0);
            console.log('Refresh button exists:', $('#refreshBtn').length > 0);
            console.log('Test connection button exists:', $('#testConnectionBtn').length > 0);
            
            // Debug: Check router dropdown options
            console.log('Router dropdown options count:', $('#routerSelect option').length);
            $('#routerSelect option').each(function(index) {
                console.log('Option ' + index + ':', $(this).val(), $(this).text());
            });
            
            // Removed test buttons and auto-test. Data loads only after selecting a router.
            
            // Test dropdown click
            $('#routerSelect').on('click', function() {
                console.log('Router dropdown clicked');
            });
            
            // Multiple event handlers for router selection
            $('#routerSelect').on('change', function() {
                console.log('Standard change event triggered');
                handleRouterSelection();
            });
            
            $('#routerSelect').on('select2:select', function(e) {
                console.log('Select2 select event triggered');
                handleRouterSelection();
            });
            
            $('#routerSelect').on('select2:change', function(e) {
                console.log('Select2 change event triggered');
                handleRouterSelection();
            });
            
            // Centralized router selection handler
            function handleRouterSelection() {
                selectedRouterId = $('#routerSelect').val();
                console.log('Router selection changed to:', selectedRouterId);
                
                if (selectedRouterId) {
                    console.log('Enabling buttons for router ID:', selectedRouterId);
                    $('#refreshBtn, #testConnectionBtn, #autoRefreshBtn').prop('disabled', false);
                    $('#connectionStatus').show().removeClass('alert-danger alert-success').addClass('alert-info');
                    $('#statusMessage').text('Router selected. Loading interface data...');
                    
                    // Clear expanded state for new router
                    expandedRows.clear();
                    
                    // Clear existing table and stop auto-refresh
                    if (autoRefreshInterval) {
                        clearInterval(autoRefreshInterval);
                        autoRefreshInterval = null;
                        $('#autoRefreshBtn').removeClass('btn-warning').addClass('btn-success').html('<i class="fas fa-play"></i> Auto Refresh');
                    }
                    
                    // Clear existing table
                    if (dataTable) {
                        console.log('Destroying existing DataTable due to router change');
                        dataTable.destroy();
                        dataTable = null;
                    }
                    $('#interfaceTable tbody').html('<tr><td colspan="16" class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Loading interface data...</td></tr>');

                    // Auto load data immediately on selection
                    loadInterfaceData();
                } else {
                    console.log('Disabling buttons - no router selected');
                    $('#refreshBtn, #testConnectionBtn, #autoRefreshBtn').prop('disabled', true);
                    $('#connectionStatus').hide();
                    
                    // Clear expanded state
                    expandedRows.clear();
                    
                    // Stop auto-refresh
                    if (autoRefreshInterval) {
                        clearInterval(autoRefreshInterval);
                        autoRefreshInterval = null;
                        $('#autoRefreshBtn').removeClass('btn-warning').addClass('btn-success').html('<i class="fas fa-play"></i> Auto Refresh');
                    }
                    
                    if (dataTable) {
                        console.log('Destroying existing DataTable due to no router selection');
                        dataTable.destroy();
                        dataTable = null;
                    }
                    $('#interfaceTable tbody').html('<tr><td colspan="16" class="text-center text-muted"><i class="fas fa-info-circle"></i> Select a router to view interface statistics</td></tr>');
                }
            }
            
            // Refresh button click
            $('#refreshBtn').on('click', function() {
                if (!selectedRouterId) return;
                
                loadInterfaceData();
            });
            
            // Test connection button
            $('#testConnectionBtn').on('click', function() {
                if (!selectedRouterId) return;
                
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Testing...');
                
                $.post('<?php echo base_url('Mikrotik/Mikrotik_api/testConnection'); ?>', {
                    router_id: selectedRouterId
                }, function(response) {
                    if (response.success) {
                        $('#connectionStatus').removeClass('alert-danger alert-info').addClass('alert-success');
                        $('#statusMessage').text(response.message);
                    } else {
                        $('#connectionStatus').removeClass('alert-success alert-info').addClass('alert-danger');
                        $('#statusMessage').text(response.error);
                    }
                    $('#connectionStatus').show();
                }, 'json').always(function() {
                    $('#testConnectionBtn').prop('disabled', false).html('<i class="fas fa-plug"></i> Test Connection');
                });
            });
            
            // Auto refresh toggle
            $('#autoRefreshBtn').on('click', function() {
                var $btn = $(this);
                
                if (autoRefreshInterval) {
                    clearInterval(autoRefreshInterval);
                    autoRefreshInterval = null;
                    $btn.removeClass('btn-warning').addClass('btn-success').html('<i class="fas fa-play"></i> Auto Refresh');
                } else {
                    autoRefreshInterval = setInterval(function() {
                        if (selectedRouterId) {
                            if ($('#ratesOnlyToggle').is(':checked')) {
                                // Fast path: update only rates for visible rows
                                fastUpdateRates();
                            } else {
                                loadInterfaceData();
                            }
                        }
                    }, 5000); // Refresh every 10 seconds
                    
                    $btn.removeClass('btn-success').addClass('btn-warning').html('<i class="fas fa-stop"></i> Stop Auto Refresh');
                }
            });

            // Save expanded rows state before refresh
            function saveExpandedState() {
                if (!dataTable) return;
                
                expandedRows.clear();
                dataTable.rows().every(function() {
                    var row = this.node();
                    var interfaceName = $(row).find('td:first').text().trim();
                    
                    // Check if this is a responsive DataTable
                    if (dataTable.responsive) {
                        // For responsive tables, check if details are shown
                        if (dataTable.row(this).child.isShown()) {
                            if (interfaceName) {
                                expandedRows.add(interfaceName);
                            }
                        }
                    } else {
                        // For regular tables with child rows
                        if ($(row).hasClass('parent')) {
                            if (interfaceName) {
                                expandedRows.add(interfaceName);
                            }
                        }
                    }
                });
                console.log('Saved expanded state:', Array.from(expandedRows));
            }
            
            // Restore expanded rows state after refresh
            function restoreExpandedState() {
                if (!dataTable || expandedRows.size === 0) return;
                
                console.log('Restoring expanded state for:', Array.from(expandedRows));
                
                // Wait a bit for DataTable to fully render
                setTimeout(function() {
                    dataTable.rows().every(function() {
                        var row = this.node();
                        var interfaceName = $(row).find('td:first').text().trim();
                        if (expandedRows.has(interfaceName)) {
                            // Check if this is a responsive DataTable
                            if (dataTable.responsive) {
                                // For responsive tables, we need to trigger the responsive details
                                var api = new $.fn.DataTable.Api(dataTable);
                                var rowIdx = this.index();
                                
                                // Check if details are already shown
                                if (!api.row(rowIdx).child.isShown()) {
                                    // Trigger the responsive details toggle
                                    $(row).find('.dtr-control').trigger('click');
                                }
                            } else {
                                // For regular tables with child rows
                                if (!dataTable.row(this).child.isShown()) {
                                    dataTable.row(this).child.show();
                                    $(row).addClass('parent');
                                }
                            }
                        }
                    });
                }, 300);
            }
            
            // Fast update of only Tx/Rx rates using lighter endpoint
            function fastUpdateRates() {
                if (!selectedRouterId) return;
                
                // Save expanded state before updating
                saveExpandedState();
                
                // Collect interface names from current table
                var names = [];
                $('#interfaceTable tbody tr').each(function() {
                    var name = $(this).find('td').eq(0).text().trim();
                    if (name && names.indexOf(name) === -1) names.push(name);
                });
                if (names.length === 0) return;
                
                $.post('<?php echo base_url('Mikrotik/Mikrotik_api/getRealTimeStats'); ?>', { router_id: selectedRouterId }, function(resp) {
                    if (!resp || !resp.success) return;
                    // Build lookup by name
                    var byName = {};
                    for (var i = 0; i < resp.data.length; i++) {
                        var r = resp.data[i];
                        if (r.name) byName[r.name] = r;
                    }
                    // Update cells
                    $('#interfaceTable tbody tr').each(function() {
                        var $row = $(this);
                        var name = $row.find('td').eq(0).text().trim();
                        var r = byName[name];
                        if (r) {
                            var txbps = Number(r['tx-bits-per-second'] || 0);
                            var rxbps = Number(r['rx-bits-per-second'] || 0);
                            var txText = txbps >= 1000000 ? (txbps/1000000).toFixed(2) + ' Mbps' : (txbps >= 1000 ? (txbps/1000).toFixed(2) + ' Kbps' : txbps + ' bps');
                            var rxText = rxbps >= 1000000 ? (rxbps/1000000).toFixed(2) + ' Mbps' : (rxbps >= 1000 ? (rxbps/1000).toFixed(2) + ' Kbps' : rxbps + ' bps');
                            // Column indices after we added Tx Rate and Rx Rate: Name(0) Type(1) Status(2) Actual MTU(3) L2 MTU(4) Tx Bytes(5) Rx Bytes(6) Tx Packets(7) Rx Packets(8) Tx Rate(9) Rx Rate(10) FP Tx Bytes(11) FP Rx Bytes(12) FP Tx Packets(13) FP Rx Packets(14)
                            $row.find('td').eq(9).text(txText);
                            $row.find('td').eq(10).text(rxText);
                        }
                    });
                    $('#lastUpdated').text(new Date().toLocaleTimeString());
                    
                    // Restore expanded state after update
                    setTimeout(function() {
                        restoreExpandedState();
                    }, 50);
                }, 'json');
            }
            
            // Load interface data
            function loadInterfaceData() {
                if (!selectedRouterId) {
                    console.log('No router selected, cannot load data');
                    return;
                }
                
                console.log('=== LOADING INTERFACE DATA ===');
                console.log('Router ID:', selectedRouterId);
                console.log('AJAX URL:', '<?php echo base_url('Mikrotik/Mikrotik_api/getInterfaceStats'); ?>');
                
                // Save expanded state before loading new data
                saveExpandedState();
                
                $('#refreshBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
                
                var ajaxData = { router_id: selectedRouterId };
                console.log('AJAX Data:', ajaxData);
                
                $.post('<?php echo base_url('Mikrotik/Mikrotik_api/getInterfaceStats'); ?>', ajaxData, function(response) {
                    console.log('=== AJAX RESPONSE RECEIVED ===');
                    console.log('Response type:', typeof response);
                    console.log('Full response:', response);
                    
                    if (response.success) {
                        console.log('Data loaded successfully, interfaces found:', response.data.length);
                        displayInterfaceData(response.data);
                        $('#connectionStatus').removeClass('alert-danger alert-info').addClass('alert-success');
                        $('#statusMessage').text('Data loaded successfully');
                        $('#lastUpdated').text(new Date().toLocaleTimeString());
                    } else {
                        console.log('Error loading data:', response.error);
                        $('#connectionStatus').removeClass('alert-success alert-info').addClass('alert-danger');
                        $('#statusMessage').text(response.error);
                        $('#interfaceTable tbody').html('<tr><td colspan="16" class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> ' + response.error + '</td></tr>');
                    }
                    $('#connectionStatus').show();
                }, 'json').fail(function(xhr, status, error) {
                    console.error('=== AJAX REQUEST FAILED ===');
                    console.error('Status:', status);
                    console.error('Error:', error);
                    console.error('Response text:', xhr.responseText);
                    console.error('Response status:', xhr.status);
                    console.error('Response headers:', xhr.getAllResponseHeaders());
                    
                    $('#connectionStatus').removeClass('alert-success alert-info').addClass('alert-danger');
                    $('#statusMessage').text('Network error: ' + error + ' (Status: ' + xhr.status + ')');
                    $('#interfaceTable tbody').html('<tr><td colspan="16" class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> Network error occurred</td></tr>');
                    $('#connectionStatus').show();
                }).always(function() {
                    console.log('AJAX request completed');
                    $('#refreshBtn').prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Refresh');
                });
            }
            
            // Display interface data in DataTable
            function displayInterfaceData(data) {
                console.log('=== DISPLAYING INTERFACE DATA ===');
                console.log('Data received:', data);
                
                // Clear any existing custom filters
                if ($.fn.dataTable.ext.search._mikrotikFilter) {
                    var idx = $.fn.dataTable.ext.search.indexOf($.fn.dataTable.ext.search._mikrotikFilter);
                    if (idx > -1) $.fn.dataTable.ext.search.splice(idx, 1);
                    delete $.fn.dataTable.ext.search._mikrotikFilter;
                }
                
                // Destroy existing DataTable if it exists
                if (dataTable) {
                    console.log('Destroying existing DataTable');
                    dataTable.destroy();
                    dataTable = null;
                }
                
                var tableBody = $('#interfaceTable tbody');
                tableBody.empty();
                
                if (data && data.length > 0) {
                    console.log('Total interfaces to display:', data.length);
                    
                    $.each(data, function(index, interface) {
                        console.log('Interface ' + index + ':', interface);
                        
                        var row = '<tr>';
                        row += '<td><strong>' + (interface.name || 'N/A') + '</strong></td>';
                        row += '<td><span class="badge badge-info">' + (interface.type || 'N/A') + '</span></td>';
                        row += '<td>' + (interface.running === 'Yes' ? '<span class="badge badge-success">Running</span>' : '<span class="badge badge-secondary">Stopped</span>') + '</td>';
                        row += '<td>' + (interface['actual-mtu'] || 'N/A') + '</td>';
                        row += '<td>' + (interface.l2mtu || 'N/A') + '</td>';
                        row += '<td>' + (interface['tx-byte'] || '0') + '</td>';
                        row += '<td>' + (interface['rx-byte'] || '0') + '</td>';
                        row += '<td>' + (interface['tx-packet'] || '0') + '</td>';
                        row += '<td>' + (interface['rx-packet'] || '0') + '</td>';
                        row += '<td>' + (interface['tx-bps'] || '0') + '</td>';
                        row += '<td>' + (interface['rx-bps'] || '0') + '</td>';
                        row += '<td>' + (interface['fp-tx-byte'] || '0') + '</td>';
                        row += '<td>' + (interface['fp-rx-byte'] || '0') + '</td>';
                        row += '<td>' + (interface['fp-tx-packet'] || '0') + '</td>';
                        row += '<td>' + (interface['fp-rx-packet'] || '0') + '</td>';
                        row += '<td class="text-center actions-column">';
                        row += '<div class="btn-group btn-group-sm" role="group">';
                        row += '<button type="button" class="btn btn-outline-primary btn-sm add-vlan-btn" data-interface="' + (interface.name || '') + '" title="Add VLAN">';
                        row += '<i class="fas fa-network-wired"></i> VLAN';
                        row += '</button>';
                        row += '<button type="button" class="btn btn-outline-success btn-sm add-ip-btn" data-interface="' + (interface.name || '') + '" title="Add IP">';
                        row += '<i class="fas fa-globe"></i> IP';
                        row += '</button>';
                        row += '<button type="button" class="btn btn-outline-info btn-sm add-route-btn" data-interface="' + (interface.name || '') + '" title="Add Route">';
                        row += '<i class="fas fa-route"></i> Route';
                        row += '</button>';
                        row += '</div>';
                        row += '</td>';
                        row += '</tr>';
                        tableBody.append(row);
                    });
                    
                    // Update statistics
                    var runningCount = data.filter(function(item) { return item.running === 'Yes'; }).length;
                    var disabledCount = data.filter(function(item) { return item.running === 'No'; }).length;
                    
                    $('#totalInterfaces').text(data.length);
                    $('#runningInterfaces').text(runningCount);
                    $('#disabledInterfaces').text(disabledCount);
                    
                    // Initialize DataTable
                    try {
                        console.log('Initializing new DataTable');
                        dataTable = $('#interfaceTable').DataTable({
                            responsive: true,
                            pageLength: 25,
                            lengthMenu: [[25, 50, 100, 250, 500, -1], [25, 50, 100, 250, 500, 'All']],
                            order: [[0, 'asc']],
                            columnDefs: [
                                { targets: [3, 4], className: 'text-center' },
                                { targets: [5, 6, 7, 8, 9, 10, 11, 12, 13, 14], className: 'text-right' },
                                { targets: [15], className: 'text-center', orderable: false, searchable: false }
                            ],
                            // Show Buttons and Length selector; keep built-in filter hidden via CSS
                            dom: 'Blrtip',
                            buttons: [
                                'copy', 'csv', 'excel', 'pdf', 'print'
                            ]
                        });
                        console.log('DataTable initialized successfully');
                        
                        // Verify DataTable is working
                        if (dataTable && typeof dataTable.draw === 'function') {
                            console.log('DataTable is properly initialized and functional');
                            // Apply filters and restore expanded state after DataTable is initialized
                            setTimeout(function() {
                                applyFilters();
                                restoreExpandedState();
                                setupActionButtons();
                            }, 100);
                        } else {
                            console.error('DataTable initialization failed - draw method not available');
                        }
                        
                    } catch (error) {
                        console.error('Error initializing DataTable:', error);
                        // Fallback to simple table
                        $('#interfaceTable').addClass('table-striped');
                        dataTable = null;
                    }
                    
                } else {
                    console.log('No data available, showing empty state');
                    tableBody.html('<tr><td colspan="16" class="text-center text-muted"><i class="fas fa-info-circle"></i> No interface data available</td></tr>');
                    $('#totalInterfaces, #runningInterfaces, #disabledInterfaces').text('0');
                }
            }
            
            // Apply filters
            function applyFilters() {
                if (!dataTable) {
                    console.log('DataTable not available for filtering');
                    return;
                }
                
                console.log('Applying filters...');
                var typeFilter = $('#interfaceTypeFilter').val();
                var statusFilter = $('#statusFilter').val();
                var searchValue = $('#searchInput').val();
                
                console.log('Filter values - Type:', typeFilter, 'Status:', statusFilter, 'Search:', searchValue);
                
                // Remove previous custom filter if present and add a new one
                if ($.fn.dataTable.ext.search._mikrotikFilter) {
                    var idx = $.fn.dataTable.ext.search.indexOf($.fn.dataTable.ext.search._mikrotikFilter);
                    if (idx > -1) $.fn.dataTable.ext.search.splice(idx, 1);
                }
                
                var filterFn = function(settings, data) {
                    var type = (data[1] || '').toLowerCase();
                    var status = (data[2] || '').toLowerCase();
                    var name = (data[0] || '').toLowerCase();
                    var typeMatch = !typeFilter || type.indexOf(typeFilter.toLowerCase()) !== -1;
                    var statusMatch = !statusFilter || status.indexOf(statusFilter.toLowerCase()) !== -1;
                    var searchMatch = !searchValue || name.indexOf(searchValue.toLowerCase()) !== -1;
                    return typeMatch && statusMatch && searchMatch;
                };
                
                $.fn.dataTable.ext.search._mikrotikFilter = filterFn;
                $.fn.dataTable.ext.search.push(filterFn);
                
                try {
                    dataTable.draw();
                    console.log('Filters applied successfully');
                } catch (error) {
                    console.error('Error applying filters:', error);
                }
            }
            
            // Filter change events
            $('#interfaceTypeFilter, #statusFilter').on('change', function() {
                applyFilters();
            });
            
            // Search input
            $('#searchInput').on('keyup', function() {
                applyFilters();
            });
            
            // Clear filters
            $('.select2').on('select2:clear', function() { applyFilters(); });

            // Setup action buttons event handlers
            function setupActionButtons() {
                // Remove existing event handlers to prevent duplicates
                $(document).off('click', '.add-vlan-btn');
                $(document).off('click', '.add-ip-btn');
                $(document).off('click', '.add-route-btn');
                
                // Add VLAN button
                $(document).on('click', '.add-vlan-btn', function() {
                    var interfaceName = $(this).data('interface');
                    console.log('Add VLAN clicked for interface:', interfaceName);
                    
                    if (!selectedRouterId) {
                        alert('Please select a router first');
                        return;
                    }
                    
                    // Redirect to add VLAN form
                    var url = '<?php echo base_url('Mikrotik/Mikrotik_api/addVlanForm'); ?>?router_id=' + selectedRouterId + '&interface_name=' + encodeURIComponent(interfaceName);
                    window.location.href = url;
                });
                
                // Add IP button
                $(document).on('click', '.add-ip-btn', function() {
                    var interfaceName = $(this).data('interface');
                    console.log('Add IP clicked for interface:', interfaceName);
                    
                    // TODO: Implement Add IP functionality
                    alert('Add IP functionality will be implemented for interface: ' + interfaceName);
                });
                
                // Add Route button
                $(document).on('click', '.add-route-btn', function() {
                    var interfaceName = $(this).data('interface');
                    console.log('Add Route clicked for interface:', interfaceName);
                    
                    // TODO: Implement Add Route functionality
                    alert('Add Route functionality will be implemented for interface: ' + interfaceName);
                });
                
                console.log('Action buttons event handlers setup completed');
            }
            
            // Reset Filters button
            $('#clearFiltersBtn').on('click', function() {
                if (dataTable) {
                    dataTable.search('').columns().search('');
                }
                // Clear selects and search
                if ($('#interfaceTypeFilter').hasClass('select2-hidden-accessible')) {
                    $('#interfaceTypeFilter').val(null).trigger('change');
                    $('#statusFilter').val(null).trigger('change');
                } else {
                    $('#interfaceTypeFilter').val('');
                    $('#statusFilter').val('');
                }
                $('#searchInput').val('');
                // Remove custom filter and redraw
                if ($.fn.dataTable.ext.search._mikrotikFilter) {
                    var idx = $.fn.dataTable.ext.search.indexOf($.fn.dataTable.ext.search._mikrotikFilter);
                    if (idx > -1) $.fn.dataTable.ext.search.splice(idx, 1);
                    delete $.fn.dataTable.ext.search._mikrotikFilter;
                }
                if (dataTable) dataTable.draw();
            });
        });
    } catch (error) {
        console.error('Error in try block:', error);
        alert('An unexpected error occurred: ' + error.message);
    }
</script>

<!-- Custom CSS -->
<style>
    .dataTables_wrapper .dataTables_filter {
        display: none;
    }
    
    .dataTables_wrapper .dataTables_length {
        margin-bottom: 10px;
    }
    
    .table th {
        background-color: #f8f9fa;
        border-top: none;
    }
    
    .badge {
        font-size: 0.75em;
    }
    
    .card-tools .input-group {
        width: 250px !important;
    }
    
    .select2-container {
        width: 100% !important;
    }
    .controls-wrap > .btn { min-width: 130px; }
    .controls-wrap .form-check { margin-top: 6px; }
    #connectionStatus { line-height: 1.4; }
    /* Normalize Select2 height to match Bootstrap form-control */
    .select2-container .select2-selection--single {
        height: 38px !important; /* match .form-control default */
        border: 1px solid #ced4da;
        border-radius: .25rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        padding-left: .75rem;
        padding-right: 2rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        right: 8px;
    }
    .controls-wrap {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
    }
    .controls-wrap .btn {
        margin-right: 10px; /* Space between buttons */
    }
    .controls-wrap .form-check-inline {
        margin-right: 10px; /* Space between checkboxes and buttons */
    }
    
    /* Action buttons styling */
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        line-height: 1.2;
    }
    
    .btn-group-sm .btn i {
        margin-right: 2px;
    }
    
    /* Ensure action buttons are visible in responsive mode */
    .dtr-details .btn-group {
        margin-top: 5px;
    }
    
    /* Action column styling */
    .actions-column {
        min-width: 200px;
        white-space: nowrap;
    }
    
    /* Responsive adjustments for action buttons */
    @media (max-width: 768px) {
        .btn-group-sm .btn {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
        }
        
        .btn-group-sm .btn i {
            margin-right: 1px;
        }
    }
</style>
