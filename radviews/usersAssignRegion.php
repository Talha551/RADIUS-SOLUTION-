<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-map-marker-alt"></i> Assign Region/Location to Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Assign Region/Location</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Users Missing Region/Location</h3>
                </div>
                <div class="card-body">
                    <!-- Filter/Search Form -->
                    <form method="get" class="mb-3" id="filter-form">
                        <div class="row mb-0">
                            <?php if (!empty($managerList)) : ?>
                            <div class="col-md-2"><label>Manager</label></div>
                            <?php endif; ?>
                            <div class="col-md-2"><label>Username</label></div>
                            <div class="col-md-6"><label>Address</label></div>
                        </div>
                        <div class="row mb-3">
                            <?php if (!empty($managerList)) : ?>
                            <div class="col-md-2">
                                <select name="manager" class="form-control">
                                    <option value="">All Managers</option>
                                    <?php foreach($managerList as $mgr): ?>
                                        <option value="<?php echo htmlspecialchars($mgr->managername); ?>" <?php if($filters['manager'] == $mgr->managername) echo 'selected'; ?>><?php echo htmlspecialchars($mgr->managername); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            <div class="col-md-2">
                                <input type="text" name="username" class="form-control" placeholder="Username" value="<?php echo htmlspecialchars($filters['username']); ?>">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="address" class="form-control" placeholder="Address" value="<?php echo htmlspecialchars($filters['address']); ?>">
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-md-2"><label>Expiration From</label></div>
                            <div class="col-md-2"><label>Expiration To</label></div>
                            <div class="col-md-2"><label>Created From</label></div>
                            <div class="col-md-2"><label>Created To</label></div>
                            <div class="col-md-4"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <input type="date" name="expiration_from" class="form-control" placeholder="Exp. From" value="<?php echo htmlspecialchars($filters['expiration_from']); ?>">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="expiration_to" class="form-control" placeholder="Exp. To" value="<?php echo htmlspecialchars($filters['expiration_to']); ?>">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="created_from" class="form-control" placeholder="Created From" value="<?php echo htmlspecialchars($filters['created_from']); ?>">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="created_to" class="form-control" placeholder="Created To" value="<?php echo htmlspecialchars($filters['created_to']); ?>">
                            </div>
                            <div class="col-md-4 d-flex align-items-end justify-content-end">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Search</button>
                            </div>
                        </div>
                    </form>
                    <!-- End Filter/Search Form -->
                    <form id="bulk-assign-form">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <select class="form-control" id="bulk-region" name="bulk-region">
                                    <option value="">Bulk Assign Region</option>
                                    <?php foreach($regions as $region): ?>
                                        <option value="<?php echo $region->segmentid; ?>"><?php echo htmlspecialchars($region->segmentname); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" id="bulk-parameter" name="bulk-parameter">
                                    <option value="">Bulk Assign Location</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-success" id="bulk-assign-btn"><i class="fas fa-save"></i> Assign Selected</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all"></th>
                                        <th>Username</th>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Owner</th>
                                        <th>Expiration</th>
                                        <th>Region</th>
                                        <th>Location</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($users as $user): ?>
                                    <tr>
                                        <td><input type="checkbox" class="user-checkbox" value="<?php echo $user->username; ?>"></td>
                                        <td><?php echo htmlspecialchars($user->username); ?></td>
                                        <td><?php echo htmlspecialchars($user->firstname . ' ' . $user->lastname); ?></td>
                                        <td><?php echo htmlspecialchars($user->mobile); ?></td>
                                        <td><?php echo htmlspecialchars($user->owner); ?></td>
                                        <td><?php echo htmlspecialchars($user->expiration); ?></td>
                                        <td>
                                            <select class="form-control region-select" data-username="<?php echo $user->username; ?>">
                                                <option value="">Select Region</option>
                                                <?php foreach($regions as $region): ?>
                                                    <option value="<?php echo $region->segmentid; ?>" <?php if($user->segmentid == $region->segmentid) echo 'selected'; ?>><?php echo htmlspecialchars($region->segmentname); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control parameter-select" data-username="<?php echo $user->username; ?>">
                                                <option value="">Select Location</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm save-individual-btn" data-username="<?php echo $user->username; ?>"><i class="fas fa-save"></i> Save</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                    <!-- Card footer pagination as per project standard -->
                    <div class="card-footer clearfix">
                        <ul class="pagination pagination-sm m-0 float-right">
                            <?php echo $pagination; ?>
                        </ul>
                    </div>
                    <!-- End card footer pagination -->
                </div>
            </div>
        </div>
    </section>
</div>


<script>
$(document).ready(function() {
    // Select all checkboxes
    $('#select-all').on('change', function() {
        $('.user-checkbox').prop('checked', this.checked);
    });

    // Load parameters for region (bulk)
    $('#bulk-region').on('change', function() {
        var segmentid = $(this).val();
        if(segmentid) {
            $.ajax({
                url: '<?php echo base_url(); ?>Userslist/getSegmentDetails',
                type: 'POST',
                data: {segmentid: segmentid},
                success: function(data) {
                    $('#bulk-parameter').html('<option value="">Bulk Assign Location</option>' + data);
                }
            });
        } else {
            $('#bulk-parameter').html('<option value="">Bulk Assign Location</option>');
        }
    });

    // Load parameters for each user row
    $('.region-select').on('change', function() {
        var segmentid = $(this).val();
        var row = $(this).closest('tr');
        var parameterSelect = row.find('.parameter-select');
        if(segmentid) {
            $.ajax({
                url: '<?php echo base_url(); ?>Userslist/getSegmentDetails',
                type: 'POST',
                data: {segmentid: segmentid},
                success: function(data) {
                    parameterSelect.html('<option value="">Select Location</option>' + data);
                }
            });
        } else {
            parameterSelect.html('<option value="">Select Location</option>');
        }
    });

    // Bulk assign button
    $('#bulk-assign-btn').on('click', function(e) {
        e.preventDefault();
        var usernames = $('.user-checkbox:checked').map(function(){ return $(this).val(); }).get();
        var segmentid = $('#bulk-region').val();
        var parameter = $('#bulk-parameter').val();
        if(usernames.length === 0) {
            alert('Please select at least one user.');
            return;
        }
        if(!segmentid && !parameter) {
            alert('Please select a region or location to assign.');
            return;
        }
        $.ajax({
            url: '<?php echo base_url(); ?>Userslist/bulkAssignRegionParameter',
            type: 'POST',
            dataType: 'json',
            data: {usernames: usernames, segmentid: segmentid, parameter: parameter},
            success: function(resp) {
                if(resp.success) {
                    alert(resp.message);
                    location.reload();
                } else {
                    alert(resp.message);
                }
            }
        });
    });

    // Individual save button
    $('.save-individual-btn').on('click', function() {
        var username = $(this).data('username');
        var row = $(this).closest('tr');
        var segmentid = row.find('.region-select').val();
        var parameter = row.find('.parameter-select').val();
        if(!segmentid && !parameter) {
            alert('Please select a region or location to assign.');
            return;
        }
        $.ajax({
            url: '<?php echo base_url(); ?>Userslist/bulkAssignRegionParameter',
            type: 'POST',
            dataType: 'json',
            data: {usernames: [username], segmentid: segmentid, parameter: parameter},
            success: function(resp) {
                if(resp.success) {
                    alert(resp.message);
                    location.reload();
                } else {
                    alert(resp.message);
                }
            }
        });
    });
});

</script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>


<script type="text/javascript">
$(document).ready(function(){
  $('ul.pagination li a').click(function (e) {
    e.preventDefault();
    var link = $(this).get(0).href;
    var value = link.substring(link.lastIndexOf('/') + 1);
    // If value is not a number or is empty, go to base page
    if (!value || isNaN(value)) {
      $("#filter-form").attr("action", baseURL + "usersAssignRegion/");
    } else {
      $("#filter-form").attr("action", baseURL + "usersAssignRegion/" + value + "/");
    }
    $("#filter-form").submit();
  });
});
</script> 

