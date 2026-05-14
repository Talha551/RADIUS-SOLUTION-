<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><i class="fa fa-users"></i> Easy Paisa Files</h1>
            <small class="text-muted">Add, Edit, Delete</small>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Easy Paisa Files</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
    <section class="content">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-12 text-right">
            <a class="btn btn-primary" href="<?php echo base_url(); ?>easypaisaAddNew"><i class="fa fa-plus"></i> Add New</a>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <?php
              $this->load->helper('form');
              $error = $this->session->flashdata('error');
              if($error)
              {
            ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?php echo $this->session->flashdata('error'); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php } ?>
            <?php  
              $success = $this->session->flashdata('success');
              if($success)
              {
            ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?php echo $this->session->flashdata('success'); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php } ?>
            <div class="row">
              <div class="col-12">
                <?php echo validation_errors('<div class="alert alert-danger alert-dismissible fade show">', ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'); ?>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card card-primary card-outline">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0"><i class="fas fa-file-alt"></i> Files List</h3>
                <form action="<?php echo base_url() ?>easypaisalist" method="POST" id="searchList" class="form-inline float-right">
                  <div class="input-group input-group-sm">
                    <input type="text" name="searchText" value="<?php echo $searchText; ?>" class="form-control" placeholder="Search"/>
                    <div class="input-group-append">
                      <button class="btn btn-default searchList" type="submit"><i class="fa fa-search"></i></button>
                    </div>
                  </div>
                </form>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                      <tr>
                        <th>S.No.</th>
                        <th>File Name</th>
                        <th>Billing Month</th>
                        <th>Due Date</th>
                        <th>Generated On</th>
                        <th>File Path</th>
                        <th class="text-center">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(!empty($easypaisaListing))
                    {
                        $row_count = 1;
                        foreach($easypaisaListing as $record)
                        {
                    ?>
                    <tr>
                      <td><?php echo $record->serial_number;?>.</td>
                      <td><?php echo $record->filename ?></td>
                      <td><?php echo $record->billingmonth ?></td>
                      <td><?php echo $record->duedate ?></td>
                      <td><?php echo $record->createdDtm ?></td>
                      <td><?php echo $record->filepath ?></td>
                      <td class="text-center">
                        <a class="btn btn-sm btn-info" href="<?= base_url(); ?>reports/<?php echo $record->filepath; ?>" title="Download"><i class="fa fa-cloud"></i></a>
                        <a class="btn btn-sm btn-info" href="#" title="Edit"><i class="fa fa-pencil-alt"></i></a>
                        <a class="btn btn-sm btn-danger deleteUser" href="#" data-userid="<?php echo $record->fileid; ?>" title="Delete"><i class="fa fa-trash"></i></a>
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
                <?php echo $this->pagination->create_links(); ?>
              </div>
            </div>
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
              jQuery("#searchList").attr("action", baseURL + "easypaisalist/" + value);
              jQuery("#searchList").submit();
          });
      });
  </script>