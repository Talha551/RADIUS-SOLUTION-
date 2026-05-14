<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>
              <i class="fas fa-users"></i> <?php if($accType == 0){ echo "User Management"; }else{ echo "Prepaid Cards Management"; } ?>
              <small>Add, Edit, Delete</small>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
              <li class="breadcrumb-item active">User Management</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
      <div class="container-fluid">
        <!-- Download section -->
        <div class="card card-custom mb-3">
          <div class="card-header">
            <h3 class="card-title">Download Customers List</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="btn-group">
                  <a class="btn btn-info mr-2" href="<?php echo base_url(); ?>customerlist_csv/1/2">
                    <i class="fas fa-download"></i> All List
                  </a>
                  <a class="btn btn-success mr-2" href="<?php echo base_url(); ?>customerlist_csv/1/1">
                    <i class="fas fa-download"></i> Active
                  </a>
                  <a class="btn btn-danger mr-2" href="<?php echo base_url(); ?>customerlist_csv/1/0">
                    <i class="fas fa-download"></i> Expired
                  </a>
                  <a class="btn btn-warning" href="<?php echo base_url(); ?>customerlist_csv/0/2">
                    <i class="fas fa-download"></i> Blocked
                  </a>
                </div>
              </div>
              <div class="col-md-6 text-right">
                <a class="btn btn-primary" href="<?php echo base_url(); if($this->ismaster == 0){ echo "usersAddNew"; } ?>">
                  <i class="fas fa-plus"></i> Add New
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Alert messages -->
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
        <div class="card card-custom">
          <div class="card-header">
            <h3 class="card-title">Users List</h3>
            <div class="card-tools">
              <form action="<?php echo base_url() ?>usersListing/<?php echo $accType; ?>" method="POST" id="searchList">
                <div class="input-group input-group-sm" style="width: 350px;">
                  <select name="searchText1" class="form-control mr-2">
                    <option value="0" <?php if($searchText1==0){ echo "selected=selected"; } ?>>Active Users</option>
                    <option value="1" <?php if($searchText1==1){ echo "selected=selected"; } ?>>Paid</option>
                    <option value="2" <?php if($searchText1==2){ echo "selected=selected"; } ?>>Expired</option>
                    <option value="3" <?php if($searchText1==3){ echo "selected=selected"; } ?>>Blocked</option>
                    <option value="4" <?php if($searchText1==4){ echo "selected=selected"; } ?>>All</option>
                    <option value="5" <?php if($searchText1==5){ echo "selected=selected"; } ?>>Online</option>
                    <option value="6" <?php if($searchText1==6){ echo "selected=selected"; } ?>>Offline-Active</option>
                    <option value="7" <?php if($searchText1==7){ echo "selected=selected"; } ?>>Offline-Inactive</option>
                    <option value="8" <?php if($searchText1==8){ echo "selected=selected"; } ?>>Expiring in 3 Days</option>
                    <option value="9" <?php if($searchText1==9){ echo "selected=selected"; } ?>>Expiring in 1 Days</option>
                    <option value="10" <?php if($searchText1==10){ echo "selected=selected"; } ?>>Expired last 1 Day</option>
                    <option value="11" <?php if($searchText1==11){ echo "selected=selected"; } ?>>Expired last 3 Days</option>
                    <option value="12" <?php if($searchText1==12){ echo "selected=selected"; } ?>>Expired last 7 Days</option>
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
                    <th>
                      <a href="#" id="btnCheckOnlineStatus">
                        <span class="badge badge-success">Online</span>
                        <span class="badge badge-info">Offline</span>
                        <i class="fas fa-sync-alt fa-lg"></i>
                      </a>
                      <br>User Name
                    </th>
                    <th>Name</th>
                    <?php 
                      $managername = $this->session->userdata('name');
                      if($this->ismaster > 0 || $managername == 'admin'){ echo "<th>Owner</th>"; };
                    ?>
                    <th>Service</th>
                    <th>PayID</th>
                    <th>Status</th>
                    <th>CNIC</th>
                    <th>Expiration</th>
                    <th>Contact</th>
                    <th>Created</th>
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
                      $curDate = date("Y-m-d");
                      $expDate = date("Y-m-d", strtotime($record->expiration));
                      $online_status = 0;
                  ?>
                  <tr>
                    <td><?php echo $row_count; ?></td>
                    <td>
                      <a class="btn btn-block btn-secondary user-status-badge" style="border: 2px solid #adb5bd;" data-username="<?php echo $record->username; ?>" href="<?php echo base_url().'usersEdit/'.$record->username; ?>" title="Edit User">
                        <?php echo $record->username; ?>
                      </a>
                    </td>
                    <td <?php if($curDate >= $expDate) echo 'style="color: red;"'; ?>>
                      <?php echo $record->firstname." ".$record->lastname ?>
                    </td>
                    <!-- If Master Add this Field -->
                    <?php 
                      if($this->ismaster > 0 || $managername == 'admin'){ 
                        echo "<td";
                        if($curDate >= $expDate){
                          echo ' style="color: red;">';
                        } else { 
                          echo ">"; 
                        }
                        echo $record->owner;
                        echo "</td>";
                      }
                    ?>
                    <td <?php if($curDate >= $expDate) echo 'style="color: red;"'; ?>>
                      <?php echo $record->servicename ?>
                    </td>
                    <td <?php if($curDate >= $expDate) echo 'style="color: red;"'; ?>>
                      <?php if(is_null($record->payid)){ echo "N/A"; } else { echo $record->payid; } ?>
                    </td>
                    <td <?php if($curDate >= $expDate) echo 'style="color: red;"'; ?>>
                      <?php if($record->enableuser == 1){ echo "Active"; } else { echo "<span class='badge badge-danger'>Blocked</span>"; } ?>
                    </td>
                    <td <?php if($curDate > $expDate) echo 'style="color: red;"'; ?>>
                      <?php if($record->verified == 1){ 
                        echo '<a class="btn btn-sm btn-success" href="'. base_url().'docsUpload/'.$accType.'/'.$record->username.'" title="CNIC Uploaded"><i class="fas fa-check"></i></a>'; 
                      } else { 
                        echo '<a class="btn btn-sm btn-warning" href="'. base_url().'docsUpload/'.$accType.'/'.$record->username.'" title="CNIC Not Uploaded"><i class="fas fa-upload"></i></a>'; 
                      } ?>
                    </td>
                    <td <?php if($curDate > $expDate) echo 'style="color: red;"'; ?>>
                      <?php echo date("d-m-Y", strtotime($record->expiration)) ?>
                    </td>
                    <td <?php if($curDate > $expDate) echo 'style="color: red;"'; ?>>
                      <?php echo $record->mobile; ?>
                    </td>
                    <td <?php if($curDate > $expDate) echo 'style="color: red;"'; ?>>
                      <?php echo date("d-m-Y", strtotime($record->createdon)) ?>
                    </td>
                    <td class="text-center">
                      <div class="btn-group">
                        <a class="btn btn-sm btn-primary mr-1" href="<?= base_url().'recharge/'.$record->username; ?>" title="Recharge">
                          <i class="fas fa-sync-alt"></i>
                        </a>
                        <a class="btn btn-sm btn-info mr-1" href="<?= base_url().'usersDashboard/'.$record->username; ?>" title="View">
                          <i class="fas fa-eye"></i>
                        </a>
                        <a class="btn btn-sm btn-danger" href="<?php echo base_url().'changeService/'.$record->username; ?>" title="Change Package">
                          <i class="fas fa-exchange-alt"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                  <?php
                    $row_count++;
                  }
                }
                else
                {
                ?>
                <tr>
                  <td colspan="12" class="text-center">No records found</td>
                </tr>
                <?php
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

  <script type="text/javascript">
    $(document).ready(function(){
      $('ul.pagination li a').click(function (e) {
        e.preventDefault();            
        var link = $(this).get(0).href;            
        var value = link.substring(link.lastIndexOf('/') + 1);
        $("#searchList").attr("action", baseURL + "usersListing/<?php echo $accType; ?>/" + value + "/");
        $("#searchList").submit();
      });

      // Online status AJAX
      $('#btnCheckOnlineStatus').click(function(e){
        e.preventDefault();
        var usernames = [];
        $('.user-status-badge').each(function(){
          usernames.push($(this).data('username'));
          // Show loading spinner in button
          $(this).removeClass('btn-success btn-info').addClass('btn-secondary').html('<i class="fas fa-spinner fa-spin"></i>');
        });
        $.ajax({
          url: '<?php echo base_url('Userslist/ajaxGetOnlineStatus'); ?>',
          type: 'POST',
          data: {usernames: usernames},
          dataType: 'json',
          success: function(resp){
            if(resp.success){
              $.each(resp.statuses, function(username, status){
                var $btn = $('.user-status-badge[data-username="'+username+'"]');
                $btn.removeClass('btn-success btn-info btn-secondary');
                if(status == 1){
                  $btn.addClass('btn-success').html(username);
                } else {
                  $btn.addClass('btn-info').html(username);
                }
              });
            } else {
              alert('Failed to get status: ' + resp.error);
            }
          },
          error: function(){
            alert('AJAX error');
          }
        });
      });
    });
  </script>