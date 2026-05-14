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
}
$alldiscount = $managerInfo->perm_allowdiscount;


?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> Documents Management
        <small>User CNIC / Edit User</small>
      </h1>
    </section>
    
    <section class="content">
    
        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
              <!-- general form elements -->
                
                
                
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Upload User Documents</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    
                    <form role="form" action="<?php echo base_url() ?>updateUser" method="post" id="updateUser" role="form">
                    
                        <div class="box-body">

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

                            </form>

                            <h3 class="box-title">User Settings</h3>

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
                                                <input type="text" class="form-control" id="discount" placeholder="Discount" name="discount" value="<?php echo $discount; ?>" maxlength="16" <?php if($alldiscount == 0) {echo "readonly";} ?> autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-2">                                
                                            <div class="form-group">
                                                <label for="adjamount">Adjustment</label>
                                                <input type="text" class="form-control" id="adjamount" placeholder="Adjustment" name="adjamount" value="<?php echo $adjamount; ?>" maxlength="16" <?php if($alldiscount == 0) {echo "readonly";} ?> autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-14">
                                        <div class="form-group">
                                            <input type="submit" value="Update Data" />
                                        <div>
                                    </div>

                                </form>

                            </h3>

                        <h3 class="box-title">Upload CNIC</h3>

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
                                        <input type="file" id="cnic_image1" name="cnic_image1" size="33" />
                                    </div>
                                    </div>


                                    <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cnic_image2"><?php echo $cnic_file2 ?></label>
                                        <input type="file" id="cnic_image2" name="cnic_image2" size="33" />
                                    </div>
                                    </div>
                                    

                                    <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="submit" value="Upload Images" />
                                    <div>
                                    </div>

                                </div>
                            </form>

                        <h4>Already Uploaed Images</h4>
                        <a href="http://login.pace-tel.com/uploads/<?php echo $cnic_file1 ?>" target="_blank">
                        <img src="http://login.pace-tel.com/uploads/<?php echo $cnic_file1 ?>" class="img-thumbnail" alt="Not Uploaded" width="80" height="25">
                        </a>
                        <a href="http://login.pace-tel.com/uploads/<?php echo $cnic_file2 ?>" target="_blank">
                        <img src="http://login.pace-tel.com/uploads/<?php echo $cnic_file2 ?>" class="img-thumbnail" alt="Not Uploaded" width="80" height="25">
                        </a>
                        <h6>Click to Enlarge</h6>


                        </h3>

                </div>

                </div>
            </div><!-- /.box-body -->

            </div><!-- /.box-header -->

            <div class="col-md-4">
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
    </section>
</div>

<script src="<?php echo base_url(); ?>assets/js/editUser.js" type="text/javascript"></script>