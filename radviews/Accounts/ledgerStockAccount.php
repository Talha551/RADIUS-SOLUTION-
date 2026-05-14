<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fas fa-box"></i> Stock Ledger
        <small>Reports / Stock Ledger</small>
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
                        <h3 class="card-title">Stock Ledger</h3>
                        <div class="card-tools">
                            <form action="<?php echo base_url() ?>Accounts/Accounts_controller/accountsLedgerStock" method="POST" id="searchList">
                                <div class="input-group input-group-sm">
                                    <select name="searchItem" class="form-control float-right" style="width: 400px; height: 30px;">
                                        <option value="">Select Item</option>
                                        <?php 
                                        if(!empty($accountsList))
                                        {
                                            foreach($accountsList as $record)
                                            {
                                                echo '<option value="'; echo $record->acctid.'"'; 
                                                if($record->acctid == $searchItem) 
                                                    { echo 'selected=selected'; }
                                                echo '>'; 
                                                echo $record->accname; 
                                                echo '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <input type="text" name="fromDate" id="fromDate" value="<?php echo $fromDate; ?>" class="form-control float-right" style="width: 150px;" autocomplete="off" placeholder="From Date(YYYY-MM-DD)"/>
                                    <input type="text" name="toDate" id="toDate" value="<?php echo $toDate; ?>" class="form-control float-right" style="width: 150px;" autocomplete="off" placeholder="To Date(YYYY-MM-DD)"/>
                                    <div class="input-group-append">
                                        <button class="btn btn-default searchList" style="height: 30px;"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div><!-- /.card-header -->
                    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
                    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
                    <script type="text/javascript">
                        $(function() {
                            $("#fromDate").datepicker({ dateFormat: "yy-mm-dd" });
                            $("#toDate").datepicker({ dateFormat: "yy-mm-dd" });
                        });
                    </script>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Entry</th>
                                    <th>Type</th>
                                    <th>Manager</th>
                                    <th>User</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Stock In</th>
                                    <th>Stock Out</th>
                                    <th>Price</th>

                                    <th>Running Qty</th>
                                    <th>Running Value</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                            
                            $runningQty = 0;
                            $runningValue = 0;
                            $totalInQty = 0;
                            $totalOutQty = 0;
                            $totalValue = 0;
                            if(!empty($stockLedger))
                            {
                                $row_count = 1;
                                foreach($stockLedger as $record)
                                {
                                    $stockIn = ($record->jvtype == 1 || $record->jvtype == 6) ? $record->invinqty : 0;
                                    $stockOut = ($record->jvtype == 4 || $record->jvtype == 7) ? $record->invinqty : 0;
                                    $price = $record->invprice;
                                    $total = $record->invtotal;
                                    if($record->jvtype == 1 || $record->jvtype == 6) {
                                        $runningQty += $stockIn;
                                        $runningValue += $total;
                                        $totalInQty += $stockIn;
                                    } else if($record->jvtype == 4 || $record->jvtype == 7) {
                                        $runningQty -= $stockOut;
                                        $runningValue -= $total;
                                        $totalOutQty += $stockOut;
                                    }
                                    $totalValue += $total;

                                    Switch ($record->jvtype) {
                                        case 1:
                                            $type = 'STOCK';
                                            break;
                                        case 4:
                                            $type = 'ISSUE';
                                            break;
                                        case 6:
                                            $type = 'IN';
                                            break;
                                        case 7:
                                            $type = 'OUT';
                                            break;
                                    }
                            ?>
                            <tr>
                                <td><?php echo $row_count; ?>.</td>
                                <td><?php echo $record->jvid; ?></td>
                                <td><?php echo $type; ?></td>
                                <td><?php echo $record->managername; ?></td>
                                <td><?php echo $record->username; ?></td>
                                <td><?php echo $record->jvdate; ?></td>
                                <td><?php echo $record->desc; ?></td>
                                <td><?php echo ($stockIn > 0) ? $stockIn : ''; ?></td>
                                <td><?php echo ($stockOut > 0) ? $stockOut : ''; ?></td>
                                <td><?php echo number_format($price,2); ?></td>

                                <td><?php echo $runningQty; ?></td>
                                <td><?php echo number_format($runningValue,2); ?></td>
                            </tr>
                            <?php
                                    $row_count++;
                                }
                            ?>
                            <tr class="font-weight-bold bg-light">
                                <td colspan="7">Totals</td>
                                <td><?php echo $totalInQty; ?></td>
                                <td><?php echo $totalOutQty; ?></td>
                                <td></td>
                                <td><?php echo $runningQty; ?></td>
                                <td><?php echo number_format($runningValue,2); ?></td>
                            </tr>
                            <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div><!-- /.card-body -->
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
            jQuery("#searchList").attr("action", baseURL + "Accounts/Accounts_controller/accountsLedgerStock/" + value);
            jQuery("#searchList").submit();
        });
    });
</script> 