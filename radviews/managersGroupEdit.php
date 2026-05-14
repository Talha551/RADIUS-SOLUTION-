<?php
$grpid = $groupInfo->grpid;
$grpname = $groupInfo->grpname;
$usergrpid = $groupInfo->usergrpid;
$usergroupname = $groupInfo->usergroupname;
$desc = $groupInfo->desc;
$managername = $groupInfo->managername;

//echo $stgtype;
//exit;

?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-users"></i> Manager Group Edit
                        <small>Add / Edit User</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>managerGroup">Manager Groups</a></li>
                        <li class="breadcrumb-item active">Edit Group</li>
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
                            <h3 class="card-title">Edit Settings Details</h3>
                        </div><!-- /.card-header -->
                        
                        <!-- form start -->
                        <form role="form" action="<?php echo base_url() ?>managerGroupUpdate" method="post" id="managerGroupUpdate" role="form">
                            <div class="card-body">
                                <div class="row">
                                    <input type="hidden" value="<?php echo $grpid; ?>" name="grpid" id="grpid" />
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="grpname">Group Name</label>
                                            <input type="text" class="form-control" id="grpname" placeholder="Group Name" name="grpname" value="<?php echo $grpname; ?>" maxlength="128" disabled>
                                            <input type="hidden" value="<?php echo $grpname; ?>" name="grpname" id="grpname" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="desc">Description</label>
                                            <input type="text" class="form-control" id="desc" placeholder="Description" name="desc" value="<?php echo $desc; ?>" maxlength="128">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="usersGroup">Users Group</label>
                                            <select class="form-control required" id="usersGroup" name="usersGroup">
                                                <option value="0">Select Manager</option>
                                                <?php
                                                    if(!empty($usersGroup))
                                                    {
                                                        foreach ($usersGroup as $rl)
                                                        {
                                                            ?>
                                                                <option value="<?php echo $rl->groupid; ?>" <?php if($rl->groupid == $usergrpid) {echo "selected=selected";} ?>><?php echo $rl->groupname ?></option>
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
                                            <label for="manager">Manager</label>
                                            <select class="form-control required" id="manager" name="manager">
                                                <option value="0">Select Manager</option>
                                                <?php
                                                    if(!empty($managerList))
                                                    {
                                                        foreach ($managerList as $rl)
                                                        {
                                                            ?>
                                                                <option value="<?php echo $rl->managername; ?>" <?php if($rl->managername == $managername) {echo "selected=selected";} ?>><?php echo $rl->managername ?></option>
                                                            <?php
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
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

<script src="<?php echo base_url(); ?>assets/js/editUser.js" type="text/javascript"></script>