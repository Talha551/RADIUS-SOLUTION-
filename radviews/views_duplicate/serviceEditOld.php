<?php

    $srvid = $serviceInfo->srvid;
    $srvname = $serviceInfo->srvname;
    $radsrvid = $serviceInfo->radsrvid;
    $manager = $serviceInfo->managername;
    $costprice = $serviceInfo->costprice;
    $saleprice = $serviceInfo->saleprice;

    print_r($serviceInfo);

?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> Edit Service
        <small>Update / Edit Service</small>
      </h1>
    </section>
    
    <section class="content">
    
        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
              <!-- general form elements -->
                
                
                
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Service Details</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <?php $this->load->helper("form"); ?>
                    <form role="form" id="serviceUpdate" action="<?php echo base_url() ?>serviceUpdate" method="post" role="form">
                        <div class="box-body">

                            <div class="row">
                                <div class="col-md-6">                                
                                    <div class="form-group">
                                    <label for="srvid">Service ID</label>
                                        <input type="hidden" value="<?php echo $srvid; ?>" name="srvid" id="srvid" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">                                
                                    <div class="form-group">
                                        <label for="srvname">Service Name</label>
                                        <input type="text" class="form-control required" value="<?php echo $srvname; ?>" id="srvname" name="srvname" maxlength="50" readonly>
                                        <input type="hidden" value="<?php echo $srvname; ?>" name="srvname" id="srvname" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="service">Packages</label>

                                        <input type="hidden" value="<?php echo $radsrvid; ?>" name="pradsrvid" id="pradsrvid" />

                                        <select class="form-control required" id="service" name="service">
                                            <option value="0">Select Package</option>
                                            <?php
                                                if(!empty($packages))
                                                {
                                                    foreach ($packages as $rl)
                                                    {
                                                        ?>
                                                            <option value="<?php echo $rl->srvid; ?>" <?php if($rl->srvid == $radsrvid) {echo "selected=selected";} ?>><?php echo $rl->srvname ?></option>
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
                                                            <option value="<?php echo $rl->managername; ?>" <?php if($rl->managername == $manager) {echo "selected=selected";} ?>><?php echo $rl->managername ?></option>
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
                                        <label for="costprice">Cost Price (Rs.)</label>
                                        <input type="text" class="form-control required" id="costprice" value="<?php echo $costprice; ?>" name="costprice" maxlength="20" autocomplete="off">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="saleprice">Sales Price (Rs.)</label>
                                        <input type="text" class="form-control required" id="saleprice" value="<?php echo $saleprice ?>" name="saleprice" maxlength="20" autocomplete="off">
                                    </div>
                                </div>
                            </div>


                        </div><!-- /.box-body -->
    
                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" value="Submit" />
                            <input type="reset" class="btn btn-default" value="Reset" />
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
    </section>
    
</div>
<script src="<?php echo base_url(); ?>assets/js/usersAddNew.js" type="text/javascript"></script>