<?php
$this->load->helper('array');

$user = $userInfo->username;
$password = $userInfo->password;

$fname = $userInfo->firstname;
$lname = $userInfo->lastname;
$address = $userInfo->address;
$mobile = $userInfo->mobile;
$email = $userInfo->email;
$cnic = $userInfo->taxid;
$srvid = $userInfo->srvid;
$srvname = $userInfo->srvname;

$packagePrice = element('unitprice',$packagePrice);

$alldiscount = $managerInfo->perm_allowdiscount;

if(empty($rechargeInfo)){
    $date1 = Date("Y-m-d");  // Service Date
    $date2 = Date("Y-m-t");  // Get Last date of month
}
else
{
    $expdate1 = $rechargeInfo->expdate;
    $date1 = date('Y-m-d', strtotime("+1 day", strtotime($expdate1)));;
    $date2 = Date("Y-m-t", strtotime($date1));
}

?>
<script>
    //alert(<?php //echo $daysremaining ?>);
</script>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> New Transaction
        <small>List Users/Recharge</small>
      </h1>
    </section>
    
    <section class="content">

        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
                <!-- general form elements -->

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Recharge User</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <?php $this->load->helper("form"); ?>
                    <form role="form" id="saverecharge" action="<?php echo base_url() ?>saverecharge" method="post" role="form">
                        <div class="box-body">

                            <div class="row">
                                <div class="col-md-6">                                
                                    <div class="form-group">
                                    <label for="username">User Name</label>
                                        <input type="label" class="form-control" id="user" placeholder="User Name" name="user" value="<?php echo $user; ?>" maxlength="50" readonly>
                                        <input type="hidden" value="<?php echo $user; ?>" name="user" id="user" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">                                
                                    <div class="form-group">
                                    <label for="service">Packages</label>
                                        <input type="label" class="form-control" id="srvname" placeholder="User Name" name="srvname" value="<?php echo $srvname; ?>" maxlength="50" disabled>
                                        <input type="hidden" value="<?php echo $srvid; ?>" name="srvid" id="srvid" />
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="service">Packages</label>
                                        <select class="form-control required" id="service" name="service">
                                            <option value="0">Select Package</option>
                                            <?php
                                                if(!empty($packages))
                                                {
                                                    foreach ($packages as $rl)
                                                    {
                                                        ?>
                                                            <option value="<?php echo $rl->srvid ?>" <?php if($rl->srvid == set_value('srvid')) {echo "selected=selected";} ?>><?php echo $rl->srvname ?></option>
                                                        <?php
                                                        $unitPrice = $rl->unitprice;

                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="price">Cost Price (Rs.)</label>
                                        <input type="text" class="form-control required" id="price" value="<?php echo $packagePrice; ?>" name="price" maxlength="20" readonly>
                                    </div>
                                </div>
                            </div>

                            <?php
                                //$dt2 = new DateTime("+1 month");
                                //$dt2 = date("Y-m-t", strtotime($dt2));
                                //$date = $dt2->format("Y-m-d");

                            ?>

                            <div class="row">
                                <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="srvdate">Renew Date (yy-mm-dd)</label>
                                            <input type="text" class="form-control required" id="srvdate" value="<?php echo $date1 ?>" name="srvdate" maxlength="16" readonly>
                                        </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="expdate">Expiry Date (yy-mm-dd)</label>
                                            <input type="text" class="form-control required" id="expdate" value="<?php echo $date2 ?>" name="expdate" maxlength="16" readonly>
                                        </div>
                                </div>
                            </div>

                            <?php
                                $dateleft = strtotime($date2);
                                $daysremaining = date('t', $dateleft) - date('j', $dateleft);
                            ?>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="amount">Recharge (Rs.)</label>
                                        <input type="text" class="form-control required" id="amount" value="<?php echo $packagePrice; ?>" name="amount" maxlength="20" <?php if($alldiscount == 0) {echo "readonly";} ?>>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount">Remarks</label>
                                        <input type="text" class="form-control required" id="remarks" value="<?php echo set_value('remarks'); ?>" name="remarks" maxlength="20">
                                    </div>
                                </div>
                            </div>

                            <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                            <link rel="stylesheet" href="/resources/demos/style.css">
                            <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
                            <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

                            <script type="text/javascript">
                                
                                $(function() {
                                    $("#expdate").datepicker({
                                        dateFormat: "yy-mm-dd"
                                    });
                                });

                                $(function() {
                                    $("#srvdate1").datepicker({
                                        dateFormat: "yy-mm-dd"
                                    });
                                });
                                
                            </script>

                            

                        </div><!-- /.box-body -->
    
                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" value="Submit" />
                            <input type="reset" class="btn btn-default" value="Reset" />
                        </div>
                    </form>
                </div>
            </div>
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
<script src="<?php echo base_url(); ?>assets/js/recharge.js" type="text/javascript"></script>

<script>
    $(document).ready(function(){
        $('#service, #expdate').change(function(){
            var service_id = $('#service').val();
            var exp_date = $('#expdate').val();
            var srv_date = $('#srvdate').val();
            if(service_id != '')
            {
               $.ajax({
                    url:"<?php echo base_url() ?>invoices/getServicePrice/",
                    method:"POST",
                    data:{service_id:service_id, exp_date:exp_date, srv_date:srv_date},
                    dataType: 'json',
                    success:function(response)
                    {
                        var unitprice = response[0].unitprice;
                        var unitprice2 = response[0].unitprice2;
                        $('#price').val(unitprice);
                        $('#amount').val(unitprice2);
                    }
               });

            }
            else
            {
                alert("Operation Failed...")
            }
        });
    });
</script>