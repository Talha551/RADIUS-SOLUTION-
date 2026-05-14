<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-ticket-alt"></i> Activation Tickets <small>List</small></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Activation Tickets</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Activation Tickets</h3>
                            <a href="<?php echo base_url('Invoices/activationTicket_addNew'); ?>" class="btn btn-success btn-sm float-right"><i class="fa fa-plus"></i> Add New</a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($error)) { ?>
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <?php echo $error; ?>
                                </div>
                            <?php } ?>
                            <?php if (!empty($success)) { ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <?php echo $success; ?>
                                </div>
                            <?php } ?>
                            <form method="POST" action="<?php echo base_url('Invoices/activationTicket_list'); ?>" class="form-inline mb-3">
                                <select name="acttype" class="form-control mr-2">
                                    <option value="">All Types</option>
                                    <?php foreach($acttype_label as $key => $label): ?>
                                        <option value="<?php echo $key; ?>" <?php echo (isset($acttype) && $acttype !== '' && $acttype == $key) ? 'selected' : ''; ?>>
                                            <?php echo $label; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="text" name="searchText" class="form-control mr-2" placeholder="Search by user, remarks, etc." value="<?php echo isset($searchText) ? htmlspecialchars($searchText) : ''; ?>">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Type</th>
                                            <th>User</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Total Amount</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($tickets)) { foreach ($tickets as $ticket): ?>
                                            <tr>
                                                <td><?php echo $ticket->transid; ?></td>
                                                <td><?php echo isset($acttype_label[$ticket->acttype]) ? $acttype_label[$ticket->acttype] : $ticket->acttype; ?></td>
                                                <td><?php echo htmlspecialchars($ticket->username); ?></td>
                                                <td><?php echo htmlspecialchars($ticket->srvdate); ?></td>
                                                <td><?php echo htmlspecialchars($ticket->expdate); ?></td>
                                                <td class="text-right"><?php echo number_format($ticket->totalamount, 2); ?></td>
                                                <td><?php echo ($ticket->actstatus == 0 ? '<span class="badge badge-warning">Pending</span>' : ($ticket->actstatus == 1 ? '<span class="badge badge-success">Completed</span>' : '<span class="badge badge-danger">Cancelled</span>')); ?></td>
                                                <td><?php echo htmlspecialchars($ticket->createdDtm); ?></td>
                                                <td>
                                                    <a href="<?php echo base_url('Invoices/activationTicket_view/' . $ticket->transid); ?>" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; } else { ?>
                                            <tr><td colspan="9" class="text-center">No tickets found.</td></tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script> 