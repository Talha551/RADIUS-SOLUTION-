<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-users"></i> Service Profile Management
                        <small>Add / Edit Service Profile</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Add Service Profile</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-8">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Enter Service Profile Details</h3>
                        </div><!-- /.card-header -->
                        <!-- form start -->
                        <?php $this->load->helper("form"); ?>
                        <form role="form" id="addService" action="<?php echo base_url() ?>Services_controller/spSaveService" method="post" role="form">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="srvname">Service Name</label><span style="color:red;">*</span>
                                            <input type="text" class="form-control required" value="<?php echo set_value('srvname'); ?>" id="srvname" name="srvname" maxlength="50">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="descr">Description</label>
                                            <input type="text" class="form-control" id="descr" value="<?php echo set_value('descr'); ?>" name="descr" maxlength="255">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="downrate">Download Rate (Mbps)</label><span style="color:red;">*</span>
                                            <input type="number" class="form-control required" id="downrate" value="<?php echo set_value('downrate'); ?>" name="downrate">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="uprate">Upload Rate (Mbps)</label><span style="color:red;">*</span>
                                            <input type="number" class="form-control required" id="uprate" value="<?php echo set_value('uprate'); ?>" name="uprate">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="limitdl">Limit Download</label>
                                            <select class="form-control" id="limitdl" name="limitdl">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="limitul">Limit Upload</label>
                                            <select class="form-control" id="limitul" name="limitul">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="limitcomb">Limit Combined</label>
                                            <select class="form-control" id="limitcomb" name="limitcomb">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="limitexpiration">Limit Expiration</label>
                                            <select class="form-control" id="limitexpiration" name="limitexpiration">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="limituptime">Limit Uptime</label>
                                            <select class="form-control" id="limituptime" name="limituptime">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="poolname">Pool Name</label>
                                            <input type="text" class="form-control" id="poolname" value="<?php echo set_value('poolname'); ?>" name="poolname" maxlength="50">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="enableservice">Enable Service</label>
                                            <select class="form-control" id="enableservice" name="enableservice">
                                                <option value="0">No</option>
                                                <option value="1" selected>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="srvtype">Service Type</label>
                                            <select class="form-control" id="srvtype" name="srvtype">
                                                <option value="0">Prepaid Registration</option>
                                                <option value="1">Prepaid Card</option>
                                                <option value="2">Postpaid</option>
                                                <option value="3">Email</option>
                                                <option value="4">ACL</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                

                            </div><!-- /.card-body -->



                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Burst Limits</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Billing</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#expiration" data-toggle="tab">Expiration</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#radiusAttributes" data-toggle="tab">Radius Attributes</a></li>
                                    </ul>
                                </div><!-- /.card-header -->
                                <div class="card-body">
                                    <div class="tab-content">
                                    <div class="active tab-pane" id="activity">
                                        <!-- Post -->

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        
                                                            
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="enableburst">Enable Burst</label>
                                                                            <select class="form-control" id="enableburst" name="enableburst">
                                                                                <option value="0">No</option>
                                                                                <option value="1">Yes</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="dlburstlimit">Download Burst Limit (Kbps)</label>
                                                                            <input type="number" class="form-control" id="dlburstlimit" name="dlburstlimit" value="0">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="ulburstlimit">Upload Burst Limit (Kbps)</label>
                                                                            <input type="number" class="form-control" id="ulburstlimit" name="ulburstlimit" value="0">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="dlburstthreshold">Download Burst Threshold (Kbps)</label>
                                                                            <input type="number" class="form-control" id="dlburstthreshold" name="dlburstthreshold" value="0">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="ulburstthreshold">Upload Burst Threshold (Kbps)</label>
                                                                            <input type="number" class="form-control" id="ulburstthreshold" name="ulburstthreshold" value="0">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="dlbursttime">Download Burst Time (seconds)</label>
                                                                            <input type="number" class="form-control" id="dlbursttime" name="dlbursttime" value="0">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="ulbursttime">Upload Burst Time (seconds)</label>
                                                                            <input type="number" class="form-control" id="ulbursttime" name="ulbursttime" value="0">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        
                                        <!-- /.post -->
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="timeline">
                                        <!-- The timeline -->
                                        
                                            <div class="card-body">
                                                <h4>Price definition</h4>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Postpaid price calculation:</label>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" id="pricecalcdownload" name="pricecalcdownload" value="1">
                                                                <label class="custom-control-label" for="pricecalcdownload">Download traffic</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" id="pricecalcupload" name="pricecalcupload" value="1">
                                                                <label class="custom-control-label" for="pricecalcupload">Upload traffic</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" id="pricecalcuptime" name="pricecalcuptime" value="1">
                                                                <label class="custom-control-label" for="pricecalcuptime">Online time</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" id="monthly" name="monthly" value="1">
                                                                <label class="custom-control-label" for="monthly">Monthly account</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" id="renew" name="renew" value="1">
                                                                <label class="custom-control-label" for="renew">Automatic renewal</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" id="carryover" name="carryover" value="1">
                                                                <label class="custom-control-label" for="carryover">Carry over remaining MBs</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" id="resetcounters" name="resetcounters" value="1">
                                                                <label class="custom-control-label" for="resetcounters">Reset counters if date has expired</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" id="enaddcredits" name="enaddcredits" value="1">
                                                                <label class="custom-control-label" for="enaddcredits">Enable additional credits</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mt-4">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <label for="unitpricetax">Net unit price</label>
                                                                    <input type="number" step="0.01" class="form-control calc-price" id="unitpricetax" name="unitpricetax" value="0">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="vat">VAT</label>
                                                                    <input type="number" step="0.01" class="form-control calc-price" id="unitpriceaddtax" name="unitpriceaddtax" value="0">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="grossprice">Gross unit price</label>
                                                                    <input type="number" step="0.01" class="form-control" id="grossprice" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <label for="unitpriceadd">Net additional unit price</label>
                                                                    <input type="number" step="0.01" class="form-control calc-add-price" id="unitpriceadd" name="unitpriceadd" value="0">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="addvat">Additional VAT</label>
                                                                    <input type="number" step="0.01" class="form-control calc-add-price" id="unitpriceaddtax" name="unitpriceaddtax" value="0">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="grossaddprice">Gross additional unit price</label>
                                                                    <input type="number" step="0.01" class="form-control" id="grossaddprice" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="expiration">
                                        
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Date addition mode:</label><br>
                                                            <label>(Do not change default values if you are not sure)</label>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="timeaddmodeexp1" name="timeaddmodeexp" value="0" checked>
                                                                <label class="custom-control-label" for="timeaddmodeexp1">Reset expiration date</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="timeaddmodeexp2" name="timeaddmodeexp" value="1">
                                                                <label class="custom-control-label" for="timeaddmodeexp2">Prolong expiration date</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="timeaddmodeexp3" name="timeaddmodeexp" value="2">
                                                                <label class="custom-control-label" for="timeaddmodeexp3">Prolong expiration date with correction</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Time addition mode:</label>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="timeaddmodeonline1" name="timeaddmodeonline" value="0" checked>
                                                                <label class="custom-control-label" for="timeaddmodeonline1">Reset online time</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="timeaddmodeonline2" name="timeaddmodeonline" value="1">
                                                                <label class="custom-control-label" for="timeaddmodeonline2">Prolong online time</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Traffic addition mode:</label>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="trafficaddmode1" name="trafficaddmode" value="0" checked>
                                                                <label class="custom-control-label" for="trafficaddmode1">Reset traffic counters</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="trafficaddmode2" name="trafficaddmode" value="1">
                                                                <label class="custom-control-label" for="trafficaddmode2">Additive</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Expiration date unit:</label>
                                                            <input type="number" class="form-control" id="timeunitexp" name="timeunitexp" value="1">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Initial:</label>
                                                            <input type="number" class="form-control" id="inittimeexp" name="inittimeexp" value="1">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Price Unit:</label>
                                                            <select class="form-control" id="timebaseexp" name="timebaseexp">
                                                                <option value="2">day(s)</option>
                                                                <option value="3">month(s)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Online time unit:</label>
                                                            <input type="number" class="form-control" id="timeunitonline" name="timeunitonline" value="0">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Initial:</label>
                                                            <input type="number" class="form-control" id="inittimeonline" name="inittimeonline" value="0">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Online Unit:</label>
                                                            <select class="form-control" id="timebaseonline" name="timebaseonline">
                                                                <option value="0">minute(s)</option>
                                                                <option value="1">hour(s)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Download traffic unit (MB):</label>
                                                            <input type="number" class="form-control" id="trafficunitdl" name="trafficunitdl" value="1024000">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Initial (MB):</label>
                                                            <input type="number" class="form-control" id="initdl" name="initdl" value="1024000">
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Upload traffic unit (MB):</label>
                                                            <input type="number" class="form-control" id="trafficunitul" name="trafficunitul" value="0">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Initial (MB):</label>
                                                            <input type="number" class="form-control" id="initul" name="initul" value="0">
                                                        </div>
                                                    </div>
                                        
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Total traffic unit (MB):</label>
                                                            <input type="number" class="form-control" id="trafficunitcomb" name="trafficunitcomb" value="0">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Initial (MB):</label>
                                                            <input type="number" class="form-control" id="inittotal" name="inittotal" value="0">
                                                        </div>
                                                    </div>
                                       
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Minimal base amount (Units):</label>
                                                            <input type="number" class="form-control" id="minamount" name="minamount" value="1">
                                                        </div>
                                                    </div>
                                                   
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Additional traffic unit (MB):</label>
                                                            <input type="number" class="form-control" id="minamountadd" name="minamountadd" value="1">
                                                        </div>
                                                    </div>
                                  
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Minimal additional amount (Units):</label>
                                                            <input type="number" class="form-control" id="addamount" name="addamount" value="1">
                                                        </div>
                                                    </div>

                                        </div>
                                            </div>
                                        
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="settings">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="nases">Allowed NAS Devices</label>
                                                    <select multiple class="form-control" id="allowednases" name="allowednases[]">
                                                        <?php
                                                        if(!empty($nasList))
                                                        {
                                                            foreach ($nasList as $nas)
                                                            {
                                                                echo '<option value="'.$nas->nasid.'">'.$nas->nasname.'</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="allowedmanagers">Allowed Managers</label>
                                                    <select multiple class="form-control" id="allowedmanagers" name="allowedmanagers[]">
                                                        <?php
                                                        if(!empty($managersList))
                                                        {
                                                            foreach ($managersList as $manager)
                                                            {
                                                                echo '<option value="'.$manager->managername.'">'.$manager->managername.'</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <!-- /.tab-pane -->
                                    <!-- Radius Attributes tab -->
                                    <div class="tab-pane" id="radiusAttributes">
                                        
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-12">
                                                        <h4>Radius Custom Attributes</h4>
                                                        <p>These attributes will be stored in the radgroupreply table and associated with this service profile name.</p>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-12">
                                                        
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-hover" id="radiusAttributesTable">
                                                                <thead>
                                                                    <tr>
                                                                        <th width="35%">Attribute</th>
                                                                        <th width="15%">Operator</th>
                                                                        <th width="40%">Value</th>
                                                                        <th width="10%">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <!-- Table rows will be added dynamically -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <!-- /.tab-pane -->
                                    </div>
                                    <!-- /.tab-content -->
                                </div><!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
        
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="reset" class="btn btn-default">Reset</button>
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
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('error'); ?>                    
                    </div>
                    <?php } ?>
                    <?php  
                        $success = $this->session->flashdata('success');
                        if($success)
                        {
                    ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                    <?php } ?>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo validation_errors('<div class="alert alert-danger alert-dismissible">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                        </div>
                    </div>
                </div>
            </div>    
        </div>
    </section>
</div>
<script src="<?php echo base_url(); ?>assets/js/addService.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/js/billing.js" type="text/javascript"></script> 

<!-- Radius Attributes JavaScript -->
<script>
    // Global variables for attributes and operators
    var groupAttributesList = <?php echo json_encode($groupAttributes); ?>;
    var operatorsList = <?php echo json_encode($operators); ?>;
    
    jQuery(document).ready(function(){
        // Add a new row to the attributes table
        jQuery('#addRadiusAttributeRow').click(function(){
            var newRow = '<tr class="new-attribute-row">' +
                          '<td>' +
                            '<select class="form-control attribute-select">' +
                              '<option value="">Select Attribute</option>';
                              
            // Add options from groupAttributesList
            jQuery.each(groupAttributesList, function(index, attr){
                newRow += '<option value="' + attr.attname + '">' + attr.attname + '</option>';
            });
            
            newRow += '</select>' +
                      '</td>' +
                      '<td>' +
                        '<select class="form-control operator-select">' +
                          '<option value="">Select Op</option>';
                          
            // Add options from operatorsList
            jQuery.each(operatorsList, function(index, op){
                newRow += '<option value="' + op.attname + '">' + op.attname + '</option>';
            });
            
            newRow += '</select>' +
                      '</td>' +
                      '<td>' +
                        '<input type="text" class="form-control value-input" placeholder="Value">' +
                      '</td>' +
                      '<td>' +
                        '<button type="button" class="btn btn-sm btn-success saveAttribute">' +
                          '<i class="fa fa-save"></i>' +
                        '</button>' +
                        '<button type="button" class="btn btn-sm btn-danger cancelAttribute ml-1">' +
                          '<i class="fa fa-times"></i>' +
                        '</button>' +
                      '</td>' +
                    '</tr>';
                    
            jQuery('#radiusAttributesTable tbody').append(newRow);
        });
        
        // Cancel adding a new attribute
        jQuery(document).on('click', '.cancelAttribute', function(){
            jQuery(this).closest('tr').remove();
        });
        
        // Save a new attribute
        jQuery(document).on('click', '.saveAttribute', function(){
            var row = jQuery(this).closest('tr');
            var attribute = row.find('.attribute-select').val();
            var operator = row.find('.operator-select').val();
            var value = row.find('.value-input').val();
            var srvname = jQuery('#srvname').val();
            
            // Validate fields
            if(!attribute) {
                alert('Please select an attribute');
                return;
            }
            
            if(!operator) {
                alert('Please select an operator');
                return;
            }
            
            if(!value) {
                alert('Please enter a value');
                return;
            }
            
            // Send AJAX request to save the attribute
            jQuery.ajax({
                url: baseURL + 'Services_controller/addRadiusGroupAttribute',
                type: 'POST',
                data: {
                    groupname: srvname,
                    attribute: attribute,
                    op: operator,
                    value: value
                },
                dataType: 'json',
                success: function(response){
                    if(response.status === true){
                        // Replace the editable row with a static row
                        var newRow = '<tr id="attr-' + response.id + '">' +
                                      '<td>' + attribute + '</td>' +
                                      '<td>' + operator + '</td>' +
                                      '<td>' + value + '</td>' +
                                      '<td>' +
                                        '<button type="button" class="btn btn-sm btn-danger deleteAttribute" data-id="' + response.id + '">' +
                                          '<i class="fa fa-trash"></i>' +
                                        '</button>' +
                                      '</td>' +
                                    '</tr>';
                                    
                        row.replaceWith(newRow);
                        
                        // Show success message
                        alert('Attribute added successfully');
                    } else {
                        alert('Failed to add attribute');
                    }
                },
                error: function(){
                    alert('An error occurred while adding the attribute');
                }
            });
        });
        
        // Delete an attribute
        jQuery(document).on('click', '.deleteAttribute', function(){
            if(confirm('Are you sure you want to delete this attribute?')){
                var id = jQuery(this).data('id');
                var row = jQuery(this).closest('tr');
                
                // Send AJAX request to delete the attribute
                jQuery.ajax({
                    url: baseURL + 'Services_controller/deleteRadiusGroupAttribute',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(response){
                        if(response.status === true){
                            // Remove the row
                            row.remove();
                            
                            // Show success message
                            alert('Attribute deleted successfully');
                        } else {
                            alert('Failed to delete attribute');
                        }
                    },
                    error: function(){
                        alert('An error occurred while deleting the attribute');
                    }
                });
            }
        });
    });
</script> 

<script type="text/javascript">
    $(document).ready(function() {
        // Initialize select2 for the attribute selector
        $('#attribute').select2();
        $('#operator').select2();
        
        // Handle add attribute button click
        $('#addRadiusAttribute').click(function() {
            var attribute = $('#attribute').val();
            var operator = $('#operator').val();
            var value = $('#attributeValue').val();
            
            // Simple validation
            if (!attribute || !operator || !value) {
                alert('Please fill in all fields');
                return;
            }
            
            // Add attribute to the table
            var attributeRow = '<tr>' +
                '<td>' + attribute + '</td>' +
                '<td>' + operator + '</td>' +
                '<td>' + value + '</td>' +
                '<td><input type="hidden" name="radius_attributes[]" value="' + attribute + '">' +
                '<input type="hidden" name="radius_operators[]" value="' + operator + '">' +
                '<input type="hidden" name="radius_values[]" value="' + value + '"></td>' +
                '</tr>';
            $('#attributesTable tbody').append(attributeRow);
            
            // Clear the form
            $('#attribute').val('').trigger('change');
            $('#operator').val('').trigger('change');
            $('#attributeValue').val('');
        });
    });
</script>
</body>
</html> 