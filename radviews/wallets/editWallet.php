<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>



<!-- Add jQuery and other required libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-wallet"></i> Wallet Management
                        <small>Edit Wallet</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>walletListing">Wallet Management</a></li>
                        <li class="breadcrumb-item active">Edit Wallet</li>
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
                            <h3 class="card-title">Edit Wallet</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <?php $this->load->helper("form"); ?>
                        <form role="form" id="editWallet" action="<?php echo base_url() ?>Invoices/editWalletProcess" method="post" role="form">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="walletname">Wallet Name</label>
                                            <input type="text" class="form-control required" id="walletname" name="walletname" value="<?php echo $walletInfo->walletname; ?>" maxlength="255" readonly>
                                            <input type="hidden" value="<?php echo $walletInfo->walletid; ?>" name="walletid" id="walletid" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="managername">Manager</label>
                                            <?php $session_manager = $this->session->userdata ( 'name' ); ?>
                                            <select class="form-control required" id="managername" name="managername" <?php if($session_manager <> "admin") echo "readonly"; ?> readonly>
                                                <option value="<?php echo $session_manager; ?>"><?php echo $session_manager; ?></option>
                                                <?php
                                                    if(!empty($managerList))
                                                    {
                                                        foreach ($managerList as $rl)
                                                        {
                                                            ?>
                                                            <option value="<?php echo $rl->managername ?>" <?php if($rl->managername == $walletInfo->managername) {echo "selected=selected";} ?>><?php echo $rl->managername ?></option>
                                                            <?php
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="wallettype">Wallet Type</label>
                                            <select class="form-control required" id="wallettype" name="wallettype" readonly>
                                                <option value="0" <?php if($walletInfo->wallettype == 0) {echo "selected=selected";} ?>>REGULAR</option>
                                                <option value="1" <?php if($walletInfo->wallettype == 1) {echo "selected=selected";} ?>>PERIODIC</option>
                                                <option value="2" <?php if($walletInfo->wallettype == 2) {echo "selected=selected";} ?>>TEMPORARY</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            
                                            <input type="hidden" class="form-control" id="profileid" name="profileid" value="<?php echo $walletInfo->profileid; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="opdate">Opening Date</label>
                                            <input type="date" class="form-control required" id="opdate" name="opdate" value="<?php echo $walletInfo->opdate; ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="opbal">Opening Balance</label>
                                            <input type="number" step="0.01" class="form-control required" id="opbal" name="opbal" value="<?php echo $walletInfo->opbal; ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="closdate">Closing Date</label>
                                            <input type="date" class="form-control" id="closdate" name="closdate" value="<?php echo $walletInfo->closdate; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="closbal">Closing Balance</label>
                                            <input type="number" step="0.01" class="form-control" id="closbal" name="closbal" value="<?php echo $walletInfo->closbal; ?>">
                                        </div>
                                    </div>
                                </div>

                                <?php if($this->session->userdata('isaccountmanager') == 1 || $this->session->userdata ( 'name' ) == 'admin'){ ?>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="acccr">Credit Account</label>
                                                <select class="form-control required" id="acccr" name="acccr">
                                                    <option value="0">Select Credit Account</option>
                                                    <?php
                                                        if(!empty($creditaccount))
                                                        {
                                                            foreach ($creditaccount as $rl)
                                                            {
                                                                ?>
                                                                <option value="<?php echo $rl->acctid ?>" <?php if($rl->acctid == $walletInfo->acccr) {echo "selected=selected";} ?>><?php echo $rl->accname; echo " - (".$rl->grpname.")"; ?></option>
                                                                <?php
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="accdr">Debit Account</label>
                                                <select class="form-control required" id="accdr" name="accdr">
                                                    <option value="0">Select Debit Account</option>
                                                    <?php
                                                        if(!empty($debitaccount))
                                                        {
                                                            foreach ($debitaccount as $rl)
                                                            {
                                                                ?>
                                                                <option value="<?php echo $rl->acctid ?>" <?php if($rl->acctid == $walletInfo->accdr) {echo "selected=selected";} ?>><?php echo $rl->accname; echo " - (".$rl->grpname.")"; ?></option>
                                                                <?php
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                <?php } ?>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="remarks">Remarks</label>
                                            <textarea class="form-control" id="remarks" name="remarks" rows="3"><?php echo $walletInfo->remarks; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
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
        </div>
    </section>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var editWalletForm = $("#editWallet");
        var validator = editWalletForm.validate({
            rules:{
                walletname :{ required : true },
                managername :{ required : true },
                wallettype :{ required : true },
                opdate :{ required : function() {
                    return $('#wallettype').val() != '2'; // Not required for TEMPORARY
                }},
                opbal :{ required : function() {
                    return $('#wallettype').val() != '2'; // Not required for TEMPORARY
                }},
                closdate :{ required : function() {
                    return $('#wallettype').val() == '1'; // Required only for PERIODIC
                }},
                closbal :{ required : false } // Always optional
            },
            messages:{
                walletname :{ required : "This field is required" },
                managername :{ required : "This field is required" },
                wallettype :{ required : "This field is required" },
                opdate :{ required : "This field is required" },
                opbal :{ required : "This field is required" },
                closdate :{ required : "Closing date is required for Periodic wallets" }
            }
        });

        // Function to handle wallet type changes
        function handleWalletTypeChange() {
            var walletType = $('#wallettype').val();
            
            // Reset all fields to enabled state first
            $('#opdate, #opbal, #closdate, #closbal').prop('disabled', false);
            
            switch(walletType) {
                case '0': // REGULAR
                    $('#closdate, #closbal').prop('disabled', true).val('');
                    break;
                case '1': // PERIODIC
                    $('#closdate').prop('disabled', false);
                    break;
                case '2': // TEMPORARY
                    $('#opdate, #opbal, #closdate, #closbal').prop('disabled', true).val('');
                    break;
            }
        }

        // Initial call to set the correct state
        handleWalletTypeChange();

        // Bind the change event to wallet type dropdown
        $('#wallettype').change(function() {
            handleWalletTypeChange();
            validator.resetForm(); // Reset validation messages
        });
    });
</script> 