<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> Edit Dynamic DNS Mapping Service
        <small>Edit NAS</small>
      </h1>
    </section>
    
    <section class="content">
    
        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
              <!-- general form elements -->
                
                
                
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">NAS Details</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <?php $this->load->helper("form"); ?>
                    <form role="form" id="saveNewService" action="<?php echo base_url() ?>Other_controller/saveNasDynamicMap" method="post" role="form">
                        
                        <div class="box-body">

                            <div class="row">
                                <div class="col-md-6">                                
                                    <div class="form-group">
                                        <label for="shortname">Short Name</label>
                                        <input type="text" class="form-control required" value="<?php echo set_value('shortname'); ?>" id="domain" name="shortname" maxlength="50" autocomplete="off" placeholder="NAS Name">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">                                
                                    <div class="form-group">
                                        <label for="nasname">NAS</label>
                                        <input type="text" class="form-control required" value="<?php echo set_value('nasname'); ?>" id="nasname" name="nasname" maxlength="50" autocomplete="off" placeholder="NAS IP Address">
                                    </div>
                                </div>
                                <div class="col-md-4">                                
                                    <div class="form-group">
                                        <label for="secret">Secret</label>
                                        <input type="text" class="form-control required" value="<?php echo set_value('secret'); ?>" id="secret" name="secret" maxlength="50" autocomplete="off" placeholder="NAS Secret">
                                    </div>
                                </div>
                            </div>



                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="authmode">Authentication Method</label>
                                        <select class="form-control required" id="authmode" name="authmode">
                                            <option value="0">Radius Only</option>
                                            <option value="1">API Only</option>
                                            <option value="2">Radius Auth/Add User API</option>
                                            <option value="3">Radius Auth/Add User API & Disable</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">                                
                                    <div class="form-group">
                                        <label for="domain">Link API</label>
                                        <input type="text" class="form-control required" value="<?php echo set_value('domain'); ?>" id="domain" name="domain" maxlength="50" autocomplete="off" placeholder="API Valid DOMAIN">
                                    </div>
                                </div>
                            </div>
                                
                            <div class="row">
                                <div class="col-md-4">                                
                                    <div class="form-group">
                                        <label for="apiuser">API User</label>
                                        <input type="text" class="form-control required" value="<?php echo set_value('apiuser'); ?>" id="apiuser" name="apiuser" maxlength="50" autocomplete="off" placeholder="API User">
                                    </div>
                                </div>

                                <div class="col-md-4">                                
                                    <div class="form-group">
                                        <label for="apipasswd">API Password</label>
                                        <input type="text" class="form-control required" value="<?php echo set_value('apipasswd'); ?>" id="apipasswd" name="apipasswd" maxlength="50" autocomplete="off" placeholder="API Password">
                                    </div>
                                </div>

                                <div class="col-md-2">                                
                                    <div class="form-group">
                                        <label for="apiport">API Port</label>
                                        <input type="text" class="form-control required" value="<?php echo set_value('apiport'); ?>" id="apiport" name="apiport" maxlength="50" autocomplete="off" placeholder="API Port">
                                    </div>
                                </div>

                                <div class="col-md-4">                                
                                    <div class="form-group">
                                        <label for="description">Router ID (Optional for Dynamic Billing)</label>
                                        <input type="text" class="form-control required" value="<?php echo set_value('description'); ?>" id="domain" name="description" maxlength="50" autocomplete="off" placeholder="Router Identity OR Calling Station ID">
                                    </div>
                                </div>

                                
                            </div>

                            <div class="box-header">
                                <h3 class="box-title">Important Note</h3>
                                <div class="row">
                                    <div class="col-md-12">
                                        <small>NOTE: For dynamic billing first priority will be give to Router Radius Calling Station ID 
                                            <b>(Hotspot Server Name)</b> or <b>Mikrotik Router Identity</b>
                                        </small>
                                    </div>
                                </div>
                            </div><!-- /.box-header -->

                            


                        </div><!-- /.box-body -->
    
                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" value="Allow NAS" />
                            <input type="reset" class="btn btn-default" value="Reset" />
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4">
                <?php
                    $this->load->helper('form');
                    $error = $this->session->flashdata('error');
                    if($error)
                    {
                ?>
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo $this->session->flashdata('error'); ?>                    
                </div>
                <?php } ?>
                <?php  
                    $success = $this->session->flashdata('success');
                    if($success)
                    {
                ?>
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
                <?php } ?>
                
                <div class="row">
                    <div class="col-md-12">
                        <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                    </div>
                </div>
            </div>
        </div>    
    </section>
    
</div>
<script src="<?php echo base_url(); ?>assets/js/usersAddNew.js" type="text/javascript"></script>