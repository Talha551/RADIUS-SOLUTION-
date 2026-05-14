<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css">
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-users"></i> Generate Bulk Users
                        <small>New Batch</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>serieslist">Series List</a></li>
                        <li class="breadcrumb-item active">Generate Cards</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-8">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Profile Details</h3>
                        </div><!-- /.card-header -->
                        <!-- form start -->
                        <?php $this->load->helper("form"); ?>
                        <form role="form" id="prepaidCreateUsersBatch" action="<?php echo base_url() ?>prepaidCreateUsersBatch" method="post" role="form">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped table-condensed">
                                            <tbody>
                                            <tr>
                                            <td class="td1">Card type</td>
                                                <td class="b1"><input name="cardtype" type="radio" value="0" checked onClick="setCardType()">
                                                    Classic prepaid<br>
                                                    <input name="cardtype" type="radio" value="1"  onClick="setCardType()">
                                                    Refill<br>
                                                </td>
                                            </tr>
                                            <tr>
                                            <td class="td1"><span style="color: #FF0000;">*</span> Quantity</td>
                                            <td class="b1"><input name="quantity" type="text" class="form-control form-control-sm d-inline-block" style="width: auto;" id="quantity" value="" size="10" maxlength="10">
                                                cards
                                            </td>
                                            </tr>
                                            <tr>
                                            <!-- <td class="td1"><span style="color: #FF0000;">*</span> Gross card value</td> -->
                                            <td class="b1"><table border="0" cellpadding="4" cellspacing="0" class="bg-light">
                                            <tr>
                                            <td nowrap class="normal"><strong>
                                            <input name="cardvalue" type="hidden" class="form-control form-control-sm" id="cardvalue" value="0" size="20" maxlength="20" readonly>
                                                </strong></td>
                                            </tr>
                                            </table>
                                            </td>
                                            </tr>
                                            <tr>
                                            <td class="td1"><span style="color: #FF0000;">*</span> Valid till</td>
                                                <td class="b1"><input name="expiration" type="text" class="tgl form-control form-control-sm d-inline-block" style="width: auto;" data-date-format="yyyy-mm-dd" id="expiration" value="" size="10" maxlength="10" autocomplete="off" readonly>
                                            </td>
                                            </tr>
                                            <tr>
                                            <td class="td1">Prefix</td>
                                            <td class="b1"><input name="prefix" type="text" class="form-control form-control-sm d-inline-block" style="width: auto;" id="prefix" value="" size="10" maxlength="10">
                                            </td>
                                            </tr>
                                            <tr>
                                            <td class="td1">PIN length</td>
                                            <td class="b1">
                                                <select name="pinlength" class="form-control form-control-sm d-inline-block" style="width: auto;" id="pinlength">
                                                    <OPTION value=4>4</OPTION>
                                                    <OPTION value=5>5</OPTION>
                                                    <OPTION value=6>6</OPTION>
                                                    <OPTION value=7>7</OPTION>
                                                    <OPTION SELECTED value=8>8</OPTION>
                                                    <OPTION value=9>9</OPTION>
                                                    <OPTION value=10>10</OPTION>
                                                    <OPTION value=11>11</OPTION>
                                                    <OPTION value=12>12</OPTION>
                                                    <OPTION value=13>13</OPTION>
                                                    <OPTION value=14>14</OPTION>
                                                    <OPTION value=15>15</OPTION>
                                                    <OPTION value=16>16</OPTION>
                                                </select>
                                            </td>
                                            </tr>
                                            <tr>
                                            <td class="td1">Password length</td>
                                            <td class="b1">
                                                <select name="pswlength" class="form-control form-control-sm d-inline-block" style="width: auto;" id="pswlength">
                                                    <OPTION value=0>0</OPTION>
                                                    <OPTION value=1>1</OPTION>
                                                    <OPTION value=2>2</OPTION>
                                                    <OPTION value=3>3</OPTION>
                                                    <OPTION SELECTED value=4>4</OPTION>
                                                    <OPTION value=5>5</OPTION>
                                                    <OPTION value=6>6</OPTION>
                                                    <OPTION value=7>7</OPTION>
                                                    <OPTION value=8>8</OPTION>
                                                </select>
                                            </td>
                                            </tr>
                                            <tr>
                                            <td class="td1">SMS verification required</td>
                                            <td class="b1">
                                                <div class="custom-control custom-checkbox">
                                                    <input name="reqverify" type="checkbox" class="custom-control-input" id="reqverify" value="1">
                                                    <label class="custom-control-label" for="reqverify"></label>
                                                </div>
                                            </td>
                                            </tr>
                                            <tr>
                                            <td class="td1">Associated Paramters</td>
                                            <td class="b1">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="managername">Manager Name</label>
                                                            <select class="form-control form-control-sm required" id="managername" name="managername">
                                                                <option value="0">Select Manager</option>
                                                                <option value="<?php echo $this->session->userdata ( 'name' ); ?>"><?php echo $this->session->userdata ( 'name' ); ?></option>
                                                                <?php
                                                                    if(!empty($managerList))
                                                                    {
                                                                        foreach ($managerList as $rl)
                                                                        {
                                                                            ?>
                                                                            <option value="<?php echo $rl->managername ?>" <?php if($rl->managername == set_value('managername')) {echo "selected=selected";} ?>><?php echo $rl->managername ?></option>
                                                                            <?php
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
                                                            <label for="usergroup">Users Group</label>
                                                            <select class="form-control form-control-sm required" id="usergroup" name="usergroup">
                                                                <option value="0">Select Group</option>
                                                                <?php
                                                                    if(!empty($usersGroup))
                                                                    {
                                                                        foreach ($usersGroup as $rl)
                                                                        {
                                                                            ?>
                                                                            <option value="<?php echo $rl->groupid ?>" <?php if($rl->groupid == set_value('groupid')) {echo "selected=selected";} ?>><?php echo $rl->groupname ?></option>
                                                                            <?php
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
                                                            <label for="packages">Associated Prepaid Services</label>
                                                            <select class="form-control form-control-sm required" id="packages" name="packages">
                                                                <option value="0">Select Service</option>
                                                                <?php
                                                                    if(!empty($packages))
                                                                    {
                                                                        foreach ($packages as $rl)
                                                                        {
                                                                            ?>
                                                                            <option value="<?php echo $rl->radsrvid ?>" <?php if($rl->radsrvid == set_value('radsrvid')) {echo "selected=selected";} ?>><?php echo $rl->srvname ?></option>
                                                                            <?php
                                                                        }
                                                                    }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            </tr>
                                            <tr>
                                            <td class="td1">Card Value</td>
                                            <td class="b1"><input name="price" type="text" class="form-control form-control-sm d-inline-block" style="width: auto;" id="price" value="0" maxlength="15" size="15">
                                            </td>
                                            </tr>
                                            <tr>
                                            <td class="td1">Download limit</td>
                                            <td class="b1"><input name="downlimit" type="text" class="form-control form-control-sm d-inline-block" style="width: auto;" id="downlimit" value="0" maxlength="15" size="15">
                                                MB<strong></strong>
                                            </td>
                                            </tr>
                                            <tr>
                                            <td class="td1">Upload limit</td>
                                            <td class="b1"><input name="uplimit" type="text" class="form-control form-control-sm d-inline-block" style="width: auto;" id="uplimit" value="0" maxlength="15" size="15">
                                                MB
                                            </td>
                                            </tr>
                                            <tr>
                                            <td class="td1">Total limit</td>
                                            <td class="b1"><input name="comblimit" type="text" class="form-control form-control-sm d-inline-block" style="width: auto;" id="comblimit" value="0" maxlength="15" size="15">
                                            MB
                                            </td>
                                            </tr>
                                            <tr>
                                            <td class="td1">Online time limit</td>
                                            <td class="b1"><input name="uptimelimit" type="text" class="form-control form-control-sm d-inline-block" style="width: auto;" id="uptimelimit" value="0" maxlength="15" size="15">
                                                <select name="timebaseonline" class="form-control form-control-sm d-inline-block" style="width: auto;" id="timebaseonline">
                                                    <OPTION SELECTED value=0>minute(s)</OPTION>
                                                    <OPTION value=1>hour(s)</OPTION>
                                                </select>
                                            </td>
                                            </tr>
                                            <tr>
                                            <td class="td1">Expiration</td>
                                                <td class="b1">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" name="expiremode" id="expiremode1" value="0" onClick="setNDays()">
                                                        <label class="custom-control-label" for="expiremode1">Defined by valid till</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" name="expiremode" id="expiremode2" value="1" checked onClick="setNDays()">
                                                        <label class="custom-control-label" for="expiremode2">Calculated from card activation</label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                            <td class="td1">Available time from card activation</td>
                                            <td class="b1">
                                                <input name="expiretime" type="text" class="form-control form-control-sm d-inline-block" style="width: auto;" id="expiretime" value="0" maxlength="15" size="15">
                                                <select name="timebaseexp" class="form-control form-control-sm d-inline-block" style="width: auto;" id="timebaseexp">
                                                    <OPTION SELECTED value=0>minute(s)</OPTION>
                                                    <OPTION value=1>hour(s)</OPTION>
                                                    <OPTION value=2>day(s)</OPTION>
                                                    <OPTION value=3>month(s)</OPTION>
                                                </select> <strong></strong>
                                            </td>
                                            </tr>
                                            <tr>
                                            <td class="td1">Simultaneous use</td>
                                            <td class="b1"><input name="simuse" type="text" class="form-control form-control-sm d-inline-block" style="width: auto;" id="simuse" value="1" maxlength="15" size="15">
                                            </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div><!-- /.card-body -->
                            
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="reset" class="btn btn-default">Reset</button>
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

<script type="text/javascript">
    $(function () {
        $('#expiration').datepicker({ dateFormat: 'yy-mm-dd' });
    });
</script>
<script src="<?php echo base_url(); ?>assets/js/usersAddNew.js" type="text/javascript"></script>

<script>
    $(document).ready(function(){
        $('#managername').change(function(){
            var managername = $('#managername').val();

            if(managername != 0)
            {
               $.ajax({
                    url: "<?php echo base_url() ?>Services_controller/getManagerPackages/",
                    method:"POST",
                    data:{managername:managername},
                    dataType: 'json',
                    success:function(response)
                    {
                        var packages = response;
                        console.log(packages);

                        // Empty the target field
                        $('#packages').empty();

                        // For each chocie in the selected option
                        for (i = 0; i < packages.length; i++) {
                            // Output choice in the target field
                            $('#packages').append("<option value=" + packages[i].radsrvid + ">" + packages[i].srvname + "</option>");
                        }
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

<script type="text/javascript">
    $(function () {
        $('.tgl').datepicker();
    });
</script>