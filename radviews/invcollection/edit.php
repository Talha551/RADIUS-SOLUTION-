<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- Add jQuery and other required libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-edit"></i> Edit Invoice Collection</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>Invoices/invcollection_list">Invoice Collections</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Edit Collection</h3>
                        </div>
                        <form id="editCollectionForm" action="" method="post">
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
                                        Collection updated successfully.
                                    </div>
                                <?php } ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="walletid">Wallet</label>
                                            <select class="form-control required" id="walletid" name="walletid">
                                                <option value="">Select Wallet</option>
                                                <?php if (!empty($wallets)) {
                                                    foreach ($wallets as $wallet) { ?>
                                                        <option value="<?php echo $wallet->walletid; ?>" <?php echo ($collection->walletid == $wallet->walletid) ? 'selected' : ''; ?>><?php echo htmlspecialchars($wallet->walletname); ?></option>
                                                <?php }} ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="paydate">Pay Date</label>
                                            <input type="date" class="form-control" id="paydate" name="paydate" value="<?php echo htmlspecialchars($collection->paydate); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="remarks">Remarks</label>
                                            <textarea class="form-control" id="remarks" name="remarks" rows="2"><?php echo htmlspecialchars($collection->remarks); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Linked Invoices</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Invoice #</th>
                                                        <th>Date</th>
                                                        <th>Customer</th>
                                                        <th>Amount</th>
                                                        <th>Paid Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($invoices)) {
                                                        foreach ($invoices as $inv) { ?>
                                                        <tr>
                                                            <td><?php echo $inv->transid; ?></td>
                                                            <td><?php echo ($inv->srvdate) ? $inv->srvdate : $inv->createdDtm; ?></td>
                                                            <td><?php echo htmlspecialchars($inv->username); ?></td>
                                                            <td class="text-right"><?php echo number_format(abs($inv->amount), 2); ?></td>
                                                            <td class="text-right">
                                                                <input type="number" class="form-control form-control-sm paid-amount-input" name="paidAmounts[<?php echo $inv->transid; ?>]" value="<?php echo (float)$inv->paid; ?>" min="0" max="<?php echo abs($inv->amount); ?>" step="0.01">
                                                            </td>
                                                        </tr>
                                                    <?php } } else { ?>
                                                        <tr><td colspan="5" class="text-center">No invoices linked to this collection.</td></tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="mt-2">
                                            <strong>Total Paid Amount: </strong>
                                            <span id="totalPaidAmount" style="font-size:1.2em; color:#007bff; font-weight:bold;">
                                                <?php
                                                $totalPaid = 0;
                                                if (!empty($invoices)) {
                                                    foreach ($invoices as $inv) {
                                                        $totalPaid += (float)$inv->paid;
                                                    }
                                                }
                                                echo number_format($totalPaid, 2);
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">Update Collection</button>
                                        <a href="<?php echo base_url('Invoices/invcollection_list'); ?>" class="btn btn-secondary">Back to List</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $('#editCollectionForm').validate({
        rules: {
            walletid: { required: true }
        },
        messages: {
            walletid: { required: 'Please select a wallet.' }
        }
    });

    // Live update total paid amount
    function updateTotalPaid() {
        let total = 0;
        $('.paid-amount-input').each(function() {
            let val = parseFloat($(this).val());
            if (!isNaN(val)) total += val;
        });
        $('#totalPaidAmount').text(total.toFixed(2));
    }
    $(document).on('input', '.paid-amount-input', updateTotalPaid);
});
</script> 