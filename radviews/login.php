<!DOCTYPE html>
<html lang="en">

  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | System Lock</title>
    <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/adminlte.min.css">
  <!-- Custom styles -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/custom.css">
  <style>
    .login-input-wide {
      min-width: 260px;
      max-width: 350px;
      width: 100%;
      margin-bottom: 8px;
    }
    /* Autofill override for Chrome, Safari, Edge, Opera */
    input:-webkit-autofill,
    input:-webkit-autofill:focus,
    input:-webkit-autofill:hover,
    input:-webkit-autofill:active {
      -webkit-box-shadow: 0 0 0 1000px #fff inset !important;
      box-shadow: 0 0 0 1000px #fff inset !important;
      background-color: #fff !important;
      color: #222 !important;
      transition: background-color 5000s ease-in-out 0s;
    }
    /* Autofill override for Firefox */
    input:-moz-autofill {
      box-shadow: 0 0 0 1000px #fff inset !important;
      background-color: #fff !important;
      color: #222 !important;
    }
  </style>
  </head>

<body class="hold-transition lockscreen">
  <!-- Automatic element centering -->
  <div class="lockscreen-wrapper">
    <div class="lockscreen-logo">
      <a href="#"><b> Billing System</b></a>
    </div>
    <!-- User name -->
    <div class="lockscreen-name">Sign In</div>

    <!-- START LOCK SCREEN ITEM -->
    <div class="lockscreen-item">
      <!-- lockscreen image -->
      <div class="lockscreen-image">
        <img src="<?php echo base_url(); ?>assets/dist/img/logo1.png" alt="User Image">
      </div>
      <!-- /.lockscreen-image -->

      <!-- lockscreen credentials (contains the form) -->
      <form class="lockscreen-credentials" action="<?php echo base_url(); ?>loginManager" method="post">
        <div class="input-group">
          <input type="text" class="form-control login-input-wide" placeholder="Username" name="username" required autofocus>
          <input type="password" class="form-control login-input-wide" placeholder="Password" name="password" required style="margin-top:8px;">
          <div class="input-group-append">
            <button type="submit" class="btn">
              <i class="fas fa-arrow-right text-muted"></i>
            </button>
            </div>
        </div>
      </form>
      <!-- /.lockscreen credentials -->
    </div>
    <!-- /.lockscreen-item -->

    <!-- Error/Success Messages -->
    <div class="row mt-4">
      <div class="col-12">
        <?php $error = $this->session->flashdata('error');
        if ($error): ?>
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?php echo $error; ?>                    
            </div>
        <?php endif; ?>
        <?php $success = $this->session->flashdata('success');
        if ($success): ?>
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?php echo $success; ?>                    
            </div>
        <?php endif; ?>
          </div>
          </div>

    <div class="help-block text-center">
      Enter your username and password to sign in
          </div>
    <div class="text-center">
      <a href="<?php echo base_url(); ?>forgotPassword">Forgot Password?</a><br>
        
    </div>
    <div class="lockscreen-footer text-center">
      Copyright &copy; <?php echo date('Y'); ?> <b>RadSpot Billing</b><br>
      All rights reserved
    </div>
  </div>
  <!-- /.center -->

  <!-- jQuery -->
  <script src="<?php echo base_url(); ?>assets/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo base_url(); ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
      <!-- AdminLTE App -->
      <script src="<?php echo base_url(); ?>assets/dist/js/adminlte.min.js"></script>
  </body>

</html>