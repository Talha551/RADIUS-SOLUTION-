<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fas fa-wallet"></i> Wallet Ledger
        <small>Reports / Wallet Ledger</small>
      </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-12 text-right">
                <div class="form-group">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
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
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Wallet Ledger</h3>
                        <div class="card-tools">
                            <form action="<?php echo base_url() ?>Accounts/Accounts_controller/ledgerWallet" method="POST" id="searchList">
                                <div class="input-group input-group-sm">
                                    <select name="walletid" class="form-control float-right" style="width: 300px; height: 30px;">
                                        <option value="">Select Wallet</option>
                                        <?php 
                                        if(!empty($walletList))
                                        {
                                            foreach($walletList as $wallet)
                                            {
                                                echo '<option value="'; echo $wallet->walletid.'"'; 
                                                if($wallet->walletid == $walletid) 
                                                    { echo 'selected=selected'; }
                                                echo '>'; 
                                                echo $wallet->walletname; 
                                                echo '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <input type="text" name="fromDate" id="fromDate" value="<?php echo $fromDate; ?>" class="form-control float-right" style="width: 150px;" autocomplete="off" placeholder="From Date(YYYY-MM-DD)"/>
                                    <input type="text" name="toDate" id="toDate" value="<?php echo $toDate; ?>" class="form-control float-right" style="width: 150px;" autocomplete="off" placeholder="To Date(YYYY-MM-DD)"/>
                                    <div class="input-group-append">
                                        <button class="btn btn-default searchList" style="height: 30px;"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div><!-- /.card-header -->
                    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
                    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
                    <script type="text/javascript">
                        $(function() {
                            $("#fromDate").datepicker({ dateFormat: "yy-mm-dd" });
                            $("#toDate").datepicker({ dateFormat: "yy-mm-dd" });
                        });
                    </script>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                            $runningBalance = 0;
                            $totalDebit = 0;
                            $totalCredit = 0;
                            if(!empty($walletLedger))
                            {
                                $row_count = 1;
                                foreach($walletLedger as $record)
                                {
                                    $debit = $record->debit;
                                    $credit = $record->credit;
                                    $runningBalance += ($debit - $credit);
                                    $totalDebit += $debit;
                                    $totalCredit += $credit;
                            ?>
                            <tr>
                                <td><?php echo $row_count; ?>.</td>
                                <td><?php echo $record->jvdate; ?></td>
                                <td><?php echo $record->desc; ?></td>
                                <td><?php echo number_format($debit,2); ?></td>
                                <td><?php echo number_format($credit,2); ?></td>
                                <td><?php echo number_format($runningBalance,2); ?></td>
                                <td><?php echo $record->typename; ?></td>
                            </tr>
                            <?php
                                    $row_count++;
                                }
                            ?>
                            <tr class="font-weight-bold bg-light">
                                <td colspan="3">Totals</td>
                                <td><?php echo number_format($totalDebit,2); ?></td>
                                <td><?php echo number_format($totalCredit,2); ?></td>
                                <td colspan="2"></td>
                            </tr>
                            <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div><!-- /.card-body -->
                </div><!-- /.card -->
            </div>
        </div>
    </section>
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('ul.pagination li a').click(function (e) {
            e.preventDefault();            
            var link = jQuery(this).get(0).href;            
            var value = link.substring(link.lastIndexOf('/') + 1);
            jQuery("#searchList").attr("action", baseURL + "Accounts/Accounts_controller/ledgerWallet/" + value);
            jQuery("#searchList").submit();
        });
    });
</script> 