<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Edit Mapped Device</h1>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Edit Device Mapping</h3>
                        </div>
                        <div class="card-body">
                            <form method="post" action="<?php echo site_url('Network_controller/updateMappedDevice'); ?>">
                                <input type="hidden" name="deviceid" value="<?php echo htmlspecialchars($device->deviceid); ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Device Name</label>
                                            <input type="text" name="devicename" class="form-control" value="<?php echo htmlspecialchars($device->devicename); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Device Type</label>
                                            <select name="devicetype" class="form-control" required>
                                                <option value="0" <?php if($device->devicetype==0) echo 'selected'; ?>>ONT</option>
                                                <option value="1" <?php if($device->devicetype==1) echo 'selected'; ?>>OLT</option>
                                                <option value="2" <?php if($device->devicetype==2) echo 'selected'; ?>>SWITCH</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Username</label>
                                            <select id="username" name="username" class="form-control" required>
                                                <option value="<?php echo htmlspecialchars($device->username); ?>" selected><?php echo htmlspecialchars($device->username); ?></option>
                                            </select>
                                        </div>
                                    </div>
                              <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Manager Name</label>
                                            <input type="text" id="managername" name="managername" class="form-control" value="<?php echo htmlspecialchars($device->managername); ?>" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Master Device ID</label>
                                            <select name="masterdeviceid" class="form-control">
                                                <option value="0">None</option>
                                                <?php if (!empty($masterDevices)): ?>
                                                    <?php foreach ($masterDevices as $md): ?>
                                                        <?php if ($md->deviceid != $device->deviceid): // Prevent self as master ?>
                                                            <option value="<?php echo $md->deviceid; ?>" <?php if($device->masterdeviceid == $md->deviceid) echo 'selected'; ?>><?php echo htmlspecialchars($md->devicename); ?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>RX</label>
                                            <input type="text" name="para1" class="form-control" value="<?php echo htmlspecialchars($device->para1); ?>" readonly>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>TX</label>
                                            <input type="text" name="para2" class="form-control" value="<?php echo htmlspecialchars($device->para2); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Is Active</label>
                                            <select name="isactive" class="form-control">
                                                <option value="1" <?php if($device->isactive==1) echo 'selected'; ?>>Active</option>
                                                <option value="0" <?php if($device->isactive==0) echo 'selected'; ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Details</label>
                                            <textarea name="details" class="form-control"><?php echo htmlspecialchars($device->details); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                        <a href="<?php echo site_url('Network_controller/mappedDeviceList'); ?>" class="btn btn-default">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Add Select2 CSS/JS if not already included -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#username').select2({
        ajax: {
            url: '<?php echo site_url('Network_controller/getActiveUsersForDeviceMapAjax'); ?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return { results: data.results };
            },
            cache: true
        },
        minimumInputLength: 1,
        width: '100%'
    });
    $('#username').on('change', function() {
        var username = $(this).val();
        if(username) {
            $.ajax({
                url: '<?php echo site_url('Network_controller/getUserOwnerAjax'); ?>',
                data: { username: username },
                dataType: 'json',
                success: function(data) {
                    $('#managername').val(data.owner);
                }
            });
        } else {
            $('#managername').val('');
        }
    });
});
</script>
<style>
/* Make Select2 match Bootstrap form-control height and font */
.select2-container--default .select2-selection--single {
    height: 38px !important;
    padding: 6px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 1rem;
    line-height: 1.5;
    background-color: #fff;
    box-sizing: border-box;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 24px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
    right: 10px;
}
</style> 