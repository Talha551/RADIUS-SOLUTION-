<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-network-wired"></i> Add VLAN Interface
                        <small>Create new VLAN on selected interface</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url('Mikrotik/Mikrotik_api'); ?>">Interface Monitor</a></li>
                        <li class="breadcrumb-item active">Add VLAN</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <!-- Flash messages -->
            <div class="row">
                <div class="col-md-12">
                    <?php
                        $this->load->helper('form');
                        $error = $this->session->flashdata('error');
                        if($error)
                        {
                    ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('error'); ?>                    
                    </div>
                    <?php } ?>
                    <?php  
                        $success = $this->session->flashdata('success');
                        if($success)
                        {
                    ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
            
            <!-- VLAN Configuration Form -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">VLAN Configuration</h3>
                        </div>
                        <div class="card-body">
                            <form id="addVlanForm" method="post" action="<?php echo base_url('Mikrotik/Mikrotik_api/addVlan'); ?>">
                                <input type="hidden" name="router_id" value="<?php echo $router_id; ?>">
                                <input type="hidden" name="interface_name" value="<?php echo $interface_name; ?>">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="vlan_name">VLAN Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="vlan_name" name="vlan_name" 
                                                   placeholder="e.g., vlan100" required>
                                            <small class="form-text text-muted">Enter a descriptive name for the VLAN interface</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="vlan_id">VLAN ID <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="vlan_id" name="vlan_id" 
                                                   min="1" max="4094" placeholder="1-4094" required>
                                            <small class="form-text text-muted">VLAN ID must be between 1 and 4094</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="vlan_comment">Comment</label>
                                            <input type="text" class="form-control" id="vlan_comment" name="vlan_comment" 
                                                   placeholder="Optional description">
                                            <small class="form-text text-muted">Optional comment for the VLAN interface</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="vlan_mtu">MTU</label>
                                            <input type="number" class="form-control" id="vlan_mtu" name="vlan_mtu" 
                                                   min="68" max="9000" placeholder="1500">
                                            <small class="form-text text-muted">Maximum Transmission Unit (default: 1500)</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="vlan_arp">ARP Mode</label>
                                            <select class="form-control" id="vlan_arp" name="vlan_arp">
                                                <option value="enabled">Enabled</option>
                                                <option value="disabled">Disabled</option>
                                                <option value="proxy-arp">Proxy ARP</option>
                                                <option value="reply-only">Reply Only</option>
                                            </select>
                                            <small class="form-text text-muted">Address Resolution Protocol mode</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="vlan_use_service_tag">Use Service Tag</label>
                                            <select class="form-control" id="vlan_use_service_tag" name="vlan_use_service_tag">
                                                <option value="false">No</option>
                                                <option value="true">Yes</option>
                                            </select>
                                            <small class="form-text text-muted">Use IEEE 802.1ad compatible service tag</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="vlan_disabled" name="vlan_disabled" value="true">
                                        <label class="custom-control-label" for="vlan_disabled">Create VLAN in disabled state</label>
                                    </div>
                                    <small class="form-text text-muted">Check this if you want to create the VLAN but keep it disabled initially</small>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-plus"></i> Create VLAN Interface
                                    </button>
                                    <a href="<?php echo base_url('Mikrotik/Mikrotik_api'); ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to Interface Monitor
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Interface Information</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Router:</strong></td>
                                    <td><?php echo $router_name; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Interface:</strong></td>
                                    <td><span class="badge badge-info"><?php echo $interface_name; ?></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Interface Type:</strong></td>
                                    <td><?php echo $interface_type; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <?php if($interface_status == 'Yes'): ?>
                                            <span class="badge badge-success">Running</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Stopped</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">VLAN Guidelines</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-info-circle text-info"></i> VLAN ID must be unique</li>
                                <li><i class="fas fa-info-circle text-info"></i> VLAN name should be descriptive</li>
                                <li><i class="fas fa-info-circle text-info"></i> MTU should not exceed parent interface</li>
                                <li><i class="fas fa-info-circle text-info"></i> Consider network security implications</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- jQuery Validation Plugin -->
<script src="<?php echo base_url(); ?>assets/plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/jquery-validation/additional-methods.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    // Form validation
    $('#addVlanForm').validate({
        rules: {
            vlan_name: {
                required: true,
                minlength: 2,
                maxlength: 32,
                pattern: /^[a-zA-Z0-9_-]+$/
            },
            vlan_id: {
                required: true,
                min: 1,
                max: 4094,
                digits: true
            },
            vlan_mtu: {
                min: 68,
                max: 9000,
                digits: true
            }
        },
        messages: {
            vlan_name: {
                required: "VLAN name is required",
                minlength: "VLAN name must be at least 2 characters",
                maxlength: "VLAN name cannot exceed 32 characters",
                pattern: "VLAN name can only contain letters, numbers, hyphens, and underscores"
            },
            vlan_id: {
                required: "VLAN ID is required",
                min: "VLAN ID must be at least 1",
                max: "VLAN ID cannot exceed 4094",
                digits: "VLAN ID must be a number"
            },
            vlan_mtu: {
                min: "MTU must be at least 68",
                max: "MTU cannot exceed 9000",
                digits: "MTU must be a number"
            }
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        },
        submitHandler: function(form) {
            // Show loading state
            $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating VLAN...');
            
            // Submit form via AJAX
            $.ajax({
                url: form.action,
                type: 'POST',
                data: $(form).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            // Redirect back to interface monitor
                            window.location.href = '<?php echo base_url('Mikrotik/Mikrotik_api'); ?>';
                        });
                    } else {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.error,
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error!',
                        text: 'Failed to create VLAN. Please try again.',
                        confirmButtonText: 'OK'
                    });
                },
                complete: function() {
                    // Reset button state
                    $('#submitBtn').prop('disabled', false).html('<i class="fas fa-plus"></i> Create VLAN Interface');
                }
            });
            
            return false; // Prevent form submission
        }
    });
    
    // Auto-generate VLAN name based on VLAN ID
    $('#vlan_id').on('input', function() {
        var vlanId = $(this).val();
        var vlanName = $('#vlan_name').val();
        
        // Only auto-generate if VLAN name is empty or matches the old pattern
        if (vlanName === '' || vlanName.match(/^vlan\d+$/)) {
            if (vlanId && vlanId >= 1 && vlanId <= 4094) {
                $('#vlan_name').val('vlan' + vlanId);
            }
        }
    });
    
    // Check for existing VLANs (optional enhancement)
    $('#vlan_id, #vlan_name').on('blur', function() {
        var vlanId = $('#vlan_id').val();
        var vlanName = $('#vlan_name').val();
        
        if (vlanId && vlanName) {
            // You could add AJAX call here to check if VLAN already exists
            // This would require additional backend functionality
        }
    });
});
</script>

<!-- SweetAlert2 for better alerts -->
<script src="<?php echo base_url(); ?>assets/plugins/sweetalert2/sweetalert2.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/sweetalert2/sweetalert2.min.css">
