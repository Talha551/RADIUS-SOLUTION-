<?php $this->load->helper('url'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fab fa-whatsapp"></i> Add WhatsApp Session</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="<?php echo site_url('Whatsapp_controller/index'); ?>" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back to List</a>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <?php if($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>
            <?php if($this->session->flashdata('show_manual_fallback')): ?>
                <div class="alert alert-info alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    Fill <strong>Manual WA session ID</strong> and <strong>Manual status</strong> (e.g. from Baileys or your notes), keep the same session name and manager, then submit again.
                </div>
            <?php endif; ?>
            <?php
                $old_session_name = $this->session->flashdata('old_session_name');
                $old_managername = $this->session->flashdata('old_managername');
                $old_session_name = is_string($old_session_name) ? $old_session_name : '';
                $old_managername_val = is_string($old_managername) ? $old_managername : '';
            ?>
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">New Session</h3>
                </div>
                <form method="post" action="<?php echo site_url('Whatsapp_controller/create'); ?>">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="session_name">Session Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="session_name" name="session_name" required maxlength="64" placeholder="Enter a name for this session" value="<?php echo htmlspecialchars($old_session_name, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="managername">Manager Name <span class="text-danger">*</span></label>
                            <select class="form-control" id="managername" name="managername" required>
                                <option value="">Select Manager</option>
                                <?php if (!empty($managers)): foreach ($managers as $manager): ?>
                                    <?php $sel = ($old_managername_val !== '' && $manager->managername === $old_managername_val) ? ' selected' : ''; ?>
                                    <option value="<?php echo htmlspecialchars($manager->managername); ?>"<?php echo $sel; ?>>
                                        <?php echo htmlspecialchars($manager->managername); ?>
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <hr>
                        <p class="text-muted small mb-2">If WAHA <code>sessions/start</code> fails, enter these and submit again — values are stored as <code>waha_session_id</code> and <code>status</code> in the database.</p>
                        <div class="form-group">
                            <label for="manual_waha_session_id">Manual WA session ID</label>
                            <input type="text" class="form-control" id="manual_waha_session_id" name="manual_waha_session_id" maxlength="255" placeholder="e.g. phone-main (must match your WhatsApp API session id)">
                        </div>
                        <div class="form-group">
                            <label for="manual_status">Manual status</label>
                            <input type="text" class="form-control" id="manual_status" name="manual_status" maxlength="64" placeholder="e.g. connected, qr_pending">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success"><i class="fa fa-plus"></i> Create Session</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div> 