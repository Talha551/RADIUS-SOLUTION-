<style>
body { font-family: sans-serif; }
#id_confrmdiv
{
    display: none;
    background-color: #eee;
    border-radius: 5px;
    border: 1px solid #aaa;
    position: fixed;
    width: 300px;
    left: 50%;
    margin-left: -150px;
    padding: 6px 8px 8px;
    box-sizing: border-box;
    text-align: center;
    z-index: 1050;
}
#id_confrmdiv button {
    background-color: #ccc;
    display: inline-block;
    border-radius: 3px;
    border: 1px solid #aaa;
    padding: 2px;
    text-align: center;
    width: 80px;
    cursor: pointer;
}
#id_confrmdiv button:hover
{
    background-color: #ddd;
}
#confirmBox .message
{
    text-align: left;
    margin-bottom: 8px;
}
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-users"></i> Online Calls
                        <small>Reports / Online Calls</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Online Calls</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <!-- Flash messages -->
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
                            <h3 class="card-title">This will disconnect/re-connect all users....</h3>
                            <div id="id_confrmdiv" class="shadow">
                                <p>Are you sure you want to restart all sessions?</p>
                                <button id="id_truebtn" class="btn btn-sm btn-danger">Yes</button>
                                <button id="id_falsebtn" class="btn btn-sm btn-secondary">No</button>
                            </div>
                            <button onclick="doSomething()" class="btn btn-warning mb-2">Restart Session</button>

                            <div class="card-tools">
                                <form action="<?php echo base_url() ?>onlineusers" method="POST" id="searchList">
                                    <div class="input-group input-group-sm" style="width: 200px;">
                                        <input type="text" name="searchText" value="<?php echo $searchText; ?>" class="form-control float-right" placeholder="Search"/>
                                        <div class="input-group-append">
                                            <button class="btn btn-default searchList"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div><!-- /.card-header -->
                        
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>UserName</th>
                                        <th>Manage/Mac-Unbind</th>
                                        <th>StartTime</th>
                                        <th>IP-Address</th>
                                        <th>Session Time</th>
                                        <th>Download</th>
                                        <th>Upload</th>
                                        <th>Owner</th>
                                        <th>Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(!empty($onlineCallsListing))
                                    {
                                        $row_count = 1;
                                        foreach($onlineCallsListing as $record)
                                        {
                                            $download = round(((($record->acctoutputoctets)/1024)/1024)/1024,2);
                                            $textClass = ($download > 25) ? 'text-danger' : '';
                                    ?>
                                    <tr id="<?php echo $record->username; ?>">
                                        <td class="<?php echo $textClass; ?>"><?php echo $record->serial_number;?>.</td>
                                        <td class="<?php echo $textClass; ?>"><?php echo $record->username ?></td>
                                        <td>
                                            <a class="btn btn-sm btn-info" href="<?php echo base_url().'usersDashboard/'.$record->username; ?>" title="View User"><i class="fas fa-eye"></i></a>
                                            <a class="btn btn-sm btn-danger disconnect" href="#" data-username="<?php echo $record->username; ?>" title="Disconnect"><i class="fas fa-unlink"></i></a>
                                            <a class="btn btn-sm btn-warning" href="<?php echo base_url(); ?>other/mac_unbind_user/<?php echo $record->username; ?>" title="MAC-UnBind"><i class="fas fa-user-times"></i></a>
                                        </td>
                                        <td class="<?php echo $textClass; ?>"><?php echo $record->acctstarttime ?></td>
                                        <td class="<?php echo $textClass; ?>"><?php echo $record->framedipaddress ?></td>                        
                                        <td class="<?php echo $textClass; ?>"><?php echo round(((($record->acctsessiontime)/60)/60),0)." Hours" ?></td>                        
                                        <td class="<?php echo $textClass; ?>"><?php echo round(((($record->acctoutputoctets)/1024)/1024)/1024,2)." Gb" ?></td>
                                        <td class="<?php echo $textClass; ?>"><?php echo round(((($record->acctinputoctets)/1024)/1024)/1024,2)." Gb" ?></td>
                                        <td class="<?php echo $textClass; ?>"><?php echo $record->owner ?></td>
                                        <td class="<?php echo $textClass; ?>"><?php echo $record->firstname."/".$record->lastname ?></td>
                                    </tr>
                                    <?php
                                            $row_count++;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div><!-- /.card-body -->
                        
                        <div class="card-footer clearfix">
                            <?php echo $this->pagination->create_links(); ?>
                        </div>
                    </div><!-- /.card -->
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('ul.pagination li a').click(function (e) {
            e.preventDefault();            
            var link = jQuery(this).get(0).href;            
            var value = link.substring(link.lastIndexOf('/') + 1);
            jQuery("#searchList").attr("action", baseURL + "onlineusers/" + value);
            jQuery("#searchList").submit();
        });
        
        // Add disconnect functionality
        jQuery('.disconnect').click(function(e) {
            e.preventDefault();
            var username = $(this).data('username');
            if(confirm('Are you sure you want to disconnect this user?')) {
                jQuery.ajax({
                    //url: baseURL + 'Reports_controller/DisconnectUser/' + encodeURIComponent(username),

                    url: '<?php echo base_url("Network_controller/disconnectExpiredUser/"); ?>' + username,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if(response.status) {
                            alert('User ' + username + ' has been disconnected.');
                            // Optionally, remove the row or refresh the table
                            jQuery('tr#' + username).fadeOut();
                        } else {
                            alert('Failed to disconnect user ' + username + '.');
                        }
                    },
                    error: function() {
                        alert('Error disconnecting user ' + username + '.');
                    }
                });
            }
        });
    });
</script>

<script>
    function doSomething(){
        document.getElementById('id_confrmdiv').style.display="block";

        document.getElementById('id_truebtn').onclick = function(){
            alert('All your users will be restarted now');
            window.location.replace("<?php echo base_url('RestartSession'); ?>");
        };
        document.getElementById('id_falsebtn').onclick = function(){
            document.getElementById('id_confrmdiv').style.display="none";
            return false;
        };
    }
</script>
