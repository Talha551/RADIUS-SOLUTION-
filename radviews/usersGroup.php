<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><i class="fas fa-users"></i> Group Management <small>Add, Edit, Delete</small></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
              <li class="breadcrumb-item active">Group Management</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Action buttons section -->
        <div class="card card-custom mb-3">
          <div class="card-body">
            <a class="btn btn-primary" href="<?php echo base_url(); ?>resellerAddNew">
              <i class="fas fa-plus"></i> Add New
            </a>
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

        <!-- Group list section -->
        <div class="card card-custom">
          <div class="card-header">
            <h3 class="card-title">Group List</h3>
            <div class="card-tools">
              <form action="<?php echo base_url() ?>resellerListing" method="POST" id="searchList">
                <div class="input-group input-group-sm" style="width: 250px;">
                  <input type="text" name="searchText" value="<?php echo isset($searchText) ? $searchText : ''; ?>" class="form-control" placeholder="Search"/>
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
                    <th>Group ID</th>
                    <th>Group Name</th>
                    <th>Description</th>
                    <th>Users</th>
                    <th class="text-center">Edit | Manage</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if(!empty($groupListing))
                  {
                      foreach($groupListing as $record)
                      {
                  ?>
                  <tr>
                      <td><?php echo $record->groupid ?></td>
                      <td>
                          <a class="btn btn-primary btn-block" href="<?php echo base_url().'resellerEditOld/'.$record->groupid; ?>" title="Edit Group">
                              <?php echo $record->groupname; ?>
                          </a>
                      </td>
                      <td><?php echo $record->descr ?></td>
                      <td><?php echo "0" ?></td>
                      <td class="text-center">
                          <div class="btn-group">
                              <a class="btn btn-sm btn-primary" href="#" title="Edit">
                                  <i class="fas fa-edit"></i>
                              </a>
                              <a class="btn btn-sm btn-info" href="#" title="View">
                                  <i class="fas fa-eye"></i>
                              </a>
                          </div>
                      </td>
                  </tr>
                  <?php
                      }
                  } else {
                  ?>
                  <tr>
                      <td colspan="5" class="text-center">No records found</td>
                  </tr>
                  <?php } ?>
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
            $("#searchList").attr("action", baseURL + "resellerListing/" + value);
            $("#searchList").submit();
        });
    });
</script>