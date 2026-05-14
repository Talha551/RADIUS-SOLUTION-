<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> Transaction History
        <small>Credit & Recharge</small>
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
                    <h3 class="box-title">Transactions List</h3>
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
                        <th>S.No.</th>
                        <th>User Name</th>
                        <th>Type</th>
                        <th>Service</th>
                        <th>Invoice</th>
                        <th>Paid</th>
                        <th>Renew On</th>
                        <th>Expires On</th>
                        <th>Remarks</th>
                    </tr>
                    <?php
                    if(!empty($invoiceRecords))
                    {
                        $row_count = 1;
                        foreach($invoiceRecords as $record)
                        {
                    ?>

                    <tr>
                    <td <?php 
                            $invType = $record->invtype;
                            if($invType == 'Credit')
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->serial_number;?>.</td>
                        <td <?php 
                            $invType = $record->invtype;
                            if($invType == 'Credit')
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->username ?></td>

                        <td <?php 
                            $invType = $record->invtype;
                            if($invType == 'Credit')
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->invtype ?></td>

                        <td <?php 
                            $invType = $record->invtype;
                            if($invType == 'Credit')
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->srvname ?></td>                        

                        <td <?php 
                            $invType = $record->invtype;
                            if($invType == 'Credit')
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->costprice ?></td>

                        <td <?php 
                            $invType = $record->invtype;
                            if($invType == 'Credit')
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->amount ?></td>

                        <td <?php 
                            $invType = $record->invtype;
                            if($invType == 'Credit')
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->srvdate ?></td>
                        
                        <td <?php 
                            $invType = $record->invtype;
                            if($invType == 'Credit')
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->expdate ?></td>
                       
                        <td <?php 
                            $invType = $record->invtype;
                            if($invType == 'Credit')
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->remarks ?></td>

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
            jQuery("#searchList").attr("action", baseURL + "invoiceListing/" + value);
            jQuery("#searchList").submit();
        });
    });
</script>
