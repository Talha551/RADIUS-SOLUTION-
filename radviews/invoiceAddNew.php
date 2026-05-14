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
$radsrvid = $userInfo->srvid;
$radsrvname = $userInfo->srvname;

$srvid = $getUserPackage->managersrvid;
$srvname = $getUserPackage->managersrvname;
$discount = $getUserPackage->discount;
$adjustment = $getUserPackage->adjamount;

$baseprice = element('baseprice',$packagePrice);
$saleprice = element('saleprice',$packagePrice);
$unitPrice = element('unitprice',$packagePrice);

$a = element('srv_date',$packagePrice);
$b = element('exp_date',$packagePrice);

$alldiscount = $managerInfo->perm_allowdiscount;


$grace_days_old = isset($gracedays_old[0]->stgvalue) ? $gracedays_old[0]->stgvalue : 0;
$grace_days_new = isset($gracedays_new[0]->stgvalue) ? $gracedays_new[0]->stgvalue : 0;

if(empty($rechargeInfo)){
    $date1 = Date("Y-m-d");  // Service Date
    $date2 = Date("Y-m-t");  // Get Last date of month
    $date2 = date('Y-m-d', strtotime("+1 month", strtotime($date1))); // Add One Day to Recharge
    $daysforDisc = abs(strtotime($date2) - strtotime($date1));
    $years = floor($daysforDisc / (365*60*60*24));
    $months = floor(($daysforDisc - $years * 365*60*60*24) / (30*60*60*24));
    $daysforDisc = floor(($daysforDisc - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

    //$discount = ($discount*$daysforDisc)/30;
    //$adjustment = ($adjustment*$daysforDisc)/30;

    $expdate1 = date('Y-m-d', strtotime("+0 month", strtotime($date1)));
    
}
else
{

    $inactiveDays = element('inactiveDays',$userInactiveDays);
    //echo $inactiveDays;
    //exit;
    if($inactiveDays > 0)
    {
        //$days = "+".$inactiveDays. " day";
        $days = "+0 day";
    }
    else{
        $days = "+0 day";
    }

    $invExpdate1 = $rechargeInfo->expdate;
    $expdate1 = $userInfo->expiration;
    $currentsysdate = $rechargeInfo->currentdate;



    // Result are from 1st June Sin Khan and closed on 11/5/2021 so system calculate 55 days
    //$currentsysdate = Date("Y-m-d");
    //$currentsysdate = date('Y-m-d', strtotime("-".($grace_days_old + 1)." day", strtotime($invExpdate1)));
    //$expdate1 = $userInfo->expiration;
    $date1 = date('Y-m-d', strtotime($days, strtotime($invExpdate1)));

    if($rechargeInfo->invtype == 'Gracedays')
    {
        $date1 = date('Y-m-d', strtotime("+0 day", strtotime($currentsysdate)));

    }elseif($date1 < $currentsysdate)
    {
        $date1 = date('Y-m-d', strtotime("+0 day", strtotime($currentsysdate)));
    }

    //$date2 = Date("Y-m-t", strtotime($date1));
    $date2 = date('Y-m-d', strtotime("+1 month", strtotime($date1)));  // Add one day to Recharge
}

if($unitPrice == $saleprice){
    $unitPrice = $unitPrice + $adjustment - $discount;
}else{
    $unitPrice = $unitPrice;
}
//$remarks = "Invoice Payment ".date('F',strtotime(Date("Y-m-d")));

?>
<script>
    //alert(<?php //echo $daysremaining ?>);
</script>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-users"></i> New Transaction
                        <small>List Users/Recharge</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">New Transaction</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-6">
                    <!-- general form elements -->

                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Service Renewal New Invoice</h3>
                        </div><!-- /.card-header -->
                        <!-- form start -->
                        <?php $this->load->helper("form"); ?>
                        <form role="form" id="saverecharge" action="<?php echo base_url() ?>saverecharge" method="post" role="form">
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-8">                                
                                        <div class="form-group">
                                        <label for="username">User Name</label>
                                            <input type="label" class="form-control" id="user" placeholder="User Name" name="user" value="<?php echo $user; ?>" maxlength="50" readonly>
                                            <input type="hidden" value="<?php echo $user; ?>" name="user" id="user" />
                                        </div>
                                    </div>
                                </div>

                                <?php

                                    //$dt2 = new DateTime("+1 month");
                                    //$dt = Date("Y-m-d");
                                    //$dt2 = date('y-m-d', strtotime("+1 month", strtotime($dt)));
                                    //$dt1 = new DateTime("+0 day");
                                    //$dt2 = date("Y-m-t", strtotime($dt2));
                                    $max_date = date('Y-m-d', strtotime("+".$grace_days_new." day", strtotime($expdate1)));
                                    $min_date = date('Y-m-d', strtotime("+1 day", strtotime($expdate1)));
                                    $grace_date = date('Y-m-d', strtotime("+".$grace_days_old." day", strtotime($expdate1)));
                                ?>

                                <div class="row">
                                    <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="srvdate">Renew Date (yy-mm-dd)</label>
                                                <input type="text" class="form-control required" id="srvdate" value="<?php echo $date1 ?>" name="srvdate" maxlength="16" readonly>
                                            </div>
                                    </div>

                                    <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="expdate">Expiry Date (yy-mm-dd)</label>
                                                <input type="text" class="form-control required" id="expdate" value="<?php echo $date2 ?>" name="expdate" maxlength="16" readonly>
                                            </div>
                                    </div>

                                    <?php
                                        $dateleft = strtotime($date2);
                                        $daysremaining = date('t', $dateleft) - date('j', $dateleft);
                                    ?>

                                    <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="duedate">Due Date (yy-mm-dd)</label>
                                                <input type="text" class="form-control required" id="duedate" value="<?php echo $date1 ?>" name="duedate" maxlength="16" readonly>
                                            </div>
                                    </div>
                                </div>


                                
                                <div class="row">
                                    <div class="col-md-8">                                
                                        <div class="form-group">
                                        <label for="service">Service Name</label>
                                            <input type="label" class="form-control" id="srvname" placeholder="User Name" name="srvname" value="<?php echo $srvname; ?>" maxlength="50" disabled>
                                            <input type="hidden" value="<?php echo $srvid; ?>" name="srvid" id="srvid" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="price">Cost Price (Rs.)</label>
                                            <input type="text" class="form-control required" id="price" value="<?php echo round($unitPrice,0); ?>" name="price" maxlength="20" readonly>
                                        </div>
                                    </div>
                                
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="amount">Recharge (Rs.)</label>
                                            <input type="text" class="form-control required" id="amount" value="<?php echo round($unitPrice,0); ?>" name="amount" maxlength="20" readonly autocomplete="off">
                                        </div>
                                    </div>
                                </div>




                            <?php if($this->session->userdata('isaccountmanager') == 5){ ?>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="acccr">Sales Account</label>
                                            <select class="form-control required" id="acccr" name="acccr">
                                                
                                                <?php
                                                    if(!empty($creditaccount))
                                                    {
                                                        foreach ($creditaccount as $rl)
                                                        {
                                                            ?>
                                                            <option value="<?php echo $rl->acctid ?>" <?php if($rl->acctid == set_value('acctid')) {echo "selected=selected";} ?>><?php echo $rl->accname; echo " - (".$rl->grpname.")"; ?></option>
                                                            <?php
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="accdr">Customer/Payment Account</label>
                                            <select class="form-control required" id="accdr" name="accdr">
                                                
                                                <?php
                                                    if(!empty($debitaccount))
                                                    {
                                                        foreach ($debitaccount as $rl)
                                                        {
                                                            ?>
                                                            <option value="<?php echo $rl->acctid ?>" <?php if($rl->acctid == set_value('acctid')) {echo "selected=selected";} ?>><?php echo $rl->accname; echo " - (".$rl->grpname.")"; ?></option>
                                                            <?php
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>
                           

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="remarks">Remarks</label>
                                                <input type="text" class="form-control required" id="remarks" value="<?php echo set_value('remarks')." ".($isnew==1 ? "New Service Custom Billing Allowed" : "Renew Service"); ?>" name="remarks" maxlength="255" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                                <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                                <link rel="stylesheet" href="/resources/demos/style.css">
                                <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
                                <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

                                <script type="text/javascript">
                                    
                                    $(function() {
                                        $("<?php if($alldiscount == 1 || $isnew == 1) {echo "#expdate";} else { echo "#expdate1"; } ?>").datepicker({
                                            dateFormat: "yy-mm-dd",
                                            maxDate: '<?php echo $max_date ?>',
                                            minDate: '<?php echo $min_date ?>'
                                        });
                                    });

                                    $(function() {
                                        $("#duedate").datepicker({
                                            dateFormat: "yy-mm-dd",
                                            maxDate: '<?php echo $grace_date ?>',
                                            minDate: '<?php echo $min_date ?>'
                                        });
                                    });

                                    $(function() {
                                        $("#srvdate1").datepicker({
                                            dateFormat: "yy-mm-dd"
                                        });
                                    });
                                    
                                </script>

                                

                            </div><!-- /.card-body -->
        
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary" id="btn-submit">Submit</button>
                                <button type="reset" class="btn btn-default">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- right column: user invoices timeline -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title">User Invoice History</h3>
                        </div>
                        <div class="card-body" style="background: #f8f9fa;">
                            <div class="timeline">
                                <?php
                                $last_expdate = '';
                                if (!empty($userInvoices)) {
                                    foreach ($userInvoices as $invoice):
                                        if ($last_expdate != $invoice->expdate):
                                ?>
                                    <!-- Date label -->
                                    <div class="time-label">
                                        <span class="bg-primary"><?php echo date('d M, Y', strtotime($invoice->expdate)); ?></span>
                                    </div>
                                <?php
                                            $last_expdate = $invoice->expdate;
                                        endif;
                                ?>
                                <!-- Timeline item -->
                                <div>
                                    <i class="fas fa-file-invoice bg-blue"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i> <?php echo date('H:i', strtotime($invoice->createdDtm)); ?></span>
                                        <h3 class="timeline-header"><b><?php echo htmlspecialchars($invoice->srvname); ?></b></h3>
                                        <div class="timeline-body">
                                            <b>Type:</b> <?php echo htmlspecialchars($invoice->invtype); ?><br>
                                            <b>Amount:</b> <?php echo htmlspecialchars($invoice->amount); ?><br>
                                            <b>Remarks:</b> <?php echo htmlspecialchars($invoice->remarks); ?><br>
                                        </div>
                                        <div class="timeline-footer">
                                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#readMoreModal<?php echo $invoice->transid; ?>">Read more</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Read More Modal -->
                                <div class="modal fade" id="readMoreModal<?php echo $invoice->transid; ?>" tabindex="-1" role="dialog">
                                  <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title"><?php echo htmlspecialchars($invoice->srvname); ?> Details</h5>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                      </div>
                                      <div class="modal-body">
                                        <b>Service Date:</b> <?php echo htmlspecialchars($invoice->srvdate); ?><br>
                                        <b>Expiry Date:</b> <?php echo htmlspecialchars($invoice->expdate); ?><br>
                                        <b>Type:</b> <?php echo htmlspecialchars($invoice->invtype); ?><br>
                                        <b>Amount:</b> <?php echo htmlspecialchars($invoice->amount); ?><br>
                                        <b>Remarks:</b> <?php echo htmlspecialchars($invoice->remarks); ?><br>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <?php endforeach;
                                } else { ?>
                                    <div class="text-center">No invoices found for this user.</div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>    

        </div>

    </section>
    
</div>



<script src="<?php echo base_url(); ?>assets/js/recharge.js" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $("#saverecharge").submit(function (e) {
            $("#btn-submit").attr("disabled", true);
            return true;
        });
    });
</script>

<script>
    $(document).ready(function(){
        $('#service, #expdate').change(function(){
            var service_id = $('#srvid').val();
            var exp_date = $('#expdate').val();
            var srv_date = $('#srvdate').val();

            if(service_id != 0)
            {

               $.ajax({
                    url: "<?php echo base_url() ?>invoices/getServicePrice/",
                    method:"POST",
                    data:{service_id:service_id, exp_date:exp_date, srv_date:srv_date},
                    dataType: 'json',
                    success:function(response)
                    {
                        var unitprice = response[0].unitprice;
                        var saleprice = response[0].saleprice;

                        $('#price').val(unitprice);
                        $('#amount').val(saleprice);

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