<?php
$this->load->helper('form');
$success = $this->session->flashdata('success');
$error = $this->session->flashdata('error');
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-bell"></i> <?= empty($alert) ? 'Add New Alert' : 'Edit Alert' ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('Whatsapp_controller/alerts') ?>">WhatsApp Alerts</a></li>
                        <li class="breadcrumb-item active"><?= empty($alert) ? 'Add New' : 'Edit' ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if($success) { ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?= $success ?>
            </div>
            <?php } ?>
            <?php if($error) { ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?= $error ?>
            </div>
            <?php } ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?= empty($alert) ? 'Add New Alert' : 'Edit Alert' ?></h3>
                        </div>
                        <form role="form" action="<?= empty($alert) ? base_url('Whatsapp_controller/create_alert') : base_url('Whatsapp_controller/update_alert/'.$alert->id) ?>" method="post">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="alert_name">Alert Name</label>
                                            <input type="text" class="form-control" id="alert_name" name="alert_name" 
                                                   value="<?= isset($alert) ? $alert->alert_name : '' ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="managername">Manager</label>
                                            <select class="form-control" id="managername" name="managername" required>
                                                <option value="">Select Manager</option>
                                                <?php foreach($managers as $manager) { ?>
                                                <option value="<?= $manager->managername ?>" 
                                                    <?= (isset($alert) && $alert->managername == $manager->managername) ? 'selected' : '' ?>>
                                                    <?= $manager->managername ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="alert_type">Alert Type</label>
                                            <select class="form-control" id="alert_type" name="alert_type" required>
                                                <option value="">Select Type</option>
                                                <option value="0" <?= (isset($alert) && $alert->alert_type == 0) ? 'selected' : '' ?>>Welcome</option>
                                                <option value="1" <?= (isset($alert) && $alert->alert_type == 1) ? 'selected' : '' ?>>Expiration</option>
                                                <option value="2" <?= (isset($alert) && $alert->alert_type == 2) ? 'selected' : '' ?>>Invoice</option>
                                                <option value="3" <?= (isset($alert) && $alert->alert_type == 3) ? 'selected' : '' ?>>Payment</option>
                                                <option value="4" <?= (isset($alert) && $alert->alert_type == 4) ? 'selected' : '' ?>>Easypaisa</option>
                                                <option value="5" <?= (isset($alert) && $alert->alert_type == 5) ? 'selected' : '' ?>>Promotions</option>
                                                <option value="6" <?= (isset($alert) && $alert->alert_type == 6) ? 'selected' : '' ?>>Taxation</option>
                                                <option value="7" <?= (isset($alert) && $alert->alert_type == 7) ? 'selected' : '' ?>>NAS Status</option>
                                                <option value="8" <?= (isset($alert) && $alert->alert_type == 8) ? 'selected' : '' ?>>Area Status</option>
                                                <option value="9" <?= (isset($alert) && $alert->alert_type == 9) ? 'selected' : '' ?>>Network Status</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="waha_session_id">WhatsApp Session</label>
                                            <?php
                                                $baileys_list = isset($baileys_sessions) && is_array($baileys_sessions) ? $baileys_sessions : array();
                                                $db_session_names = array();
                                                foreach ($sessions as $_s) {
                                                    if (isset($_s->session_name) && $_s->session_name !== '') {
                                                        $db_session_names[$_s->session_name] = true;
                                                    }
                                                }
                                                $current_sid = isset($alert) ? (string) $alert->waha_session_id : '';
                                                $found_current = ($current_sid !== '' && isset($db_session_names[$current_sid]));
                                                if (!$found_current && $current_sid !== '') {
                                                    foreach ($baileys_list as $_row) {
                                                        if (isset($_row['session_id']) && (string) $_row['session_id'] === $current_sid) {
                                                            $found_current = true;
                                                            break;
                                                        }
                                                    }
                                                }
                                            ?>
                                            <?php if (!empty($baileys_sessions_error)) { ?>
                                            <div class="alert alert-warning py-1 px-2 small mb-2"><?= htmlspecialchars($baileys_sessions_error) ?></div>
                                            <?php } ?>
                                            <select class="form-control" id="waha_session_id" name="waha_session_id" required>
                                                <option value="">Select Session</option>
                                                <?php if ($current_sid !== '' && !$found_current) { ?>
                                                <option value="<?= htmlspecialchars($current_sid) ?>" selected><?= htmlspecialchars($current_sid) ?> (saved — not in DB or API list)</option>
                                                <?php } ?>
                                                <optgroup label="Database (tbl_whatsapp_sessions)">
                                                <?php foreach($sessions as $session) { ?>
                                                <option value="<?= htmlspecialchars($session->session_name) ?>"
                                                    <?= (isset($alert) && $alert->waha_session_id == $session->session_name) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($session->session_name) ?>
                                                </option>
                                                <?php } ?>
                                                </optgroup>
                                                <optgroup label="Baileys API (live)">
                                                <?php foreach ($baileys_list as $brow) {
                                                    $bsid = isset($brow['session_id']) ? (string) $brow['session_id'] : '';
                                                    if ($bsid === '' || !empty($db_session_names[$bsid])) {
                                                        continue;
                                                    }
                                                    $bst = isset($brow['status']) ? (string) $brow['status'] : '';
                                                    $blabel = $bsid . ($bst !== '' ? ' — ' . $bst : '');
                                                ?>
                                                <option value="<?= htmlspecialchars($bsid) ?>"
                                                    <?= (isset($alert) && $alert->waha_session_id == $bsid) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($blabel) ?>
                                                </option>
                                                <?php } ?>
                                                </optgroup>
                                            </select>
                                            <small class="form-text text-muted">Value stored is the session id (same field used for Baileys <code>instance_id</code> in workers). Baileys list respects your login; admin sees all API sessions.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mobile">Mobile Number</label>
                                            <input type="text" class="form-control" id="mobile" name="mobile" 
                                                   value="<?= isset($alert) ? $alert->mobile : '' ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="1" <?= (isset($alert) && $alert->status == 1) ? 'selected' : '' ?>>Active</option>
                                                <option value="0" <?= (isset($alert) && $alert->status == 0) ? 'selected' : '' ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="interval">Interval (minutes)</label>
                                            <input type="number" class="form-control" id="interval" name="interval" 
                                                   value="<?= isset($alert) ? $alert->interval : '60' ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="duration">Duration (days)</label>
                                            <input type="number" class="form-control" id="duration" name="duration" 
                                                   value="<?= isset($alert) ? $alert->duration : '30' ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="frequency">Frequency (per day)</label>
                                            <input type="number" class="form-control" id="frequency" name="frequency" 
                                                   value="<?= isset($alert) ? $alert->frequency : '1' ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="alert_message_row" style="display: none;">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="alert_message">Alert Message</label>
                                            <textarea class="form-control" id="alert_message" name="alert_message" rows="3"><?= isset($alert) ? $alert->alert_message : '' ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="<?= base_url('Whatsapp_controller/alerts') ?>" class="btn btn-default">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    // Handle alert type change
    $('#alert_type').change(function() {
        var type = $(this).val();
        var managername = $('#managername').val();
        
        // Show/hide message field for promotions
        if(type == '5') {
            $('#alert_message_row').show();
            $('#alert_message').prop('required', true);
        } else {
            $('#alert_message_row').hide();
            $('#alert_message').prop('required', false);
        }
        
        // Auto-fill mobile for status alerts
        if(type >= '0' && type <= '9' && managername) {
            $.ajax({
                url: '<?= base_url('Whatsapp_controller/get_manager_mobile') ?>',
                type: 'POST',
                data: { managername: managername },
                success: function(response) {
                    var data = JSON.parse(response);
                    if(data.mobile) {
                        $('#mobile').val(data.mobile);
                    }
                }
            });
        }
    });

    // Trigger change on load if editing
    $('#alert_type').trigger('change');
});
</script> 