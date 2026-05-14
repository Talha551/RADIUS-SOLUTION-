<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-users"></i> Accounts Management
                        <small>Add, Edit, Delete</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Accounts Management</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-6 text-left">
                    <div class="form-group">
                        <a class="btn btn-info" href="<?php echo base_url(); ?>servicelist_csv/1/2"><i class="fas fa-download"></i> All List</a>
                        <a class="btn btn-success" href="<?php echo base_url(); ?>servicelist_csv/1/1"><i class="fas fa-download"></i> Active</a>
                    </div>
                </div>
                <div class="col-6 text-right">
                    <div class="form-group">
                        <a class="btn btn-primary" href="<?php echo base_url(); ?>accountsAddNew"><i class="fas fa-plus"></i> Add New</a>
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
                    $managerFilterUrl = $managerFilter;
            ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5><b></b> <span class="badge badge-dark"> Opening : <?php if(!empty($sumAccountsDrCr->opening)){ echo $sumAccountsDrCr->opening; }else{ echo "0"; } ?>
                            </span> <span class="badge badge-danger">Monthly : <?php if(!empty($sumAccountsDrCr->debit)){ echo $sumAccountsDrCr->debit; }else{ echo "0"; } ?>
                            <?php $total = ($sumAccountsDrCr->opening + $sumAccountsDrCr->debit) - $sumAccountsDrCr->credit ?></span>
                            <span class="badge badge-danger"> Clsoing : <?php if(!empty($sumAccountsDrCr->total)){ echo $sumAccountsDrCr->total; }else{ echo "0"; } ?> </span>
                            </h5>
                            <div class="card-tools">
                                <form action="<?php echo base_url() ?>accountslist/" method="POST" id="searchList">
                                    <div class="input-group input-group-sm">
                                        <select name="searchText1" class="form-control float-right" style="width: 150px;">
                                        <?php 
                                        if(!empty($accountsGroup))
                                        {
                                            $row_count = 0;
                                            foreach($accountsGroup as $record)
                                            {
                                                echo '<option value="'; echo $record->grpname.'"'; 
                                                if($record->grpname == $searchText1) 
                                                    { echo 'selected=selected'; }
                                                echo '>'; 
                                                echo $record->grpname; 
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
                                        <th>Acct ID</th>
                                        <th>Account</th>
                                        <th>Group</th>
                                        <th>Manager</th>
                                        <th class="text-right">opening</th>
                                        <th class="text-right">debit</th>
                                        <th class="text-right">credit</th>
                                        <th class="text-right">balance</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(!empty($accountsListing))
                                    {
                                        $row_count = 1;
                                        foreach($accountsListing as $record)
                                        {
                                            $opening = $record->openingDr-$record->openingCr;
                                            $balance = (($record->openingDr-$record->openingDr) + $record->debit) - $record->credit;
                                    ?>
                                    <tr>
                                        <td><?php echo $record->serial_number;?>.</td>
                                        <td><?php echo $record->acctid ?></td>
                                        <td><?php echo $record->accname ?></td>
                                        <td><?php echo $record->grpname ?></td>
                                        <td><?php echo $record->managername ?></td>
                                        <td class="text-right"><?php if(!empty($opening)){ echo abs($opening); } else { echo ' -- '; } ?></td>
                                        <td class="text-right"><?php if(!empty($record->debit)){ echo abs($record->debit); } else { echo ' -- '; } ?></td>
                                        <td class="text-right"><?php if(!empty($record->credit)){ echo abs($record->credit); } else { echo ' -- '; } ?></td>
                                        <td class="text-right"><?php if(!empty($balance)){ if($balance < 0){ echo "(".abs($balance).") Cr."; }else{ echo $balance." Dr."; } } else { echo "--"; } ?></td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-primary" href="<?= base_url().'login-history/'.$record->acctid; ?>" title="View Details"><i class="fas fa-eye"></i></a> | 
                                            <a class="btn btn-sm btn-info" href="<?php echo base_url().'accountsEdit/'.$record->acctid; ?>" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                                            <a class="btn btn-sm btn-danger deleteUser" href="#" data-userid="<?php echo $record->acctid; ?>" title="Delete"><i class="fas fa-trash"></i></a>
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
            jQuery("#searchList").attr("action", baseURL + "accountslist/" + value);
            jQuery("#searchList").submit();
        });
    });
</script>