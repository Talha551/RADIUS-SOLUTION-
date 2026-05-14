<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> Users Data Import Form
        <small> Format File Fields (username, password, package, firstname, lastname, address, emailaddress, mobileno, cnic)</small>
      </h1>
    </section>

    <section class="content">

        
                <div class="container" style="margin-top:50px">
                    <br>
        
                    <?php if (isset($error)): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($this->session->flashdata('success') == TRUE): ?>
                        <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
                    <?php endif; ?>
        
                    <h2>IMPORT USERS </h2>


                    <form enctype="multipart/form-data" method="post" action="<?php echo base_url() ?>usersimportReview/review" role="form">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="manager">Manager</label>
                                <select class="form-control required" id="manager" name="manager">
                                    <option value="0">Select Manager</option>
                                    <?php
                                        if(!empty($managername))
                                        {
                                            foreach ($managername as $rl)
                                            {
                                                ?>
                                                <option value="<?php echo $rl->managername ?>"><?php echo $rl->managername ?></option>
                                                <?php
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="service">Packages</label>
                                        <select class="form-control required" id="service" name="service">
                                            <option value="0">Select Package</option>
 
                                                <option value=""></option>

                                        </select>
                                    </div>
                                </div>
                        </div>

                        <br><br>
                        <br><br>
                       
                        <div class="form-group">
                        <label for="exampleInputFile">File Upload</label>
                        <input type="file" name="file" id="file" size="150">
                        <p class="help-block">Only Excel/CSV File Import.</p>
                        </div>
                        <button type="submit" class="btn btn-default" name="submit" value="submit">Upload</button>
                        </form>

                        <br><br>
        
        
                    <footer>
                        <p>&copy;Users Import</p>
                    </footer>
        
                </div>

        </section>

</div>

<script>

$(document).ready(function(){

    $('#manager').change(function(){

        var manager_id = $('#manager').val();

        if(manager_id != '')
        {
            $.ajax({
                url:"<?php echo base_url('/Usersimport_controller/getManagerPackages'); ?>",
                method:"POST",
                data:{manager_id:manager_id},
                dataType:"JSON",
                success:function(data)
                {
                    var html = '<option value="">Select Package</option>';

                    for(var count = 0; count < data.length; count++)
                    {

                        html += '<option value="'+data[count].srvid+'">'+data[count].srvname+'</option>';

                    }

                    $('#service').html(html);
                }
            });
        }
    });

});

</script>
