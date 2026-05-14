<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-edit"></i> Edit Collection
                        <small>Update collection details</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>Invoices/cardscollection_list">Collections</a></li>
                        <li class="breadcrumb-item active">Edit Collection</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Edit Collection</h3>
                        </div>

                        <?php $this->load->helper("form"); ?>
                        <form role="form" action="<?php echo base_url('Invoices/cardscollection_update/' . $collection->TransID); ?>" method="post">
                            <div class="card-body">
                                <!-- Series Input (read-only) -->
                                <div class="form-group">
                                    <label for="series">Series</label>
                                    <input type="text" class="form-control" id="series" name="series" value="<?php echo $collection->series; ?>" readonly>
                                </div>

                                <!-- Date Input -->
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" class="form-control" id="date" name="date" value="<?php echo $collection->date; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="cardreturn">Returned</label>
                                    <input type="number" class="form-control" id="cardreturn" name="cardreturn" value="<?php echo $collection->cardreturn; ?>" step="0.01" required>
                                </div>

                                <div class="form-group">
                                    <label for="sold">Sold</label>
                                    <input type="number" class="form-control" id="sold" name="sold" value="<?php echo $collection->sold; ?>" step="1" required>
                                </div>

                                <div class="form-group">
                                    <label for="value">Sales</label>
                                    <input type="number" class="form-control" id="value" name="value" value="<?php echo $collection->value; ?>" step="1" required>
                                </div>

                                <!-- Amount Input -->
                                <div class="form-group">
                                    <label for="amount">Amount</label>
                                    <input type="number" class="form-control" id="amount" name="amount" value="<?php echo $collection->amount; ?>" step="1" required>
                                </div>

                                <!-- Remarks Input -->
                                <div class="form-group">
                                    <label for="remarks">Remarks</label>
                                    <input type="text" class="form-control" id="remarks" name="remarks" value="<?php echo $collection->remarks; ?>" placeholder="Remarks (optional)">
                                </div>

                                <!-- Collected By Input -->
                                <div class="form-group">
                                    <label for="collectBy">Collected By</label>
                                    <input type="text" class="form-control" id="collectBy" name="collectBy" value="<?php echo $collection->collectBy; ?>" required>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="<?php echo base_url('Invoices/cardscollection_list'); ?>" class="btn btn-default">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Success and Error Messages -->
                <div class="col-md-4">
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
        </div>
    </section>
</div>
