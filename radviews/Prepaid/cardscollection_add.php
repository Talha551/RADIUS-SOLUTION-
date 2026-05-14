<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-money-bill-alt"></i> Add Collection
                        <small>Enter collection details</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>serieslist">Series List</a></li>
                        <li class="breadcrumb-item active">Add Collection</li>
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
                            <h3 class="card-title">New Collection</h3>
                        </div>

                        <?php $this->load->helper("form"); ?>
                        <form role="form" action="<?php echo base_url('Invoices/cardscollection_save'); ?>" method="post">
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-6">  
                                        <!-- Series Dropdown -->
                                        <div class="form-group">
                                            <label for="series">Series</label>
                                            <select class="form-control" id="series" name="series" required>
                                                <option value="">Select Series</option>
                                                <?php foreach ($series_list as $series): ?>
                                                    <?php $seriesselected = ($seriesref == $series->series)? "selected=selected" : "" ?>
                                                    <option value="<?php echo $series->series; ?>" <?php echo $seriesselected; ?>><?php echo $series->series."-".$series->owner."-".$series->date; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Date Input -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="date">Date</label>
                                            <input type="date" class="form-control" id="date" name="date" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">  
                                        <div class="form-group">
                                            <label for="cardreturn">Returned</label>
                                            <input type="number" class="form-control" id="cardreturn" name="cardreturn" value="<?php echo $cardsseriesInfo->unsold_cards; ?>" placeholder="Card Returned" step="1" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">  
                                        <div class="form-group">
                                            <label for="sold">Sold</label>
                                            <input type="number" class="form-control" id="sold" name="sold" value="<?php echo $cardsseriesInfo->sold_cards; ?>" placeholder="Sold Cards" step="1" required readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="value">Sales</label>
                                            <input type="number" class="form-control" id="value" name="value" value="<?php echo $cardsseriesInfo->sales_value; ?>" placeholder="Sales Value" step="1" required readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="amount">Amount</label>
                                            <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter Amount" step="1" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Remarks Input -->
                                <div class="form-group">
                                    <label for="remarks">Remarks</label>
                                    <input type="text" class="form-control" id="remarks" name="remarks" placeholder="Remarks (optional)">
                                </div>

                                <!-- Collected By Input -->
                                <div class="form-group">
                                    <label for="collectBy">Collected By</label>
                                    <input type="text" class="form-control" id="collectBy" name="collectBy" placeholder="Enter name of collector" required>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="reset" class="btn btn-default">Reset</button>
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
