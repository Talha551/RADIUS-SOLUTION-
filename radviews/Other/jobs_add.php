<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Add Job <small>to Queue</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo base_url('Other_controller/jobsList'); ?>">Jobs Queue</a></li>
            <li class="breadcrumb-item active">Add Job</li>
          </ol>
        </div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-8 offset-md-2">
          <?php echo validation_errors('<div class="alert alert-danger">','</div>'); ?>
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Add New Job</h3>
            </div>
            <form method="post" action="<?php echo base_url('Other_controller/jobsAdd'); ?>">
              <div class="card-body">
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="jobtype">Job Type</label>
                    <select class="form-control" name="jobtype" id="jobtype" required>
                      <option value="0" <?php echo set_select('jobtype', '0', TRUE); ?>>Controller</option>
                      <option value="1" <?php echo set_select('jobtype', '1'); ?>>WebHook</option>
                      <option value="2" <?php echo set_select('jobtype', '2'); ?>>Backup</option>
                      <option value="3" <?php echo set_select('jobtype', '3'); ?>>CronJob</option>
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="jobname">Job Name</label>
                    <input type="text" class="form-control" name="jobname" id="jobname" value="<?php echo set_value('jobname'); ?>" required>
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-8">
                    <label for="url">Job URL</label>
                    <input type="text" class="form-control" name="url" id="url" value="<?php echo set_value('url'); ?>" required>
                  </div>
                  <div class="form-group col-md-4 d-flex align-items-center">
                    <div class="form-check mt-4">
                      <input type="checkbox" class="form-check-input" name="active" id="active" value="1" <?php echo set_checkbox('active', '1', TRUE); ?>>
                      <label class="form-check-label" for="active">Active</label>
                    </div>
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label for="jobstart">Job Start DateTime</label>
                    <input type="datetime-local" class="form-control" name="jobstart" id="jobstart" value="<?php echo set_value('jobstart'); ?>" required>
                  </div>
                  <div class="form-group col-md-4">
                    <label for="jobend">Job End DateTime</label>
                    <input type="datetime-local" class="form-control" name="jobend" id="jobend" value="<?php echo set_value('jobend'); ?>" required>
                  </div>
                  <div class="form-group col-md-4">
                    <label for="jobinterval">Job Interval (seconds)</label>
                    <input type="number" class="form-control" name="jobinterval" id="jobinterval" value="<?php echo set_value('jobinterval'); ?>" required>
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-primary">Add Job</button>
                <a href="<?php echo base_url('Other_controller/jobsList'); ?>" class="btn btn-secondary">Back</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div> 