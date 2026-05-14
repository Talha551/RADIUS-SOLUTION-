<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> New Account
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
                        <h3 class="box-title">Accounts Details</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <?php $this->load->helper("form"); ?>
                    <form role="form" id="saveAccounts" action="<?php echo base_url() ?>saveAccounts" method="post" role="form">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-6">                                
                                    <div class="form-group">
                                        <label for="acctid">Accounts ID (will be Auto Generated)</label>
                                        <input type="text" class="form-control required" value="<?php echo set_value('acctid'); ?>" id="acctid" name="acctid" maxlength="50" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">                                
                                    <div class="form-group">
                                        <label for="accname">Accounts Name</label>
                                        <input type="text" class="form-control required" value="<?php echo set_value('accname'); ?>" id="accname" name="accname" maxlength="50" autocomplete="off">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="managername">Manager Name</label>
                                        <select class="form-control required" id="managername" name="managername">
                                            <option value="0">Select Manager</option>
                                            <option value="default">All Managers</option>
                                            <option value="<?php echo $this->session->userdata ( 'name' ); ?>"><?php echo $this->session->userdata ( 'name' ); ?></option>
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

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="grpname">Account Group</label>
                                        <select class="form-control required" id="grpname" name="grpname">
                                            <option value="0">Select Group</option>
                                            <?php
                                                if(!empty($accountsGroup))
                                                {
                                                    foreach ($accountsGroup as $rl)
                                                    {
                                                        ?>
                                                        <option value="<?php echo $rl->grpid ?>" <?php if($rl->grpid == set_value('grpid')) {echo "selected=selected";} ?>><?php echo $rl->grpname ?></option>
                                                        <?php
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="opening">Opening (Rs.)</label>
                                        <input type="text" class="form-control required" id="opening" value="<?php echo set_value('opening'); ?>" name="opening" maxlength="20" autocomplete="off" readonly>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="debit">Total Debit (Rs.)</label>
                                        <input type="text" class="form-control required" id="debit" value="<?php echo set_value('debit'); ?>" name="debit" maxlength="20" autocomplete="off" readonly>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="credit">Total Credit (Rs.)</label>
                                        <input type="text" class="form-control required" id="credit" value="<?php echo set_value('credit'); ?>" name="credit" maxlength="20" autocomplete="off" readonly>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="balance">Closing (Rs.)</label>
                                        <input type="text" class="form-control required" id="balance" value="<?php echo set_value('balance'); ?>" name="balance" maxlength="20" autocomplete="off" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="box-header">
                                <h3 class="box-title">Sotck Details</h3>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="opqty">Opening (qty)</label>
                                        <input type="text" class="form-control required" id="opqty" value="<?php echo set_value('opqty'); ?>" name="opqty" maxlength="20" autocomplete="off" readonly>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="inqty">In (qty)</label>
                                        <input type="text" class="form-control required" id="inqty" value="<?php echo set_value('inqty'); ?>" name="inqty" maxlength="20" autocomplete="off" readonly>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="outqty">Out (qty)</label>
                                        <input type="text" class="form-control required" id="outqty" value="<?php echo set_value('outqty'); ?>" name="outqty" maxlength="20" autocomplete="off" readonly>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="clqty">Closing (qty)</label>
                                        <input type="text" class="form-control required" id="clqty" value="<?php echo set_value('clqty'); ?>" name="clqty" maxlength="20" autocomplete="off" readonly>
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
<script src="<?php echo base_url(); ?>assets/js/usersAddNew.js" type="text/javascript"></script>