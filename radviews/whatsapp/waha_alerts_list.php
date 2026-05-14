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
                    <h1><i class="fas fa-bell"></i> WhatsApp Alerts</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                        <li class="breadcrumb-item active">WhatsApp Alerts</li>
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
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">WhatsApp Alerts List</h3>
                            <?php if($this->session->userdata('name') == 'admin' || $this->ismaster > 0) { ?>
                                <div class="card-tools">
                                    <a href="<?= base_url('Whatsapp_controller/add_alert') ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Add New Alert
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="alerts_table" class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Alert Name</th>
                                        <th>Manager</th>
                                        <th>Type</th>
                                        <th>Mobile</th>
                                        <th>WhatsApp Session</th>
                                        <th>Status</th>
                                        <th>Last Sent</th>
                                        <?php if($this->session->userdata('type') == 'admin' || $this->ismaster > 0) { ?>
                                        <th>Actions</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($alerts)) {
                                        foreach($alerts as $alert) { ?>
                                    <tr>
                                        <td><?= $alert->id ?></td>
                                        <td><?= $alert->alert_name ?></td>
                                        <td><?= $alert->managername ?></td>
                                        <td><?= get_alert_type_name($alert->alert_type) ?></td>
                                        <td><?= $alert->mobile ?></td>
                                        <td><?= $alert->waha_session_id ?></td>
                                        <td>
                                            <?php if($alert->status == 1) { ?>
                                                <span class="badge badge-success">Active</span>
                                            <?php } else { ?>
                                                <span class="badge badge-danger">Inactive</span>
                                            <?php } ?>
                                        </td>
                                        <td><?= $alert->last_sent ? date('Y-m-d H:i:s', strtotime($alert->last_sent)) : 'Never' ?></td>

                                        <?php if($this->session->userdata('name') == 'admin' || $this->ismaster > 0) { ?>
                                            <td>
                                                <a href="<?= base_url('Whatsapp_controller/edit_alert/'.$alert->id) ?>" class="btn btn-info btn-sm">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <a href="<?= base_url('Whatsapp_controller/delete_alert/'.$alert->id) ?>" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('Are you sure you want to delete this alert?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    $('#alerts_table').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true
    });
});
</script>

<?php
function get_alert_type_name($type) {
    $types = array(
        0 => 'Welcome',
        1 => 'Expiration',
        2 => 'Invoice',
        3 => 'Payment',
        4 => 'Easypaisa',
        5 => 'Promotions',
        6 => 'Taxation',
        7 => 'NAS Status',
        8 => 'Area Status',
        9 => 'Network Status'
    );
    return isset($types[$type]) ? $types[$type] : 'Unknown';
} 