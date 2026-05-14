<?php

    $acctid = $accountsInfo->acctid;
    $accname = $accountsInfo->accname;
    $grpid = $accountsInfo->accgroup;
    $manager = $accountsInfo->managername;
    $opqty = $accountsInfo->opqty;
    $inqty = $accountsInfo->inqty;
    $outqty = $accountsInfo->outqty;
    $clqty = $accountsInfo->clqty;
    $opening = $accountsInfo->opening;
    $debit = $accountsInfo->debit;
    $credit = $accountsInfo->credit;
    $balance = $accountsInfo->balance;

    //print_r($accountsInfo);
    $managername = $this->session->userdata ( 'name' ); 

?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fas fa-users"></i> Edit Accounts
        <small>Update / Edit Accounts</small>
      </h1>
    </section>
    
    <section class="content">
    
        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
              <!-- general form elements -->
                
                
                
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Account Details</h3>
                    </div><!-- /.card-header -->
                    <!-- form start -->
                    <?php $this->load->helper("form"); ?>
                    <form role="form" id="updateAccount" action="<?php echo base_url() ?>updateAccount" method="post" role="form">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-2">                                
                                    <div class="form-group">
                                    <label for="acctid">Account ID</label>
                                        <input type="text" class="form-control required" value="<?php echo $acctid; ?>" id="acctid" name="acctid" maxlength="50" readonly>
                                        <input type="hidden" value="<?php echo $acctid; ?>" name="acctid" id="acctid" />
                                    </div>
                                </div>

                                <div class="col-md-6">                                
                                    <div class="form-group">
                                        <label for="accname">Account Name</label>
                                        <input type="text" class="form-control required" value="<?php echo $accname; ?>" id="accname" name="accname" maxlength="50" <?php if($managername <> 'admin'){ echo "readonly"; } ?>>
                                        <input type="hidden" value="<?php echo $accname; ?>" name="accname1" id="accname1" />
                                    </div>
                                </div>

                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="managername">Select Manager</label>

                                            <select class="form-control required" id="managername" name="managername">
                                                <option value="0">Select Manager</option>
                                                <option value="default" <?php if($manager == "default") {echo "selected=selected";} ?>>All Managers</option>
                                                <option value="<?php echo $this->session->userdata ( 'name' ); ?>" <?php if($this->session->userdata ( 'name' ) == $manager) {echo "selected=selected";} ?>><?php echo $this->session->userdata ( 'name' ); ?></option>
                                                <?php
                                                    if(!empty($managerList))
                                                    {
                                                        foreach ($managerList as $rl)
                                                        {
                                                            ?>
                                                                <option value="<?php echo $rl->managername; ?>" <?php if($rl->managername == $manager) {echo "selected=selected";} ?>><?php echo $rl->managername ?></option>
                                                            <?php
                                                        }
                                                    }
                                                ?>
                                            </select>
                                            
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="accgroup">Account Group</label>

                                            <select class="form-control required" id="accgroup" name="accgroup">
                                                <option value="0">Select Group</option>
                                                <?php
                                                    if(!empty($accountsGroup))
                                                    {
                                                        foreach ($accountsGroup as $rl)
                                                        {
                                                            ?>
                                                                <option value="<?php echo $rl->grpid; ?>" <?php if($rl->grpid == $grpid) {echo "selected=selected";} ?>><?php echo $rl->grpname ?></option>
                                                            <?php
                                                        }
                                                    }
                                                ?>
                                            </select>
                                            
                                        </div>
                                    </div>

                                <div class="col-md-4">                                
                                    <div class="form-group">
                                        <label for="manager">Manager Name</label>
                                        <input type="text" class="form-control required" value="<?php echo $manager; ?>" id="manager" name="manager" maxlength="50" readonly>
                                        <input type="hidden" value="<?php echo $manager; ?>" name="manager" id="manager" />
                                    </div>
                                </div>
                            </div>

                            <h5><b>Account Summery</b></h5>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="opening">Opening (Rs.)</label>
                                        <input type="text" class="form-control required" id="opening" value="<?php echo $opqty; ?>" name="opening" maxlength="20" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="debit">Debit (Rs.)</label>
                                        <input type="text" class="form-control required" id="debit" value="<?php echo $debit ?>" name="debit" maxlength="20" autocomplete="off" readonly>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="credit">Credit (Rs.)</label>
                                        <input type="text" class="form-control required" id="credit" value="<?php echo $credit ?>" name="credit" maxlength="20" autocomplete="off" readonly>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="balance">Closing (Rs.)</label>
                                        <input type="text" class="form-control required" id="balance" value="<?php echo $balance ?>" name="balance" maxlength="20" autocomplete="off" readonly>
                                    </div>
                                </div>
                            </div>

                            <h5><b>Stock Summery</b></h5>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="opqty">Opening (qty)</label>
                                        <input type="text" class="form-control required" id="opqty" value="<?php echo $opqty; ?>" name="opqty" maxlength="20" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="inqty">In (qty)</label>
                                        <input type="text" class="form-control required" id="inqty" value="<?php echo $inqty ?>" name="inqty" maxlength="20" autocomplete="off" readonly>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="outqty">Out (qty)</label>
                                        <input type="text" class="form-control required" id="outqty" value="<?php echo $inqty ?>" name="outqty" maxlength="20" autocomplete="off" readonly>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="clqty">Closing (qty)</label>
                                        <input type="text" class="form-control required" id="clqty" value="<?php echo $clqty ?>" name="clqty" maxlength="20" autocomplete="off" readonly>
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
    </section>
    
</div>
<script src="<?php echo base_url(); ?>assets/js/usersAddNew.js" type="text/javascript"></script>