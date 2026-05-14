<!-- This is inside recharge_form.php -->
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

if(!empty($getUserPackage)){
    $srvid = $getUserPackage->managersrvid;
    $srvname = $getUserPackage->managersrvname;
    $discount = $getUserPackage->discount;
    $adjustment = $getUserPackage->adjamount;
}else{
    $srvid = 0;
    $srvname = "";
    $discount = 0;
    $adjustment = 0;
}

$baseprice = element('baseprice', $packagePrice);
$saleprice = element('saleprice', $packagePrice);
$unitPrice = element('unitprice', $packagePrice);

$a = element('srv_date', $packagePrice);
$b = element('exp_date', $packagePrice);

$alldiscount = $managerInfo->perm_allowdiscount;

if (empty($rechargeInfo)) {
    $date1 = Date("Y-m-d");  // Service Date
    $date2 = Date("Y-m-t");  // Get Last date of month
    $date2 = date('Y-m-d', strtotime("+1 month", strtotime($date1))); // Add One Day to Recharge
    $daysforDisc = abs(strtotime($date2) - strtotime($date1));
    $years = floor($daysforDisc / (365 * 60 * 60 * 24));
    $months = floor(($daysforDisc - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
    $daysforDisc = floor(($daysforDisc - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

    $discount = ($discount * $daysforDisc) / 30;
    $adjustment = ($adjustment * $daysforDisc) / 30;

    $expdate1 = date('Y-m-d', strtotime("+0 month", strtotime($date1)));
} else {

    $inactiveDays = element('inactiveDays', $userInactiveDays);
    //echo $inactiveDays;
    //exit;
    if ($inactiveDays > 30) {
        $days = "+" . $inactiveDays . " day";
    } else {
        $days = "+0 day";
    }

    // Result are from 1st June Sin Khan and closed on 11/5/2021 so system calculate 55 days
    $currentsysdate = Date("Y-m-d");
    $expdate1 = $userInfo->expiration;
    $date1 = date('Y-m-d', strtotime("+0 day", strtotime($expdate1)));
    if ($date1 < $currentsysdate) {
        $date1 = date('Y-m-d', strtotime("+0 day", strtotime($currentsysdate)));
    }
    //$date2 = Date("Y-m-t", strtotime($date1));
    $date2 = date('Y-m-d', strtotime("+1 month", strtotime($date1)));  // Add one day to Recharge
}

if ($unitPrice == $saleprice and $alldiscount == 1) {
    $unitPrice = $unitPrice + $adjustment - $discount;
} else {
    $unitPrice = $unitPrice;
}
//$remarks = "Invoice Payment ".date('F',strtotime(Date("Y-m-d")));

?>
<script>
    //alert(<?php //echo $daysremaining 
            ?>);
</script>
                    

    <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="title">
                    <h5>Instant Invoice</h5>
                </div>
            </div>
            
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">

                    <div class="x_content">
                        
                        <br />
                        <?php $this->load->helper("form"); ?>
                        <form role="form" id="saverecharge" action="<?php echo base_url('Invoices/subscriberInvoicePost'); ?>" method="post">

                            <div class="box-body">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="username">User Name</label>
                                            <input type="label" class="form-control" id="user" placeholder="User Name" name="user" value="<?php echo $user; ?>" maxlength="50" readonly>
                                            <input type="hidden" value="<?php echo $user; ?>" name="user" id="user" />
                                        </div>
                                    </div>
                                    

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="service">Packages</label>
                                            <select class="form-control required" data-style="btn-outline-danger" id="service" name="service">
                                                <option data-subtext="French's" value="0">Select Package</option>
                                                <?php
                                                if (!empty($packages)) {
                                                    foreach ($packages as $rl) {
                                                ?>
                                                        <option value="<?php echo $rl->srvid ?>" 
                                                        <?php if ($rl->srvid == $srvid) {
                                                            echo "selected=selected";
                                                        } ?>><?php echo $rl->srvname . " (" . $rl->managername . ")"; ?></option>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <small class="form-text text-muted">Package change will be dynamically applied on current package expiration.</small>
                                        </div>
                                        
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="price">Cost Price (Rs.)</label>
                                            <input type="text" class="form-control required" id="price" value="<?php echo round($unitPrice, 0); ?>" name="price" maxlength="20" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="amount">Recharge (Rs.)</label>
                                            <input type="text" class="form-control required" id="amount" value="<?php echo round($unitPrice, 0); ?>" name="amount" maxlength="20" readonly autocomplete="off">
                                        </div>
                                    </div>
                                </div>


                                <?php
                                //$dt2 = new DateTime("+1 month");
                                //$dt = Date("Y-m-d");
                                //$dt2 = date('y-m-d', strtotime("+1 month", strtotime($dt)));
                                //$dt1 = new DateTime("+0 day");
                                //$dt2 = date("Y-m-t", strtotime($dt2));
                                $max_date = date('Y-m-d', strtotime("+1 month +1day", strtotime($expdate1)));
                                $min_date = date('Y-m-d', strtotime("+0 day", strtotime($expdate1)));
                                ?>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="srvdate">Renew Date (yy-mm-dd)</label>
                                            <input type="text" class="form-control required" id="srvdate" value="<?php echo $date1 ?>" name="srvdate" maxlength="16" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
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

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="walletid">Payment Wallet</label>
                                            <select class="form-control required" data-style="btn-outline-danger" id="walletid" name="walletid">
                                                <option data-subtext="Wallet" value="0">Select Wallet</option>
                                                <?php
                                                if (!empty($wallets)) {
                                                    foreach ($wallets as $rl) {
                                                ?>
                                                        <option value="<?php echo $rl->walletid ?>" 
                                                        ><?php echo $rl->walletname; ?></option>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="remarks">Remarks</label>
                                            <input type="text" class="form-control required" id="remarks" value="<?php echo "recharge by Manager " . $this->session->userdata('name'); ?>" name="remarks" maxlength="255" autocomplete="off">
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>

                            </div><!-- /.box-body -->

                            <div class="box-footer">
                                <input type="submit" class="btn btn-primary" value="Submit" id="Submit" />
                            </div>

                        </form>
                </div>
            </div>
            <?php $this->load->helper("form"); ?>
        </div>
    </div>


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

<script src="<?php echo base_url(); ?>assets/js/recharge.js" type="text/javascript"></script>

<!-- MODEL -->

<script>
    $(document).ready(function() {
        // Function to handle form submission
        function submitForm() {
            var formData = $("#saverecharge").serialize(); // Serialize form data

            $.ajax({
                url: "<?php echo base_url('Invoices/subscriberInvoicePost'); ?>",
                type: "POST",
                data: formData,
                success: function(response) {
                    console.log(response);

                    // Process success response
                    alert("Invoice Generated Successfully");
                    $('#actionModal').modal('hide'); // Close the modal on success
                    $("#saverecharge").trigger("reset");
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Process error response
                    alert("Error: " + textStatus);
                }
            });
        }

        // Attach submit event handler to your form
        $("#saverecharge").on('submit', function(e) {
            e.preventDefault(); // Prevent default submission
            submitForm(); // Call the function to submit form data via AJAX
        });

        // Optionally, handle button click if you specifically need it for submission
        $("#Submit").click(function() {
            submitForm(); // Submit form data via AJAX on button click
        });

        $('#service1').change(function(){
            var service_id = $(this).val();
            var srv_date = $('#srvdate').val();
            var exp_date = $('#expdate').val();

            $.ajax({
                    url: '<?php echo base_url() ?>invoices/getServicePrice/',
                    method: 'POST',
                    data: {service_id:service_id, srv_date:srv_date, exp_date:exp_date},
                    dataType: 'json',
                    success: function(response){
                        console.log(response);
                        var unitprice = response[0].unitprice;
                        var saleprice = response[0].saleprice;
                        $('#price').val(unitprice);
                        $('#amount').val(saleprice);
                    },
                    error: function(){
                        alert('Error retrieving data. Please try again.');
                    }
                });
        });

        $('#service, #expdate').change(function(){

            var service_id = $('#service').val();
            var exp_date = $('#expdate').val();
            var srv_date = $('#srvdate').val();
            var username = $('#user').val();

            if(service_id != 0)
            {
               $.ajax({
                    url: "<?php echo base_url() ?>invoices/getServicePrice/",
                    method:"POST",
                    data:{username:username, service_id:service_id, exp_date:exp_date, srv_date:srv_date},
                    dataType: 'json',
                    success:function(response)
                    {
                        //console.log(service_id+" - "+exp_date+" - "+srv_date);

                        console.log(response);
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