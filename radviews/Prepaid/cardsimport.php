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
                        <i class="fas fa-file-alt"></i> Prepaid Users/Cards Data Import Form
                        <small> Format File Fields (username, password, expiration "YYYY-MM-DD" if empty or wrong format selected date will be applied)</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Import Cards</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($this->session->flashdata('success') == TRUE): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?php echo $this->session->flashdata('success'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">IMPORT USERS/PREPAID CARDS</h3>
                </div>
                <div class="card-body">
                    <form enctype="multipart/form-data" method="post" action="<?php echo base_url() ?>cardsimportReview/review" role="form">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="managername">Manager Name</label>
                                    <select class="form-control required" id="managername" name="managername">
                                        <option value="0">Select Manager</option>
                                        <option value="<?php echo $this->session->userdata ( 'name' ); ?>"><?php echo $this->session->userdata ( 'name' ); ?></option>
                                        <?php
                                            if(!empty($managername))
                                            {
                                                foreach ($managername as $rl)
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
                        
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="service">Packages</label>
                                    <select class="form-control required" id="service" name="service">
                                        <option value="0">Select Package</option>
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="usergroup">Users Group</label>
                                    <select class="form-control required" id="usergroup" name="usergroup">
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

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="expiration">Expiration</label>
                                    <input name="expiration" type="text" class="tgl form-control" data-date-format="yyyy-mm-dd" id="expiration" value="" size="10" maxlength="10" autocomplete="off" readonly>
                                </div>
                            </div>
                        </div>
                        <script type="text/javascript">
                            $(function () {
                                $('#expiration').datepicker({ dateFormat: 'yy-mm-dd' });
                            });
                        </script>                        

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="downlimit">Download Limit (MB)</label>
                                    <input name="downlimit" type="text" class="form-control" id="downlimit" value="0" maxlength="15">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="uplimit">Upload Limit (MB)</label>
                                    <input name="uplimit" type="text" class="form-control" id="uplimit" value="0" maxlength="15">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="comblimit">Total Limit (MB)</label>
                                    <input name="comblimit" type="text" class="form-control" id="comblimit" value="0" maxlength="15">
                                </div>
                            </div> 
                        </div>
                    
                        <div class="row">                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <small class="text-muted">Optional (Traffic Limits for upload and download during service period)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="uptimelimit">Online time limit</label>
                                    <input name="uptimelimit" type="text" class="form-control" id="uptimelimit" value="0" maxlength="15">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="timebaseonline">Duration</label>
                                    <select name="timebaseonline" class="form-control" id="timebaseonline">
                                        <OPTION SELECTED value=0>minute(s)</OPTION>
                                        <OPTION value=1>hour(s)</OPTION>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h4>Account Expiration Based On</h4>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="expiremode" id="expiremode1" value="0" onClick="setNDays()">
                                        <label class="custom-control-label" for="expiremode1">Defined by valid till</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="expiremode" id="expiremode2" value="1" checked onClick="setNDays()">
                                        <label class="custom-control-label" for="expiremode2">Calculated from card activation</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <label><small>Available time from card activation</small></label>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="expiretime">Unit</label>
                                    <input name="expiretime" type="text" class="form-control" id="expiretime" value="0" maxlength="15">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="timebaseexp">Duration</label>
                                    <select name="timebaseexp" class="form-control" id="timebaseexp">
                                        <OPTION SELECTED value=0>minute(s)</OPTION>
                                        <OPTION value=1>hour(s)</OPTION>
                                        <OPTION value=2>day(s)</OPTION>
                                        <OPTION value=3>month(s)</OPTION>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="simuse">Simultaneous use</label>
                                    <input name="simuse" type="text" class="form-control" id="simuse" value="1" maxlength="15">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="file">File Upload</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="file" id="file">
                                            <label class="custom-file-label" for="file">Choose file</label>
                                        </div>
                                    </div>
                                    <small class="text-muted">Only Excel/CSV File Import.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary" name="submit" value="submit">Upload</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <p class="text-muted">&copy; Pace Solutions</p>
                </div>
            </div>
        </div>
    </section>
</div>

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
                        $('#service').empty();

                        // For each chocie in the selected option
                        for (i = 0; i < packages.length; i++) {
                            // Output choice in the target field
                            $('#service').append("<option value=" + packages[i].radsrvid + ">" + packages[i].srvname + "</option>");
                        }
                    }
               });
            }
            else
            {
                alert("Operation Failed...")
            }
        });
        
        // Initialize custom file input
        $(document).on('change', '.custom-file-input', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    });
</script>
