<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-user-circle"></i> Profile Management
                        <small>Add, Edit, Delete</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Profile Management</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12 text-right">
                    <a class="btn btn-primary" href="<?php echo base_url(); ?>Reseller_controller/profileAddNew">
                        <i class="fas fa-plus"></i> Add New Profile
                    </a>
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
                            <h3 class="card-title">Profiles List</h3>
                            <div class="card-tools">
                                <form action="<?php echo base_url() ?>profileListing" method="POST" id="searchList">
                                    <div class="input-group input-group-sm" style="width: 300px;">
                                        <input type="text" name="searchText" value="<?php echo $searchText; ?>" class="form-control float-right" placeholder="Search"/>
                                        <div class="input-group-append">
                                            <button class="btn btn-default searchList"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div><!-- /.card-header -->
                        
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Profile ID</th>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Role</th>
                                        <th>Owner</th>
                                        <th>Created Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(!empty($profileListing))
                                    {
                                        $row_count = 1;
                                        foreach($profileListing as $record)
                                        {
                                    ?>
                                    <tr>
                                        <td><?php echo $row_count; ?>.</td>
                                        <td>
                                            <span class="badge badge-info"><?php echo $record->profileid; ?></span>
                                        </td>
                                        <td><?php echo $record->name; ?></td>
                                        <td><?php echo $record->mobile; ?></td>
                                        <td>
                                            <span class="badge badge-secondary"><?php echo $record->role; ?></span>
                                        </td>
                                        <td>
                                            <?php echo $record->managername; ?>
                                        </td>
                                        <td><?php echo date('d-m-Y H:i', strtotime($record->createdDtm)); ?></td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-primary" href="<?php echo base_url().'Reseller_controller/profileEditOld/'.$record->userId; ?>" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a class="btn btn-sm btn-danger deleteProfile" href="#" data-userid="<?php echo $record->userId; ?>" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
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
            jQuery("#searchList").attr("action", baseURL + "profileListing/" + value);
            jQuery("#searchList").submit();
        });
        
        // Delete profile
        jQuery('.deleteProfile').on('click', function(e) {
            e.preventDefault();
            var userId = jQuery(this).data('userid');
            if(confirm('Are you sure you want to delete this profile?')) {
                jQuery.ajax({
                    url: baseURL + 'deleteProfile',
                    type: 'POST',
                    data: { userId: userId },
                    dataType: 'json',
                    success: function(resp) {
                        if(resp.status == true) {
                            location.reload();
                        } else {
                            alert('Failed to delete profile');
                        }
                    },
                    error: function() {
                        alert('Failed to delete profile');
                    }
                });
            }
        });
    });
</script> 