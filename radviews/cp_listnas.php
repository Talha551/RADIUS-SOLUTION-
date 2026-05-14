<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  

  <section class="content">
    <div class="row">
      <div class="col-lg-10">
        <div class="box box-solid box-primary">

          <div class="box-header">
            <h3 class="box-title">List of NAS</h3>    

          </div>
          <section class="content">

            <div class="col-xs-6 text-left">
              <div class="form-group">
                <div class="row">
                  <a class="btn btn-primary" href="<?php echo base_url(); if($this->session->userdata ( 'name' ) == 'admin'){ echo "nasadd"; } ?>"><i class="fa fa-plus"></i> Add New</a>
                </div>
              </div>
            </div>

            <embed type="text/html" src="master/admin.php?cont=list_nases" style="width:100%;height:95vh;">

        </div>

      </div>

    </div>

  </section>

</div>
