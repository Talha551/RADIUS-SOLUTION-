
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> Users File Import Review
        <small> Format File Fields (username, password, firstname, lastname, address, emailaddress, mobileno, cnic)</small>
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
        
                    <h2>REVIEW DATA</h2>
                    <form enctype="multipart/form-data" method="post" action="<?php echo base_url() ?>usersimportReview/insertdata" role="form">

                    <div class="box-body">

                        <div class="row">
                                <div class="row">
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                        <label for="manager">Manager Name</label>
                                            <input type="label" class="form-control" id="manager" placeholder="manager" name="manager" value="<?php echo $manager; ?>" maxlength="50" readonly>
                                            <input type="hidden" value="<?php echo $manager; ?>" name="manager" id="manager" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                        <label for="service_id">ServiceID</label>
                                            <input type="label" class="form-control" id="service_id" placeholder="Service ID" name="service_id" value="<?php echo $service_id; ?>" maxlength="50" readonly>
                                            <input type="hidden" value="<?php echo $service_id; ?>" name="service_id" id="service_id" />
                                        </div>
                                    </div>
                                </div>
                    </div>

                    </div>
                    <button type="submit" class="btn btn-default" id="upload_data" name="submit" value="submit">Upload</button>
                    </form>

                    <br><br>
                    <table class="table table-striped table-hover table-bordered" id="importTable">
                        <caption>Easy Paisa File Import</caption>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Password</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Address</th>
                                <th>Mobile</th>
                                <th>CNIC</th>
                                <th>SNo.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 

                            //print_r($userdata);

                            if ($userdata == FALSE): ?>
                                <tr><td colspan="4">There are currently No Data Or Duplicate Data</td></tr>
                            <?php else: ?>
                                <?php $sno = 1; ?>
                                <?php foreach ($userdata as $row): ?>
                                    <tr>
                                        
                                        <td><?php echo $row['username']; ?></td>
                                        <td><?php echo $row['password']; ?></td>
                                        <td><?php echo $row['firstname']; ?></td>
                                        <td><?php echo $row['lastname']; ?></td>
                                        <td><?php echo $row['address']; ?></td>
                                        <td><?php echo $row['mobile']; ?></td>
                                        <td><?php echo $row['taxid']; ?></td>
                                        <td><?php echo $sno; ?></td>
                                    </tr>
                                    <?php $sno = $sno +1; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <p><input type="button" id="bt" value="Create Users Profiles" onclick="savedata1()" /></p>

        
        
                    <footer>
                        <p>Review and submit data</p>
                    </footer>
        
                </div>

        </section>

</div>

<script>

    function savedata_nw() { 

        var manager_id = $('#manager').val();
        var service_id = $('#service_id').val();

        var obj = $('#importTable tbody tr').map(function() {
            
            var $row = $(this);
            var username = $row.find(':nth-child(1)').text();
            var password = $row.find(':nth-child(2)').text();
            var firstname = $row.find(':nth-child(3)').text();
            var lastname = $row.find(':nth-child(4)').text();
            var address = $row.find(':nth-child(5)').text();
            var mobile = $row.find(':nth-child(6)').text();
            var cnic = $row.find(':nth-child(7)').text();

            console.log("Data Read:"+username+" Password:"+password);

            $.ajax({
                    url:"<?php echo base_url('/Usersimport_controller/usersCreateNewFromCSV'); ?>",
                    method:"POST",
                    data:{manager_id:manager_id, service_id:service_id, username:username, password:password, firstname:firstname, lastname:lastname, address:address, mobile:mobile, cnic:cnic,
                    dataType:"JSON",
                    success:function(data)
                    {
                        console.log("Data Imported");
                        alert("Data Imported and users created...");
                        window.location.href = "<?php echo base_url('usersListing'); ?>";
                    }
                }});
        
        }).get();

        //window.location.href = '/dashboard';

    }

    function savedata1() { 
        var manager_id = $('#manager').val();
        var service_id = $('#service_id').val();

        // Get the number of rows in the table
        var numRows = $('#importTable tbody tr').length;

        let i = 0;
        while (i < numRows) {
            var batchRows = $('#importTable tbody tr').slice(i, Math.min(i + 10, numRows));
            $.each(batchRows, function(index, value) {
                var $row = $(value);
                var username = $row.find(':nth-child(1)').text();
                var password = $row.find(':nth-child(2)').text();
                var firstname = $row.find(':nth-child(3)').text();
                var lastname = $row.find(':nth-child(4)').text();
                var address = $row.find(':nth-child(5)').text();
                var mobile = $row.find(':nth-child(6)').text();
                var cnic = $row.find(':nth-child(7)').text();

                $.ajax({
                    url: "<?php echo base_url('/Usersimport_controller/usersCreateNewFromCSV'); ?>",
                    method: "POST",
                    data: {
                        manager_id: manager_id,
                        service_id: service_id,
                        username: username,
                        password: password,
                        firstname: firstname,
                        lastname: lastname,
                        address: address,
                        mobile: mobile,
                        cnic: cnic
                    },
                    dataType: "JSON",
                    success: function(data) {
                        console.log("Data Imported");
                        window.location.href = "<?php echo base_url('usersListing'); ?>";
                    }
                });
            });
            i += 10;
        }

        alert("Data Imported and users created...");
    }

</script>
