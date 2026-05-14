<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> Dynamic DNS NAS List
        <small>Auto Dynamic List</small>
      </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12 text-right">
                <div class="form-group">
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
            <div class="col-xs-12 text-left">
                <h4>Add New Dynamic NAS Mapping</h4>
            </div>
        </div>

        <div class="row">

            <div class="col-xs-3 text-left">
                <div class="form-group">
                    <a class="btn btn-primary" href="<?php echo base_url(); ?>dyndnsAddNew"><i class="fa fa-plus"></i> Add New</a>
                </div>
            </div>
        </div>

        
        <div class="row">
            <div class="col-xs-12">
              <div class="box">
                <div class="box-header">
                    <h3 class="box-title">NAS List not editable. If require delete and create new mapping.</h3>
                    <div class="box-tools">
                        <form action="<?php echo base_url() ?>invoiceListing" method="POST" id="searchList">
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
                        <th>Name</th>
                        <th>NAS IP</th>
                        <th>Domain</th>
                        <th>A Record</th>
                        <th>Auth Mode</th>
                        <th>API User</th>
                        <th>API Port</th>
                        <th>Manager</th>
                        <th>created On</th>
                        <th class="text-center">Action</th>
                    </tr>
                    <?php
                    if(!empty($dynNasListing))
                    {
                        $row_count = 1;
                        foreach($dynNasListing as $record)
                        {
                    ?>

                    <tr>
                        <td ><?php echo $record->shortname ?></td>
                        <td ><?php echo $record->nasname ?></td>
                        <td ><?php echo $record->domain ?></td>
                        <td ><?php $resolved = gethostbyname($record->domain); if($resolved <> $record->domain){ echo gethostbyname($record->domain); }else{ echo "Host Unknow"; }?></td>
                        <td ><?php if($record->authmode == 0){ echo "Radius"; }else{ echo "API"; } ?></td>
                        <td ><?php echo $record->apiuser ?></td>
                        <td ><?php echo $record->apiport ?></td>
                        <td ><?php echo $record->managername ?></td>
                        <td ><?php echo $record->createdDtm ?></td>
                        <td class="text-center">
                            <a class="btn btn-sm btn-primary" href="<?= base_url().'null/'.$record->domain; ?>" title="Update Link"><i class="fa fa-external-link-square"></i></a> | 
                            <a class="btn btn-sm btn-danger deleteUser" href="<?= base_url().'other_controller/nasMappingDelete/'.$record->nasname; ?>" title="Delete"><i class="fa fa-trash"></i></a>
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
            jQuery("#searchList").attr("action", baseURL + "invoiceListing/" + value);
            jQuery("#searchList").submit();
        });
    });
</script>
