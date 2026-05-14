<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-users"></i> Reseller Management
                        <small>Add, Edit, Delete</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Reseller Management</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12 text-right">
                    <a class="btn btn-primary" href="<?php echo base_url(); ?>resellerAddNew">
                        <i class="fas fa-plus"></i> Add New
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
                            <h3 class="card-title">Resellers List</h3>
                            <div class="card-tools">
                                <form action="<?php echo base_url() ?>resellerListing" method="POST" id="searchList">
                                    <div class="input-group input-group-sm" style="width: 400px;">
                                        <select name="filterType" class="form-control float-right" style="width: 150px; margin-right: 10px;">
                                            <option value="0" <?php echo ($filterType == 0) ? 'selected' : ''; ?>>All</option>
                                            <option value="1" <?php echo ($filterType == 1) ? 'selected' : ''; ?>>Master</option>
                                            <option value="2" <?php echo ($filterType == 2) ? 'selected' : ''; ?>>User Panel</option>
                                        </select>
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
                                        <th>Manager</th>
                                        <th>Master</th>
                                        <th>Name</th>
                                        <th>City</th>
                                        <th>Active</th>
                                        <th>Expired</th>
                                        <th>Total</th>
                                        <th>New</th>
                                        <th>Online</th>
                                        <th>Balance</th>
                                        <th class="text-center">Packages | View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(!empty($resellerListing))
                                    {
                                        $row_count = 1;
                                        foreach($resellerListing as $record)
                                        {
                                            $balaceAlert = 100;
                                            $balance = $record->balance;
                                            $textClass = ($balaceAlert > $balance) ? 'text-danger' : '';
                                    ?>
                                    <tr>
                                        <td class="<?php echo $textClass; ?>"><?php echo $row_count; ?>.</td>
                                        <td>
                                            <a class="btn btn-primary btn-block" href="<?php echo base_url().'resellerEditOld/'.$record->managername; ?>" title="Edit User" role="button">
                                                <?php echo $record->managername; ?>
                                            </a>
                                        </td>
                                        <td class="<?php echo $textClass; ?>"><?php echo $record->mastername ?></td>
                                        <td class="<?php echo $textClass; ?>"><?php echo $record->firstname." ".$record->lastname ?></td>
                                        <td class="<?php echo $textClass; ?>"><?php echo $record->city ?></td>
                                        <td class="<?php echo $textClass; ?>"><?php echo $record->active; ?></td>
                                        <td class="<?php echo $textClass; ?>"><?php echo $record->expired_last_month; ?></td>
                                        <td class="<?php echo $textClass; ?>"><?php echo $record->total; ?></td>
                                        <td class="<?php echo $textClass; ?>"><?php echo $record->new_users; ?></td>
                                        <td class="<?php echo $textClass; ?>">
                                            <span class="online-count" id="online-count-<?php echo $record->managername; ?>">0</span>
                                            <button class="btn btn-xs btn-info get-online-btn" data-managername="<?php echo $record->managername; ?>">Get Online</button>
                                        </td>
                                        <td class="<?php echo $textClass; ?>"><?php echo $record->balance ?></td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-primary" href="<?php echo base_url().'Services_controller/resellerAssignPackages/'.$record->managername; ?>" title="Edit">
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
            jQuery("#searchList").attr("action", baseURL + "resellerListing/" + value);
            jQuery("#searchList").submit();
        });
        // AJAX for online user count
        jQuery('.get-online-btn').on('click', function(e) {
            e.preventDefault();
            var btn = jQuery(this);
            var managername = btn.data('managername');
            var countSpan = jQuery('#online-count-' + managername);
            btn.prop('disabled', true).text('Loading...');
            jQuery.ajax({
                url: baseURL + 'Reseller_controller/getOnlineCount',
                type: 'POST',
                data: { managername: managername },
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        countSpan.text(resp.count);
                    } else {
                        countSpan.text('Err');
                    }
                },
                error: function() {
                    countSpan.text('Err');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Get Online');
                }
            });
        });
    });
</script>