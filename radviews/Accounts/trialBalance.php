<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fas fa-users"></i> Trial Balance Report
        <small>Reports / Trial Balance Report</small>
      </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-12 text-right">
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
                    <h3 class="card-title">Trial Balance Report</h3>
                    <div class="card-tools">
                        <form action="<?php echo base_url() ?>trialBalance" method="POST" id="searchList">





                            <div class="input-group input-group-sm">

                            <?php 
                                $managername = $this->session->userdata ( 'name' ); 
                                if($managername == 'admin' ||  $this->ismaster > 0){
                            
                            ?>
                                <select name="searchText" class="form-control float-left" style="width: 150px; height: 30px;">
                                    <option value="admin">ALL</option>
                                    <?php 
                                    //echo $searchText;
                                    //exit;
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

                                <?php }else{ ?>
                                    <input type="text" name="searchText" value="<?php echo $searchText; ?>" class="form-control float-left" style="width: 150px;" placeholder="Owner" readonly/>
                                <?php } ?>

                                <input type="text" name="searchText1" id="searchText1" value="<?php echo $searchText1; ?>" class="form-control float-left" style="width: 150px;" autocomplete="off" placeholder="From Date(YYYY-MM-DD)"/>
                                <input type="text" name="searchText2" id="searchText2" value="<?php echo $searchText2; ?>" class="form-control float-left" style="width: 150px;" autocomplete="off" placeholder="To Date(YYYY-MM-DD)"/>
                                <!--<input type="text" name="searchText4" value="<?php //echo $searchText4; ?>" class="form-control input-sm pull-left" style="width: 150px;" placeholder="Conference"/> -->

                                <!-- JVs Type List-->
                                <select name="searchText3" class="form-control float-left" style="width: 200px; height: 30px;">
                                <option value="0">Account Group (Default ALL)</option>
                                <?php 
                                    if(!empty($accountsGroup))
                                    {
                                        $row_count = 0;
                                        foreach($accountsGroup as $record)
                                        {
                                            echo '<option value="'; echo $record->grpid.'"'; 
                                            if($record->grpid == $searchText3) 
                                                { echo 'selected=selected'; }
                                            echo '>'; 
                                            echo $record->grpname; 
                                            echo '</option>';
                                            $row_count++;
                                        }
                                    }
                                ?>
                                </select>

                                <div class="input-group-append">
                                    <button class="btn btn-sm btn-default searchList" style="height: 30px;"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div><!-- /.card-header -->

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

                <div class="card-body table-responsive p-0">
                  <table class="table table-striped table-hover">
                    <thead>
                      <tr>
                          <th>S.No.</th>
                          <th>Code</th>
                          <th>Account</th>
                          <th>Group</th>
                          <th style="text-align:right">Opening Dr.</th>
                          <th style="text-align:right">Opening Cr.</th>
                          <th style="text-align:right">Debit</th>
                          <th style="text-align:right">Credit</th>
                          <th style="text-align:right">Closing Dr.</th>
                          <th style="text-align:right">Closing Cr.</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(!empty($ledgerReport))
                    {
                        $row_count = 1;
                        $op_balance = 0;
                        $cur_balance = 0;
                        foreach($ledgerReport as $record)
                        {
                            if($record->opdebit > 0 OR $record->opcredit > 0 OR $record->credit > 0 OR $record->debit > 0){
                    ?>

                    <tr>
                        <td ><?php echo $row_count;?>.</td>
                            <td ><?php echo $record->acctid ?></td>
                            <td ><?php echo $record->accname ?></td>
                            <td ><?php echo $record->grpname ?></td>                        
                            <td style="text-align:right"><?php if($record->opdebit >= $record->opcredit){ echo $record->opdebit-$record->opcredit; } else { echo "0"; } ?></td>
                            <td style="text-align:right"><?php if($record->opcredit >= $record->opdebit){ echo $record->opcredit-$record->opdebit; } else { echo "0"; } ?></td>
                            <td style="text-align:right"><?php if($record->debit >= $record->credit){ echo $record->debit-$record->credit; } else { echo "0"; } ?></td>
                            <td style="text-align:right"><?php if($record->credit >= $record->debit){ echo $record->credit-$record->debit; } else { echo "0"; } ?></td>
                            <td style="text-align:right"><?php if(($record->debit+$record->opdebit) >= ($record->credit+$record->opcredit)){ echo (($record->debit+$record->opdebit)-($record->credit+$record->opcredit)); } else { echo "0"; } ?></td>
                            <td style="text-align:right"><?php if(($record->credit+$record->opcredit) >= ($record->debit+$record->opdebit)){ echo (($record->credit+$record->opcredit)-($record->debit+$record->opdebit)); } else { echo "0"; } ?></td>
                        </td>
                    </tr>

                    <?php
                            }
                            $row_count++;

                            //$op_balance = $op_balance + ($record->opdebit);

                            if($record->debit >= $record->credit)
                                {   $cur_balance = $cur_balance + ($record->debit-$record->credit);    }
                            if($record->opdebit >= $record->opcredit)
                                {   $op_balance = $op_balance + ($record->opdebit-$record->opcredit);    }

                        }

                    }
                    ?>
                    </tbody>
                  </table>
                  
                </div><!-- /.card-body -->
                <div class="card-footer clearfix">
                    
                </div>
              </div><!-- /.card -->
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-tools">
                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped table-hover">
                                <thead>
                                  <tr>
                                      <th>Manager</th>
                                      <th>Entries Count</th>
                                      <th style="text-align:right">Opening</th>
                                      <th style="text-align:right">Current Total</th>
                                      <th style="text-align:right">Closing</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                      <td><?php echo $this->session->userdata ( 'name' ); ?></td>
                                      <td><?php echo $row_count; ?></td>
                                      <td style="text-align:right"><?php echo $op_balance; ?></td>
                                      <td style="text-align:right"><?php echo $cur_balance; ?></td>
                                      <td style="text-align:right"><?php echo $op_balance+$cur_balance; ?></td>
                                  </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
            jQuery("#searchList").attr("action", baseURL + "ledgerReport/" + value);
            jQuery("#searchList").submit();
        });
    });
</script>