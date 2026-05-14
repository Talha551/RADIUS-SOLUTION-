      <footer class="main-footer">
          <div class="float-right d-none d-sm-block">
            <b>RadSpot</b> Admin System | Version 1.0
          </div>
          <strong>Copyright &copy; 2017-2024 <a href="https://portal.pace-tel.com/">RAD SPOT</a>.</strong> All rights reserved.
      </footer>
      
      <!-- Control Sidebar -->
      <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
      </aside>
      <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->



    <!-- REQUIRED SCRIPTS -->
    <!-- Bootstrap 4 -->
    <script src="<?php echo base_url(); ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="<?php echo base_url(); ?>assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo base_url(); ?>assets/dist/js/adminlte.min.js"></script>
    <!-- Validation scripts -->
    <script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/validation.js"></script>
    
    <!-- Active menu highlighting -->
    <script>
      $(document).ready(function() {
        var windowURL = window.location.href;
        var pageURL = window.location.href.substring(0, window.location.href.lastIndexOf('/'));
        
        $('a[href="'+pageURL+'"]').addClass('active').parents('.nav-item').addClass('menu-open').children('.nav-link').addClass('active');
        $('a[href="'+windowURL+'"]').addClass('active').parents('.nav-item').addClass('menu-open').children('.nav-link').addClass('active');
      });
    </script>
  </body>
</html>