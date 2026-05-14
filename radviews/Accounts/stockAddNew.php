<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> <?php echo $pagetitle; ?>
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
                        <h3 class="card-title">Stock Details</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <?php $this->load->helper("form"); ?>
                    <form role="form" id="saveAccounts" action="<?php echo base_url() ?>saveStockEntry/<?php echo $stocktype; ?>" method="post" role="form">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="jvtype">Voucher Type</label>
                                        <input type="text" class="form-control required" value="<?php echo $jvtype; ?>" id="jvtype" name="jvtype" maxlength="50" readonly>
                                    </div>
                                </div>
                            </div>

                            <?php
                            $managername = $this->session->userdata('name');
                            if ($this->ismaster > 0 || $managername == 'admin') {
                            ?>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="manager">Manager</label>
                                            <select class="form-control required" id="manager" name="manager">
                                                <option value="">Select Manager</option>
                                                <?php
                                                if (!empty($managerList)) {
                                                    foreach ($managerList as $rl) {
                                                ?>
                                                        <option value="<?php echo $rl->managername ?>" <?php if ($rl->managername == set_value('managername')) {
                                                                                                            echo "selected=selected";
                                                                                                        } ?>><?php echo $rl->managername ?></option>
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

                            <?php if ($jvtype == 'STOCK') { ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="walletid">Wallet</label>
                                            <select class="form-control required" id="walletid" name="walletid">
                                                <option value="0">Select Wallet</option>
                                                <?php
                                                if (!empty($wallets)) {
                                                    foreach ($wallets as $rl) {
                                                ?>
                                                        <option value="<?php echo $rl->walletid ?>" <?php if ($rl->walletname == set_value('managername')) {
                                                                                                        echo "selected=selected";
                                                                                                    } ?>><?php echo $rl->walletname ?></option>
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="jvdate">Transaction Date (yy-mm-dd)</label>
                                        <input type="text" class="form-control required" id="jvdate" value="<?php echo $date1 ?>" name="jvdate" maxlength="16" readonly>
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

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="acccr">Account For</label>
                                        <select class="form-control required" id="acccr" name="acccr">
                                            <option value="0">Select Group</option>
                                            <?php
                                            if (!empty($creditaccount)) {
                                                foreach ($creditaccount as $rl) {
                                            ?>
                                                    <option value="<?php echo $rl->acctid ?>" <?php if ($rl->acctid == set_value('acctid')) {
                                                                                                    echo "selected=selected";
                                                                                                } ?>><?php echo $rl->accname ?></option>
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
                                        <label for="accdr">Item Account</label>
                                        <select class="form-control required" id="accdr" name="accdr">
                                            <option value="0">Select Group</option>
                                            <?php
                                            if (!empty($debitaccount)) {
                                                foreach ($debitaccount as $rl) {
                                            ?>
                                                    <option value="<?php echo $rl->acctid ?>" <?php if ($rl->acctid == set_value('acctid')) {
                                                                                                    echo "selected=selected";
                                                                                                } ?>><?php echo $rl->accname ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="invinqty">Qty (M/Pcs)</label>
                                        <input type="text" class="form-control required" id="invinqty" value="<?php echo set_value('invinqty'); ?>" name="invinqty" maxlength="20" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="invprice">Rate (Rs.)</label>
                                        <input type="number" class="form-control required" id="invprice" value="<?php echo set_value('invprice'); ?>" name="invprice" maxlength="20" placeholder='0.00' autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="invtotal">Total (Rs.)</label>
                                        <input type="number" class="form-control required" id="invtotal" value="<?php echo set_value('invtotal'); ?>" name="invtotal" maxlength="20" placeholder='0.00' autocomplete="off" readonly>
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
                                <div class="col-md-5">                                
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input type="text" class="form-control required" value="<?php echo $username; ?>" id="username" name="username" maxlength="250" autocomplete="off" readonly>
                                    </div>
                                </div>
                            </div>

                        </div><!-- /.box-body -->

                        <div class="card-footer">
                            <input type="submit" class="btn btn-primary" value="Submit" />
                            <button type="button" class="btn btn-default" onclick="window.location.href='<?php echo base_url('stockList'); ?>'">Back</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4">
                <?php
                $this->load->helper('form');
                $error = $this->session->flashdata('error');
                if ($error) {
                ?>
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php } ?>
                <?php
                $success = $this->session->flashdata('success');
                if ($success) {
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

<script>
    $("#invprice").change(
        function() {
            $('#invtotal').val(
                $("#invinqty").val() * $("#invprice").val());
        });

    $("#invinqty").change(
        function() {
            $('#invtotal').val(
                $("#invinqty").val() * $("#invprice").val());
        });

</script>

<script src="<?php echo base_url(); ?>assets/js/usersAddNew.js" type="text/javascript"></script>


<!-- Add Select2 CSS and JS files -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Custom CSS to fix Select2 height -->
<style>
    /* Make Select2 height match other form controls */
    .select2-container .select2-selection--single {
        height: 38px !important;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.5;
    }
    
    /* Adjust the dropdown arrow position */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    
    /* Fix the text vertical alignment */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px;
    }
    
    /* Match border radius with Bootstrap */
    .select2-container--default .select2-selection--single {
        border-radius: 4px;
        border-color: #ced4da;
    }
    
    /* Fix focus state */
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    
</style>

<script>
    // Initialize Select2 for better dropdown experience
    $('.select2').select2({
        placeholder: "Select Username",
        allowClear: true
    });
</script>