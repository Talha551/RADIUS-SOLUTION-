<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Add Wireguard VPN User <small class="text-muted" style="font-size:18px;">Create new Wireguard VPN user</small></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('wireguard'); ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url('wireguard'); ?>">Wireguard VPN Users</a></li>
                        <li class="breadcrumb-item active">Add User</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title mb-0">Add New Wireguard VPN User</h3>
                </div>
                <div class="card-body">
                    <?php echo form_open('wireguard/add'); ?>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Search username..." required>
                            <div id="usernameResults" class="search-results"></div>
                        </div>
                        <div class="mb-3">
                            <label for="server_id" class="form-label">Wireguard Server</label>
                            <select class="form-control" id="server_id" name="server_id" required>
                                <option value="">Select Server</option>
                                <?php foreach($servers as $server): ?>
                                    <option value="<?php echo $server->segmentid; ?>">
                                        <?php echo $server->segmentname; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Create User</button>
                        <a href="<?php echo base_url('wireguard'); ?>" class="btn btn-default">Cancel</a>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    var searchTimeout;
    var selectedUsername = '';

    $('#username').on('keyup', function() {
        clearTimeout(searchTimeout);
        var searchTerm = $(this).val();

        if (searchTerm.length < 2) {
            $('#usernameResults').hide();
            return;
        }

        searchTimeout = setTimeout(function() {
            $.get('<?php echo base_url("wireguard/search_users"); ?>', { term: searchTerm }, function(data) {
                var results = JSON.parse(data);
                var html = '';
                
                if (results.length > 0) {
                    results.forEach(function(user) {
                        html += '<div class="search-result-item" data-username="' + user.username + '">' + 
                                user.username + ' (' + user.name + ')</div>';
                    });
                    $('#usernameResults').html(html).show();
                } else {
                    $('#usernameResults').hide();
                }
            });
        }, 300);
    });

    $(document).on('click', '.search-result-item', function() {
        selectedUsername = $(this).data('username');
        $('#username').val(selectedUsername);
        $('#usernameResults').hide();
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#username, #usernameResults').length) {
            $('#usernameResults').hide();
        }
    });
});
</script>

<style>
.search-results {
    position: absolute;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    max-height: 200px;
    overflow-y: auto;
    width: 100%;
    z-index: 1000;
    display: none;
}

.search-result-item {
    padding: 8px 12px;
    cursor: pointer;
}

.search-result-item:hover {
    background-color: #f5f5f5;
}
</style> 