<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-box"></i> Assign Packages
                        <small>Manage Reseller Packages</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Assign Packages</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Available Packages for <?php echo $managername; ?></h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="packagesTable" class="table table-bordered table-striped dataTable">
                                <thead>
                                    <tr>
                                        <th>Manager</th>
                                        <th>Package ID</th>
                                        <th>Package Name</th>
                                        <th>Base Service</th>
                                        <th>Base Price</th>
                                        <th>Cost Price</th>
                                        <th>Sale Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(!empty($packages)) {
                                        foreach($packages as $package) {
                                    ?>
                                    <tr>
                                        <td><?php echo $package->managername; ?></td>
                                        <td><?php echo $package->srvid; ?></td>
                                        <td><?php echo $package->srvname; ?></td>
                                        <td><?php echo $package->radsrvname; ?></td>
                                        <td><?php echo number_format($package->baseprice, 2); ?></td>
                                        <td><?php echo number_format($package->costprice, 2); ?></td>
                                        <td><?php echo number_format($package->saleprice, 2); ?></td>
                                        <td>
                                            <a class="btn btn-sm btn-primary assign-package" href="#" 
                                               title="Assign Package"
                                               data-srvid="<?php echo $package->srvid; ?>"
                                               data-managername="<?php echo $managername; ?>">
                                                <i class="fas fa-plus"></i>
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
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
</div>

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">

<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>

<script type="text/javascript">
    jQuery(document).ready(function(){
        // Initialize DataTable with proper options
        var table = jQuery('#packagesTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 10,
            "language": {
                "search": "Search packages:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "Showing 0 to 0 of 0 entries",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            }
        });

        // Add click handler for assign package button
        jQuery(document).on('click', '.assign-package', function(e) {
            e.preventDefault();
            
            var srvid = jQuery(this).data('srvid');
            var managername = jQuery(this).data('managername');

            console.log(srvid);
            console.log(managername);
            
            if(confirm('Are you sure you want to assign this package?')) {
                jQuery.ajax({
                    url: '<?php echo base_url(); ?>Services_controller/resellerPackageAdd',
                    type: 'POST',
                    data: {
                        srvid: srvid,
                        managername: managername
                    },
                    dataType: 'json',
                    success: function(response) {
                        if(response.status) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Error occurred while processing your request');
                    }
                });
            }
        });
    });
</script> 