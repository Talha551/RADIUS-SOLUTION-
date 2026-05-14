<?php

//echo $this->ismaster;
//exit;

?>
<style>
    .username {
        background-color: white;
        border-radius: 0px;
        border: 0px solid #21AD73;
        padding: 5px;
    }
    
    /* Added CSS for better spacing */
    .content-wrapper {
        padding-bottom: 20px;
    }
    
    .card {
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
        border-radius: 3px;
        background: #ffffff;
        border-top: 3px solid #d2d6de;
        width: 100%;
    }
    
    /* Add card styling to content section */
    section.content {
        padding-top: 15px;
        padding-bottom: 15px;
    }
    
    section.content > .container-fluid,
    section.content > .row {
        background: #ffffff;
        border-radius: 3px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
        padding: 15px;
        margin-bottom: 20px;
        border-top: 3px solid #3c8dbc;
    }
    
    /* Make content responsive */
    @media (max-width: 767px) {
        .content-wrapper {
            padding: 10px;
        }
        
        .card {
            overflow-x: auto;
        }
        
        .table-responsive {
            border: 0;
        }
    }
    
    .card-header {
        padding: 15px;
        border-bottom: 1px solid #f4f4f4;
    }
    
    .row {
        margin-bottom: 15px;
    }
    
    .btn-group {
        margin-right: 5px;
    }
    
    .btn {
        margin-right: 5px;
    }
    
    .navbar {
        margin-bottom: 15px;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .table {
        margin-top: 15px;
        width: 100%;
        max-width: 100%;
        margin-bottom: 20px;
    }
    
    .table-responsive {
        min-height: .01%;
        overflow-x: auto;
    }
    
    .pagination {
        margin-top: 10px;
    }
    
    /* Improve dropdown button appearance */
    .dropdown-toggle {
        margin-left: 0;
    }
    
    /* Fix for the button group spacing */
    .btn-group .btn {
        margin-right: 0;
    }
    
    /* Improve form spacing */
    .form-inline {
        margin-bottom: 15px;
    }
    
    /* Add space between navbar and content */
    .navbar-brand {
        margin-right: 15px;
    }
    
    /* Fix for the Split button */
    .btn-group button {
        border-radius: 3px;
    }
    
    /* Fix for the dropdown menu */
    .dropdown-menu {
        border-color: #ddd;
        border-radius: 3px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
    }
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-users"></i> User Bulk Management
                        <small>Add, Edit, Delete</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">User Bulk Management</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Download section -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Download Customers List</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <a class="btn btn-info" href="<?php echo base_url(); ?>customerlist_csv/1/2"><i class="fas fa-download"></i> All List</a>
                        <a class="btn btn-success" href="<?php echo base_url(); ?>customerlist_csv/1/1"><i class="fas fa-download"></i> Active</a>
                        <a class="btn btn-danger" href="<?php echo base_url(); ?>customerlist_csv/1/0"><i class="fas fa-download"></i> Expired</a>
                        <a class="btn btn-warning" href="<?php echo base_url(); ?>customerlist_csv/0/2"><i class="fas fa-download"></i> Blocked</a>
                    </div>
                </div>
            </div>

            <!-- Alerts section -->
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $this->load->helper('form');
                    $error = $this->session->flashdata('error');
                    if ($error) {
                    ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                    <?php } ?>
                    <?php
                    $success = $this->session->flashdata('success');
                    if ($success) {
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

            <!-- Users list section -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bulk User Management</h3>
                    <div class="card-tools">
                        <form class="form-inline" action="<?php echo base_url() ?>userQuickEdit" method="POST" id="searchList">
                            <div class="input-group">
                                <select name="searchText1" class="form-control mr-2">
                                    <option value="0">Active Users</option>
                                    <option value="1">Paid</option>
                                    <option value="2">Expired</option>
                                    <option value="3">Blocked</option>
                                    <option value="4">All</option>
                                    <option value="5">Online</option>
                                    <option value="6">Offline-Active</option>
                                    <option value="7">Offline-Inactive</option>
                                </select>
                                <input type="text" name="searchText" class="form-control" placeholder="Search">
                                <div class="input-group-append">
                                    <button class="btn btn-default" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-custom" id="usersTable">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th><input type="checkbox" id="chkSelectAll" /></th>
                                    <th>User Name</th>
                                    <th>Name</th>
                                    <?php
                                        $managername = $this->session->userdata('name');
                                        if ($this->ismaster > 0 || $managername == 'admin') {
                                            echo "<th>Owner</th>";
                                        };
                                    ?>
                                    <th>Service</th>
                                    <th>PayID</th>
                                    <th>Status</th>
                                    <th>Expiration</th>
                                    <th>Created On</th>                                    
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($userRecords)) {
                                    $row_count = 1;
                                    foreach ($userRecords as $record) {
                                ?>
                                        <tr class="row-select" id="row_<?php echo $record->username; ?>">
                                            <td class="sno" <?php
                                                            $curDate = strtotime(date("d-m-Y"));
                                                            $expDate = strtotime($record->expiration);
                                                            if ($curDate >= $expDate)
                                                                echo 'style="color: red;"';
                                                            ?>><?php //echo $record->serial_number;
                                                echo $row_count; ?>.</td>
                                            <td scope="row">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="0" />
                                                </div>
                                            </td>
                                            <td class="username" id="<?php echo $record->username; ?>"><?php echo $record->username; ?></td>
                                            <td <?php
                                                $curDate = strtotime(date("d-m-Y"));
                                                $expDate = strtotime($record->expiration);
                                                if ($curDate >= $expDate)
                                                    echo 'style="color: red;"';
                                                ?>><?php echo $record->firstname . " " . $record->lastname ?></td>
                                            <?php
                                            $managername = $this->session->userdata('name');
                                            if ($this->ismaster > 0 || $managername == 'admin') {
                                                echo "<td";
                                                if ($curDate >= $expDate) {
                                                    echo ' style="color: red;">';
                                                } else {
                                                    echo ">";
                                                };
                                                echo $record->owner;
                                                echo "</td>";
                                            }
                                            ?>
                                            <td <?php
                                                $curDate = strtotime(date("d-m-Y"));
                                                $expDate = strtotime($record->expiration);
                                                if ($curDate >= $expDate)
                                                    echo 'style="color: red;"';
                                                ?>><?php echo $record->servicename ?></td>
                                            <td <?php
                                                $curDate = strtotime(date("d-m-Y"));
                                                $expDate = strtotime($record->expiration);
                                                if ($curDate >= $expDate)
                                                    echo 'style="color: red;"';
                                                ?>><?php if (is_null($record->payid)) {
                                            echo "N/A";
                                        } else {
                                            echo $record->payid;
                                        }
                                ?></td>
                                            <td <?php
                                                $curDate = strtotime(date("d-m-Y"));
                                                $expDate = strtotime($record->expiration);
                                                if ($curDate >= $expDate)
                                                    echo 'style="color: red;"';
                                                ?>><?php if ($record->enableuser == 1) {
                                            echo "Active";
                                        } else {
                                            echo "<span class='badge bg-danger'>Blocked</span>";
                                        } ?></td>
                                            <td id="expdt_<?php echo $record->username; ?>" <?php
                                                                                            $curDate = strtotime(date("d-m-Y"));
                                                                                            $expDate = strtotime($record->expiration);
                                                                                            if ($curDate > $expDate)
                                                                                                echo 'style="color: red;"';
                                                                                            ?>>
                                                <?php echo date("d-m-Y", strtotime($record->expiration)) ?></td>
                                            <td <?php
                                                $curDate = strtotime(date("d-m-Y"));
                                                $expDate = strtotime($record->expiration);
                                                if ($curDate > $expDate)
                                                    echo 'style="color: red;"';
                                                ?>><?php echo date("d-m-Y", strtotime($record->createdon)) ?></td>
                                            <td class="text-center">
                                                <a class="btn btn-sm btn-primary" href="<?= base_url() . 'userQuickEdit/' . $record->username; ?>" title="Quick Recharge"><i class="fa fa-sync-alt"></i></a> |
                                                <a class="btn btn-sm btn-info enableit" href="#" data-username="<?php echo $record->username; ?>" title="Enable customer"><i class="fa fa-unlock"></i></a>
                                                <a class="btn btn-sm btn-danger disableit" href="#" data-username="<?php echo $record->username; ?>" title="Disable customer"><i class="fa fa-lock"></i></a>
                                            </td>
                                        </tr>
                                <?php
                                        $row_count++;
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Split button -->
                            <div class="btn-group">
                                <button type="button" class="btn btn-default" onclick="#">Selected Users Operation</button>
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" onclick="bulkActionRecharge();return false;">Recharge Selected</a>
                                    <a class="dropdown-item" href="#" onclick="bulkActionBlock(0)">Block Selected</a>
                                    <a class="dropdown-item" href="#" onclick="bulkActionBlock(1)">Unblock Selected</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#" onclick="deleteSelected()">Delete Selected</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <ul class="pagination pagination-sm m-0 float-right">
                                <?php echo $this->pagination->create_links(); ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#chkSelectAll').click(function() {

            $('.form-check-input').each(function() {
                $(this).prop('checked', true);
                name = $(this).closest('tr').find('.name').html();
            })

        })
    });

    function bulkActionBlock(actionType) {

        $('.row-select input:checked').each(function() {
            var userId;
            //id = $(this).closest('tr').find('.chkbox').html();
            userId = ($(this).closest('tr').find('.username').html()).replaceAll(' ', '');
            console.log("username: " + userId + " Block Type: " + actionType);
            hitUrl = baseURL + "userQuickBlock/" + userId.replaceAll(' ', '') + "/" + actionType;

            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: hitUrl,
                data: {
                    userId: userId,
                    status: actionType
                }
            }).done(function(data) {
                console.log("Username : " + userId + " Completed...")
            });

        });

    }

    async function bulkActionRecharge111() {

        await ($('.row-select input:checked').each(async function() {
            //var userId;
            //id = $(this).closest('tr').find('.chkbox').html();
            let userId = ($(this).closest('tr').find('.username').html()).replaceAll(' ', '');
            //console.log( "username: " + userId + " Recharged ");
            hitUrl = baseURL + "userQuickRecharge/" + userId.replaceAll(' ', '');

            await rechargeRequest(userId, hitUrl);

        }));
    }

    function bulkActionRecharge() {

        var obj = $('.row-select input:checked').map(async function() {

            var $row = $(this);
            //var userId = $row.find(':nth-child(3)').text();
            var userId = ($(this).closest('tr').find('.username').html()).replaceAll(' ', '');
            var sno = ($(this).closest('tr').find('.sno').html()).replaceAll(' ', '');

            hitUrl = baseURL + "userQuickRecharge/" + userId.replaceAll(' ', '');
            rechargeRequest(userId, hitUrl, sno);

        }).get();

    }
    function rechargeRequest(userId, hitUrl, sno) {
        console.log("Initiating recharge for: " + userId);
        
        $.ajax({
            type: "POST",
            dataType: "html",
            url: hitUrl,
            data: {
                userId: userId
            }
        })
        .done(function(response) {
            console.log("Response received:", response);
            
            if (!response || !response[0]) {
                console.error("Invalid response format");
                document.getElementById(userId).innerHTML = "<span style='color:red'>" + userId + " (Error: Invalid Response)</span>";
                return;
            }

            const result = response[0];
            const userElement = document.getElementById(userId);
            const expElement = document.getElementById("expdt_" + userId);
            switch(result.errorBalance) {
                case 1:
                    userElement.innerHTML = "<span style='color:red'>" + userId + " (Failed)</span>";
                    if (result.expdate && expElement) {
                        expElement.innerHTML = "<span style='color:red'>" + result.expdate + "</span>";
                    }
                    break;
                case 2:
                    userElement.innerHTML = "<span style='color:darkred'>" + userId + " (Not Allowed)</span>";
                    if (result.expdate && expElement) {
                        expElement.innerHTML = "<span style='color:darkred'>" + result.expdate + "</span>";
                    }
                    break;
                case 3:
                default:
                    userElement.innerHTML = "<span style='color:green'>" + userId + " (Success)</span>";
                    if (result.expdate && expElement) {
                        expElement.innerHTML = "<span style='color:green'>" + result.expdate + "</span>";
                    }
                    break;
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Request failed:", textStatus, errorThrown);
            document.getElementById(userId).innerHTML = "<span style='color:red'>" + userId + " (Error: " + textStatus + ")</span>";
        });
    }



    function GetTableData() {

        var obj = $('#usersTable tbody tr').map(function() {
            var $row = $(this);
            var userId = ($row.find(':nth-child(3)').text()).replaceAll(' ', '');
            hitUrl = baseURL + "userQuickRecharge/" + userId.replaceAll(' ', '')

            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: hitUrl,
                data: {
                    userId: userId,
                    status: 0
                }
            }).done(function(data) {
                console.log("Username : " + userId + " Completed...")
            });

        }).get();

    }

    jQuery(document).ready(function() {
        jQuery('ul.pagination li a').click(function(e) {
            e.preventDefault();
            var link = jQuery(this).get(0).href;
            var value = link.substring(link.lastIndexOf('/') + 1);
            //jQuery("#searchList").attr("action", baseURL + "userQuickEdit/" + value);//
            jQuery("#searchList").attr("action", baseURL + "userQuickEdit/0/" + value + "/");
            jQuery("#searchList").submit();
        });
    });


    // ******* Disable Active Customers ********** //
    jQuery(document).on("click", ".disableit", function() {
        var userId = $(this).data("username"),
            hitURL = baseURL + "userQuickBlock/" + userId + "/0",
            currentRow = $(this);

        var confirmation = confirm("Are you sure to disable this user ?");

        if (confirmation) {
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: hitURL,
                data: {
                    userId: userId,
                    status: 0
                }
            }).done(function(data) {
                currentRow.parents('tr').remove();
                if (data.status = true) {
                    alert("User successfully disabled");
                } else if (data.status = false) {
                    alert("User disabled failed");
                } else {
                    alert("Access denied..!");
                }
            });
        }
    });

    // ******* Enable Customers ********** //
    jQuery(document).on("click", ".enableit", function() {
        var userId = $(this).data("username"),
            hitURL = baseURL + "userQuickBlock/" + userId + "/1",
            currentRow = $(this);

        var confirmation = confirm("Are you sure to enable this user ?");

        if (confirmation) {
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: hitURL,
                data: {
                    userId: userId,
                    status: 1
                }
            }).done(function(data) {
                currentRow.parents('tr').remove();
                if (data.status = true) {
                    alert("User successfully enabled");
                } else if (data.status = false) {
                    alert("User enable failed");
                } else {
                    alert("Access denied..!");
                }
            });
        }
    });
</script>