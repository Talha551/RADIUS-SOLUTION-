<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-users"></i> Manager Portal Group Mapping
                        <small>Add / Edit User</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>managerGroup">Manager Groups</a></li>
                        <li class="breadcrumb-item active">Add New Group</li>
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
                            <h3 class="card-title">Portal Mapping Details</h3>
                        </div><!-- /.card-header -->
                        
                        <!-- form start -->
                        <?php $this->load->helper("form"); ?>
                        <form role="form" id="managerGroupSave" action="<?php echo base_url() ?>managerGroupSave" method="post" role="form">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="shortname">Nas List</label>
                                            <select class="form-control required" id="shortname" name="shortname">
                                                <option value="0">Select NAS</option>
                                                <?php
                                                    if(!empty($nasname))
                                                    {
                                                        foreach ($nasname as $rl)
                                                        {
                                                            ?>
                                                            <option value="<?php echo $rl->shortname ?>" <?php if($rl->shortname == set_value('shortname')) {echo "selected=selected";} ?>><?php echo $rl->shortname."-".$rl->nasname ?></option>
                                                            <?php
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- <script>
                                    document.addEventListener('DOMContentLoaded', e => {
                                        $('#grpname').autocomplete()
                                    }, false);
                                </script> -->

                                <div class="row">
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="desc">Description</label>
                                            <input type="text" class="form-control required" placeholder="Type any details" value="<?php echo set_value('desc'); ?>" id="desc" name="desc" maxlength="128" autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="managername">Manager Name</label>
                                            <select class="form-control required" id="managername" name="managername">
                                                <option value="0">Select Manager</option>
                                                <option value="default">All Managers</option>
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

<script src="<?php echo base_url(); ?>assets/js/usersAddNew.js" type="text/javascript"></script>