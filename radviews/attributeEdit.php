<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-edit"></i> Attribute Management
                        <small>Add / Edit Attribute</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>Services_controller/attributesList">Attributes</a></li>
                        <li class="breadcrumb-item active">Edit Attribute</li>
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
                            <h3 class="card-title">Edit Attribute Details</h3>
                        </div><!-- /.card-header -->
                        <!-- form start -->
                        <?php $this->load->helper("form"); ?>
                        <form role="form" id="editAttribute" action="<?php echo base_url() ?>Services_controller/updateAttribute" method="post" role="form">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="attname">Attribute Name</label><span style="color:red;">*</span>
                                            <input type="text" class="form-control required" value="<?php echo $attributeInfo->attname; ?>" id="attname" name="attname" maxlength="255">
                                            <input type="hidden" value="<?php echo $attributeInfo->attid; ?>" name="attid" id="attid" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="atttype">Attribute Type</label><span style="color:red;">*</span>
                                            <select class="form-control required" id="atttype" name="atttype">
                                                <option value="0" <?php if($attributeInfo->atttype == 0) { echo "selected"; } ?>>Attribute-User</option>
                                                <option value="1" <?php if($attributeInfo->atttype == 1) { echo "selected"; } ?>>Attribute-Group</option>
                                                <option value="2" <?php if($attributeInfo->atttype == 2) { echo "selected"; } ?>>OP</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="descr">Description</label>
                                            <textarea class="form-control" id="descr" name="descr" maxlength="255" rows="3"><?php echo $attributeInfo->descr; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.card-body -->
            
                            <div class="card-footer">
                                <input type="submit" class="btn btn-primary" value="Submit" />
                                <a href="<?php echo base_url(); ?>Services_controller/attributesList" class="btn btn-default">Cancel</a>
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

<script src="<?php echo base_url(); ?>assets/js/jquery.validate.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
    $("#editAttribute").validate({
        rules: {
            attname: {
                required: true,
                maxlength: 255
            },
            atttype: {
                required: true
            },
            descr: {
                maxlength: 255
            }
        },
        messages: {
            attname: {
                required: "This field is required",
                maxlength: "Attribute name cannot exceed 255 characters"
            },
            atttype: {
                required: "Please select an attribute type"
            },
            descr: {
                maxlength: "Description cannot exceed 255 characters"
            }
        }
    });
});
</script> 