<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-wallet"></i> Wallet Management
                        <small>List Wallets</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Wallet Management</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Wallet Listing</h3>
                            <div class="card-tools">
                                <a class="btn btn-primary" href="<?php echo base_url(); ?>Invoices/addNewWallet"><i class="fas fa-plus"></i> Add New Wallet</a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
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
                            <div class="row">
                                <div class="col-md-12">
                                    <form action="<?php echo base_url() ?>Invoices/walletListing" method="POST" id="searchList">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="input-group mb-3">
                                                    <input type="text" name="searchText" value="<?php echo $searchText; ?>" class="form-control" placeholder="Search">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                    </div>
                                                    <input type="date" name="searchFromDate" value="<?php echo $searchFromDate; ?>" class="form-control" placeholder="From Date">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                    </div>
                                                    <input type="date" name="searchToDate" value="<?php echo $searchToDate; ?>" class="form-control" placeholder="To Date">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group mb-3">
                                                    <button class="btn btn-primary" type="submit">
                                                        <i class="fas fa-search"></i> Search
                                                    </button>
                                                    <a href="<?php echo base_url() ?>Invoices/walletListing" class="btn btn-secondary ml-2">
                                                        <i class="fas fa-times"></i> Clear
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Wallet ID</th>
                                        <th>Wallet</th>
                                        <th>Manager</th>
                                        <th>Type</th>
                                        <th>Open Date</th>
                                        <th>Opening</th>
                                        <th>Collect</th>
                                        <th>Deposit</th>
                                        <th>Closing</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sum_opbal = 0;
                                    $sum_totdebit = 0;
                                    $sum_totcredit = 0;
                                    $sum_closbal = 0;
                                    if(!empty($walletRecords))
                                    {
                                        foreach($walletRecords as $record)
                                        {
                                            $sum_opbal += $record->adjusted_opbal;
                                            $sum_totdebit += $record->totdebit;
                                            $sum_totcredit += $record->totcredit;
                                            $sum_closbal += $record->closbal;
                                    ?>
                                        <tr>
                                            <td><?php echo $record->walletid ?></td>
                                            <td><?php echo $record->walletname ?></td>
                                            <td><?php echo $record->managername ?></td>
                                            <td><?php if($record->wallettype == 0) echo "REGULAR"; elseif($record->wallettype == 1) echo "PERIODIC"; 
                                                        elseif($record->wallettype == 2) echo "TEMPORARY"; ?></td>
                                            <td><?php echo $record->opdate ?></td>
                                            <td><?php echo number_format($record->adjusted_opbal, 2); ?></td>
                                            <td><?php echo $record->totdebit ?></td>
                                            <td><?php echo $record->totcredit ?></td>
                                            <td><?php echo $record->closbal ?></td>
                                            <td class="text-center">
                                                <a class="btn btn-sm btn-info" href="<?php echo base_url().'Invoices/editWallet/'.$record->walletid; ?>" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                                            </td>
                                        </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-right">Totals:</th>
                                        <th><?php echo number_format($sum_opbal,2); ?></th>
                                        <th><?php echo number_format($sum_totdebit,2); ?></th>
                                        <th><?php echo number_format($sum_totcredit,2); ?></th>
                                        <th><?php echo number_format($sum_closbal,2); ?></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div><!-- /.card-body -->
                        
                        <div class="card-footer clearfix">
                            <?php echo $this->pagination->create_links(); ?>
                        </div>
                    </div><!-- /.card -->
                </div>
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
            jQuery("#searchList").attr("action", baseURL + "walletListing/" + value);
            jQuery("#searchList").submit();
        });
    });
</script>