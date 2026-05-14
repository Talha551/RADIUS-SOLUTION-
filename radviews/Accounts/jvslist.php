<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fas fa-users"></i> Vouchers List
        <small>Add, Edit, Delete</small>
      </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-6 text-left">
                <div class="form-group">
                    <a class="btn btn-info" href="<?php echo base_url(); ?>servicelist_csv/1/2"><i class="fas fa-download"></i> All List</a>
                    <a class="btn btn-success" href="<?php echo base_url(); ?>servicelist_csv/1/1"><i class="fas fa-download"></i> Active</a>
                </div>
            </div>
            <div class="col-6 text-right">
                <div class="form-group">
                    <a class="btn btn-primary" href="<?php echo base_url(); ?>expenseAddNew"><i class="fas fa-plus"></i> Add Expense</a>
                    <a class="btn btn-primary" href="<?php echo base_url(); ?>depositAddNew"><i class="fas fa-plus"></i> Bank Deposit</a>
                    <a class="btn btn-primary" href="<?php echo base_url(); ?>salesAddNew"><i class="fas fa-plus"></i> Sales Entry</a>
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

        <?php $searchText1 = $type; 
                //$managerFilterUrl = $managerFilter;
        ?>
        
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                    <h3 class="card-title">JVs List</h3>
                    <div class="card-tools">
                        <form action="<?php echo base_url() ?>jvslist/" method="POST" id="searchList">
                            <div class="input-group input-group-sm">
                                <select name="searchText1" class="form-control float-right" style="width: 150px;">
                                <?php 
                                if(!empty($typesList))
                                {
                                    $row_count = 0;
                                    foreach($typesList as $record)
                                    {
                                        echo '<option value="'; echo $record->typename.'"'; 
                                        if($record->typename == $searchText1) 
                                            { echo 'selected=selected'; }
                                        echo '>'; 
                                        echo $record->typename; 
                                        echo '</option>';
                                        $row_count++;
                                    }
                                }
                                ?>
                                </select>
                              <input type="text" name="searchText" value="<?php echo $searchText; ?>" class="form-control float-right" style="width: 150px;" placeholder="Search"/>
                              <div class="input-group-append">
                                <button class="btn btn-default searchList"><i class="fas fa-search"></i></button>
                              </div>
                            </div>
                        </form>
                    </div>
                </div><!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                  <table class="table table-hover text-nowrap">
                    <thead>
                      <tr>
                          <th>S.No.</th>
                          <th>JV No.</th>
                          <th>Date</th>
                          <th>JV Type</th>
                          <th>Description</th>
                          <th>Manager</th>
                          <th>Debit</th>
                          <th>Credit</th>
                          <th>Amount</th>
                          <th class="text-center">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if(!empty($jvslist))
                      {
                          $row_count = 1;
                          foreach($jvslist as $record)
                          {
                      ?>

                      <tr>
                      <td><?php echo $record->serial_number;?>.</td>
                          <td><?php echo $record->jvid; ?></td>
                          <td><?php echo $record->jvdate; ?></td>
                          <td><?php echo $record->typename; ?></td>
                          <td><?php echo substr($record->desc,0,20); ?></td>
                          <td><?php echo $record->managername; ?></td>
                          <td><?php echo $record->accnamedr; ?></td>
                          <td><?php echo $record->accnamecr; ?></td>
                          <td><?php echo $record->debit; ?></td>
                          <?php if($record->typename == 'EXPENSES'){ $typename = 'expenseEdit'; }else if($record->typename == 'DEPOSIT'){ $typename = 'depositEdit'; }else if($record->typename == 'SALES'){ $typename = 'salesEdit'; } else { $typename = 'stockList'; } ?>
                          <td class="text-center">
                              <a class="btn btn-sm btn-primary" href="<?= base_url().'login-history/'.$record->acctdr; ?>" title="View Details"><i class="fas fa-eye"></i></a> | 
                              <a class="btn btn-sm btn-info" href="<?php echo base_url().$typename.'/'.$record->jvid; ?>" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                              <a class="btn btn-sm btn-danger deleteUser" href="#" data-userid="<?php echo $record->acctdr; ?>" title="Delete"><i class="fas fa-trash"></i></a>
                          </td>
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
            jQuery("#searchList").attr("action", baseURL + "jvslist/" + value);
            jQuery("#searchList").submit();
        });
    });
</script>