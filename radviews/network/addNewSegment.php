<?php
    $this->load->helper('url');
    $base = base_url() . index_page();
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-network-wired"></i> Add New Network Segment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Add Network Segment</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">


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
                    
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo validation_errors('<div class="alert alert-danger alert-dismissible">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Add New Network Segment</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo base_url(); ?>Network_controller/saveSegment" method="POST" id="addSegment">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="segmentname">Segment Name</label>
                                            <input type="text" class="form-control" id="segmentname" name="segmentname" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="segmenttype">Segment Type</label>
                                            <select class="form-control" id="segmenttype" name="segmenttype" required>
                                                <option value="">Select Type</option>
                                                <?php foreach($segmentTypes as $key => $value) { ?>
                                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="managername">Manager</label>
                                            
                                            <?php if(!empty($managers)) { ?>
                                            <select class="form-control" id="managername" name="managername" required>
                                                <option value="">Select Manager</option>
                                                <?php echo ($this->session->userdata ( 'name' ) == 'admin') ? 
                                                    '<option value="default">Default</option>' : ''?>
                                                <?php foreach($managers as $manager) { ?>
                                                    <option value="<?php echo $manager->managername; ?>"><?php echo $manager->managername; ?></option>
                                                <?php } ?>
                                            </select>
                                            <?php } else { ?>
                                                <input type="text" class="form-control" id="managername" name="managername" value="<?php echo $this->session->userdata('name'); ?>" readonly>
                                            <?php } ?>


                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="activationdate">Activation Date</label>
                                            <input type="date" class="form-control" id="activationdate" name="activationdate" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="isgroup">Is Group</label>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="isgroup" name="isgroup" value="1">
                                                <label class="form-check-label" for="isgroup">Group</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mastersegmentid">Parent Segment</label>
                                            <select class="form-control" id="mastersegmentid" name="mastersegmentid">
                                                <option value="">Select Parent Segment</option>
                                                <?php foreach($parentSegments as $segment) { ?>
                                                    <option value="<?php echo $segment->segmentid; ?>"><?php echo $segment->segmentname; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="details">Details</label>
                                            <textarea class="form-control" id="details" name="details" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Segment Details</h4>
                                        <div id="segmentDetails">
                                            <div class="row detail-row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="segmentdetails[0][parameter]" placeholder="Parameter" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <select class="form-control" name="segmentdetails[0][parametertype]" required>
                                                            <option value="0">Location</option>
                                                            <option value="1">Type</option>
                                                            <option value="2">IP-Address</option>
                                                            <option value="3">Domain</option>
                                                            <option value="4">Port</option>
                                                            <option value="5">Username</option>
                                                            <option value="6">Password</option>
                                                            <option value="7">SSH</option>
                                                            <option value="8">SNMP</option>
                                                            <option value="9">API</option>
                                                            <option value="10">Key</option>
                                                            <option value="11">Wireguard</option>
                                                            <option value="12">SSTP</option>
                                                            <option value="13">PTP</option>
                                                            <option value="14">L2TP</option>
                                                            <option value="15">UID</option>
                                                            <option value="16">ACS</option>
                                                            <option value="17">VLAN</option>
                                                            <option value="18">Other</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="segmentdetails[0][paravalue1]" placeholder="ParaValue1">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="segmentdetails[0][paravalue2]" placeholder="ParaValue2">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="segmentdetails[0][paravalue3]" placeholder="ParaValue3">
                                                    </div>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-center">
                                                    <div class="form-group flex-grow-1 mr-2">
                                                        <input type="text" class="form-control" name="segmentdetails[0][paravalue4]" placeholder="ParaValue4">
                                                    </div>
                                                    <button type="button" class="btn btn-danger remove-detail ml-2" style="display:none;"><i class="fa fa-trash"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-success" id="addDetail"><i class="fa fa-plus"></i> Add Detail</button>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                        <a href="<?php echo base_url(); ?>Network_controller/segmentList" class="btn btn-default">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        var detailCount = 1;
        
        // Function to check for duplicate parameters
        function checkDuplicateParameter(newParameter, excludeIndex = -1) {
            var isDuplicate = false;
            $('.detail-row').each(function(index) {
                if (index !== excludeIndex) {
                    var existingParameter = $(this).find('input[name$="[parameter]"]').val();
                    if (existingParameter && existingParameter.toLowerCase() === newParameter.toLowerCase()) {
                        isDuplicate = true;
                        return false; // break the loop
                    }
                }
            });
            return isDuplicate;
        }

        // Function to validate and add new row
        function addNewDetailRow() {
            var newRow = jQuery('.detail-row:first').clone();
            newRow.find('input, select').val('');
            newRow.find('input, select').each(function(){
                var name = $(this).attr('name');
                name = name.replace(/\[0\]/, '[' + detailCount + ']');
                $(this).attr('name', name);
            });
            newRow.find('.remove-detail').show();
            jQuery('#segmentDetails').append(newRow);
            detailCount++;
        }

        // Add new detail row
        jQuery('#addDetail').on('click', function(){
            addNewDetailRow();
        });

        // Remove detail row
        jQuery(document).on('click', '.remove-detail', function(){
            jQuery(this).closest('.detail-row').remove();
        });

        // Validate parameter uniqueness on input change
        jQuery(document).on('change', 'input[name$="[parameter]"]', function() {
            var currentValue = $(this).val();
            var currentIndex = $(this).closest('.detail-row').index();
            
            if (checkDuplicateParameter(currentValue, currentIndex)) {
                alert('This parameter already exists. Please use a different parameter name.');
                $(this).val('');
                $(this).focus();
            }
        });

        // Form submission validation
        jQuery('#addSegment').on('submit', function(e) {
            var hasDuplicates = false;
            var parameters = [];
            
            $('.detail-row').each(function() {
                var parameter = $(this).find('input[name$="[parameter]"]').val();
                if (parameter) {
                    if (parameters.indexOf(parameter.toLowerCase()) > -1) {
                        hasDuplicates = true;
                        return false; // break the loop
                    }
                    parameters.push(parameter.toLowerCase());
                }
            });

            if (hasDuplicates) {
                e.preventDefault();
                alert('Please remove duplicate parameters before submitting the form.');
                return false;
            }
        });
    });
</script> 