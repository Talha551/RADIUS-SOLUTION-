<?php
$srvid = $serviceInfo->srvid;
$srvname = $serviceInfo->srvname;
$descr = $serviceInfo->descr;
$downrate = $serviceInfo->downrate;
$uprate = $serviceInfo->uprate;
$limitdl = $serviceInfo->limitdl;
$limitul = $serviceInfo->limitul;
$limitcomb = $serviceInfo->limitcomb;
$limitexpiration = $serviceInfo->limitexpiration;
$limituptime = $serviceInfo->limituptime;
$poolname = $serviceInfo->poolname;
$enableservice = $serviceInfo->enableservice;
$srvtype = $serviceInfo->srvtype;

// Add new billing variables
$pricecalcdownload = $serviceInfo->pricecalcdownload;
$pricecalcupload = $serviceInfo->pricecalcupload;
$pricecalcuptime = $serviceInfo->pricecalcuptime;
$monthly = $serviceInfo->monthly;
$renew = $serviceInfo->renew;
$carryover = $serviceInfo->carryover;
$resetcounters = $serviceInfo->resetcounters;
$enaddcredits = $serviceInfo->enaddcredits;
$unitpricetax = $serviceInfo->unitpricetax;
$unitpriceaddtax = $serviceInfo->unitpriceaddtax;
$unitpriceadd = $serviceInfo->unitpriceadd;

// Add expiration variables
$timeaddmodeexp = $serviceInfo->timeaddmodeexp;
$timeaddmodeonline = $serviceInfo->timeaddmodeonline;
$trafficaddmode = $serviceInfo->trafficaddmode;
$timebaseexp = $serviceInfo->timebaseexp;
$timeunitexp = $serviceInfo->timeunitexp;
$inittimeexp = $serviceInfo->inittimeexp;
$timebaseonline = $serviceInfo->timebaseonline;
$timeunitonline = $serviceInfo->timeunitonline;
$inittimeonline = $serviceInfo->inittimeonline;
$trafficunitdl = $serviceInfo->trafficunitdl;
$initdl = $serviceInfo->initdl;
$trafficunitul = $serviceInfo->trafficunitul;
$initul = $serviceInfo->initul;
$trafficunitcomb = $serviceInfo->trafficunitcomb;
$inittotal = $serviceInfo->inittotal;
$minamount = $serviceInfo->minamount;
$minamountadd = $serviceInfo->minamountadd;
$addamount = $serviceInfo->addamount;

// Fetch unique attributes from radgroupreply for select2
$uniqueAttributes = [];
$this->db->distinct();
$this->db->select('attribute');
$this->db->from('radgroupreply');
$query = $this->db->get();
foreach ($query->result() as $row) {
    $uniqueAttributes[] = $row->attribute;
}
?>

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
                        <li class="breadcrumb-item active">Edit Service Profile</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-10">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Edit Service Profile Details</h3>
                        </div><!-- /.card-header -->
                        <!-- form start -->
                        <?php $this->load->helper("form"); ?>
                        <form role="form" action="<?php echo base_url() ?>Services_controller/spUpdateService" method="post" id="editService" role="form">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="srvname">Service Name</label><span style="color:red;">*</span>
                                            <input type="text" class="form-control required" value="<?php echo $srvname; ?>" id="srvname" name="srvname" maxlength="50">
                                            <input type="hidden" value="<?php echo $srvid; ?>" name="srvid" id="srvid" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="descr">Description</label>
                                            <input type="text" class="form-control" id="descr" value="<?php echo $descr; ?>" name="descr" maxlength="255">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="downrate">Download Rate (Mbps)</label><span style="color:red;">*</span>
                                            <input type="number" class="form-control required" id="downrate" value="<?php echo $downrate; ?>" name="downrate">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="uprate">Upload Rate (Mbps)</label><span style="color:red;">*</span>
                                            <input type="number" class="form-control required" id="uprate" value="<?php echo $uprate; ?>" name="uprate">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="limitdl">Limit Download</label>
                                            <select class="form-control" id="limitdl" name="limitdl">
                                                <option value="0" <?php if($limitdl == 0) { echo "selected"; } ?>>No</option>
                                                <option value="1" <?php if($limitdl == 1) { echo "selected"; } ?>>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="limitul">Limit Upload</label>
                                            <select class="form-control" id="limitul" name="limitul">
                                                <option value="0" <?php if($limitul == 0) { echo "selected"; } ?>>No</option>
                                                <option value="1" <?php if($limitul == 1) { echo "selected"; } ?>>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="limitcomb">Limit Combined</label>
                                            <select class="form-control" id="limitcomb" name="limitcomb">
                                                <option value="0" <?php if($limitcomb == 0) { echo "selected"; } ?>>No</option>
                                                <option value="1" <?php if($limitcomb == 1) { echo "selected"; } ?>>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="limitexpiration">Limit Expiration</label>
                                            <select class="form-control" id="limitexpiration" name="limitexpiration">
                                                <option value="0" <?php if($limitexpiration == 0) { echo "selected"; } ?>>No</option>
                                                <option value="1" <?php if($limitexpiration == 1) { echo "selected"; } ?>>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="limituptime">Limit Uptime</label>
                                            <select class="form-control" id="limituptime" name="limituptime">
                                                <option value="0" <?php if($limituptime == 0) { echo "selected"; } ?>>No</option>
                                                <option value="1" <?php if($limituptime == 1) { echo "selected"; } ?>>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="poolname">Pool Name</label>
                                            <input type="text" class="form-control" id="poolname" value="<?php echo $poolname; ?>" name="poolname" maxlength="50">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="enableservice">Enable Service</label>
                                            <select class="form-control" id="enableservice" name="enableservice">
                                                <option value="0" <?php if($enableservice == 0) { echo "selected"; } ?>>No</option>
                                                <option value="1" <?php if($enableservice == 1) { echo "selected"; } ?>>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="srvtype">Service Type</label>
                                            <select class="form-control" id="srvtype" name="srvtype">
                                                <option value="0" <?php if($srvtype == 0) { echo "selected"; } ?>>Prepaid Registration</option>
                                                <option value="1" <?php if($srvtype == 1) { echo "selected"; } ?>>Prepaid Card</option>
                                                <option value="2" <?php if($srvtype == 2) { echo "selected"; } ?>>Postpaid</option>
                                                <option value="3" <?php if($srvtype == 3) { echo "selected"; } ?>>Email</option>
                                                <option value="4" <?php if($srvtype == 4) { echo "selected"; } ?>>ACL</option>
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
                                                                    <option value="0" <?php if($serviceInfo->enableburst == 0) echo "selected"; ?>>No</option>
                                                                    <option value="1" <?php if($serviceInfo->enableburst == 1) echo "selected"; ?>>Yes</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="dlburstlimit">Download Burst Limit (Kbps)</label>
                                                                <input type="number" class="form-control" id="dlburstlimit" name="dlburstlimit" value="<?php echo $serviceInfo->dlburstlimit; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="ulburstlimit">Upload Burst Limit (Kbps)</label>
                                                                <input type="number" class="form-control" id="ulburstlimit" name="ulburstlimit" value="<?php echo $serviceInfo->ulburstlimit; ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="dlburstthreshold">Download Burst Threshold (Kbps)</label>
                                                                <input type="number" class="form-control" id="dlburstthreshold" name="dlburstthreshold" value="<?php echo $serviceInfo->dlburstthreshold; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="ulburstthreshold">Upload Burst Threshold (Kbps)</label>
                                                                <input type="number" class="form-control" id="ulburstthreshold" name="ulburstthreshold" value="<?php echo $serviceInfo->ulburstthreshold; ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="dlbursttime">Download Burst Time (seconds)</label>
                                                                <input type="number" class="form-control" id="dlbursttime" name="dlbursttime" value="<?php echo $serviceInfo->dlbursttime; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="ulbursttime">Upload Burst Time (seconds)</label>
                                                                <input type="number" class="form-control" id="ulbursttime" name="ulbursttime" value="<?php echo $serviceInfo->ulbursttime; ?>">
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
                                            <input type="checkbox" class="custom-control-input" id="pricecalcdownload" name="pricecalcdownload" value="1" <?php if($pricecalcdownload == 1) echo "checked"; ?>>
                                            <label class="custom-control-label" for="pricecalcdownload">Download traffic</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="pricecalcupload" name="pricecalcupload" value="1" <?php if($pricecalcupload == 1) echo "checked"; ?>>
                                            <label class="custom-control-label" for="pricecalcupload">Upload traffic</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="pricecalcuptime" name="pricecalcuptime" value="1" <?php if($pricecalcuptime == 1) echo "checked"; ?>>
                                            <label class="custom-control-label" for="pricecalcuptime">Online time</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="monthly" name="monthly" value="1" <?php if($monthly == 1) echo "checked"; ?>>
                                            <label class="custom-control-label" for="monthly">Monthly account</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="renew" name="renew" value="1" <?php if($renew == 1) echo "checked"; ?>>
                                            <label class="custom-control-label" for="renew">Automatic renewal</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="carryover" name="carryover" value="1" <?php if($carryover == 1) echo "checked"; ?>>
                                            <label class="custom-control-label" for="carryover">Carry over remaining MBs</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="resetcounters" name="resetcounters" value="1" <?php if($resetcounters == 1) echo "checked"; ?>>
                                            <label class="custom-control-label" for="resetcounters">Reset counters if date has expired</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="enaddcredits" name="enaddcredits" value="1" <?php if($enaddcredits == 1) echo "checked"; ?>>
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
                                                <input type="number" step="0.01" class="form-control calc-price" id="unitpricetax" name="unitpricetax" value="<?php echo $unitpricetax; ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="vat">VAT</label>
                                                <input type="number" step="0.01" class="form-control calc-price" id="unitpriceaddtax" name="unitpriceaddtax" value="<?php echo $unitpriceaddtax; ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="grossprice">Gross unit price</label>
                                                <input type="number" step="0.01" class="form-control" id="grossprice" readonly value="<?php echo $unitpricetax + ($unitpricetax * $unitpriceaddtax / 100); ?>">
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
                                                <input type="number" step="0.01" class="form-control calc-add-price" id="unitpriceadd" name="unitpriceadd" value="<?php echo $unitpriceadd; ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="addvat">Additional VAT</label>
                                                <input type="number" step="0.01" class="form-control calc-add-price" id="unitpriceaddtax" name="unitpriceaddtax" value="<?php echo $unitpriceaddtax; ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="grossaddprice">Gross additional unit price</label>
                                                <input type="number" step="0.01" class="form-control" id="grossaddprice" readonly value="<?php echo $unitpriceadd + ($unitpriceadd * $unitpriceaddtax / 100); ?>">
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
                                        <label>Date addition mode:</label>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" id="timeaddmodeexp1" name="timeaddmodeexp" value="0" <?php if($timeaddmodeexp == 0) echo "checked"; ?>>
                                            <label class="custom-control-label" for="timeaddmodeexp1">Reset expiration date</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" id="timeaddmodeexp2" name="timeaddmodeexp" value="1" <?php if($timeaddmodeexp == 1) echo "checked"; ?>>
                                            <label class="custom-control-label" for="timeaddmodeexp2">Prolong expiration date</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" id="timeaddmodeexp3" name="timeaddmodeexp" value="2" <?php if($timeaddmodeexp == 2) echo "checked"; ?>>
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
                                            <input type="radio" class="custom-control-input" id="timeaddmodeonline1" name="timeaddmodeonline" value="0" <?php if($timeaddmodeonline == 0) echo "checked"; ?>>
                                            <label class="custom-control-label" for="timeaddmodeonline1">Reset online time</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" id="timeaddmodeonline2" name="timeaddmodeonline" value="1" <?php if($timeaddmodeonline == 1) echo "checked"; ?>>
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
                                            <input type="radio" class="custom-control-input" id="trafficaddmode1" name="trafficaddmode" value="0" <?php if($trafficaddmode == 0) echo "checked"; ?>>
                                            <label class="custom-control-label" for="trafficaddmode1">Reset traffic counters</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" id="trafficaddmode2" name="trafficaddmode" value="1" <?php if($trafficaddmode == 1) echo "checked"; ?>>
                                            <label class="custom-control-label" for="trafficaddmode2">Additive</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Expiration date unit:</label>
                                        <input type="number" class="form-control" id="timeunitexp" name="timeunitexp" value="<?php echo $timeunitexp; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Initial:</label>
                                        <input type="number" class="form-control" id="inittimeexp" name="inittimeexp" value="<?php echo $inittimeexp; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Unit:</label>
                                        <select class="form-control" id="timebaseexp" name="timebaseexp">
                                            <option value="2" <?php if($timebaseexp == 2) echo "selected"; ?>>day(s)</option>
                                            <option value="3" <?php if($timebaseexp == 3) echo "selected"; ?>>month(s)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Online time unit:</label>
                                        <input type="number" class="form-control" id="timeunitonline" name="timeunitonline" value="<?php echo $timeunitonline; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Initial:</label>
                                        <input type="number" class="form-control" id="inittimeonline" name="inittimeonline" value="<?php echo $inittimeonline; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Time Unit:</label>
                                        <select class="form-control" id="timebaseonline" name="timebaseonline">
                                            <option value="0" <?php if($timebaseonline == 0) echo "selected"; ?>>minute(s)</option>
                                            <option value="1" <?php if($timebaseonline == 1) echo "selected"; ?>>hour(s)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Download traffic unit (MB):</label>
                                        <input type="number" class="form-control" id="trafficunitdl" name="trafficunitdl" value="<?php echo $trafficunitdl; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Initial:</label>
                                        <input type="number" class="form-control" id="initdl" name="initdl" value="<?php echo $initdl; ?>">
                                    </div>
                                </div>
             
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Upload traffic unit (MB):</label>
                                        <input type="number" class="form-control" id="trafficunitul" name="trafficunitul" value="<?php echo $trafficunitul; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Initial:</label>
                                        <input type="number" class="form-control" id="initul" name="initul" value="<?php echo $initul; ?>">
                                    </div>
                                </div>
                           
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Total traffic unit (MB):</label>
                                        <input type="number" class="form-control" id="trafficunitcomb" name="trafficunitcomb" value="<?php echo $trafficunitcomb; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Initial:</label>
                                        <input type="number" class="form-control" id="inittotal" name="inittotal" value="<?php echo $inittotal; ?>">
                                    </div>
                                </div>
                           
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Minimal base amount (Units):</label>
                                        <input type="number" class="form-control" id="minamount" name="minamount" value="<?php echo $minamount; ?>">
                                    </div>
                                </div>
                      
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Additional traffic unit (MB):</label>
                                        <input type="number" class="form-control" id="minamountadd" name="minamountadd" value="<?php echo $minamountadd; ?>">
                                    </div>
                                </div>
                           
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Minimal additional amount (units):</label>
                                        <input type="number" class="form-control" id="addamount" name="addamount" value="<?php echo $addamount; ?>">
                                    </div>
                                </div>
                          
                            </div>
                        </div>
                    
                  </div>

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
                                                        $selected = in_array($nas->nasid, $allowedNases) ? 'selected' : '';
                                                        echo '<option value="'.$nas->nasid.'" '.$selected.'>'.$nas->nasname.'</option>';
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
                                                        $selected = in_array($manager->managername, $allowedManagers) ? 'selected' : '';
                                                        echo '<option value="'.$manager->managername.'" '.$selected.'>'.$manager->managername.'</option>';
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
                                    <h4>Radius Group Reply Attributes</h4>
                                    <p>These attributes are stored in the radgroupreply table and associated with this service profile name.</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" id="addRadiusAttributeRow">
                                        <i class="fa fa-plus"></i> Add Attribute
                                    </button>
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
                                                <?php
                                                if(!empty($radiusAttributes))
                                                {
                                                    foreach($radiusAttributes as $attr)
                                                    {
                                                ?>
                                                <tr id="attr-<?php echo $attr->id; ?>">
                                                    <td><?php echo $attr->attribute; ?></td>
                                                    <td><?php echo $attr->op; ?></td>
                                                    <td><?php echo $attr->value; ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-primary editAttribute" data-id="<?php echo $attr->id; ?>">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger deleteAttribute" data-id="<?php echo $attr->id; ?>">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php
                                                    }
                                                }
                                                ?>
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

<script src="<?php echo base_url(); ?>assets/js/editService.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/js/billing.js" type="text/javascript"></script> 

<!-- Radius Attributes JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script>
    // Define baseURL variable
    var baseURL = "<?php echo base_url(); ?>";
    
    // Global variables for attributes and operators
    var groupAttributesList = <?php echo json_encode($groupAttributes); ?>;
    var operatorsList = <?php echo json_encode($operators); ?>;
    var uniqueAttributes = <?php echo json_encode($uniqueAttributes); ?>;
    
    jQuery(document).ready(function(){
        // Add a new row to the attributes table
        jQuery('#addRadiusAttributeRow').click(function(){
            var newRow = '<tr class="new-attribute-row">' +
                          '<td>' +
                            '<select class="form-control attribute-select" style="width:100%">';
            newRow += '<option value="">Select Attribute</option>';
            // Add options from uniqueAttributes
            uniqueAttributes.forEach(function(attr) {
                newRow += '<option value="' + attr + '">' + attr + '</option>';
            });
            newRow += '</select>' +
                      '</td>' +
                      '<td>' +
                        '<select class="form-control operator-select">' +
                          '<option value="">Select Op</option>'+ '<option value="=">=</option>'+ '<option value="==">==</option>'+ '<option value="+=">+=</option>'+ '<option value="-=">-=</option>'+ '<option value=":=">:=</option>'+ '<option value="!=">!=</option>'+ '<option value="=*">=*</option>'+ '<option value="!*">!*</option>';
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
            // Initialize select2 for the new attribute-select
            jQuery('.attribute-select').last().select2({
                tags: true,
                placeholder: 'Select or type attribute',
                allowClear: true,
                width: 'resolve',
                data: uniqueAttributes.map(function(attr) { return {id: attr, text: attr}; })
            });
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
            
            // Check for duplicate attribute in the table for this group
            var duplicate = false;
            jQuery('#radiusAttributesTable tbody tr').not(row).each(function(){
                var existingAttr = jQuery(this).find('td:first').text().trim();
                if(existingAttr === attribute) {
                    duplicate = true;
                    return false; // break loop
                }
            });
            if(duplicate) {
                alert('This attribute already exists for this group.');
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
                                        '<button type="button" class="btn btn-sm btn-primary editAttribute" data-id="' + response.id + '"><i class="fa fa-edit"></i></button>' +
                                        '<button type="button" class="btn btn-sm btn-danger deleteAttribute" data-id="' + response.id + '"><i class="fa fa-trash"></i></button>' +
                                      '</td>' +
                                    '</tr>';
                                    
                        row.replaceWith(newRow);
                        
                        // Show success message
                        alert('Attribute added successfully');
                    } else {
                        alert(response.error ? response.error : 'Failed to add attribute');
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

        // Add JS for edit functionality
        jQuery(document).on('click', '.editAttribute', function(){
            var row = jQuery(this).closest('tr');
            var id = jQuery(this).data('id');
            var attribute = row.find('td:eq(0)').text().trim();
            var operator = row.find('td:eq(1)').text().trim();
            var value = row.find('td:eq(2)').text().trim();
            var editRow = '<tr class="edit-attribute-row">' +
                '<td><select class="form-control attribute-select" style="width:100%"></select></td>' +
                '<td><select class="form-control operator-select"></select></td>' +
                '<td><input type="text" class="form-control value-input" value="'+value+'"></td>' +
                '<td>' +
                    '<button type="button" class="btn btn-sm btn-success updateAttribute" data-id="'+id+'"><i class="fa fa-save"></i></button>' +
                    '<button type="button" class="btn btn-sm btn-danger cancelEditAttribute"><i class="fa fa-times"></i></button>' +
                '</td>' +
            '</tr>';
            row.replaceWith(editRow);
            // Populate attribute select2
            var $attrSelect = jQuery('.edit-attribute-row .attribute-select');
            $attrSelect.select2({
                tags: true,
                placeholder: 'Select or type attribute',
                allowClear: true,
                width: '100%',
                data: uniqueAttributes.map(function(attr) { return {id: attr, text: attr}; })
            }).val(attribute).trigger('change');
            // Populate operator select
            var $opSelect = jQuery('.edit-attribute-row .operator-select');
            $opSelect.append('<option value="">Select Op</option>'+ '<option value="=">=</option>'+ '<option value="==">==</option>'+ '<option value="+=">+=</option>'+ '<option value="-=">-=</option>'+ '<option value=":=">:=</option>'+ '<option value="!=">!=</option>'+ '<option value="=*">=*</option>'+ '<option value="!*">!*</option>');
            jQuery.each(operatorsList, function(index, op){
                $opSelect.append('<option value="' + op.attname + '">' + op.attname + '</option>');
            });
            $opSelect.val(operator);
        });
        // Cancel edit
        jQuery(document).on('click', '.cancelEditAttribute', function(){
            location.reload(); // simplest way, or re-render the row manually
        });
        // Save update
        jQuery(document).on('click', '.updateAttribute', function(){
            var row = jQuery(this).closest('tr');
            var id = jQuery(this).data('id');
            var attribute = row.find('.attribute-select').val();
            var operator = row.find('.operator-select').val();
            var value = row.find('.value-input').val();
            var srvname = jQuery('#srvname').val();
            if(!attribute) { alert('Please select an attribute'); return; }
            if(!operator) { alert('Please select an operator'); return; }
            if(!value) { alert('Please enter a value'); return; }
            // Check for duplicate attribute (excluding this row)
            var duplicate = false;
            jQuery('#radiusAttributesTable tbody tr').not(row).each(function(){
                var existingAttr = jQuery(this).find('td:first').text().trim();
                if(existingAttr === attribute) {
                    duplicate = true;
                    return false;
                }
            });
            if(duplicate) {
                alert('This attribute already exists for this group.');
                return;
            }
            jQuery.ajax({
                url: baseURL + 'Services_controller/updateRadiusGroupAttribute',
                type: 'POST',
                data: {
                    id: id,
                    groupname: srvname,
                    attribute: attribute,
                    op: operator,
                    value: value
                },
                dataType: 'json',
                success: function(response){
                    if(response.status === true){
                        var newRow = '<tr id="attr-' + id + '">' +
                            '<td>' + attribute + '</td>' +
                            '<td>' + operator + '</td>' +
                            '<td>' + value + '</td>' +
                            '<td>' +
                                '<button type="button" class="btn btn-sm btn-primary editAttribute" data-id="' + id + '"><i class="fa fa-edit"></i></button>' +
                                '<button type="button" class="btn btn-sm btn-danger deleteAttribute" data-id="' + id + '"><i class="fa fa-trash"></i></button>' +
                            '</td>' +
                        '</tr>';
                        row.replaceWith(newRow);
                        alert('Attribute updated successfully');
                    } else {
                        alert(response.error ? response.error : 'Failed to update attribute');
                    }
                },
                error: function(){
                    alert('An error occurred while updating the attribute');
                }
            });
        });
    });
</script>

<style>
.select2-container .select2-selection--single {
    height: 38px !important;
    min-height: 38px !important;
    line-height: 38px !important;
    box-sizing: border-box;
    cursor: pointer;
    display: block;
    user-select: none;
    -webkit-user-select: none;
    padding: 0;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 38px !important;
    height: 38px !important;
    padding-left: 12px;
    font-size: 1rem;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
    top: 0 !important;
}
</style>
</body>
</html> 