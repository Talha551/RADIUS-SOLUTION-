<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><i class="fa fa-users"></i> Easy Paisa Data Import</h1>
            <small class="text-muted">Format: Consumer Number, Customer Name, Amount Paid, Transaction Date</small>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-6">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-upload"></i> Import Data</h3>
              </div>
              <div class="card-body">
                <?php if (isset($error) && $error): ?>
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('success')): ?>
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $this->session->flashdata('success'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('Error')): ?>
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $this->session->flashdata('Error'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                <?php endif; ?>
                <form enctype="multipart/form-data" method="post" action="<?php echo base_url() ?>Accounts/Easypaisa_controller/importcsvfile" role="form">
                  <div class="form-group">
                    <label for="file"><i class="fas fa-file-csv"></i> Upload CSV File</label>
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" name="file" id="file" required accept=".csv">
                      <label class="custom-file-label" for="file">Choose CSV file</label>
                    </div>
                    <small class="form-text text-muted">Only CSV files allowed. <a href="#" tabindex="-1">Download template</a></small>
                  </div>
                  <button type="submit" class="btn btn-primary" name="submit" value="submit"><i class="fas fa-upload"></i> Upload</button>
                </form>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="card card-info card-outline mb-4">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list"></i> Owner Collection Summary</h3>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-striped table-hover table-bordered mb-0">
                    <thead class="thead-light">
                      <tr>
                        <th>Owner</th>
                        <th>Collection</th>
                        <th>Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                        $total = 0;
                        if ($epdataowner == FALSE): ?>
                        <tr><td colspan="3" class="text-center text-muted">No data available</td></tr>
                      <?php else: ?>
                        <?php foreach ($epdataowner as $row): 
                              $total = $total + $row['amount_paid'];
                        ?>
                          <tr>
                            <td><?php echo $row['owner']; ?></td>
                            <td><?php echo number_format($row['amount_paid']); ?></td>
                            <td><?php echo $row['transaction_date']; ?></td>
                          </tr>
                        <?php endforeach; ?>
                        <tr class="font-weight-bold bg-light">
                          <td>TOTAL</td>
                          <td><?php echo number_format($total); ?></td>
                          <td></td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="card-footer bg-white">
                <form enctype="multipart/form-data" method="post" action="<?php echo base_url() ?>Accounts/Easypaisa_controller/postresellercredit" role="form" class="d-inline">
                  <input type="hidden" value="<?php echo $total; ?>" name="gtotal" id="gtotal" />
                  <button type="submit" class="btn btn-success" name="submit" value="submit"><i class="fas fa-check-circle"></i> Post Reseller Credit</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card card-secondary card-outline">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-table"></i> Imported Easy Paisa Transactions</h3>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-striped table-hover table-bordered mb-0">
                    <thead class="thead-light">
                      <tr>
                        <th>Consumer</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Import On</th>
                        <th>User</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if ($epdata == FALSE): ?>
                        <tr><td colspan="6" class="text-center text-muted">No data available</td></tr>
                      <?php else: ?>
                        <?php foreach ($epdata as $row): ?>
                          <tr>
                            <td><?php echo $row['consumer_number']; ?></td>
                            <td><?php echo $row['customer_name']; ?></td>
                            <td><?php echo number_format($row['amount_paid']); ?></td>
                            <td><?php echo $row['transaction_date']; ?></td>
                            <td><?php echo $row['createdDtm']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <footer class="mt-4 mb-2 text-center text-muted small">
          &copy; <?php echo date('Y'); ?> Easy Paisa Import
        </footer>
      </div>
    </section>
</div>
<script>
// Show file name in custom file input
$(document).on('change', '.custom-file-input', function (event) {
  var inputFile = event.currentTarget;
  $(inputFile).parent().find('.custom-file-label').html(inputFile.files[0].name);
});
</script>
