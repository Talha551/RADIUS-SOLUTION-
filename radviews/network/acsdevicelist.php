<?php

$this->load->helper('url');
$base = base_url() . index_page();

// Helper function to get device value from multiple possible paths
function get_device_value($device, $primary_path, $fallback_path = [], $default = '-') {
    $value = $device;
    foreach ($primary_path as $key) {
        if (!isset($value[$key])) {
            $value = null;
            break;
        }
        $value = $value[$key];
    }
    if (!empty($value)) return $value;
    if (!empty($fallback_path)) {
        $value = $device;
        foreach ($fallback_path as $key) {
            if (!isset($value[$key])) {
                $value = null;
                break;
            }
            $value = $value[$key];
        }
        if (!empty($value)) return $value;
    }
    return $default;
}

// Filtering logic for Last Inform
$showDevices = isset($_POST['showDevices']) ? $_POST['showDevices'] : 'all';
$filteredDevices = $devices;
if (!empty($devices) && $showDevices !== 'all') {
    $now = time();
    $filteredDevices = array_filter($devices, function($device) use ($showDevices, $now) {
        if (empty($device['_lastInform'])) return false;
        $lastInform = strtotime($device['_lastInform']);
        if ($showDevices === '1h') {
            return ($now - $lastInform) <= 3600;
        } elseif ($showDevices === '24h') {
            return ($now - $lastInform) <= 86400;
        }
        return true;
    });
}

$serialSearch = isset($_POST['serialSearch']) ? trim($_POST['serialSearch']) : '';
if (!empty($serialSearch) && !empty($filteredDevices)) {
    $filteredDevices = array_filter($filteredDevices, function($device) use ($serialSearch) {
        if (empty($device['_id'])) return false;
        return stripos($device['_id'], $serialSearch) !== false;
    });
}
// Exclude devices with Model DISCOVERYSERVICE
$filteredDevices = array_filter($filteredDevices, function($device) {
    $model = get_device_value(
        $device,
        ['InternetGatewayDevice','DeviceInfo','ModelName','_value'],
        ['_deviceId','_ProductClass']
    );
    return strtoupper($model) !== 'DISCOVERYSERVICE';
});
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-network-wired"></i> ACS Device List</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">ACS Device List</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Devices from ACS Server</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo base_url(); ?>Network_controller/acsDeviceList" method="POST" class="mb-4">
                                <div class="row align-items-end">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="segmentId">Select ACS Server</label>
                                            <select class="form-control" id="segmentId" name="segmentId" required>
                                                <option value="">Select Server</option>
                                                <?php foreach($serverSegments as $segment) { ?>
                                                    <option value="<?php echo $segment->segmentid; ?>" <?php echo ($selectedSegmentId == $segment->segmentid) ? 'selected' : ''; ?>>
                                                        <?php echo $segment->segmentname; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="showDevices">Show devices</label>
                                            <select class="form-control" id="showDevices" name="showDevices">
                                                <option value="1h" <?php echo ($showDevices === '1h') ? 'selected' : ''; ?>>Last One Hour</option>
                                                <option value="24h" <?php echo ($showDevices === '24h') ? 'selected' : ''; ?>>Last 24 Hours</option>
                                                <option value="all" <?php echo ($showDevices === 'all') ? 'selected' : ''; ?>>All Devices</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="serialSearch">Search by Serial Number</label>
                                            <input type="text" class="form-control" id="serialSearch" name="serialSearch" value="<?php echo htmlspecialchars($serialSearch); ?>" placeholder="Enter serial number...">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary ml-md-2 w-100">Get Devices</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <?php if (!empty($api_error)) { ?>
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <?php echo htmlspecialchars($api_error); ?>
                                </div>
                            <?php } ?>
                            <?php if (empty($filteredDevices)) { ?>
                                <div class="alert alert-info">No devices found.</div>
                            <?php } else { ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Device ID</th>
                                            <th>Model</th>
                                            <th>Manufacturer</th>
                                            <th>Software Version</th>
                                            <th>Last Inform</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($filteredDevices as $device): 
                                            if (!empty($mappedDeviceNames) && in_array($device['_id'], $mappedDeviceNames)) continue;
                                        ?>
                                        <tr>
                                            <td><?php echo isset($device['_id']) ? htmlspecialchars($device['_id']) : '-'; ?></td>
                                            <td><?php echo htmlspecialchars(get_device_value(
                                                $device,
                                                ['InternetGatewayDevice','DeviceInfo','ModelName','_value'],
                                                ['_deviceId','_ProductClass']
                                            )); ?></td>
                                            <td><?php echo htmlspecialchars(get_device_value(
                                                $device,
                                                ['InternetGatewayDevice','DeviceInfo','Manufacturer','_value'],
                                                ['_deviceId','_Manufacturer']
                                            )); ?></td>
                                            <td><?php echo isset($device['InternetGatewayDevice']['DeviceInfo']['SoftwareVersion']['_value']) ? htmlspecialchars($device['InternetGatewayDevice']['DeviceInfo']['SoftwareVersion']['_value']) : '-'; ?></td>
                                            <td><?php 
                                                if (isset($device['_lastInform']) && !empty($device['_lastInform'])) {
                                                    $ts = strtotime($device['_lastInform']);
                                                    echo $ts ? date('n/j/Y, g:i:s A', $ts) : '-';
                                                } else {
                                                    echo '-';
                                                }
                                            ?></td>
                                            <td><?php echo isset($device['InternetGatewayDevice']['DeviceInfo']['Description']['_value']) ? htmlspecialchars($device['InternetGatewayDevice']['DeviceInfo']['Description']['_value']) : '-'; ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info map-user-btn" 
                                                    data-deviceid="<?php echo htmlspecialchars($device['_id']); ?>">
                                                    MAP USER
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- MAP USER Modal -->
<div class="modal fade" id="mapUserModal" tabindex="-1" role="dialog" aria-labelledby="mapUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mapUserModalLabel">Map Device to User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="mapUserForm">
          <input type="hidden" name="devicetype" value="0">
          <div class="form-group">
            <label for="devicename">Device ID</label>
            <input type="text" class="form-control" id="devicename" name="devicename" readonly>
          </div>
          <div class="form-group">
            <label for="username">Username</label>
            <select class="form-control" id="username" name="username" style="width:100%"></select>
          </div>
          <div class="form-group">
            <label for="details">Details</label>
            <textarea class="form-control" id="details" name="details" rows="2"></textarea>
          </div>
          <div id="mapUserMsg" class="mb-2"></div>
          <button type="submit" class="btn btn-primary">Map User</button>
        </form>
      </div>
    </div>
  </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>

$(function() {
    // Open modal and set device id
    $('.map-user-btn').on('click', function() {
        var deviceid = $(this).data('deviceid');
        $('#devicename').val(deviceid);
        $('#username').val(null).trigger('change');
        $('#details').val('');
        $('#mapUserMsg').html('');
        $('#mapUserModal').modal('show');
    });
    // Init select2 for username
    $('#username').select2({
        theme: 'bootstrap4',
        dropdownParent: $('#mapUserModal'),
        placeholder: 'Type to search user...',
        minimumInputLength: 2,
        ajax: {
            url: '<?php echo base_url(); ?>Network_controller/getActiveUsersForDeviceMapAjax',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { q: params.term };
            },
            processResults: function(data) {
                return data;
            },
            cache: true
        }
    });
    // Handle form submit
    var mappingSuccess = false;
    $('#mapUserForm').on('submit', function(e) {
        e.preventDefault();
        $('#mapUserMsg').html('');
        $.ajax({
            url: '<?php echo base_url(); ?>Network_controller/mapDeviceToUser',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(resp) {
                if (resp.success) {
                    $('#mapUserMsg').html('<div class="alert alert-success">' + resp.message + '</div>');
                    mappingSuccess = true;
                    setTimeout(function() {
                        $('#mapUserModal').modal('hide');
                    }, 1000);
                } else {
                    $('#mapUserMsg').html('<div class="alert alert-danger">' + resp.message + '</div>');
                }
            },
            error: function() {
                $('#mapUserMsg').html('<div class="alert alert-danger">Server error. Please try again.</div>');
            }
        });
    });
    // Refresh device list after modal closes if mapping was successful
    $('#mapUserModal').on('hidden.bs.modal', function () {
        if (mappingSuccess) {
            location.reload();
        }
        mappingSuccess = false;
    });
});
</script> 