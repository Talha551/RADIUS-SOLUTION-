<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> Group Message
        <small>Reports / Group SMS</small>
      </h1>
    </section>
    
    <section class="content">
    
        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
              <!-- general form elements -->
                
                
                
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Group SMS Details</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <?php $this->load->helper("form"); ?>

                    <form role="form" id="sendMsgNow" action="<?php echo base_url() ?>sendGroupMsgNow" method="post" role="form">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <select name="type" class="selectpicker pull-left" style="width: 150px; height: 30px;">
                                            <option value="0" selected=selected>GROUP</option>
                                            <option value="1">USER</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="managername">Group</label>
                                        <select class="form-control required" id="managername" name="managername">
                                            <option value="0">Select User Group</option>
                                            <?php
                                                if(!empty($getManagersList))
                                                {
                                                    foreach ($getManagersList as $rl)
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
                                        <label for="title">Title</label>
                                        <input type="text" class="form-control required" id="title" value="<?php echo set_value('title'); ?>" name="title" maxlength="30">
                                        <h5>Title: e.g. INFO, WARNNING, BILLING, SERVICES, SYSTEM etc.</h5>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="subject">Mobile (For single SMS)</label>
                                        <input type="text" class="form-control required" id="subject" value="<?php echo set_value('subject'); ?>" name="subject" maxlength="30">
                                        <h5>Single Message type Mobile Number format e.g. 923001234567</h5>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="txtmsg">Message</label>
                                        <textarea class="form-control required" id="txtmsg" value="<?php echo set_value('txtmsg'); ?>" name="txtmsg" maxlength="250" rows="3"></textarea>
                                        <h5>Group Message tags add #username (Username) #name (Name) #payinfo (Payment Info) fields to include user details.</h5>
                                    </div>
                                </div>
                                
                            </div>

                        </div><!-- /.box-body -->
    
                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" value="Send SMS" id="btn-submit" />
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
<script src="<?php echo base_url(); ?>assets/js/usersAddNew.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#saveNewUser").submit(function (e) {
            $("#btn-submit").attr("disabled", true);
            return true;
        });
    });
</script>