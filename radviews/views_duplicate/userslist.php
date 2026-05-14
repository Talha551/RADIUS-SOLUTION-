<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> User Management
        <small>Add, Edit, Delete</small>
      </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12 text-right">
                <div class="form-group">
                    <a class="btn btn-primary" href="<?php echo base_url(); ?>usersAddNew"><i class="fa fa-plus"></i> Add New</a>
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
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo $this->session->flashdata('error'); ?>                    
                </div>
                <?php } ?>
                <?php  
                    $success = $this->session->flashdata('success');
                    if($success)
                    {
                ?>
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
                <?php } ?>
                
                <div class="row">
                    <div class="col-md-12">
                        <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
              <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Users List</h3>
                    <div class="box-tools">
                        <form action="<?php echo base_url() ?>usersListing" method="POST" id="searchList">
                            <div class="input-group">
                              <input type="text" name="searchText" value="<?php echo $searchText; ?>" class="form-control input-sm pull-right" style="width: 150px;" placeholder="Search"/>
                              <div class="input-group-btn">
                                <button class="btn btn-sm btn-default searchList"><i class="fa fa-search"></i></button>
                              </div>
                            </div>
                        </form>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                  <table class="table table-hover">
                    <tr>
                        <th>S.No.</th>
                        <th>User Name</th>
                        <th>Name</th>
                        <th>Service</th>
                        <th>Mobile</th>
                        <th>Status</th>
                        <th>CNIC</th>
                        <th>Expiration</th>
                        <th>Created On</th>
                        <th class="text-center">Recharge | Manage</th>
                    </tr>
                    <?php
                    if(!empty($userRecords))
                    {
                        $row_count = 1;
                        foreach($userRecords as $record)
                        {
                    ?>

                    <tr>
                    <td <?php 
                            $curDate = strtotime(date("d-m-Y"));
                            $expDate = strtotime($record->expiration);
                            if($curDate > $expDate)
                                echo 'style="color: red;"';
                        ?>><?php //echo $record->serial_number;
                            echo $row_count; ?>.</td>
                        <td <?php 
                            $curDate = strtotime(date("d-m-Y"));
                            $expDate = strtotime($record->expiration);
                            if($curDate > $expDate)
                                //echo 'style="color: red;"';
                        ?>><a class="btn btn-primary btn-block" href="<?php echo base_url().'usersEdit/'.$record->username; ?>" title="Edit User" role="button"><?php echo $record->username; ?></a></td>
                        <td <?php 
                            $curDate = strtotime(date("d-m-Y"));
                            $expDate = strtotime($record->expiration);
                            if($curDate > $expDate)
                                echo 'style="color: red;"';
                        ?>><?php echo $record->firstname." ".$record->lastname ?></td>
                        <td <?php 
                            $curDate = strtotime(date("d-m-Y"));
                            $expDate = strtotime($record->expiration);
                            if($curDate > $expDate)
                                echo 'style="color: red;"';
                        ?>><?php echo $record->servicename ?></td>
                        <td <?php 
                            $curDate = strtotime(date("d-m-Y"));
                            $expDate = strtotime($record->expiration);
                            if($curDate > $expDate)
                                echo 'style="color: red;"';
                        ?>><?php echo $record->mobile ?></td>
                        <td <?php 
                            $curDate = strtotime(date("d-m-Y"));
                            $expDate = strtotime($record->expiration);
                            if($curDate > $expDate)
                                echo 'style="color: red;"';
                        ?>><?php if($record->enableuser == 1){ echo "Active"; } else { echo "<span class='badge bg-danger'>Blocked</span>"; } ?></td>
                        <td <?php 
                            $curDate = strtotime(date("d-m-Y"));
                            $expDate = strtotime($record->expiration);
                            if($curDate > $expDate)
                                echo 'style="color: red;"';

                        ?>><?php if($record->verified == 1){ echo '<a class="btn btn-sm btn-success" href="'. base_url().'docsUpload/'.$record->username; echo '" title="CNIC Uploaded"><i class="fa fa-check"></i></a>'; } else { echo '<a class="btn btn-sm btn-warning" href="'. base_url().'docsUpload/'.$record->username; echo '" title="CNIC Not Uploaded"><i class="fa fa-upload"></i></a>'; } ?></td>
                        <?php //echo "{".date("d-m-Y", strtotime($record->expiration))." - ".date("d-m-Y")."}  "; ?>
                        <td <?php 
                            $curDate = strtotime(date("d-m-Y"));
                            $expDate = strtotime($record->expiration);
                            if($curDate > $expDate)
                                echo 'style="color: red;"';
                        ?>>
                        <?php echo date("d-m-Y", strtotime($record->expiration)) ?></td>
                        <td <?php 
                            $curDate = strtotime(date("d-m-Y"));
                            $expDate = strtotime($record->expiration);
                            if($curDate > $expDate)
                                echo 'style="color: red;"';
                        ?>><?php echo date("d-m-Y", strtotime($record->createdon)) ?></td>
                        <td class="text-center">
                            <a class="btn btn-sm btn-primary" href="<?= base_url().'recharge/'.$record->username; ?>" title="Recharge"><i class="fa fa-money"></i></a> | 
                            <a class="btn btn-sm btn-info" href="<?php echo base_url().'usersEdit/'.$record->username; ?>" title="Manage User"><i class="fa fa-eye"></i></a>
                            <a class="btn btn-sm btn-danger deleteUser" href="<?php echo base_url().'changeService/'.$record->username; ?>" title="Change Package"><i class="fa fa-refresh"></i></a>
                        </td>
                    </tr>

                    <?php
                            $row_count++;
                            //echo $record->username."  -   ".$record->verified."     ";
                            //exit;
                        }
                    }
                    ?>
                  </table>
                  
                </div><!-- /.box-body -->
                <div class="box-footer clearfix">
                    <?php echo $this->pagination->create_links(); ?>
                </div>
              </div><!-- /.box -->
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
            jQuery("#searchList").attr("action", baseURL + "usersListing/" + value);
            jQuery("#searchList").submit();
        });
    });
</script>