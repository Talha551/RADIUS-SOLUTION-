<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">User Dashboard</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <?php if($this->session->userdata('name') == 'admin'){ ?> 
          <div class="col-lg-3 col-6">
            <form action="<?php echo base_url(); ?>userProfile" method="POST">
              <div>
                <input type=hidden name="username" id="username" value="<?php echo $username; ?>" />
              </div>
              <div>
                <button class="btn btn-primary"><i class="fas fa-user"></i> View User Control Profile</button>
              </div>
            </form>
          </div>
        <?php } ?>

        <div class="col-lg-3 col-6">
          <a href="<?php echo base_url(); ?>other/mac_bind_user/<?php echo $username; ?>" class="btn btn-primary">
            <i class="fas fa-user"></i> Bind MAC Address
          </a>
        </div>

        <?php 
        if($perm_macbinding == 1){
          if ($this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(1, 2)) 
                          || $this->session->userdata('login_type') == 'manager' || $perm_macbinding == 1) { ?>

        <div class="col-lg-3 col-6">
          <a href="<?php echo base_url(); ?>other/mac_unbind_user/<?php echo $username; ?>" class="btn btn-primary">
            <i class="fas fa-user"></i> Un-Bind MAC Address
          </a>
        </div>
        <?php }} ?>

        <div class="col-lg-3 col-6">
          <a href="" class="btn btn-secondary">
             MAC: <?php echo $userInfo->mac; ?>
          </a>
        </div>

        <div class="col-lg-3 col-6">
          <a href="" class="btn <?php echo ($userInfo->usemacauth == 1) ? "btn-success" : "btn-danger"; ?>">
             MAC: <?php echo ($userInfo->usemacauth == 1) ? "MAC BINDED" : "NOT BINDED"; ?>
          </a>
        </div>

      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">


      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Customer Name</h3>
            </div>
            <div class="card-body">
              <?php echo $userInfo->firstname . " " . $userInfo->lastname; ?>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">User Name</h3>
            </div>
            <div class="card-body">
              <?php echo $userInfo->username; ?>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Password</h3>
            </div>
            <div class="card-body">
              <?php if(!empty($radInfo->value)){ echo $radInfo->value; }else{ echo "N/A"; }?>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Payment ID</h3>
            </div>
            <div class="card-body">
              <?php
              if ($this->users_model->checkDocumentsExists($userInfo->username) == TRUE) {
                $payInfo = $this->users_model->getUserDocsInfo($userInfo->username);
                echo $payInfo->payid;
              } else {
                echo "N/A";
              }
              ?>
            </div>
          </div>
        </div>
      </div>

      <?php 
        $this->load->model('Reports_model');
        $connectedUser = NULL;
        if($this->Reports_model->checkUserOnlineStatus($userInfo->username) == TRUE)
        {
          $connectedUser = $this->Reports_model->disconnectUser($userInfo->username);
          $userStatus = 1;
        }else{
          $userStatus = 0;
        }
      ?>

      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">IP Address</h3>
            </div>
            <div class="card-body">
              <?php if(!empty($connectedUser))
                    { 
                      if($userStatus == 1){ 
                        echo $connectedUser->nasipaddress; 
                      }else{ 
                        echo "N/A"; 
                      } 
                    }else{
                      echo "N/A"; 
                    }
              ?>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Online Since</h3>
            </div>
            <div class="card-body">
              <?php 
                if(!empty($connectedUser))
                { 
                  if($userStatus == 1){ 
                    echo $connectedUser->acctstarttime; 
                  }else{ 
                    echo "N/A"; 
                  } 
                }else{
                  echo "N/A";
                }
              ?>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Last Seen</h3>
            </div>
            <div class="card-body">
              <?php 
                $lastSession = $this->Reports_model->lastOnlineUserStatus($userInfo->username);
                if(!empty($lastSession->acctstoptime)){
                  echo $lastSession->acctstoptime;
                }else{
                  echo "Online";
                }
              ?>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Device ID</h3>
            </div>
            <div class="card-body">
              <?php
                if(!empty($cardInfo)){
                  echo $cardInfo->deviceid;
                }else{
                  echo "Not Available";
                }
              ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-3 col-sm-6 col-12">
          <div class="info-box bg-info">
            <span class="info-box-icon"><i class="fas fa-globe"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Speed Profile</span>
              <span class="info-box-text"><?php if(!empty($packageInfo->managersrvname)){echo $packageInfo->managersrvname;} ?></span>
              <div class="progress">
                <div class="progress-bar" style="width: 70%"></div>
              </div>
              <span class="progress-description">
                Package Info
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
          <div class="info-box bg-success">
            <span class="info-box-icon"><i class="fas fa-cloud-download-alt"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Download Rate</span>
              <span class="info-box-number">
                <?php 
                  $packageDownload = round(((($userInfo->dlburstlimit > 0) ? $userInfo->dlburstlimit : $userInfo->downrate) / 1000) / 1000, 0);
                  echo $packageDownload;
                ?>
                Mbps</span>
              <div class="progress">
                <div class="progress-bar" style="width: 70%"></div>
              </div>
              <span class="progress-description">
                Max allowed download rate
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
          <div class="info-box bg-warning">
            <span class="info-box-icon"><i class="fas fa-cloud-upload-alt"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Upload Rate</span>
              <span class="info-box-number"><?php echo round(((($userInfo->ulburstlimit > 0) ? $userInfo->ulburstlimit : $userInfo->uprate) / 1000) / 1000, 0); ?>
                Mbps</span>
              <div class="progress">
                <div class="progress-bar" style="width: 70%"></div>
              </div>
              <span class="progress-description">
                Max allowed upload rate
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
          <div class="info-box bg-danger">
            <span class="info-box-icon"><i class="fas fa-calendar-times"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Expiration</span>
              <span class="info-box-number">
                <?php
                $expdate = strtotime($userInfo->expiration);
                echo date('d-m-y', $expdate);
                ?></span>
              <div class="progress">
                <div class="progress-bar" style="width: 70%"></div>
              </div>
              <span class="progress-description">
                Created On <?php echo $userInfo->createdon; ?>
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Users Last 5 Sessions</h3>
            </div>
            <div class="card-body">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>NAS-IP</th>
                    <th>Start</th>
                    <th>Stop</th>
                    <th>Location</th>
                    <th>MAC</th>
                    <th>Session</th>
                    <th>Download</th>
                    <th>Upload</th>
                    <th>View</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if (!empty($connectionInfo)) {
                    $row_count = 0;
                    foreach ($connectionInfo as $record) {
                  ?>
                      <tr>
                        <td><?php echo $record->nasipaddress; ?></td>
                        <td><?php echo $record->acctstarttime; ?></td>
                        <td><?php echo $record->acctstoptime; ?></td>
                        <td><?php echo $record->calledstationid; ?></td>
                        <td><?php echo $record->callingstationid; ?></td>
                        <td><?php echo round(($record->acctsessiontime)/60, 0)." Minutes"; ?></td>
                        <td><?php echo round((($record->acctoutputoctets)/1024)/1024, 0)." MB"; ?></td>
                        <td><?php echo round((($record->acctinputoctets)/1024)/1024, 0)." MB"; ?></td>
                        <td class="text-center">
                          <a class="btn btn-sm btn-info" href="<?= base_url() . 'usersDashboard/' . $record->username; ?>" title="View"><i class="fas fa-eye"></i></a>
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
        </div>
      </div>
    </div>
  </section>
</div>
