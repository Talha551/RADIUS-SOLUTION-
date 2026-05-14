<?php $this->load->helper('url'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fab fa-whatsapp text-success"></i> Add Baileys Session</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="<?php echo site_url('Whatsapp_controller/whats_sessions'); ?>" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back to Baileys sessions</a>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
            <?php endif; ?>
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">New Baileys session</h3>
                </div>
                <form method="post" action="<?php echo site_url('Whatsapp_controller/whats_session_create'); ?>">
                    <div class="card-body">
                        <p class="text-muted small">Creates the session on your Baileys FastAPI (<code>POST /sessions</code>). Use letters, digits, <code>_</code>, <code>-</code> only (max 64 chars). Then open the session list and scan the QR code.</p>
                        <?php
                            $is_admin = isset($baileys_is_admin) && $baileys_is_admin;
                            $mgr = isset($baileys_manager_login) ? trim((string) $baileys_manager_login) : '';
                        ?>
                        <?php if (!$is_admin && $mgr !== ''): ?>
                            <div class="alert alert-info small mb-3">
                                <strong>Your sessions:</strong> use <code><?php echo htmlspecialchars($mgr); ?></code> as the session id, or <code><?php echo htmlspecialchars($mgr); ?>_</code> followed by a suffix (e.g. <code><?php echo htmlspecialchars($mgr); ?>_office</code>). The part before the first underscore must match your login. Only <strong>admin</strong> may use arbitrary session names.
                            </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="session_id">Session ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="session_id" name="session_id" required maxlength="64" pattern="[a-zA-Z0-9_-]{1,64}" placeholder="<?php echo $is_admin ? 'e.g. phone-main' : htmlspecialchars($mgr . '_office'); ?>">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success"><i class="fa fa-plus"></i> Create on Baileys API</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
