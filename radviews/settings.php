<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="col-12">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>
                            <i class="fas fa-cog"></i> Settings Management
                            <small>Add, Edit, Delete</small>
                        </h1>
                    </div>
                    <div class="col-sm-3 text-right">
                        <a class="btn btn-primary" href="<?php echo base_url(); ?>Other_controller/jobsList">
                            <i class="fas fa-plus"></i> Jobs List
                        </a>
                    </div>
                    <div class="col-sm-3 text-right">
                        <a class="btn btn-primary" href="<?php echo base_url(); ?>settingAddNew">
                            <i class="fas fa-plus"></i> Add New
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
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
                <div class="col-12">
                    <?php echo validation_errors('<div class="alert alert-danger alert-dismissible">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Settings List</h3>
                            <div class="card-tools">
                                <form action="<?php echo base_url() ?>settingsList" method="POST" id="searchList">
                                    <div class="input-group input-group-sm" style="width: 300px;">
                                        <input type="text" name="searchText" value="<?php echo $searchText; ?>" class="form-control float-right" placeholder="Search"/>
                                        <div class="input-group-append">
                                            <button class="btn btn-default searchList">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Settings Type</th>
                                        <th>Value</th>
                                        <th>Manager</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(!empty($settingRecords))
                                    {
                                        foreach($settingRecords as $record)
                                        {
                                    ?>
                                    <tr>
                                        <td><?php echo $record->stgname ?></td>
                                        <td><?php echo $record->stgtype ?></td>
                                        <td><?php echo $record->stgvalue ?></td>
                                        <td><?php echo $record->managername ?></td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-info" href="<?php echo base_url().'settingseditOld/'.$record->stgid; ?>" title="Edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a class="btn btn-sm btn-danger deleteUser" href="#" data-userid="<?php echo $record->stgid; ?>" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer clearfix">
                            <?php echo $this->pagination->create_links(); ?>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('ul.pagination li a').click(function (e) {
            e.preventDefault();            
            var link = jQuery(this).get(0).href;            
            var value = link.substring(link.lastIndexOf('/') + 1);
            jQuery("#searchList").attr("action", baseURL + "settingsList/" + value);
            jQuery("#searchList").submit();
        });
    });
</script>
