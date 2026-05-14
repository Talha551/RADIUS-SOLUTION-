<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-users"></i> New Service
                        <small>Add / Edit User</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">New Service</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-8">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Service Details</h3>
                        </div><!-- /.card-header -->
                        <!-- form start -->
                        <?php $this->load->helper("form"); ?>
                        <form role="form" id="saveNewService" action="<?php echo base_url() ?>saveNewService" method="post" role="form">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="srvname">Service Name</label>
                                            <input type="text" class="form-control required" value="<?php echo set_value('srvname'); ?>" id="srvname" name="srvname" maxlength="50">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="service">Packages</label>
                                            <select class="form-control required" id="service" name="service">
                                                <option value="0">Select Package</option>
                                                <?php
                                                    if(!empty($packages))
                                                    {
                                                        foreach ($packages as $rl)
                                                        {
                                                            ?>
                                                            <option value="<?php echo $rl->srvid ?>" <?php if($rl->srvid == set_value('srvid')) {echo "selected=selected";} ?>><?php echo $rl->srvname ?></option>
                                                            <?php
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="manager">Manager</label>
                                            <select class="form-control required" id="manager" name="manager">
                                                <option value="0">Select Manager</option>
                                                <?php
                                                    if(!empty($managername))
                                                    {
                                                        foreach ($managername as $rl)
                                                        {
                                                            ?>
                                                            <option value="<?php echo $rl->managername ?>" <?php if($rl->managername == set_value('managername')) {echo "selected=selected";} ?>><?php echo $rl->managername ?></option>
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
                                            <label for="costprice">Base Price (Rs.)</label>
                                            <input type="text" class="form-control required" id="baseprice" value="<?php echo set_value('baseprice'); ?>" name="baseprice" maxlength="20" autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="costprice">Cost Price (Rs.)</label>
                                            <input type="text" class="form-control required" id="costprice" value="<?php echo set_value('costprice'); ?>" name="costprice" maxlength="20" autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="saleprice">Sales Price (Rs.)</label>
                                            <input type="text" class="form-control required" id="saleprice" value="<?php echo set_value('saleprice'); ?>" name="saleprice" maxlength="20" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.card-body -->
    
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="reset" class="btn btn-default">Reset</button>
                            </div>





                        </form>
                    </div>
                </div>
                <div class="col-md-4">
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
        </div>
    </section>
</div>
<script src="<?php echo base_url(); ?>assets/js/usersAddNew.js" type="text/javascript"></script>