<?php

$username = "Credit";
$manager = $managerInfo->managername;
$allcredit = $managerInfo->perm_addcredits;
$alldiscount = $managerInfo->perm_allowdiscount;

?>
<script>
    //alert(<?php //echo $daysremaining ?>);
</script>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-users"></i> New Transaction
                        <small>Credit / </small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">New Credit Transaction</li>
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
                            <h3 class="card-title">Credit Reseller</h3>
                        </div><!-- /.card-header -->
                        
                        <!-- form start -->
                        <?php $this->load->helper("form"); ?>
                        <form role="form" id="savecredit" action="<?php echo base_url() ?>savecredit" method="post" role="form">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="invtype">Invoice Type</label>
                                            <select class="form-control required" id="invtype" name="invtype">
                                                <option value="">Invoice Type</option>
                                                <option value="Credit" selected>Credit</option>
                                                <option value="Debit">Debit</option>
                                            </select>
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

                                <?php
                                    $date1 = date("Y-m-d");
                                ?>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="hidden" class="form-control required" id="srvdate" value="<?php echo $date1 ?>" name="srvdate" maxlength="16" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="hidden" class="form-control required" id="expdate" value="<?php echo $date1 ?>" name="expdate" maxlength="16" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="amount">Recharge (Rs.)</label>
                                            <input type="text" class="form-control required" id="amount" value="<?php echo set_value('amount'); ?>" name="amount" maxlength="20" <?php if($this->ismaster == 0 && $this->session->userdata ( 'name' ) <> 'admin' || $managerInfo->perm_addcredits == 0) {echo "readonly";} ?> autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="remarks">Remarks</label>
                                            <input type="text" class="form-control required" id="remarks" value="<?php echo set_value('remarks'); ?>" name="remarks" maxlength="255">
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.card-body -->
        
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

<!-- jQuery UI -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/jquery-ui/jquery-ui.min.css">
<script src="<?php echo base_url(); ?>assets/plugins/jquery-ui/jquery-ui.min.js"></script>

<script type="text/javascript">
    $(function() {
        $("#expdate").datepicker({
            dateFormat: "yy-mm-dd"
        });
    });

    $(function() {
        $("#srvdate1").datepicker({
            dateFormat: "yy-mm-dd"
        });
    });
</script>

<script src="<?php echo base_url(); ?>assets/js/credit.js" type="text/javascript"></script>