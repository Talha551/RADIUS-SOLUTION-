<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-list"></i> Attributes Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Attributes</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Attributes List</h3>
                            <div class="card-tools">
                                <a class="btn btn-primary" href="<?php echo base_url(); ?>Services_controller/addNewAttribute"><i class="fa fa-plus"></i> Add New</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
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
                                </div>
                                <div class="col-md-4">
                                    <form action="<?php echo base_url() ?>Services_controller/attributesList" method="POST" id="searchList">
                                        <div class="input-group">
                                          <input type="text" name="searchText" value="<?php echo $searchText; ?>" class="form-control" placeholder="Search for..."/>
                                          <select name="filterType" class="form-control">
                                            <option value="">All Types</option>
                                            <option value="0" <?php echo (isset($filterType) && $filterType == '0') ? 'selected' : ''; ?>>Attribute-User</option>
                                            <option value="1" <?php echo (isset($filterType) && $filterType == '1') ? 'selected' : ''; ?>>Attribute-Group</option>
                                            <option value="2" <?php echo (isset($filterType) && $filterType == '2') ? 'selected' : ''; ?>>OP</option>
                                          </select>
                                          <div class="input-group-append">
                                            <button class="btn btn-default" type="submit">
                                              <i class="fa fa-search"></i>
                                            </button>
                                          </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="table-responsive mt-3">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Attribute Name</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        if(!empty($attributesRecords))
                                        {
                                            foreach($attributesRecords as $record)
                                            {
                                        ?>
                                        <tr>
                                            <td><?php echo $record->attid ?></td>
                                            <td><?php echo $record->attname ?></td>
                                            <td>
                                                <?php 
                                                    if($record->atttype == 0) {
                                                        echo "Attribute-User";
                                                    } else if($record->atttype == 1) {
                                                        echo "Attribute-Group";
                                                    } else {
                                                        echo "OP";
                                                    }
                                                ?>
                                            </td>
                                            <td><?php echo $record->descr ?></td>
                                            <td class="text-center">
                                                <a class="btn btn-sm btn-info" href="<?php echo base_url().'Services_controller/editAttribute/'.$record->attid; ?>" title="Edit"><i class="fa fa-pencil-alt"></i></a>
                                                <a class="btn btn-sm btn-danger deleteAttribute" href="#" data-attid="<?php echo $record->attid; ?>" title="Delete"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        <?php
                                            }
                                        }
                                        else
                                        {
                                        ?>
                                        <tr>
                                            <td colspan="5">No records found</td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <?php echo $this->pagination->create_links(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
jQuery(document).ready(function(){

    jQuery('ul.pagination li a').click(function (e) {
            e.preventDefault();            
            var link = jQuery(this).get(0).href;            
            var value = link.substring(link.lastIndexOf('/') + 1);
            jQuery("#searchList").attr("action", baseURL + "attributesList/" + value);
            jQuery("#searchList").submit();
        });



    jQuery(document).on("click", ".deleteAttribute", function(){
        var attid = $(this).data("attid"),
            hitURL = baseURL + "Services_controller/deleteAttribute/" + attid;
        
        var confirmation = confirm("Are you sure to delete this attribute?");
        
        if(confirmation)
        {
            window.location.href = hitURL;
        }
    });
});
</script> 