<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> Settings Management
        <small>Add / Edit User</small>
      </h1>
    </section>
    
    <section class="content">
    
        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
              <!-- general form elements -->
                
                
                
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Enter Setting Details</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <?php $this->load->helper("form"); ?>
                    <form role="form" id="settingSaveNew" action="<?php echo base_url() ?>settingSaveNew" method="post" role="form">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">                                
                                    <div class="form-group">
                                        <label for="stgname">Setting Name</label>
                                        <input type="text" class="form-control required" value="<?php echo set_value('stgname'); ?>" id="stgname" name="stgname" maxlength="128">
                                    </div>
                                    
                                </div>
                        </div>
                        

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="stgtype">Setting Type</label>
                                        <select class="form-control required" id="stgtype" name="stgtype">
                                            <option value="0">Select Type</option>
                                            <option value="GRACE-DAYS">Allow Grace Days</option>
                                            <option value="POSTPAID-MANAGER">Post Paid Account Limit</option>
                                            <option value="ALLOW-ACCOUNTS">Allow Accounts to Manager</option>
                                            <option value="DEFAULT-ACCOUNT">Default Account Setting</option>
                                            <option value="ALLOW-SMS">Allow SMS</option>
                                            <option value="ALLOW-BULK-SMS">Allow BULK SMS</option>
                                            <option value="TAX-GST">TAX GST Rate</option>
                                            <option value="TAX-AIT">TAX AIT Rate</option>
                                            <option value="DIV-RATIO">DIV Ratio</option>
                                            <option value="CHECK-MAC">Hotspot Random Mac Check</option>
                                            <option value="BIND-MAC">BIND USER MAC</option>
                                        </select>
                                    </div>
                                </div>  
                                
                            </div>
                            <div class="row">
                                <div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="stgvalue">Setting Value</label>
                                            <input type="text" class="form-control required digits" id="stgvalue" value="<?php echo set_value('stgvalue'); ?>" name="stgvalue" maxlength="10">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="manager">Manager</label>
                                        <select class="form-control required" id="manager" name="manager">
                                            <option value="">Select Manager</option>
                                            <?php
                                                if(!empty($managerList))
                                                {
                                                    foreach ($managerList as $rl)
                                                    {
                                                        ?>
                                                            <option value="<?php echo $rl->managername ?>" <?php if($rl->managername == set_value('managername')) {echo "selected=selected";} ?>><?php echo $rl->managername ?></option>
                                                        <?php
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div><!-- /.box-body -->
    
                        <div class="card-footer">
                            <input type="submit" class="btn btn-primary" value="Submit" />
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
