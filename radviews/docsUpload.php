<?php
$user = $userInfo->username;

$fname = $userInfo->firstname;
$lname = $userInfo->lastname;
$address = $userInfo->address;
$mobile = $userInfo->mobile;
$email = $userInfo->email;
$cnic = $userInfo->taxid;

if(!empty($userDocsInfo)){
    $payid = $userDocsInfo->payid;
    if($userDocsInfo->payname == "N/A" or $userDocsInfo->payname == "")
    {
        $payname = $userInfo->firstname." ".$userInfo->lastname;
    }
    else{
        $payname = $userDocsInfo->payname;
    }
    $discount = $userDocsInfo->discount;
    $adjamount = $userDocsInfo->adjamount;
    $cnic_file1 = $userDocsInfo->cnic_file1;
    $cnic_file2 = $userDocsInfo->cnic_file2;

    $inst_name = $userDocsInfo->inst_name;
    $inst_box = $userDocsInfo->inst_box;
    $inst_wifi = $userDocsInfo->inst_wifi;
    $inst_fiber = $userDocsInfo->inst_fiber;
    $inst_meter = $userDocsInfo->inst_meter;
    $inst_chrg = $userDocsInfo->inst_chrg;
    $inst_cost = $userDocsInfo->inst_cost;
    $inst_disc = $userDocsInfo->inst_disc;
    //echo $cnic_file1;
    //exit;
}
else{
    $payid = "N/A";
    $payname = $userInfo->firstname." ".$userInfo->lastname;
    $discount = 0;
    $adjamount = 0;
    $cnic_file1 = "Front Side";
    $cnic_file2 = "Back Side";
    
    $inst_name = $payname;
    $inst_box = '';
    $inst_wifi = '';
    $inst_fiber = '';
    $inst_meter = '0';
    $inst_chrg = '0.00';
    $inst_cost = '0.00';
    $inst_disc = '0.00';
}
$alldiscount = $managerInfo->perm_allowdiscount;


?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-users"></i> Documents Management
                        <small>User CNIC / Edit User</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Documents Management</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-10">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Upload User Documents</h3>
                        </div><!-- /.card-header -->
                        <!-- form start -->
                        
                        <form role="form" action="<?php echo base_url() ?>updateUser" method="post" id="updateUser" role="form">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="username">User Name</label>
                                            <input type="label" class="form-control" id="user" placeholder="First Name" name="user" value="<?php echo $user; ?>" maxlength="50" readonly>
                                            <input type="hidden" value="<?php echo $user; ?>" name="user" id="user" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="firstname">First Name</label>
                                            <input type="text" class="form-control" id="fname" placeholder="First Name" name="fname" value="<?php echo $fname; ?>" maxlength="20" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="lastname">Last Name</label>
                                            <input type="text" class="form-control" id="lname" placeholder="Last Name" name="lname" value="<?php echo $lname; ?>" maxlength="20" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="mobile">Mobile Number</label>
                                            <input type="text" class="form-control" id="mobile" placeholder="Mobile Number" name="mobile" value="<?php echo $mobile; ?>" maxlength="13" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-4">                                
                                        <div class="form-group">
                                            <label for="firstname">CNIC</label>
                                            <input type="text" class="form-control" id="cnic" placeholder="C.N.I.C No." name="cnic" value="<?php echo $cnic; ?>" maxlength="16" readonly>
                                            <input type="hidden" value="<?php echo $cnic; ?>" name="cnic" id="cnic" />    
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="card-body">
                            <div class="alert alert-info" role="alert">User Payment ID / Discount Information</div>

                            <form role="form" id="updateDocsInfo" action="<?php echo base_url() ?>userslist/updateDocsInfo" method="post" role="form">
                                <div class="row">
                                    <div class="col-md-0">                                
                                        <div class="form-group">
                                            <input type="hidden" value="<?php echo $user; ?>" name="user" id="user" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">                                
                                        <div class="form-group">
                                            <label for="payid">Payment ID</label>
                                            <input type="text" class="form-control" id="payid" placeholder="Payment ID" name="payid" value="<?php echo $payid; ?>" maxlength="16" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">                                
                                        <div class="form-group">
                                            <label for="payname">Full Name (Payment Document)</label>
                                            <input type="text" class="form-control" id="payname" placeholder="Payment Name" name="payname" value="<?php echo $payname; ?>" maxlength="16">
                                        </div>
                                    </div>
                                    <div class="col-md-2">                                
                                        <div class="form-group">
                                            <label for="discount">Discount</label>
                                            <input type="text" class="form-control" id="discount" placeholder="Discount" name="discount" value="<?php echo $discount; ?>" maxlength="16" autocomplete="off" <?php if($managerInfo->perm_allowdiscount == 0) { echo "readonly"; } ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-2">                                
                                        <div class="form-group">
                                            <label for="adjamount">Adjustment</label>
                                            <input type="text" class="form-control" id="adjamount" placeholder="Adjustment" name="adjamount" value="<?php echo $adjamount; ?>" maxlength="16" autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Update Data/Payment ID</button>
                                </div>
                            </form>

                            <hr>

                            <div class="alert alert-info" role="alert">Upload CNIC Images</div>
                            <form method="post" action="<?php echo base_url() ?>userslist/storeImages" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-0">                                
                                        <div class="form-group">
                                            <input type="hidden" value="<?php echo $user; ?>" name="user" id="user" />
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cnic_image1"><?php echo $cnic_file1 ?></label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="cnic_image1" name="cnic_image1">
                                                    <label class="custom-file-label" for="cnic_image1">Choose file</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cnic_image2"><?php echo $cnic_file2 ?></label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="cnic_image2" name="cnic_image2">
                                                    <label class="custom-file-label" for="cnic_image2">Choose file</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Upload Images</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="card-body">
                            <div class="alert alert-info" role="alert">Already Uploaded Images</div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">

                                        <div class="card-body text-center">
                                            <?php if(!empty($userDocsInfo) && !empty($userDocsInfo->cnic_file1)): ?>
                                                <a href="<?php echo base_url('uploads/'.$userDocsInfo->cnic_file1); ?>" target="_blank">
                                                    <img src="<?php echo base_url('uploads/'.$userDocsInfo->cnic_file1); ?>" class="img-fluid img-thumbnail" alt="Front Side">
                                                </a>
                                            <?php else: ?>
                                                <img src="<?php echo base_url('assets/images/no-image.png'); ?>" class="img-fluid img-thumbnail" alt="Not Uploaded">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <?php if(!empty($userDocsInfo) && !empty($userDocsInfo->cnic_file2)): ?>
                                                <a href="<?php echo base_url('uploads/'.$userDocsInfo->cnic_file2); ?>" target="_blank">
                                                    <img src="<?php echo base_url('uploads/'.$userDocsInfo->cnic_file2); ?>" class="img-fluid img-thumbnail" alt="Back Side">
                                                </a>
                                            <?php else: ?>
                                                <img src="<?php echo base_url('assets/images/no-image.png'); ?>" class="img-fluid img-thumbnail" alt="Not Uploaded">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted text-center mt-2">Click on images to view full size</p>
                        </div>

                        <?php 
                        $managername = $this->session->userdata('name');
                        if($this->accountsmanager == 1 || $managername == 'admin'){ ?>
                        <div class="card">
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link active" href="#installation" data-toggle="tab">Installation</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#activation" data-toggle="tab">Activation</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#stock" data-toggle="tab">Stock Issue</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#accounts" data-toggle="tab">Accounts</a></li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <!-- Installation Tab -->
                                    <div class="active tab-pane" id="installation">
                                        <div class="alert alert-info" role="alert">User Installation Details / Cost</div>
                                        <form role="form" id="updateInstallationInfo" action="<?php echo base_url() ?>userslist/updateInstallationInfo" method="post" role="form">
                                            <div class="row">
                                                <div class="col-md-3">                                
                                                    <div class="form-group">
                                                        <label for="inst_name">Installation Name</label>
                                                        <input type="text" class="form-control" id="inst_name" placeholder="<?php echo $inst_name; ?>" name="inst_name" value="<?php echo $inst_name; ?>" maxlength="255">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">                                
                                                    <div class="form-group">
                                                        <label for="inst_box">ONU MODEL / MAKE / S.NO.</label>
                                                        <input type="text" class="form-control" id="inst_box" placeholder="ONU MODEL / MAKE / S.NO." name="inst_box" value="<?php echo $inst_box; ?>" maxlength="255">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">                                
                                                    <div class="form-group">
                                                        <label for="inst_wifi">WiFi Router / MAKE / S.NO.</label>
                                                        <input type="text" class="form-control" id="inst_wifi" placeholder="WiFi Router / MAKE / S.NO." name="inst_wifi" value="<?php echo $inst_wifi; ?>" maxlength="255">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">                                
                                                    <div class="form-group">
                                                        <label for="inst_fiber">Fiber Length (Meters)</label>
                                                        <input type="text" class="form-control" id="inst_fiber" placeholder="Fiber Length (Meters)" name="inst_fiber" value="<?php echo $inst_fiber; ?>" maxlength="255">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">                                
                                                    <div class="form-group">
                                                        <label for="inst_meter">Meter Reading</label>
                                                        <input type="text" class="form-control" id="inst_meter" placeholder="Meter Reading" name="inst_meter" value="<?php echo $inst_meter; ?>" maxlength="255">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">                                
                                                    <div class="form-group">
                                                        <label for="inst_chrg">Installation</label>
                                                        <input type="text" class="form-control" id="inst_chrg" placeholder="Installation Charges" name="inst_chrg" value="<?php echo $inst_chrg; ?>" maxlength="255">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">                                
                                                    <div class="form-group">
                                                        <label for="inst_cost">Cost Price</label>
                                                        <input type="text" class="form-control" id="inst_cost" placeholder="Cost Price" name="inst_cost" value="<?php echo $inst_cost; ?>" maxlength="255">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">                                
                                                    <div class="form-group">
                                                        <label for="inst_disc">Discount</label>
                                                        <input type="text" class="form-control" id="inst_disc" placeholder="Discount" name="inst_disc" value="<?php echo $inst_disc; ?>" maxlength="255">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button type="submit" class="btn btn-primary">Update Installation Info</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Stock Issue Tab -->
                                    <div class="tab-pane" id="stock">
                                        <div class="alert alert-info" role="alert">Stock Issue Details</div>

                                        <div class="col-6 text-left">
                                            <div class="form-group">
                                                <a class="btn btn-primary" href="<?php echo base_url(); ?>Accounts/Stock_controller/stockAddNew/1/<?php echo $user; ?>"><i class="fas fa-plus"></i> Stock Purchases</a>
                                                <a class="btn btn-primary" href="<?php echo base_url(); ?>Accounts/Stock_controller/stockAddNew/4/<?php echo $user; ?>"><i class="fas fa-plus"></i> Stock Issue</a>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="stockList" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Type</th>
                                                        <th>Item</th>
                                                        <th>Qty</th>
                                                        <th>Price</th>
                                                        <th>Total</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if(!empty($stockList))
                                                    {
                                                        foreach($stockList as $record)
                                                        {
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php 
                                                            if($record->typename == 'STOCK') {
                                                                echo "STOCK-IN";
                                                            } else {
                                                                echo "STOCK-OUT";
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php 
                                                                if($record->typename == 'STOCK') {
                                                                    echo $record->accnamedr;
                                                                } else {
                                                                    echo $record->accnamecr;
                                                                }
                                                            ?>                                                        
                                                        <td>
                                                            <?php 
                                                                echo $record->invinqty;
                                                            ?>
                                                        </td>
                                                        <td><?php echo number_format($record->invprice, 2); ?></td>
                                                        <td><?php echo number_format($record->invtotal, 2); ?></td>
                                                        <td class="text-center">
                                                            <a class="btn btn-sm btn-info" href="<?php echo base_url().'stockEdit/'.$record->jvid.'/'.($record->typename == 'STOCK' ? '1' : '2'); ?>" title="Edit">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                    </div>

                                    <div class="tab-pane" id="activation">
                                        <div class="alert alert-info" role="alert">Activation Ticket</div>


                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Type</th>
                                                        <th>User</th>
                                                        <th>Start Date</th>
                                                        <th>End Date</th>
                                                        <th>Total Amount</th>
                                                        <th>Status</th>
                                                        <th>Created</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($activationList)) { foreach ($activationList as $ticket): ?>
                                                        <tr>
                                                            <td><?php echo $ticket->transid; ?></td>
                                                            <td><?php echo isset($acttype_label[$ticket->acttype]) ? $acttype_label[$ticket->acttype] : $ticket->acttype; ?></td>
                                                            <td><?php echo htmlspecialchars($ticket->username); ?></td>
                                                            <td><?php echo htmlspecialchars($ticket->srvdate); ?></td>
                                                            <td><?php echo htmlspecialchars($ticket->expdate); ?></td>
                                                            <td class="text-right"><?php echo number_format($ticket->totalamount, 2); ?></td>
                                                            <td><?php echo ($ticket->actstatus == 0 ? '<span class="badge badge-warning">Pending</span>' : ($ticket->actstatus == 1 ? '<span class="badge badge-success">Completed</span>' : '<span class="badge badge-danger">Cancelled</span>')); ?></td>
                                                            <td><?php echo htmlspecialchars($ticket->createdDtm); ?></td>
                                                            <td>
                                                                <a href="<?php echo base_url('Invoices/activationTicket_view/' . $ticket->transid); ?>" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> View</a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; } else { ?>
                                                        <tr><td colspan="9" class="text-center">No tickets found.</td></tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                    </div>

                                    <!-- Accounts Tab -->
                                    <div class="tab-pane" id="accounts">
                                        <div class="alert alert-info" role="alert">Accounts Transactions</div>
                                        <!-- Accounts content will be added later -->
                                        <div class="col-8 text-left">
                                            <div class="form-group">
                                                <a class="btn btn-primary" href="<?php echo base_url(); ?>Accounts/Jvs_controller/expenseAddNew/<?php echo $user; ?>"><i class="fas fa-plus"></i> Add Expense</a>
                                                <a class="btn btn-primary" href="<?php echo base_url(); ?>Accounts/Jvs_controller/salesAddNew/<?php echo $user; ?>"><i class="fas fa-plus"></i> Sales Entry</a>
                                                <a class="btn btn-primary" href="<?php echo base_url(); ?>Accounts/Jvs_controller/depositAddNew/<?php echo $user; ?>"><i class="fas fa-plus"></i> DEPOSIT</a>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table id="accounts" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Type</th>
                                                        <th>ID</th>
                                                        <th>Account Dr.</th>
                                                        <th>Account Cr.</th>
                                                        <th>Amount</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if(!empty($jvsList))
                                                    {
                                                        foreach($jvsList as $record)
                                                        {
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php 
                                                            if($record->typename == 'EXPENSES') {
                                                                echo "EXPENSE";
                                                            } elseif($record->typename == 'SALES') {
                                                                echo "SALES";
                                                            } else {
                                                                echo "DEPOSIT";
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?php echo $record->jvid; ?></td>
                                                        <td><?php echo $record->accnamedr; ?></td>
                                                        <td><?php echo $record->accnamecr; ?></td>
                                                        
                                                        <td><?php echo number_format($record->debit, 2); ?></td>
                                                        <td class="text-center">
                                                            <a class="btn btn-sm btn-info" href="<?php echo base_url().'stockEdit/'.$record->jvid.'/'.($record->typename == 'STOCK' ? '1' : '2'); ?>" title="Edit">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php
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
                        <?php } ?>
                        
                        <div class="card-footer">
                            <!-- Footer content if needed -->
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
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
        </div>
    </section>
</div>

<script src="<?php echo base_url(); ?>assets/js/editUser.js" type="text/javascript"></script>
<script>
$(document).ready(function () {
    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
});
</script>