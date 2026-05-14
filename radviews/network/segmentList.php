    <?php
        $this->load->helper('url');
        $base = base_url() . index_page();
        $img_base = base_url("assets/images/");
    ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-network-wired"></i> Network Segments
                        <small>Add, Edit, Delete</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Network Segments</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-6">
                    <!-- Export buttons can go here if needed -->
                </div>
                <div class="col-md-6 text-right">
                    <a class="btn btn-primary" href="<?php echo base_url(); ?>Network_controller/addNewSegment"><i class="fas fa-plus"></i> Add New</a>
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
                            <h3 class="card-title">Network Segments List</h3>
                            <div class="card-tools">
                                <form action="<?php echo base_url(); ?>Network_controller/segmentList" method="POST" id="searchList">
                                    <div class="input-group input-group-sm">
                                        <select name="segmentType" class="form-control" style="width:auto;display:inline-block;">
                                            <option value="">All Types</option>
                                            <?php foreach($segmentTypes as $key => $type): ?>
                                                <option value="<?php echo $key; ?>" <?php echo (isset($segmentType) && $segmentType !== '' && $segmentType == $key) ? 'selected' : ''; ?>>
                                                    <?php echo $type; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="text" name="searchText" value="<?php echo $searchText; ?>" class="form-control" placeholder="Search by segment name or manager"/>
                                        <div class="input-group-append">
                                            <button class="btn btn-default" type="submit">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Segment Name</th>
                                        <th>Type</th>
                                        <th>Manager</th>
                                        <th>Activation Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(!empty($segmentRecords))
                                    {
                                        foreach($segmentRecords as $record)
                                        {
                                    ?>
                                    <tr>
                                        <td><?php echo $record->segmentname ?></td>
                                        <td><?php echo $segmentTypes[$record->segmenttype] ?></td>
                                        <td><?php echo $record->managername ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($record->activationdate)) ?></td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-info" href="<?php echo base_url(); ?>Network_controller/editSegment/<?php echo $record->segmentid; ?>" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a class="btn btn-sm btn-danger deleteSegment" href="#" data-segmentid="<?php echo $record->segmentid; ?>" title="Delete"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer clearfix">
                            <ul class="pagination pagination-sm m-0 float-right">
                                <?php echo $this->pagination->create_links(); ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('.deleteSegment').on('click', function(e){
            e.preventDefault();
            var segmentId = $(this).data("segmentid");
            if(confirm('Are you sure you want to delete this segment?')) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>Network_controller/deleteSegment",
                    dataType: "JSON",
                    data: {segmentId: segmentId},
                    success: function(data) {
                        if(data.status == 'access') {
                            alert('You are not allowed to delete segments');
                        } else if(data.status == true) {
                            alert('Segment deleted successfully');
                            location.reload();
                        } else {
                            alert('Failed to delete segment');
                        }
                    }
                });
            }
        });

        jQuery('ul.pagination li a').click(function (e) {
            e.preventDefault();
            var link = jQuery(this).get(0).href;
            var value = link.substring(link.lastIndexOf('/') + 1);
            jQuery("#searchList").attr("action", baseURL + "segmentList/" + value);
            jQuery("#searchList").submit();
        });
    });
</script> 
