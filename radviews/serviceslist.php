<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-users"></i> Services Management
                        <small>Add, Edit, Delete</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Services Management</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="btn-group">
                        <a class="btn btn-info" href="<?php echo base_url(); ?>servicelist_csv/1/2"><i class="fas fa-download"></i> All List</a>
                        <a class="btn btn-success" href="<?php echo base_url(); ?>servicelist_csv/1/1"><i class="fas fa-download"></i> Active</a>
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <a class="btn btn-primary" href="<?php echo base_url(); ?>serviceAddNew"><i class="fas fa-plus"></i> Add New</a>
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

            <?php $searchText1 = $type; 
                    $managerFilterUrl = $managerFilter;
            ?>
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Services List</h3>
                            <div class="card-tools">
                                <form action="<?php echo base_url() ?>serviceslist/" method="POST" id="searchList">
                                    <div class="input-group input-group-sm">
                                        <select name="searchText1" class="form-control mr-2" style="width: 150px;">
                                            <option value="<?php echo $this->session->userdata('name'); ?>" <?php if($this->session->userdata('name') == $searchText){echo 'selected=selected';} ?>><?php echo $this->session->userdata('name'); ?></option>
                                            <?php 
                                            if(!empty($managerList))
                                            {
                                                $row_count = 0;
                                                foreach($managerList as $record)
                                                {
                                                    echo '<option value="'; echo $record->managername.'"'; 
                                                    if($record->managername == $searchText1) 
                                                        { echo 'selected=selected'; }
                                                    echo '>'; 
                                                    echo $record->managername; 
                                                    echo '</option>';
                                                    $row_count++;
                                                }
                                            }
                                            ?>
                                        </select>
                                        <input type="text" name="searchText" value="<?php echo $searchText; ?>" class="form-control" style="width: 150px;" placeholder="Search"/>
                                        <div class="input-group-append">
                                            <button class="btn btn-default" type="submit">
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
                                        <th>S.No.</th>
                                        <th>Service Name</th>
                                        
                                        <?php 
                                            $managername = $this->session->userdata('name');
                                            if($managername == 'admin' || $this->ismaster > 0){
                                                echo "<th>Base Service</th>";
                                            } 
                                        ?>
                                        <th>Manager</th>
                                        <?php 
                                            $managername = $this->session->userdata('name');
                                            if($managername == 'admin' || $this->ismaster > 0){
                                                echo "<th>Price</th>";
                                            } 
                                        ?>
                                        <th>Cost Price</th>
                                        <th>Sale Price</th>
                                        <th>Users</th>
                                        <th>Cost</th>
                                        <th>Revenue</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(!empty($serviceListing))
                                    {
                                        $row_count = 1;
                                        foreach($serviceListing as $record)
                                        {
                                    ?>
                                    <tr>
                                        <td><?php echo $record->serial_number;?>.</td>
                                        <td><?php echo $record->srvname ?></td>
                                        <?php if($managername == 'admin' || $this->ismaster > 0){
                                                echo "<td>";
                                                echo $record->radsrvname;
                                                echo "</td>";
                                            } ?>
                                        <td><?php echo $record->managername ?></td>
                                        <?php 
                                        if($managername == 'admin' || $this->ismaster > 0){
                                                echo "<td>";
                                                echo $record->baseprice;
                                                echo "</td>";
                                            }
                                        ?>
                                        <td><?php echo $record->costprice ?></td>
                                        <td><?php echo $record->saleprice ?></td>
                                        <td><?php echo $record->NoOfUsers ?></td>
                                        <td><?php echo $record->Cost ?></td>
                                        <td><?php echo $record->Sales ?></td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-primary" href="<?= base_url().'serviceEdit/'.$record->srvid; ?>" title="View Details"><i class="fas fa-eye"></i></a> | 
                                            <a class="btn btn-sm btn-info" href="<?php echo base_url().'serviceEdit/'.$record->srvid; ?>" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a class="btn btn-sm btn-danger deleteService" href="#" data-srvid="<?php echo $record->srvid; ?>" title="Delete"><i class="fas fa-trash"></i></a>
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
                            <ul class="pagination pagination-sm m-0 float-right">
                                <?php echo $this->pagination->create_links(); ?>
                            </ul>
                        </div>
                    </div><!-- /.card -->
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('ul.pagination li a').click(function (e) {
            e.preventDefault();            
            var link = jQuery(this).get(0).href;            
            var value = link.substring(link.lastIndexOf('/') + 1);
            jQuery("#searchList").attr("action", baseURL + "serviceslist/" + value);
            jQuery("#searchList").submit();
        });

        // Delete Service AJAX functionality
        jQuery('.deleteService').click(function(e){
            e.preventDefault();
            var srvid = jQuery(this).data('srvid');
            if(confirm('Are you sure you want to delete this service?')) {
                jQuery.ajax({
                    url: baseURL + 'Services_controller/deleteService',
                    type: 'POST',
                    data: { srvid: srvid },
                    dataType: 'json',
                    success: function(response) {
                        if(response.status === true) {
                            location.reload(); // Reload the page to show updated list
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while deleting the service.');
                    }
                });
            }
        });
    });
</script>