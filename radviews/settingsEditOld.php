<?php
$stgid = $settingsInfo->stgid;
$stgname = $settingsInfo->stgname;
$stgtype = $settingsInfo->stgtype;
$stgvalue = $settingsInfo->stgvalue;
$managername = $settingsInfo->managername;

//echo $stgtype;
//exit;

?>

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
                
                
                
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Edit Settings Details</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    
                    <form role="form" action="<?php echo base_url() ?>settingUpdate" method="post" id="settingUpdate" role="form">
                        <div class="box-body">

                            <div class="row">

                                <input type="hidden" value="<?php echo $stgid; ?>" name="stgid" id="stgid" />

                                <div class="col-md-6">                                
                                    <div class="form-group">
                                        <label for="stgname">Setting Name</label>
                                        <input type="text" class="form-control" id="stgname" placeholder="Settings Name" name="stgname" value="<?php echo $stgname; ?>" maxlength="128">
                                        <input type="hidden" value="<?php echo $stgname; ?>" name="stgname" id="stgname" />    
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="stgvalue">Mobile Number</label>
                                        <input type="text" class="form-control" id="stgvalue" placeholder="Mobile Number" name="stgvalue" value="<?php echo $stgvalue; ?>" maxlength="10">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="stgtype">Setting Type</label>
                                        <select class="form-control required" id="stgtype" name="stgtype">
                                            <option value="0">Select Type</option>
                                            <option value="ALLOW-ACCOUNTS">Allowd Accounts to Manager</option>
                                            <option value="DEFAULT-ACCOUNT">Default Account Setting</option>
                                            <option value="ALLOW-SMS">Allow SMS</option>
                                            <option value="ALLOW-BULK-SMS">Allow BULK SMS</option>
                                            <option value="POSTPAID-MANAGER">Post Paid Account</option>
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="manager">Manager</label>
                                        <select class="form-control required" id="manager" name="manager">
                                            <option value="0">Select Manager</option>
                                            <?php
                                                if(!empty($managerList))
                                                {
                                                    foreach ($managerList as $rl)
                                                    {
                                                        ?>
                                                            <option value="<?php echo $rl->managername; ?>" <?php if($rl->managername == $managername) {echo "selected=selected";} ?>><?php echo $rl->managername ?></option>
                                                        <?php
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <script type = "text/javascript">
                                var stgtype = document.getElementById("stgtype");
                                var grade = '<?php echo $stgtype; ?>';
                                switch (grade) {
                                    case "ALLOW-ACCOUNTS":
                                        stgtype.options.selectedIndex  = 1;
                                        break;
                                    case "DEFAULT-CASH":
                                        stgtype.options.selectedIndex  = 2;
                                        break;
                                    case "ALLOW-SMS":
                                        stgtype.options.selectedIndex  = 3;
                                        break;
                                    case "ALLOW-BULK-SMS":
                                        stgtype.options.selectedIndex  = 4;
                                        break;
                                    case "POSTPAID-MANAGER":
                                        stgtype.options.selectedIndex  = 5;
                                        break;
                                    default:
                                        stgtype.options.selectedIndex  = 0;
                                } 
                            </script>

                        </div><!-- /.box-body -->
    
                        <div class="box-footer">
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

<script src="<?php echo base_url(); ?>assets/js/editUser.js" type="text/javascript"></script>