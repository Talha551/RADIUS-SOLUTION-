
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
                    <h1><i class="fas fa-money-check-alt"></i> Invoice Collections</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Invoice Collections</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">List of Invoice Collections</h3>
                            <a class="btn btn-primary btn-sm" href="<?php echo base_url(); ?>Invoices/invcollection_add">
                                <i class="fa fa-plus"></i> Add New Collection
                            </a>
                        </div>
                        <div class="card-body pt-3 pb-0">
                            <form class="form-inline mb-3" method="post" action="" id="searchList">
                                <div class="form-group mr-2">
                                    <label for="walletid" class="mr-2">Wallet</label>
                                    <select name="walletid" id="walletid" class="form-control form-control-sm">
                                        <option value="">All Wallets</option>
                                        <?php if (!empty($wallets)) foreach ($wallets as $wallet): ?>
                                            <option value="<?php echo $wallet->walletid; ?>" <?php if (set_value('walletid', isset($searchWalletId) ? $searchWalletId : '') == $wallet->walletid) echo 'selected'; ?>><?php echo htmlspecialchars($wallet->walletname); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group mr-2">
                                    <label for="fromdate" class="mr-2">From</label>
                                    <input type="date" name="fromdate" id="fromdate" class="form-control form-control-sm" value="<?php echo set_value('fromdate', isset($searchFromDate) ? $searchFromDate : ''); ?>">
                                </div>
                                <div class="form-group mr-2">
                                    <label for="todate" class="mr-2">To</label>
                                    <input type="date" name="todate" id="todate" class="form-control form-control-sm" value="<?php echo set_value('todate', isset($searchToDate) ? $searchToDate : ''); ?>">
                                </div>
                                <button type="submit" class="btn btn-sm btn-info">Search</button>
                                <a href="<?php echo base_url('Invoices/invcollection_list'); ?>" class="btn btn-sm btn-secondary ml-2">Reset</a>
                            </form>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Wallet</th>
                                            <th>Payment Date</th>
                                            <th>Amount</th>
                                            <th>Invoices</th>
                                            <th>Remarks</th>
                                            <th>Created Date</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sum_paidamount = 0;
                                        $sum_invoice_count = 0;
                                        if(!empty($collections))
                                        {
                                            foreach($collections as $record)
                                            {
                                                $sum_paidamount += $record->paidamount;
                                                $sum_invoice_count += $record->invoice_count;
                                                $canDelete = false;
                                                if (!empty($record->createdDtm)) {
                                                    $createdTime = strtotime($record->createdDtm);
                                                    $now = time();
                                                    if (($now - $createdTime) <= 86400) { // 24 hours = 86400 seconds
                                                        $canDelete = true;
                                                    }
                                                }
                                        ?>
                                        <tr>
                                            <td><?php echo $record->transid; ?></td>
                                            <td><?php echo $record->walletname; ?></td>
                                            <td><?php echo $record->paydate; ?></td>
                                            <td><?php echo number_format($record->paidamount, 2); ?></td>
                                            <td><?php echo $record->invoice_count; ?></td>
                                            <td><?php echo $record->remarks; ?></td>
                                            <td><?php echo $record->createdDtm; ?></td>
                                            <td class="text-center">
                                                <a class="btn btn-sm btn-info" href="<?php echo base_url().'Invoices/invcollection_edit/'.$record->transid; ?>" title="View"><i class="fa fa-eye"></i></a>
                                                <?php if ($canDelete) { ?>
                                                    <a class="btn btn-sm btn-danger deleteCollection" href="#" data-id="<?php echo $record->transid; ?>" title="Delete"><i class="fa fa-trash"></i></a>
                                                <?php } else { ?>
                                                    <button class="btn btn-sm btn-secondary" disabled title="Can only delete within 24 hours of creation"><i class="fa fa-trash"></i></button>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                            }
                                        }
                                        else
                                        {
                                        ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No collections found</td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-right">Totals:</th>
                                            <th><?php echo number_format($sum_paidamount, 2); ?></th>
                                            <th><?php echo $sum_invoice_count; ?></th>
                                            <th colspan="3"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer clearfix">
                            <?php echo $this->pagination->create_links(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>
<script type="text/javascript">
$(document).ready(function() {
    jQuery('ul.pagination li a').click(function (e) {
        e.preventDefault();
        var link = jQuery(this).get(0).href;
        var value = link.substring(link.lastIndexOf('/') + 1);
        jQuery("#searchList").attr("action", baseURL + "invcollection_list/" + value);
        jQuery("#searchList").submit();
    });
    $('.deleteCollection').click(function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this collection? This will also release all associated invoices.')) {
            const collectionId = $(this).data('id');
            window.location.href = '<?php echo base_url('Invoices/invcollection_delete/'); ?>' + collectionId;
        }
    });
});
</script> 