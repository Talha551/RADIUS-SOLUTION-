<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fas fa-users"></i> Ledger Report
        <small>Reports / Ledger Report</small>
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
                    <div class="card-tools">
                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Manager</th>
                                        <th>Entries Count</th>
                                        <th class="text-right">Opening</th>
                                        <th class="text-right">Current Debit</th>
                                        <th class="text-right">Current Credit</th>
                                        <th class="text-right">Closing</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                                if(!empty($ledgerSummery))
                                {
                                    $row_count = 1;
                                    foreach($ledgerSummery as $record)
                                    {
                                ?>
                                <tr>
                                    <td><?php echo $record->managername; ?></td>
                                    <td><?php echo $record->Entries; ?></td>
                                    <td class="text-right"><?php echo $record->openingDr-$record->openingCr; ?></td>
                                    <td class="text-right"><?php echo $record->debit; ?></td>
                                    <td class="text-right"><?php echo $record->credit; ?></td>
                                    <td class="text-right"><?php echo $record->closingDr-$record->closingCr; ?></td>
                                </tr>
                                <?php
                                        $row_count++;
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ledger Report</h3>
                    <div class="card-tools">
                        <form action="<?php echo base_url() ?>ledgerReport" method="POST" id="searchList">
                            <div class="input-group input-group-sm">

                            <?php 
                                $managername = $this->session->userdata ( 'name' ); 
                                if($managername == 'admin' ||  $this->ismaster > 0){
                            
                            ?>
                                <select name="searchText" class="form-control float-right" style="width: 150px; height: 30px;">
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
                                    <input type="text" name="searchText" value="<?php echo $searchText; ?>" class="form-control float-right" style="width: 150px;" placeholder="Owner" readonly/>
                                <?php } ?>
                                <input type="text" name="searchText1" id="searchText1" value="<?php echo $searchText1; ?>" class="form-control float-right" style="width: 150px;" autocomplete="off" placeholder="From Date(YYYY-MM-DD)"/>
                                <input type="text" name="searchText2" id="searchText2" value="<?php echo $searchText2; ?>" class="form-control float-right" style="width: 150px;" autocomplete="off" placeholder="To Date(YYYY-MM-DD)"/>
                                <!--<input type="text" name="searchText4" value="<?php //echo $searchText4; ?>" class="form-control input-sm pull-left" style="width: 150px;" placeholder="Conference"/> -->
                                <select name="searchText3" class="form-control float-right" style="width: 200px; height: 30px;">
                                <?php 
                                    if(!empty($accountsList))
                                    {
                                        $row_count = 0;
                                        foreach($accountsList as $record)
                                        {
                                            echo '<option value="'; echo $record->acctid.'"'; 
                                            if($record->acctid == $searchText3) 
                                                { echo 'selected=selected'; }
                                            echo '>'; 
                                            echo $record->accname; 
                                            echo '</option>';
                                            $row_count++;
                                        }
                                    }
                                ?>
                                </select>

                                <!-- JVs Type List-->
                                <select name="searchText4" class="form-control float-right" style="width: 200px; height: 30px;">
                                <option value="0">Entry Type (Default ALL)</option>
                                <?php 
                                    if(!empty($jvTypes))
                                    {
                                        $row_count = 0;
                                        foreach($jvTypes as $record)
                                        {
                                            echo '<option value="'; echo $record->typeid.'"'; 
                                            if($record->typeid == $searchText4) 
                                                { echo 'selected=selected'; }
                                            echo '>'; 
                                            echo $record->typename; 
                                            echo '</option>';
                                            $row_count++;
                                        }
                                    }
                                ?>
                                </select>

                                <div class="input-group-append">
                                    <button class="btn btn-default searchList" style="height: 30px;"><i class="fas fa-search"></i></button>
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
                            <th>Entry ID</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Entry</th>
                            <th>Manager</th>
                            <th class="text-right">Amount</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(!empty($ledgerReport))
                    {
                        $row_count = 1;
                        foreach($ledgerReport as $record)
                        {
                    ?>

                    <tr>
                    <td <?php 
                            $acctcr = $record->acctcr;
                            if($acctcr == $searchText3)
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->serial_number;?>.</td>
                        <td <?php 
                            if($acctcr == $searchText3)
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->jvid ?></td>

                        <td <?php 
                            if($acctcr == $searchText3)
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->jvdate ?></td>

                        <td <?php 
                            if($acctcr == $searchText3)
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->desc ?></td>                        

                        <td <?php 
                            if($acctcr == $searchText3)
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->typename ?></td>

                        <td <?php 
                            if($acctcr == $searchText3)
                                echo 'style="color: blue;"';
                        ?>><?php echo $record->managername ?></td>
                        
                        <td <?php 
                            if($acctcr == $searchText3)
                                {echo 'style="color: blue; text-align:right"';}else{echo 'style="text-align:right"';}
                        ?> ><?php 
                            if($searchText3 <> 0)
                                {   if($record->acctdr == $searchText3){ echo $record->debit; }else{ echo "(".$record->debit.")";}  }

                        ?></td>

                        <td <?php 
                            if($acctcr == $searchText3 || $record->craccgroup == $searchText4)
                                echo 'style="color: blue;"';
                        ?>><?php
                            if($searchText3 <> 0)
                                {   if($record->acctdr == $searchText3){ echo "Debit"; }else{ echo "(Credit)"; }    }
                        ?></td>

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
