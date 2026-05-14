<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> Online Calls
        <small>Reports / Online Calls</small>
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
            <div class="col-xs-12">
              <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Online Calls List</h3>
                    <div class="box-tools">
                        <form action="<?php echo base_url() ?>onlineusers" method="POST" id="searchList">
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
                        <th>Manage</th>
                        <th>Start Time</th>
                        <th>Session Time</th>
                        <th>Download</th>
                        <th>Upload</th>
                        <th>Connected To</th>
                        <th>First/Last Name</th>
                    </tr>
                    <?php
                    


                    if(!empty($onlineCallsListing))
                    {
                        $row_count = 1;
                        foreach($onlineCallsListing as $record)
                        {
                    ?>

                    <tr id="<?php echo $record->username; ?>">
                    <td <?php 
                            $download = round(((($record->acctoutputoctets)/1024)/1024)/1024,2);
                            if($download > 25)
                                echo 'style="color: red;"';
                        ?>><?php echo $record->serial_number;?>.</td>
                        <td <?php 
                            if($download > 25)
                                echo 'style="color: red;"';
                        ?>><?php echo $record->username ?></td>

                        <td>
                            <a class="btn btn-sm btn-info" href="<?php echo base_url().'usersEdit/'.$record->username; ?>" title="View User"><i class="fa fa-eye"></i></a>
                            <a class="btn btn-sm btn-danger disconnect" href="#" data-username="<?php echo $record->username; ?>" title="Disconnect"><i class="fa fa-chain-broken"></i></a>
                        </td>

                        <td <?php 
                            if($download > 25)
                                echo 'style="color: red;"';
                        ?>><?php echo $record->acctstarttime ?></td>

                        <td <?php 
                            if($download > 25)
                                echo 'style="color: red;"';
                        ?>><?php echo round(((($record->acctsessiontime)/60)/60),0)." Hours" ?></td>                        

                        <td <?php 
                            if($download > 25)
                                echo 'style="color: red;"';
                        ?>><?php echo round(((($record->acctoutputoctets)/1024)/1024)/1024,2)." Gb" ?></td>

                        <td <?php 
                            if($download > 25)
                                echo 'style="color: red;"';
                        ?>><?php echo round(((($record->acctinputoctets)/1024)/1024)/1024,2)." Gb" ?></td>
                        
                        <td <?php 
                            if($download > 25)
                                echo 'style="color: red;"';
                        ?>><?php echo $record->description ?></td>
                       
                        <td <?php 
                            if($download > 25)
                                echo 'style="color: red;"';
                        ?>><?php echo $record->firstname."/".$record->lastname ?></td>

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
            jQuery("#searchList").attr("action", baseURL + "onlineusers/" + value);
            jQuery("#searchList").submit();
        });
    });
</script>