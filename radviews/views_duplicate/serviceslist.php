<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> Services Management
        <small>Add, Edit, Delete</small>
      </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12 text-right">
                <div class="form-group">
                    <a class="btn btn-primary" href="<?php echo base_url(); ?>serviceAddNew"><i class="fa fa-plus"></i> Add New</a>
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
                    <h3 class="box-title">Services List</h3>
                    <div class="box-tools">
                        <form action="<?php echo base_url() ?>serviceslist" method="POST" id="searchList">
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
                        <th>Service Name</th>
                        
                        <?php 
                            $managername = $this->session->userdata ( 'name' );
                            if($managername == 'admin'){
                                echo "<th>Base Service</th>";
                            } 
                        ?>
                        <th>Manager</th>
                        <th>Cost Price</th>
                        <th>Sale Price</th>
                        <th class="text-center">Actions</th>
                    </tr>
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
                        <?php if($managername == 'admin'){
                                echo "<td>";
                                echo $record->radsrvname;
                                echo "</td>"; 
                            } ?>
                        <td><?php echo $record->managername ?></td>
                        <td><?php echo $record->costprice ?></td>
                        <td><?php echo $record->saleprice ?></td>
                        <td class="text-center">
                            <a class="btn btn-sm btn-primary" href="<?= base_url().'login-history/'.$record->srvid; ?>" title="View Details"><i class="fa fa-eye"></i></a> | 
                            <a class="btn btn-sm btn-info" href="<?php echo base_url().'serviceEdit/'.$record->srvid; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                            <a class="btn btn-sm btn-danger deleteUser" href="#" data-userid="<?php echo $record->srvid; ?>" title="Delete"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>

                    <?php
                            $row_count++;
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
            jQuery("#searchList").attr("action", baseURL + "serviceslist/" + value);
            jQuery("#searchList").submit();
        });
    });
</script>