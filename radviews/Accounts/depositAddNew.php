<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> New Deposit
        <small>Add / Edit </small>
      </h1>
    </section>
    
    <section class="content">
    
        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
              <!-- general form elements -->
                
                
                
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Deposit Details</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <?php $this->load->helper("form"); ?>
                    <form role="form" id="saveAccounts" action="<?php echo base_url() ?>saveJv" method="post" role="form">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-6">                                
                                    <div class="form-group">
                                        <label for="jvtype">Voucher Type</label>
                                        <input type="text" class="form-control required" value="DEPOSIT" id="jvtype" name="jvtype" maxlength="50" readonly>
                                    </div>
                                </div>
                            </div>

                            <?php 
                                    $managername = $this->session->userdata ( 'name' ); 
                                    if($this->ismaster > 0 || $managername == 'admin'){
                            ?>

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
                            <?php } ?>

                            <?php $date1 = Date("Y-m-d"); ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="walletid">Wallet</label>
                                        <select class="form-control required" id="walletid" name="walletid">
                                            <option value="0">Select Wallet</option>
                                            <?php
                                                if(!empty($wallets))
                                                {
                                                    foreach ($wallets as $rl)
                                                    {
                                                        ?>
                                                            <option value="<?php echo $rl->walletid ?>" <?php if($rl->walletname == set_value('managername')) {echo "selected=selected";} ?>><?php echo $rl->walletname ?></option>
                                                        <?php
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="jvdate">Transaction Date (yy-mm-dd)</label>
                                            <input type="text" class="form-control required" id="jvdate" value="<?php echo $date1 ?>" name="jvdate" maxlength="16" readonly>
                                        </div>
                                </div>
                            </div>

                            <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                            <link rel="stylesheet" href="/resources/demos/style.css">
                            <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
                            <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

                            <script type="text/javascript">
                                
                                $(function() {
                                    $("#jvdate").datepicker({
                                        dateFormat: "yy-mm-dd"
                                    });
                                });
                                
                            </script>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="acccr">Widthdrawl From/Credit Account</label>
                                        <select class="form-control required" id="acccr" name="acccr">
                                            <option value="0">Select Group</option>
                                            <?php
                                                if(!empty($defaultaccount))
                                                {
                                                    foreach ($defaultaccount as $rl)
                                                    {
                                                        ?>
                                                        <option value="<?php echo $rl->acctid ?>" <?php if($rl->acctid == set_value('acctid')) {echo "selected=selected";} ?>><?php echo $rl->accname; echo " - (".$rl->grpname.")"; ?></option>
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
                                        <label for="accdr">Deposit To/Debit Account</label>
                                        <select class="form-control required" id="accdr" name="accdr">
                                            <option value="0">Select Group</option>
                                            <?php
                                                if(!empty($depositaccount))
                                                {
                                                    foreach ($depositaccount as $rl)
                                                    {
                                                        ?>
                                                        <option value="<?php echo $rl->acctid ?>" <?php if($rl->acctid == set_value('acctid')) {echo "selected=selected";} ?>><?php echo $rl->accname; echo " - (".$rl->grpname.")"; ?></option>
                                                        <?php
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-10">                                
                                    <div class="form-group">
                                        <label for="desc">Transaction Details</label>
                                        <input type="text" class="form-control required" value="<?php echo set_value('desc'); ?>" id="desc" name="desc" maxlength="250" autocomplete="off">
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="amount">Amount (Rs.)</label>
                                        <input type="text" class="form-control required" id="amount" value="<?php echo set_value('amount'); ?>" name="amount" maxlength="20" autocomplete="off">
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="usernamet">Username</label>
                                        <input type="text" class="form-control required" id="username" value="<?php echo $username; ?>" name="username" maxlength="20" autocomplete="off" readonly>
                                    </div>
                                </div>
                            </div>


                        </div><!-- /.box-body -->
    
                        <div class="card-footer">
                            <input type="submit" class="btn btn-primary" value="Submit" />
                            <button type="button" class="btn btn-default" onclick="window.location.href='<?php echo base_url('Accounts/Jvs_controller/jvsListing'); ?>'">Back</button>
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
<script>
    $(document).ready(function() {
        $('#walletid').change(function() {
            var walletid = $(this).val();
            if(walletid) {
                $.ajax({
                    url: '<?php echo base_url("Accounts/Accounts_controller/getWalletAccount"); ?>',
                    type: 'POST',
                    data: {walletid: walletid},
                    dataType: 'json',
                    success: function(response) {
                        if(response.acccr) {
                            $('#acccr').val(response.acccr);
                        }
                    }
                });
            }
        });
    });
</script>