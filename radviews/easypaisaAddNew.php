<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><i class="fa fa-users"></i> Generate Easy Paisa List</h1>
            <small class="text-muted">Add / Edit User</small>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Generate Easy Paisa List</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-lg-8">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-alt"></i> Easy Paisa Listing</h3>
              </div>
              <div class="card-body">
                <?php $this->load->helper("form"); ?>
                <form role="form" id="easypaisaAddNew" action="<?php echo base_url() ?>saveEasypaisaList" method="post" role="form">
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="filename">File Name</label>
                      <input type="text" class="form-control required" value="<?php echo set_value('filename'); ?>" id="filename" name="filename" maxlength="150">
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-4">
                      <label for="billingmonth">Billing Month (YYYYMM)</label>
                      <input type="text" class="form-control required" id="billingmonth" value="<?php echo set_value('billingmonth'); ?>" name="billingmonth" maxlength="6" autocomplete="off">
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-4">
                      <label for="duedate">Due Date (YYYYMMDD)</label>
                      <input type="text" class="form-control required" id="duedate" value="<?php echo set_value('duedate'); ?>" name="duedate" maxlength="8" autocomplete="off">
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-8">
                      <label for="filepath">Target File</label>
                      <input type="text" class="form-control required" id="filepath" value="<?php echo 'easypaisa_report_on_'.date('Ymd').'.csv'; ?>" name="filepath" maxlength="200" autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group mt-3">
                    <input type="submit" class="btn btn-primary" value="Submit" />
                    <input type="reset" class="btn btn-secondary" value="Reset" />
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <?php
              $this->load->helper('form');
              $error = $this->session->flashdata('error');
              if($error)
              {
            ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?php echo $this->session->flashdata('error'); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php } ?>
            <?php  
              $success = $this->session->flashdata('success');
              if($success)
              {
            ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?php echo $this->session->flashdata('success'); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php } ?>
            <div class="row">
              <div class="col-12">
                <?php echo validation_errors('<div class="alert alert-danger alert-dismissible fade show">', ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
</div>
<script src="<?php echo base_url(); ?>assets/js/usersAddNew.js" type="text/javascript"></script>