<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-box"></i> Packages List (Managers)</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Services List</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <!-- Manager Filter Dropdown -->
            <form method="get" id="managerFilterForm" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label for="managerSelect"><strong>Filter by Manager:</strong></label>
                            <select id="managerSelect" name="managername" class="form-control">
                                
                                <option value="<?php echo $this->session->userdata('name'); ?>" <?php if($this->session->userdata('name') == $searchText){echo 'selected=selected';} ?>><?php echo $this->session->userdata('name'); ?></option>
                                <?php foreach($managerList as $manager): ?>
                                    <option value="<?php echo htmlspecialchars($manager->managername); ?>" <?php if(isset($type) && $type == $manager->managername) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($manager->managername); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
            <script>
            document.getElementById('managerSelect').addEventListener('change', function() {
                var manager = this.value;
                var url = '<?= base_url('Services_controller/servicesListingview/'); ?>' + (manager ? encodeURIComponent(manager) : '');
                window.location.href = url;
            });
            </script>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Packages</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped projects">
                        <thead>
                            <tr>
                                <th style="width: 1%">#</th>
                                <th style="width: 20%">Service Name</th>
                                <th style="width: 30%">Prices</th>
                                <th>Users Progress</th>
                                <th style="width: 8%" class="text-center">Status</th>
                                <th style="width: 20%"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $totalUsers = 0;
                        foreach($serviceListing as $record) {
                            $totalUsers += $record->NoOfUsers;
                        }
                        $row_count = 1;
                        foreach($serviceListing as $record) {
                            $percent = $totalUsers > 0 ? round(($record->NoOfUsers / $totalUsers) * 100) : 0;
                        ?>
                            <tr>
                                <td>#</td>
                                <td>
                                    <a><?php echo htmlspecialchars($record->srvname); ?></a>
                                    <br/>
                                    <small>Profile: <?php echo htmlspecialchars($record->radsrvname); ?></small><br/>
                                    <small>Manager: <?php echo htmlspecialchars($record->managername); ?></small>
                                </td>
                                
                                <td>
                                    <ul class="list-inline">
                                        <li class="list-inline-item" title="Cost Price">
                                            <img alt="Cost" class="table-avatar" src="<?php echo base_url('assets/dist/img/costprice.png'); ?>">
                                            <span class="d-block text-center small"><?php echo htmlspecialchars($record->costprice); ?></span>
                                        </li>
                                        <li class="list-inline-item" title="Sale Price">
                                            <img alt="Sale" class="table-avatar" src="<?php echo base_url('assets/dist/img/sale-price.png'); ?>">
                                            <span class="d-block text-center small"><?php echo htmlspecialchars($record->saleprice); ?></span>
                                        </li>
                                        <li class="list-inline-item" title="Base Price">
                                            <img alt="Base" class="table-avatar" src="<?php echo base_url('assets/dist/img/base-price.jpg'); ?>">
                                            <span class="d-block text-center small"><?php echo htmlspecialchars($record->baseprice); ?></span>
                                        </li>
                                    </ul>
                                </td>
                                <td class="project_progress">
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-green" role="progressbar" aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percent; ?>%">
                                        </div>
                                    </div>
                                    <small>
                                        <?php echo $percent; ?>% Active Users (<?php echo $record->NoOfUsers; ?> users)
                                    </small>
                                </td>
                                <td class="project-state">
                                    <span class="badge badge-success">Active</span>
                                </td>
                                <td class="project-actions text-right">

                                    <?php if((isset($this->ismaster) && $this->ismaster > 0) || (isset($this->session) && $this->session->userdata('name') == 'admin')): ?>
                                    <a class="btn btn-warning btn-sm btn-subreseller-model" href="#" data-srvid="<?php echo $record->srvid; ?>">
                                        <i class="fas fa-sitemap"></i> Sub-Packages
                                    </a>
                                    <?php endif; ?>
                                    <a class="btn btn-info btn-sm" href="<?php echo base_url().'serviceEdit/'.$record->srvid; ?>">
                                        <i class="fas fa-pencil-alt"></i> Edit
                                    </a>
                                    
                                    
                                </td>
                            </tr>
                        <?php $row_count++; } ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </section>
</div>
<!-- Add AdminLTE 3 CSS if not already included -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
.table-avatar { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; }
#subResellerModal .modal-body {
  max-height: 60vh;
  overflow-y: auto;
}
body.modal-open {
  overflow: hidden !important;
}
</style>
<script>
// Optional: Add JS for delete confirmation, etc.
</script>

<!-- Sub-Reseller Model Modal -->
<div class="modal fade" id="subResellerModal" tabindex="-1" role="dialog" aria-labelledby="subResellerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="subResellerModalLabel">Sub-Reseller Services</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="subResellerLoading" class="text-center my-3">
          <i class="fas fa-spinner fa-spin"></i> Loading...
        </div>
        <div id="subResellerTableWrap" style="display:none;">
          <table class="table table-striped table-valign-middle">
            <thead>
              <tr>
                <th>Srvid</th>
                <th>Service Name</th>
                <th>Profile</th>
                <th>Manager</th>
                <th>Base Price</th>
                <th>Cost Price</th>
                <th>Sale Price</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="subResellerTableBody">
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Edit Sub-Reseller Service Modal -->
<div class="modal fade" id="editSubResellerModal" tabindex="-1" role="dialog" aria-labelledby="editSubResellerModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editSubResellerModalLabel">Edit Sub-Reseller Service</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" tabindex="0">
        <form id="editSubResellerForm">
          <input type="hidden" name="srvid" id="editSrvid">
          <div class="form-group">
            <label for="editBasePrice">Base Price</label>
            <input type="number" step="0.01" class="form-control" name="baseprice" id="editBasePrice" required>
          </div>
          <div class="form-group">
            <label for="editCostPrice">Cost Price</label>
            <input type="number" step="0.01" class="form-control" name="costprice" id="editCostPrice" required>
          </div>
          <div class="form-group">
            <label for="editSalePrice">Sale Price</label>
            <input type="number" step="0.01" class="form-control" name="saleprice" id="editSalePrice" required>
          </div>
          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Track last clicked sub-reseller package
var lastSubResellerSrvid = null;
var lastSubResellerManager = null;

$(function() {
  // Handler for Sub-Reseller Model button
  $(document).on('click', '.btn-subreseller-model', function(e) {
    e.preventDefault();
    lastSubResellerSrvid = $(this).data('srvid');
    lastSubResellerManager = $('#managerSelect').val();
    var manager = lastSubResellerManager;
    var srvid = lastSubResellerSrvid;
    $('#subResellerTableBody').empty();
    $('#subResellerTableWrap').hide();
    $('#subResellerLoading').show();
    $('#subResellerModal').modal('show');
    $.getJSON('<?= base_url('Services_controller/getSubResellerServicesAjax'); ?>', { managername: manager, srvid: srvid }, function(data) {
      renderSubResellerTable(data);
      $('#subResellerLoading').hide();
      $('#subResellerTableWrap').show();
    }).fail(function() {
      $('#subResellerTableBody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load data</td></tr>');
      $('#subResellerLoading').hide();
      $('#subResellerTableWrap').show();
    });
  });

  // Update the JS that populates the subResellerTableBody to add an Edit button
  function renderSubResellerTable(data) {
    var html = '';
    if (data && data.length > 0) {
      $.each(data, function(i, s) {
        html += '<tr>' +
          '<td>' + (s.srvid ? s.srvid : '-') + '</td>' +
          '<td>' + (s.srvname ? s.srvname : '-') + '</td>' +
          '<td>' + (s.radsrvname ? s.radsrvname : '-') + '</td>' +
          '<td>' + (s.managername ? s.managername : '-') + '</td>' +
          '<td>' + (s.baseprice ? s.baseprice : '-') + '</td>' +
          '<td>' + (s.costprice ? s.costprice : '-') + '</td>' +
          '<td>' + (s.saleprice ? s.saleprice : '-') + '</td>' +
          '<td><button class="btn btn-info btn-sm btn-edit-subreseller" data-srvid="' + s.srvid + '" data-baseprice="' + s.baseprice + '" data-costprice="' + s.costprice + '" data-saleprice="' + s.saleprice + '">Edit</button></td>' +
          '</tr>';
      });
    } else {
      html = '<tr><td colspan="8" class="text-center">No sub-reseller services found.</td></tr>';
    }
    $('#subResellerTableBody').html(html);
  }

  // Handler for Edit button
  $(document).on('click', '.btn-edit-subreseller', function() {
    var srvid = $(this).data('srvid');
    var baseprice = $(this).data('baseprice');
    var costprice = $(this).data('costprice');
    var saleprice = $(this).data('saleprice');
    $('#editSrvid').val(srvid);
    $('#editBasePrice').val(baseprice);
    $('#editCostPrice').val(costprice);
    $('#editSalePrice').val(saleprice);
    $('#editSubResellerModal').modal('show');
  });

  // After showing/reloading the subResellerModal, ensure scroll focus stays inside
  function reloadSubResellerModal() {
    $('#subResellerTableBody').empty();
    $('#subResellerTableWrap').hide();
    $('#subResellerLoading').show();
    $('#subResellerModal').modal('show');
    $('#subResellerModal').off('shown.bs.modal').on('shown.bs.modal', function () {
      var $body = $('#subResellerModal .modal-body');
      $body.focus();
      $body.scrollTop(0);
    });
    $.getJSON('<?= base_url('Services_controller/getSubResellerServicesAjax'); ?>', { managername: lastSubResellerManager, srvid: lastSubResellerSrvid }, function(data) {
      renderSubResellerTable(data);
      $('#subResellerLoading').hide();
      $('#subResellerTableWrap').show();
    }).fail(function() {
      $('#subResellerTableBody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load data</td></tr>');
      $('#subResellerLoading').hide();
      $('#subResellerTableWrap').show();
    });
  }

  // Replace the reload logic in the AJAX success handler:
  $('#editSubResellerForm').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
      url: '<?= base_url('Services_controller/updateSubResellerPrices'); ?>',
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          $('#editSubResellerModal').modal('hide');
          if (lastSubResellerSrvid && lastSubResellerManager !== null) {
            reloadSubResellerModal();
          }
        } else {
          alert('Failed to update service: ' + (response.error || 'Unknown error'));
        }
      },
      error: function() {
        alert('Failed to update service.');
      }
    });
  });
});
</script> 