<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-users"></i> Manager Group List
                        <small>Add, Edit, Delete</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Manager Group List</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12 text-right">
                    <div class="form-group">
                        <a class="btn btn-primary" href="<?php echo base_url(); ?>managerGroupAdd">
                            <i class="fas fa-plus"></i> Add New
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
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
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Group List</h3>
                            <div class="card-tools">
                                <form action="<?php echo base_url() ?>managerGroup" method="POST" id="searchList">
                                    <div class="input-group input-group-sm" style="width: 150px;">
                                        <input type="text" name="searchText" value="<?php echo $searchText; ?>" class="form-control float-right" placeholder="Search">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-default">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div><!-- /.card-header -->
                        
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Group ID</th>
                                        <th>Group Name</th>
                                        <th>User Group</th>
                                        <th>Manager</th>
                                        <th>Description</th>
                                        <th class="text-center">Edit | Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(!empty($groupListing))
                                    {
                                        $row_count = 1;
                                        foreach($groupListing as $record)
                                        {
                                    ?>
                                    <tr>
                                        <td><?php echo $record->grpid ?></td>
                                        <td>
                                            <a class="btn btn-primary btn-block" href="<?php echo base_url().'managerGroupEdit/'.$record->grpid; ?>" title="Edit User" role="button">
                                                <?php echo $record->grpname; ?>
                                            </a>
                                        </td>
                                        <td><?php echo $record->groupname ?></td>
                                        <td><?php echo $record->managername ?></td>
                                        <td><?php echo $record->desc ?></td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-primary" href="<?php echo base_url().'managerGroupEdit/'.$record->grpid; ?>" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a> | 
                                            <a class="btn btn-sm btn-info" href="#" title="View User">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <!-- 
                                            <a class="btn btn-sm btn-danger deleteUser" href="#" title="Change Package">
                                                <i class="fas fa-sync-alt"></i>
                                            </a>
                                            -->
                                        </td>
                                    </tr>
                                    <?php
                                            $row_count++;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div><!-- /.card-body -->
                        
                        <div class="card-footer clearfix">
                            <?php echo $this->pagination->create_links(); ?>
                        </div>
                    </div><!-- /.card -->
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
            jQuery("#searchList").attr("action", baseURL + "managerGroup/" + value);
            jQuery("#searchList").submit();
        });
    });
</script>