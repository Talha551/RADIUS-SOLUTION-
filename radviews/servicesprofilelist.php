<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-users"></i> Service Profiles Management
                        <small>Add, Edit, Delete</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Service Profiles</li>
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
                        <a class="btn btn-info" href="<?php echo base_url(); ?>splist_csv/1/2"><i class="fas fa-download"></i> All List</a>
                        <a class="btn btn-success" href="<?php echo base_url(); ?>splist_csv/1/1"><i class="fas fa-download"></i> Active</a>
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <a class="btn btn-primary" href="<?php echo base_url(); ?>spAddNew"><i class="fas fa-plus"></i> Add New</a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Service Profiles List</h3>
                            <div class="card-tools">
                                <form action="<?php echo base_url() ?>spList" method="POST" id="searchList">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="searchText" value="<?php echo $searchText; ?>" class="form-control" style="width: 150px;" placeholder="Search"/>
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
                                        <th>ID</th>
                                        <th>Service Name</th>
                                        <th>Description</th>
                                        <th>Download Rate</th>
                                        <th>Upload Rate</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(!empty($serviceRecords))
                                    {
                                        foreach($serviceRecords as $record)
                                        {
                                    ?>
                                    <tr>
                                        <td><?php echo $record->srvid; ?></td>
                                        <td><?php echo $record->srvname; ?></td>
                                        <td><?php echo $record->descr; ?></td>
                                        <td><?php echo round((($record->downrate)), 0); ?> Mbps</td>
                                        <td><?php echo round((($record->uprate)), 0); ?> Mbps</td>
                                        <td>
                                            <?php if($record->enableservice == 1): ?>
                                                <span class="badge badge-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-info" href="<?php echo base_url().'Services_controller/spEditOld/'.$record->srvid; ?>" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a class="btn btn-sm btn-danger deleteService" href="#" data-serviceid="<?php echo $record->srvid; ?>" title="Delete"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php
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
            jQuery("#searchList").attr("action", baseURL + "spList/" + value);
            jQuery("#searchList").submit();
        });

        jQuery(document).on("click", ".deleteService", function(){
            var serviceId = $(this).data("serviceid"),
                hitURL = baseURL + "spDeleteProfile",
                currentRow = $(this);
            
            var confirmation = confirm("Are you sure to delete this service profile?");
            
            if(confirmation)
            {
                jQuery.ajax({
                type : "POST",
                dataType : "json",
                url : hitURL,
                data : { srvid : serviceId } 
                }).done(function(data){
                    console.log(data);
                    currentRow.parents('tr').remove();
                    if(data.status = true) { alert(data.message); }
                    else if(data.status = false) { alert("Service Profile deletion failed"); }
                    else { alert("Access denied..!"); }
                });
            }
        });
    });
</script> 