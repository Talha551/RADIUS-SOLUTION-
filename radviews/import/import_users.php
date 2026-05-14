<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-upload"></i> Import Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Import Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <?php if($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
            <?php endif; ?>
            <?php if($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Import Users from CSV</h3>
                    <div class="card-tools">
                        <a href="<?php echo base_url('import/ImportUsers/logs'); ?>" class="btn btn-info btn-sm">
                            <i class="fas fa-history"></i> View Import Logs
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if($this->session->flashdata('start_import')): ?>
                        <div id="import-progress-container" class="alert alert-info">
                            <h5><i class="fas fa-spinner fa-spin"></i> Importing Users...</h5>
                            <p id="import-progress-text"><?php echo $this->session->flashdata('import_message'); ?></p>
                            
                            <div class="progress mb-3">
                                <div id="import-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                            </div>

                            <p><strong>Imported: <span id="import-current-count">0</span> / <span id="import-total-count"><?php echo $this->session->userdata('import_total'); ?></span></strong></p>
                            
                            <div style="max-height: 200px; overflow-y: auto; background: #f8f9fa; padding: 10px; border: 1px solid #dee2e6; border-radius: 4px;">
                                <ul id="import-log-list" class="list-unstyled mb-0" style="font-family: monospace; font-size: 12px;">
                                    <li>Ready to start...</li>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($managerList)): ?>
                    <form action="<?php echo base_url('import/ImportUsers'); ?>" method="POST" class="mb-3">
                        <div class="form-group">
                            <label for="managername">Select Manager</label>
                            <div class="input-group">
                                <div class="col-sm-4">
                                    <select class="form-control" name="managername" id="managername" required>
                                        <?php foreach($managerList as $manager): ?>
                                            <option value="<?php echo $manager->managername; ?>" <?php echo ($selectedManager == $manager->managername) ? 'selected' : ''; ?>><?php echo $manager->managername; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-group-append">
                                        <button type="submit" name="show_services" value="1" class="btn btn-secondary">Show Services</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php endif; ?>
                    <form action="<?php echo base_url('import/ImportUsers/upload'); ?>" method="POST" enctype="multipart/form-data">
                        <?php if (!empty($managerList)): ?>
                            <input type="hidden" name="managername" value="<?php echo htmlspecialchars($selectedManager); ?>">
                        <?php endif; ?>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <label for="import_file">CSV File</label>
                                <input type="file" class="form-control" name="import_file" id="import_file" accept=".csv" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <a href="<?php echo $sampleUrl; ?>" class="btn btn-info"><i class="fas fa-download"></i> Download Sample File</a>
                        </div>
                        <div class="form-group">
                            <label>Available Services for Manager <b><?php echo htmlspecialchars($selectedManager); ?></b>:</label>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Service ID</th>
                                            <th>Service Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($services)): ?>
                                            <?php foreach($services as $srv): ?>
                                                <tr>
                                                    <td><?php echo $srv->radsrvid; ?></td>
                                                    <td><?php echo $srv->srvname; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="2">No services found for this manager.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Required Fields & Example Data</label>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Field</th>
                                            <th>Example</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>username</td><td>johndoe</td><td>No spaces, min 4 chars, unique</td></tr>
                                        <tr><td>password</td><td>password123</td><td>Min 6 chars, no spaces</td></tr>
                                        <tr><td>firstname</td><td>John</td><td>Required</td></tr>
                                        <tr><td>lastname</td><td>Doe</td><td>Required</td></tr>
                                        <tr><td>address</td><td>123 Main St</td><td>Optional</td></tr>
                                        <tr><td>mobile</td><td>923001234567</td><td>Pakistan format: 03XXXXXXXXX or 92XXXXXXXXXX</td></tr>
                                        <tr><td>email</td><td>john@example.com</td><td>Optional, must be valid if provided</td></tr>
                                        <tr><td>taxid</td><td>1234567890</td><td>A CNIC or Tax ID is required</td></tr>
                                        <tr><td>expiration</td><td>2025-12-31</td><td>Optional, defaults to now. Any format, will be converted</td></tr>
                                        <tr><td>service_id</td><td>46</td><td>Must match a Service ID above</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Import</button>
                        </div>
                    </form>
                </div>
            </div>
    </section>
</div> 

<?php if($this->session->flashdata('start_import')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let totalRecords = parseInt('<?php echo $this->session->userdata("import_total") ?: 0; ?>');
    let currentRecord = 0;
    
    function processNextBatch() {
        fetch('<?php echo base_url("import/ImportUsers/process_ajax"); ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'complete') {
                // Done!
                document.getElementById('import-progress-text').innerHTML = '<strong>Import Completed Successfully!</strong> You can now view the updated logs.';
                document.getElementById('import-progress-bar').classList.remove('progress-bar-animated');
                
                let li = document.createElement('li');
                li.className = 'text-success font-weight-bold';
                li.innerText = 'File processing completed.';
                document.getElementById('import-log-list').appendChild(li);
                return;
            }

            if(data.status === 'processing') {
                currentRecord++;
                
                // Update specific counters
                document.getElementById('import-current-count').innerText = currentRecord;
                
                // Update progress bar
                let percent = Math.floor((currentRecord / totalRecords) * 100);
                let progressBar = document.getElementById('import-progress-bar');
                progressBar.style.width = percent + '%';
                progressBar.setAttribute('aria-valuenow', percent);
                progressBar.innerText = percent + '%';
                
                // Add to log list
                let li = document.createElement('li');
                if (data.success) {
                    li.className = 'text-success';
                    li.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                } else {
                    li.className = 'text-danger';
                    li.innerHTML = '<i class="fas fa-times-circle"></i> ' + data.message;
                }
                
                let logList = document.getElementById('import-log-list');
                logList.appendChild(li);
                
                // Auto scroll to bottom of log
                logList.parentElement.scrollTop = logList.parentElement.scrollHeight;

                // Process next record immediately
                processNextBatch();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            let li = document.createElement('li');
            li.className = 'text-danger';
            li.innerText = 'AJAX Request Failed. Stopping import process.';
            document.getElementById('import-log-list').appendChild(li);
            document.getElementById('import-progress-bar').classList.add('bg-danger');
        });
    }

    // Start cycle
    if (totalRecords > 0) {
        processNextBatch();
    }
});
</script>
<?php endif; ?>