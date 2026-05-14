<?php

    $jvid = $transInfo->jvid;
    $jvdate = $transInfo->jvdate;
    $acctdr = $transInfo->acctdr;
    $acctcr = $transInfo->acctcr;
    $desc = $transInfo->desc;
    $jvtype = $transInfo->jvtype;
    $manager = $transInfo->managername;
    $debit = $transInfo->debit;
    $credit = $transInfo->credit;

    //print_r($accountsInfo);

?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><i class="fas fa-users"></i> Edit Deposit Entry <small class="text-muted">Update / Edit Deposit Entry</small></h1>
          </div>
        </div>
      </div>
    </section>
    
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-8">
            <div class="card card-primary">
              <div class="card-header bg-primary">
                <h3 class="card-title text-white mb-0">Deposit Details</h3>
              </div>
              <?php $this->load->helper("form"); ?>
              <form role="form" id="updateDepositEntry" action="<?php echo base_url() ?>updateDepositEntry" method="post">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="jvid">Voucher ID</label>
                        <input type="text" class="form-control" value="<?php echo $jvid; ?>" id="jvid" name="jvid" maxlength="50" readonly>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="jvtype">Voucher Type</label>
                        <input type="text" class="form-control" value="DEPOSIT" id="jvtype" name="jvtype" maxlength="50" readonly>
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
                        <select class="form-control" id="manager" name="manager">
                          <option value="">Select Manager</option>
                          <?php
                            if(!empty($managerList))
                            {
                              foreach ($managerList as $rl)
                              {
                          ?>
                          <option value="<?php echo $rl->managername ?>" <?php if($rl->managername == $manager) {echo "selected=selected";} ?>><?php echo $rl->managername ?></option>
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
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="walletid">Wallet</label>
                        <select class="form-control" id="walletid" name="walletid">
                          <option value="0">Select Wallet</option>
                          <?php
                            if(!empty($wallets))
                            {
                              foreach ($wallets as $rl)
                              {
                          ?>
                          <option value="<?php echo $rl->walletid ?>" <?php if($rl->walletid == $transInfo->walletid) {echo "selected=selected";} ?>><?php echo $rl->walletname ?></option>
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
                        <input type="text" class="form-control" id="jvdate" value="<?php echo $jvdate ?>" name="jvdate" maxlength="16" readonly>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="acccr">Withdrawl From/Credit Account</label>
                        <select class="form-control" id="acccr" name="acccr">
                          <option value="0">Select Group</option>
                          <?php
                            if(!empty($defaultaccount))
                            {
                              foreach ($defaultaccount as $rl)
                              {
                          ?>
                          <option value="<?php echo $rl->acctid ?>" <?php if($rl->acctid == $acctcr) {echo "selected=selected";} ?>><?php echo $rl->accname; echo " - (".$rl->grpname.")"; ?></option>
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
                        <select class="form-control" id="accdr" name="accdr">
                          <option value="0">Select Group</option>
                          <?php
                            if(!empty($depositaccount))
                            {
                              foreach ($depositaccount as $rl)
                              {
                          ?>
                          <option value="<?php echo $rl->acctid ?>" <?php if($rl->acctid == $acctdr) {echo "selected=selected";} ?>><?php echo $rl->accname; echo " - (".$rl->grpname.")"; ?></option>
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
                        <input type="text" class="form-control" value="<?php echo $desc; ?>" id="desc" name="desc" maxlength="250" autocomplete="off">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="amount">Amount (Rs.)</label>
                        <input type="text" class="form-control" id="amount" value="<?php echo $debit; ?>" name="amount" maxlength="20" autocomplete="off">
                      </div>
                    </div>
                  </div>
                </div><!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
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