<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> Sales Report
        <small>Reports / Sales Report</small>
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
                            <table class="table table-hover">
                                <tr>
                                    <th>Manager</th>
                                    <th>Trans Count</th>
                                    <th>Active</th>
                                    <th>Type</th>
                                    <th>Invoice</th>
                                    <th>Balance</th>
                                </tr>
                                <?php 
                                //print_r($salesSummery);
                                //exit;
                                if(!empty($salesSummery))
                                {
                                    $row_count = 1;
                                    foreach($salesSummery as $record)
                                    {
                                ?>
                                <tr>
                                    <td><?php echo $record->managername; ?></td>
                                    <td><?php echo $record->Users; ?></td>

                                    <!-- Get Online Customers -->
                                    <td><?php 
                                    //$curDate = strtotime(date("d-m-Y"));
                                    $curDate = date("y-m-d");
                                    $managername = $record->managername;;
                                    //Total Customers from Table
                                    $this->db->select('username');
                                    $this->db->from('radacct');
                                    $this->db->where('acctstoptime is null', null, false);
                                    if($managername <> 'admin'){
                                        $this->db->where("username IN (Select username from rm_users where owner='".$managername."')", null, false);
                                    }
                                    echo $this->db->count_all_results();
                                    ?></td>


                                    <td><?php echo $record->type; ?></td>
                                    <td><?php echo $record->price; ?></td>
                                    <td><?php echo $record->amount; ?></td>
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
                    <h3 class="box-title">Sales Report </h3>
                    <p><?php "ssssss ".$this->session->userdata ( 'name' ); ?></p>

                    <div class="box-tools">
                        <form action="<?php echo base_url() ?>salesReport_gen" method="POST" id="searchList">
                            <div class="input-group">
                                <?php if($this->session->userdata ( 'name' ) <> 'admin' && $this->ismaster == 0){ ?>
                                    <input type="text" name="searchText" value="<?php echo $searchText; ?>" class="form-control input-sm pull-left" style="width: 150px;" placeholder="Owner" readonly/>
                                <?php }else{ ?>
                                    
                                    <select name="searchText" class="selectpicker pull-left" style="width: 150px; height: 30px;">
                                    <option value="<?php echo $this->session->userdata ( 'name' ); ?>" <?php if($this->session->userdata ( 'name' ) == $searchText){echo 'selected=selected';} ?>><?php echo $this->session->userdata ( 'name' ); ?></option>
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
                                        <option value="0" <?php if($searchText3==0){ echo "selected=selected"; } ?>>By Service Date</option>
                                        <option value="1" <?php if($searchText3==1){ echo "selected=selected"; } ?>>Dr. By Service Date</option>
                                        <option value="2" <?php if($searchText3==2){ echo "selected=selected"; } ?>>Cr. By Service Date</option>
                                        <option value="3" <?php if($searchText3==3){ echo "selected=selected"; } ?>>By Transaction Date</option>
                                        <option value="4" <?php if($searchText3==4){ echo "selected=selected"; } ?>>Dr. By Transaction Date</option>
                                        <option value="5" <?php if($searchText3==5){ echo "selected=selected"; } ?>>Cr. By Transaction Date</option>
                                        <option value="6" <?php if($searchText3==6){ echo "selected=selected"; } ?>>Net Balance Report</option>
                                        <option value="7" <?php if($searchText3==7){ echo "selected=selected"; } ?>>Credit Refund Report</option>
                                        <option value="8" <?php if($searchText3==8){ echo "selected=selected"; } ?>>Reseller Summery Report</option>

                                        <option value="9" <?php if($searchText3==9){ echo "selected=selected"; }   ?>>Package SalesBy Service Name</option>
                                        <option value="10" <?php if($searchText3==10){ echo "selected=selected"; } ?>>Package SalesBy Controller Service</option>
                                        <option value="11" <?php if($searchText3==11){ echo "selected=selected"; } ?>>Package SalesBy All Manager</option>
                                        <option value="12" <?php if($searchText3==12){ echo "selected=selected"; } ?>>Package SalesBy All Masters</option>
                                        <option value="13" <?php if($searchText3==13){ echo "selected=selected"; } ?>>Package SalesBy Master Resellers</option>
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
                        <th>Type</th>
                        <th>Service</th>
                        <th>Invoice</th>
                        <th>Paid</th>
                        <th>Renew On</th>
                        <th>Expires On</th>
                        <th>Trans Date</th>
                        <th>Remarks</th>
                    </tr>
                    <?php
                    if(!empty($salesReport))
                    {
                        $row_count = 1;
                        foreach($salesReport as $record)
                        {
                    ?>

                    <tr <?php if($record->eppay<>NULL){ echo 'style="font-weight: bold; color: green;"'; } ?>>
                    <td style="white-space:nowrap" <?php 
                            $invType = $record->invtype;
                            if($invType == 'Credit')
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->serial_number;?>.</td>

                        <td style="white-space:nowrap" <?php 
                            $invType = $record->invtype;
                            if($invType == 'Credit')
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->username ?></td>

                        <td style="white-space:nowrap" <?php 
                            $invType = $record->invtype;
                            if($invType == 'Credit')
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->invtype ?></td>

                        <td style="white-space:nowrap" <?php 
                            $invType = $record->invtype;
                            if($invType == 'Credit')
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->srvname ?></td>                        

                        <td style="white-space:nowrap" <?php 
                            $invType = $record->invtype;
                            if($invType == 'Credit')
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->costprice ?></td>

                        <td style="white-space:nowrap" <?php 
                            $invType = $record->invtype;
                            if($invType == 'Credit')
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->amount ?></td>

                        <td style="white-space:nowrap" <?php 
                            $invType = $record->invtype;
                            if($invType == 'Credit')
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->srvdate ?></td>
                        
                        <td style="white-space:nowrap" <?php 
                            $invType = $record->invtype;
                            if($invType == 'Credit')
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->expdate ?></td>

                        <td style="white-space:nowrap" <?php 
                            $invType = $record->invtype;
                            if($invType == 'Credit')
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->createdDtm ?></td>

                        <td <?php 
                            $invType = $record->invtype;
                            if($invType == 'Credit')
                                echo 'style="color: blue;"';
                        ?>><?php if($record->eppay<>NULL){ echo "(EasyPaisa Pay:".$record->eppay.") ".$record->remarks; } else { echo $record->remarks; } ?></td>

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
