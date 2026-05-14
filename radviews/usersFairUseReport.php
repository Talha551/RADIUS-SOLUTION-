<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> Fair Use Policy / Bandwidth Report
            <small>Reports / FUP Report</small>
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
                if ($error) {
                ?>
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php } ?>
                <?php
                $success = $this->session->flashdata('success');
                if ($success) {
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
                            <table class="table table-striped table-hover">
                                <tr>
                                    <th>S.No.</th>
                                    <th>Reseller</th>
                                    <th>Total Upload</th>
                                    <th>Total Download</th>
                                </tr>
                                <?php
                                if (!empty($userSummery)) {
                                    $row_count = 1;
                                    foreach ($userSummery as $record) {
                                ?>
                                        <tr>
                                            <td><?php echo $row_count; ?></td>
                                            <td><?php echo $record->owner." - Total Download "
                                                    //"<b>".round(((($record->download*0.003042))*31),0)." Mbps) <b>"; ?></td>
                                            <td><?php echo round(($record->upload/1000),2)." TB"; ?></td>
                                            <td><?php echo round(($record->download)/1000,2)." TB"; ?></td>
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
                        <h3 class="box-title">Ledger Report</h3>
                        <div class="box-tools">
                            <form action="<?php echo base_url() ?>userFairUseReport" method="POST" id="searchList">
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
                                    <input type="text" name="searchText1" id="searchText1" value="<?php echo $searchText1; ?>" class="form-control input-sm pull-left" style="width: 150px;" autocomplete="off" placeholder="From Date(YYYY-MM-DD)" />
                                    <input type="text" name="searchText2" id="searchText2" value="<?php echo $searchText2; ?>" class="form-control input-sm pull-left" style="width: 150px;" autocomplete="off" placeholder="To Date(YYYY-MM-DD)" />
                                    <!--<input type="text" name="searchText4" value="<?php //echo $searchText4; 
                                                                                        ?>" class="form-control input-sm pull-left" style="width: 150px;" placeholder="Conference"/> -->
                                    <select name="searchText3" class="selectpicker pull-left" style="width: 200px; height: 30px;">
                                        <option value="500" <?php if($searchText3 == "500"){echo 'selected=selected';} ?>>More Than 500GB</option>
                                        <option value="1000" <?php if($searchText3 == "1000"){echo 'selected=selected';} ?>>More Than 1000GB</option>
                                        <option value="1200" <?php if($searchText3 == "1200"){echo 'selected=selected';} ?>>More Than 1200GB</option>
                                        <option value="1500" <?php if($searchText3 == "1500"){echo 'selected=selected';} ?>>More Than 1500GB</option>
                                        <option value="2000" <?php if($searchText3 == "2000"){echo 'selected=selected';} ?>>More Than 2000GB</option>
                                    </select>

                                    <!-- JVs Type List-->
                                    <input type="text" name="searchText4" value="<?php echo $searchText4; ?>" class="form-control input-sm pull-right" style="width: 150px;" placeholder="Search"/>

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
                        <table class="table table-striped table-hover">
                            <tr>
                                <th>S.No.</th>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Manager</th>
                                <th>Upload</th>
                                <th>Download</th>
                            </tr>
                            <?php
                            if (!empty($userRecords)) {
                                $row_count = 1;
                                foreach ($userRecords as $record) {
                            ?>

                                    <tr>
                                        <td><?php echo $row_count; ?>.</td>
                                        <td><?php echo $record->username ?></td>
                                        <td><?php echo $record->firstname." ".$record->lastname; ?></td>
                                        <td><?php echo $record->mobile ?></td>
                                        <td><?php echo $record->owner ?></td>
                                        <td><?php echo $record->upload ?></td>
                                        <td><?php echo $record->download ?></td>
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
    jQuery(document).ready(function() {
        jQuery('ul.pagination li a').click(function(e) {
            e.preventDefault();
            var link = jQuery(this).get(0).href;
            var value = link.substring(link.lastIndexOf('/') + 1);
            jQuery("#searchList").attr("action", baseURL + "ledgerReport/" + value);
            jQuery("#searchList").submit();
        });
    });
</script>