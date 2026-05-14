<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-list"></i> Collection Transactions
                        <small>View and manage collections</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Collection Transactions</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-right">
                    <div class="form-group">
                        <a class="btn btn-primary" href="<?php echo base_url('serieslist'); ?>"><i class="fas fa-plus"></i> Add Collection</a>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->session->flashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?php echo $this->session->flashdata('success'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Collection Transactions List</h3>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>TransID</th>
                                        <th>Series</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Remarks</th>
                                        <th>Collected By</th>
                                        <th>Creation Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($cardscollection)): ?>
                                        <?php foreach ($cardscollection as $collection): ?>
                                            <tr>
                                                <td><?php echo $collection->TransID; ?></td>
                                                <td><?php echo $collection->series; ?></td>
                                                <td><?php echo $collection->date; ?></td>
                                                <td><?php echo $collection->amount; ?></td>
                                                <td><?php echo $collection->remarks; ?></td>
                                                <td><?php echo $collection->collectBy; ?></td>
                                                <td><?php echo $collection->creation_date; ?></td>
                                                <td class="text-center">
                                                    <a class="btn btn-sm btn-primary" href="<?php echo base_url('Invoices/cardscollection_edit/' . $collection->TransID); ?>" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No records found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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
