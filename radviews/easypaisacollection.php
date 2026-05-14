<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> Easy Paisa Collection Report
        <small>Reports / EasyPaisa Payment</small>
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
            <h3 class="box-title">Summery Report</h3>
                <div class="box">
                    <div class="box-tools">
                        <div class="box-body table-responsive no-padding">
                            <table class="table table-hover" id="summery">
                                <tr>
                                    <th>Manager</th>
                                    <th>Payments</th>
                                    <th>Amount</th>
                                    <th>Total</th>
                                </tr>
                                <?php 
                                if(!empty($easypaisaCollectionSummery))
                                {
                                    $row_count = 1;
                                    $total = 0;
                                    foreach($easypaisaCollectionSummery as $record)
                                    {
                                ?>
                                <tr>
                                    <td><?php echo $record->owner; ?></td>
                                    <td><?php echo $record->Payments; ?></td>
                                    <td><?php echo $record->amount_paid; ?></td>
                                    <td><?php echo $total = $total + $record->amount_paid; ?></td>
                                </tr>
                                <?php
                                        $row_count++;
                                    }
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
              <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Sales Report</h3>
                    <div class="box-tools">
                        <form action="<?php echo base_url() ?>easypaisaCollectionReport" method="POST" id="searchList">
                            <div class="input-group">
                                <?php if($this->session->userdata ( 'name' ) <> 'admin'){ ?>
                                    <input type="text" name="searchText" value="<?php echo $searchText; ?>" class="form-control input-sm pull-left" style="width: 150px;" placeholder="Owner" readonly/>
                                <?php }else{ ?>
                                    
                                    <select name="searchText" class="selectpicker pull-left" style="width: 150px; height: 30px;">
                                    <option value="admin">ALL</option>
                                    <?php 
                                    if(!empty($managerList))
                                    {
                                        $row_count = 0;
                                        foreach($managerList as $record)
                                        {
                                            echo '<option value="'; echo $record->managername.'"'; 
                                            if($record->managername == $searchText) 
                                                { echo 'selected=selected'; }
                                            echo '>'; 
                                            echo $record->managername; 
                                            echo '</option>';
                                            $row_count++;
                                        }
                                    }
                                    ?>
                                    </select>

                                <?php } ?>
                                <input type="text" name="searchText1" id="searchText1" value="<?php echo $searchText1; ?>" class="form-control input-sm pull-left" style="width: 150px;" autocomplete="off" placeholder="From Date(YYYY-MM-DD)"/>
                                <input type="text" name="searchText2" id="searchText2" value="<?php echo $searchText2; ?>" class="form-control input-sm pull-left" style="width: 150px;" autocomplete="off" placeholder="To Date(YYYY-MM-DD)"/>
                                <!--<input type="text" name="searchText4" value="<?php //echo $searchText4; ?>" class="form-control input-sm pull-left" style="width: 150px;" placeholder="Conference"/> -->
                                <select name="searchText3" class="selectpicker pull-left" style="width: 150px; height: 30px;">
                                        <option value="0" <?php if($searchText3==0){ echo "selected=selected"; } ?>>By Posting Date</option>
                                        <option value="1" <?php if($searchText3==1){ echo "selected=selected"; } ?>>By Transaction date</option>
                                        <option value="3" <?php if($searchText3==2){ echo "selected=selected"; } ?>>Posted</option>
                                        <option value="4" <?php if($searchText3==2){ echo "selected=selected"; } ?>>Un-Posted</option>
                                </select>
                                <select name="searchText4" class="selectpicker pull-left" style="width: 150px; height: 30px;">
                                        <option value="0" <?php if($searchText4==0){ echo "selected=selected"; } ?>>Details</option>
                                        <option value="1" <?php if($searchText4==1){ echo "selected=selected"; } ?>>Summery By Manager</option>
                                        <option value="2" <?php if($searchText4==2){ echo "selected=selected"; } ?>>All Summery</option>
                                </select>
                                <div class="input-group-btn">
                                    <button class="btn btn-sm btn-default searchList" style="height: 30px;"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div><!-- /.box-header -->

                <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                <link rel="stylesheet" href="/resources/demos/style.css">
                <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
                <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

                <script type="text/javascript">
                    
                    $(function() {
                        $("#searchText1").datepicker({
                            dateFormat: "yy-mm-dd"
                        });
                    });

                    $(function() {
                        $("#searchText2").datepicker({
                            dateFormat: "yy-mm-dd"
                        });
                    });
                    
                </script>

                <div class="box-body table-responsive no-padding">
                  <table class="table table-hover">
                    <tr>
                        <th>S.No.</th>
                        <th>User Name</th>
                        <th>Pay ID</th>
                        <th>Customer Name</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Posted</th>
                        <th>Manager</th>
                        <th>Import Date</th>
                    </tr>
                    <?php
                    if(!empty($collectionReport))
                    {
                        $row_count = 1;
                        foreach($collectionReport as $record)
                        {
                    ?>

                    <tr <?php if($record->posted == 0){ echo 'style="font-weight: bold; color: green;"'; } ?>>
                        <td style="white-space:nowrap"><?php echo $record->serial_number;?>.</td>
                        <td style="white-space:nowrap"><?php echo $record->username ?></td>
                        <td style="white-space:nowrap"><?php echo $record->consumer_number ?></td>
                        <td style="white-space:nowrap"><?php echo $record->customer_name ?></td>
                        <td><?php echo $record->amount_paid ?></td>
                        <td style="white-space:nowrap"><?php echo $record->transaction_date ?></td>                        
                        <td><?php if($record->posted == 1){ echo "POSTED"; } else { echo "Un-POSTED"; } ?></td>
                        <td><?php echo $record->owner ?></td>
                        <td><?php echo $record->createdDtm ?></td>
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
            jQuery("#searchList").attr("action", baseURL + "salesReport/" + value);
            jQuery("#searchList").submit();
        });
    });
</script>
