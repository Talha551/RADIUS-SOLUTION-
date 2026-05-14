<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-ticket-alt"></i> Activation Ticket <small>View</small></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url('Invoices/activationTicket_list'); ?>">Activation Tickets</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">

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
                <div class="col-md-10 mx-auto">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Ticket Details</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($main)) { ?>
                                <div class="alert alert-danger">Ticket not found.</div>
                            <?php } else { ?>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <strong>ID:</strong> <?php echo $main->transid; ?><br>
                                        <strong>Type:</strong> <?php echo isset($acttype_label[$main->acttype]) ? $acttype_label[$main->acttype] : $main->acttype; ?><br>
                                        <strong>User:</strong> <?php echo htmlspecialchars($main->username); ?><br>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Start Date:</strong> <?php echo htmlspecialchars($main->srvdate); ?><br>
                                        <strong>End Date:</strong> <?php echo htmlspecialchars($main->expdate); ?><br>
                                        <strong>Status:</strong> 
                                        <?php 
                                        $status_labels = array(0 => 'Pending', 1 => 'Approved', 2 => 'Cancelled');
                                        $status_badges = array(0 => 'badge-warning', 1 => 'badge-success', 2 => 'badge-danger');
                                        ?>
                                        <span class="badge <?php echo $status_badges[$main->actstatus]; ?>">
                                            <?php echo $status_labels[$main->actstatus]; ?>
                                        </span><br>
                                        <?php if ($main->actstatus != 2) { // Not Cancelled ?>
                                        <form method="post" action="<?php echo base_url('Invoices/activationTicket_update_status/' . $main->transid); ?>" class="form-inline mt-2">
                                            <div class="form-group mr-2">
                                                <select name="actstatus" class="form-control form-control-sm">
                                                    <?php foreach ($status_labels as $key => $label): ?>
                                                        <option value="<?php echo $key; ?>" <?php if ($main->actstatus == $key) echo 'selected'; ?>><?php echo $label; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
                                        </form>
                                        <?php } ?>
                                </div>
                                <hr>
                                <h5>Details</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Account ID</th>
                                                <th>Details</th>
                                                <th>Qty</th>
                                                <th>Price</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($details)) { $i=1; foreach ($details as $d): ?>
                                                <tr>
                                                    <td><?php echo $i++; ?></td>
                                                    <td><?php echo htmlspecialchars($d->acctid); ?></td>
                                                    <td><?php echo htmlspecialchars($d->details); ?></td>
                                                    <td><?php echo htmlspecialchars($d->invqty); ?></td>
                                                    <td class="text-right"><?php echo number_format($d->invprice, 2); ?></td>
                                                    <td class="text-right"><?php echo number_format($d->invamount, 2); ?></td>
                                                </tr>
                                            <?php endforeach; } else { ?>
                                                <tr><td colspan="6" class="text-center">No details found.</td></tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-right">
                                    <a href="<?php echo base_url('Invoices/activationTicket_list'); ?>" class="btn btn-secondary">Back to List</a>
                                </div>
                            <?php } ?>
                            <?php if (!empty($status_update_success)) { ?>
                                <div class="alert alert-success mt-2">Status updated successfully.</div>
                            <?php } elseif (!empty($status_update_error)) { ?>
                                <div class="alert alert-danger mt-2">Failed to update status.</div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script> 