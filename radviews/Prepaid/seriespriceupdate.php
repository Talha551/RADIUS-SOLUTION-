<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-money-bill-alt"></i> Update Card Price
                        <small>Update price by Series</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>serieslist">Series List</a></li>
                        <li class="breadcrumb-item active">Update Price</li>
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
                            <h3 class="card-title">Update Price for Specific Series</h3>
                        </div>
                        
                        <!-- Form start -->
                        <?php $this->load->helper("form"); ?>
                        <form role="form" id="updatePriceForm" action="<?php echo base_url('Invoices/batchpriceUpdate'); ?>" method="post">
                            <div class="card-body">
                                <!-- Series Input -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="series">Series</label>
                                            <input type="text" class="form-control required" id="series" name="series" value="<?php echo $series; ?>" placeholder="Enter Series" maxlength="16" required readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- New Price Input -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="new_price">New Price (All series cards will effect)</label>
                                            <input type="number" class="form-control required" id="new_price" name="new_price" placeholder="Enter New Price" step="0.01" maxlength="22" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Footer -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update Price</button>
                                <button type="reset" class="btn btn-default">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Display Messages -->
                <div class="col-md-4">
                    <?php
                        $this->load->helper('form');
                        $error = $this->session->flashdata('error');
                        if($error) {
                    ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                    <?php } ?>
                    
                    <?php
                        $success = $this->session->flashdata('success');
                        if($success) {
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
