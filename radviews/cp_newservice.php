<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  

  <section class="content">
    <div class="row">
      <div class="col-lg-10">
        <div class="box box-solid box-primary">

          <div class="box-header">
            <h3 class="box-title">Profile New</h3>    

          </div>
          <section class="content">

            <div class="col-xs-6 text-left">
              <div class="form-group">
                <div class="row">
                  <a class="btn btn-primary" href="<?php echo base_url(); if($this->session->userdata ( 'name' ) == 'admin'){ echo "profilelist"; } ?>"><i class="fa fa-plus"></i> List Profile</a>
                </div>
              </div>
            </div>

            <embed type="text/html" src="master/admin.php?cont=new_service" style="width:100%;height:130vh;">

        </div>

      </div>

    </div>

  </section>

</div>
