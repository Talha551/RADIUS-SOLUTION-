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
              <li class="breadcrumb-item active">User Quick Edit</li>
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
            <div class="user-list-actions">
              <a class="btn btn-info btn-nav" href="<?php echo base_url(); ?>customerlist_csv/1/2">
                <i class="fas fa-download"></i> All List
              </a>
              <a class="btn btn-success btn-nav" href="<?php echo base_url(); ?>customerlist_csv/1/1">
                <i class="fas fa-download"></i> Active
              </a>
              <a class="btn btn-danger btn-nav" href="<?php echo base_url(); ?>customerlist_csv/1/0">
                <i class="fas fa-download"></i> Expired
              </a>
              <a class="btn btn-warning btn-nav" href="<?php echo base_url(); ?>customerlist_csv/0/2">
                <i class="fas fa-download"></i> Blocked
              </a>
            </div>
          </div>
        </div>

        <!-- Alerts section -->
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

        <?php $searchText1 = $type; ?>

        <!-- Users list section -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Bulk User Management</h3>
            <div class="card-tools">
              <form action="<?php echo base_url() ?>userQuickEdit" method="POST" id="searchList">
                <div class="input-group">
                  <select name="searchText1" class="form-control mr-2">
                    <option value="0" <?php if($searchText1==0){ echo "selected=selected"; } ?>>Active Users</option>
                    <option value="1" <?php if($searchText1==1){ echo "selected=selected"; } ?>>Paid</option>
                    <option value="2" <?php if($searchText1==2){ echo "selected=selected"; } ?>>Expired</option>
                    <option value="3" <?php if($searchText1==3){ echo "selected=selected"; } ?>>Blocked</option>
                    <option value="4" <?php if($searchText1==4){ echo "selected=selected"; } ?>>All</option>
                    <option value="5" <?php if($searchText1==5){ echo "selected=selected"; } ?>>Online</option>
                    <option value="6" <?php if($searchText1==6){ echo "selected=selected"; } ?>>Offline-Active</option>
                    <option value="7" <?php if($searchText1==7){ echo "selected=selected"; } ?>>Offline-Inactive</option>
                  </select>
                  <input type="text" name="searchText" value="<?php echo $searchText; ?>" class="form-control" placeholder="Search"/>
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
              <table class="table table-custom">
                <thead>
                  <tr>
                    <th>S.No.</th>
                    <th>User Name <span class="badge badge-success">Online</span> <span class="badge badge-info">Offline</span></th>
                    <th>Name</th>
                    <?php 
                      $managername = $this->session->userdata('name');
                      if($this->ismaster > 0 || $managername == 'admin'){ echo "<th>Owner</th>"; };
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
                  if(!empty($userRecords))
                  {
                    $row_count = 1;
                    foreach($userRecords as $record)
                    {
                  ?>
                  <tr>
                    <td><?php echo $row_count; ?></td>
                    <td>
                      <?php 
                        if($record->verified == 1){ echo "<span class='badge badge-success'>".$record->username."</span>"; }
                        else{ echo "<span class='badge badge-info'>".$record->username."</span>"; }
                      ?>
                      <a href="#" data-toggle="modal" data-target="#myModal<?php echo $record->username; ?>">
                        <i class="fas fa-chart-line"></i>
                      </a>
                    </td>
                    <td><?php echo $record->firstname; ?></td>
                    <?php if($this->ismaster > 0 || $managername == 'admin'){ echo "<td>".$record->owner."</td>"; }; ?>
                    <td><?php echo $record->srvname; ?></td>
                    <td><?php echo $record->payid; ?></td>
                    <td>
                      <?php 
                        if($record->enableuser == 1 && $record->expiration > date('Y-m-d')){ echo "<span class='badge badge-success'>Active</span>"; }
                        elseif($record->enableuser == 1 && $record->expiration <= date('Y-m-d')){ echo "<span class='badge badge-danger'>Expired</span>"; }
                        else{ echo "<span class='badge badge-warning'>Blocked</span>"; }
                      ?>
                    </td>
                    <td><?php echo $record->expiration; ?></td>
                    <td><?php echo $record->createdon; ?></td>
                    <td class="text-center">
                      <div class="btn-group">
                        <a class="btn btn-sm btn-success" href="<?php echo base_url().'rechargeUser/'.$record->username; ?>" title="Recharge">
                          <i class="fas fa-sync-alt"></i>
                        </a>
                        <?php if($record->enableuser == 1) { ?>
                          <a class="btn btn-sm btn-danger disableit" href="#" data-username="<?php echo $record->username; ?>" title="Disable">
                            <i class="fas fa-ban"></i>
                          </a>
                        <?php } else { ?>
                          <a class="btn btn-sm btn-success enableit" href="#" data-username="<?php echo $record->username; ?>" title="Enable">
                            <i class="fas fa-check"></i>
                          </a>
                        <?php } ?>
                        <a class="btn btn-sm btn-info" href="<?php echo base_url().'editOldUser/'.$record->username; ?>" title="Edit">
                          <i class="fas fa-edit"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                  
                  <!-- Modal -->
                  <div id="myModal<?php echo $record->username; ?>" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg">
                      <!-- Modal content-->
                      <div class="modal-content">
                        <div class="modal-header">
                          <h4 class="modal-title">Username <?php echo $record->username; ?> Traffic Graph</h4>
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                          <p>Daily data Usage Graph Interval 5 Minutes</p>
                          <div class="media-left">
                            <?php 
                              $this->db->select('nasipaddress');
                              $this->db->from('radacct');
                              $this->db->where('username', $record->username);
                              $this->db->order_by('radacctid', 'DESC');
                              $query = $this->db->get();
                              $result = $query->row();
                            ?>
                            <img src="http://<?php echo $result->nasipaddress; ?>:8375/graphs/queue/<pppoe-<?php echo $record->username; ?>>/daily.gif" class="img-fluid" alt="Traffic Graph" width="608" height="472"> 
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                  </div>

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
            <ul class="pagination pagination-sm m-0 float-right">
              <?php echo $this->pagination->create_links(); ?>
            </ul>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
  </div>

  <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>
  <script type="text/javascript">
    $(document).ready(function(){
      $('ul.pagination li a').click(function (e) {
        e.preventDefault();            
        var link = $(this).get(0).href;            
        var value = link.substring(link.lastIndexOf('/') + 1);
        $("#searchList").attr("action", baseURL + "userQuickEdit/" + value);
        $("#searchList").submit();
      });
    });

    // Disable Active Customers
    $(document).on("click", ".disableit", function(){
      var userId = $(this).data("username"),
        hitURL = baseURL + "userQuickBlock/" + userId+"/0",
        currentRow = $(this);
      
      var confirmation = confirm("Are you sure to disable this user?");
      
      if(confirmation) {
        $.ajax({
          type: "POST",
          dataType: "json",
          url: hitURL,
          data: { userId: userId, status: 0 } 
        }).done(function(data){
          currentRow.parents('tr').remove();
          if(data.status = true) { alert("User successfully disabled"); }
          else if(data.status = false) { alert("User disabled failed"); }
          else { alert("Access denied!"); }
        });
      }
    });

    // Enable Customers
    $(document).on("click", ".enableit", function(){
      var userId = $(this).data("username"),
        hitURL = baseURL + "userQuickBlock/" + userId+"/1",
        currentRow = $(this);
      
      var confirmation = confirm("Are you sure to enable this user?");
      
      if(confirmation) {
        $.ajax({
          type: "POST",
          dataType: "json",
          url: hitURL,
          data: { userId: userId, status: 1 } 
        }).done(function(data){
          currentRow.parents('tr').remove();
          if(data.status = true) { alert("User successfully enabled"); }
          else if(data.status = false) { alert("User enable failed"); }
          else { alert("Access denied!"); }
        });
      }
    });
  </script>
