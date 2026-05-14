<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url');
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-user-shield"></i> Wireguard VPN Users
                        <small>Manage Wireguard VPN users</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Wireguard VPN Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-6"></div>
                <div class="col-md-6 text-right">
                    <a class="btn btn-primary" href="<?php echo base_url('wireguardvpn/add'); ?>"><i class="fas fa-plus"></i> Add New User</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
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
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Wireguard VPN Users List</h3>
                            <div class="card-tools">
                                <form action="<?php echo base_url('wireguardvpn'); ?>" method="GET" id="searchForm">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="searchText" value="<?php echo isset($searchText) ? $searchText : ''; ?>" class="form-control" placeholder="Search by username, IP, or server"/>
                                        <div class="input-group-append">
                                            <button class="btn btn-default" type="submit">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table id="usersTable" class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>IP Address</th>
                                    <th>Server</th>
                                    <th>Port</th>
                                        <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($users)): ?>
                                    <?php foreach($users as $user): ?>
                                    <tr>
                                        <td><?php echo $user->username; ?></td>
                                        <td><?php echo $user->ipaddress; ?></td>
                                        <td><?php echo $user->serveripaddress; ?></td>
                                        <td><?php echo $user->listenport; ?></td>
                                            <td class="text-center">
                                                <a href="<?php echo base_url('wireguardvpn/view/'.$user->username); ?>" class="btn btn-info btn-sm" title="View">
                                                    <i class="fa fa-eye"></i>
                                            </a>
                                                <a href="<?php echo base_url('wireguardvpn/delete/'.$user->username); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No users found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
        responsive: true,
        autoWidth: false
    });
});
</script> 
</script> 