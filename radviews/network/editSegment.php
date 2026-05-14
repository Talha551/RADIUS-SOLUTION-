<?php
    $this->load->helper('url');
    $base = base_url() . index_page();
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-network-wired"></i> Edit Network Segment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Edit Network Segment</li>
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
                            <h3 class="card-title">Edit Network Segment</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo base_url(); ?>Network_controller/updateSegment" method="POST" id="editSegment">
                                <input type="hidden" name="segmentid" value="<?php echo $segmentInfo->segmentid; ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="segmentname">Segment Name</label>
                                            <input type="text" class="form-control" id="segmentname" name="segmentname" value="<?php echo $segmentInfo->segmentname; ?>" required readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="segmenttype">Segment Type</label>
                                            <select class="form-control" id="segmenttype" name="segmenttype" required>
                                                <option value="">Select Type</option>
                                                <?php foreach($segmentTypes as $key => $value) { ?>
                                                    <option value="<?php echo $key; ?>" <?php echo ($key == $segmentInfo->segmenttype) ? 'selected' : ''; ?>><?php echo $value; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="managername">Manager</label>
                                            <select class="form-control" id="managername" name="managername" required>
                                                <option value="">Select Manager</option>
                                                <?php echo ($this->session->userdata ( 'name' ) == 'admin') ? '<option value="default"' . ((isset($segmentInfo->managername) && $segmentInfo->managername == 'default') ? ' selected' : '') . '>Default</option>' : ''?>
                                                <?php foreach($managers as $manager) { ?>
                                                    <option value="<?php echo $manager->managername; ?>" <?php echo (isset($segmentInfo->managername) && $manager->managername == $segmentInfo->managername) ? 'selected' : ''; ?>><?php echo $manager->managername; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="activationdate">Activation Date</label>
                                            <input type="date" class="form-control" id="activationdate" name="activationdate" value="<?php echo date('Y-m-d', strtotime($segmentInfo->activationdate)); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="isgroup">Is Group</label>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="isgroup" name="isgroup" value="1" <?php echo ($segmentInfo->isgroup == 1) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="isgroup">Group</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mastersegmentid">Parent Segment</label>
                                            <select class="form-control" id="mastersegmentid" name="mastersegmentid">
                                                <option value="">Select Parent Segment</option>
                                                <?php foreach($parentSegments as $segment) { 
                                                    if ($segment->segmentid == $segmentInfo->segmentid) continue; // Skip self
                                                ?>
                                                    <option value="<?php echo $segment->segmentid; ?>" <?php echo ($segment->segmentid == $segmentInfo->mastersegmentid) ? 'selected' : ''; ?>><?php echo $segment->segmentname; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="details">Details</label>
                                            <textarea class="form-control" id="details" name="details" rows="3"><?php echo $segmentInfo->details; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Segment Details</h4>
                                        <div id="segmentDetails">
                                            <?php 
                                            if(!empty($segmentDetails)) {
                                                $count = 0;
                                                foreach($segmentDetails as $detail) {
                                            ?>
                                            <div class="row detail-row align-items-center">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="segmentdetails[<?php echo $count; ?>][parameter]" value="<?php echo htmlspecialchars($detail->parameter); ?>" placeholder="Parameter" required
                                                        <?php
                                                            if ($this->session->userdata('name') == 'admin') {
                                                                echo '';
                                                            } else {
                                                                echo (!empty($detail->parameter)) ? 'readonly' : '';
                                                            }
                                                        ?>>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <select class="form-control" name="segmentdetails[<?php echo $count; ?>][parametertype]" required>
                                                            <option value="0" <?php echo (isset($detail->parametertype) && $detail->parametertype == '0') ? 'selected' : ''; ?>>Location</option>
                                                            <option value="1" <?php echo (isset($detail->parametertype) && $detail->parametertype == '1') ? 'selected' : ''; ?>>Type</option>
                                                            <option value="2" <?php echo (isset($detail->parametertype) && $detail->parametertype == '2') ? 'selected' : ''; ?>>IP-Address</option>
                                                            <option value="3" <?php echo (isset($detail->parametertype) && $detail->parametertype == '3') ? 'selected' : ''; ?>>Domain</option>
                                                            <option value="4" <?php echo (isset($detail->parametertype) && $detail->parametertype == '4') ? 'selected' : ''; ?>>Port</option>
                                                            <option value="5" <?php echo (isset($detail->parametertype) && $detail->parametertype == '5') ? 'selected' : ''; ?>>Username</option>
                                                            <option value="6" <?php echo (isset($detail->parametertype) && $detail->parametertype == '6') ? 'selected' : ''; ?>>Password</option>
                                                            <option value="7" <?php echo (isset($detail->parametertype) && $detail->parametertype == '7') ? 'selected' : ''; ?>>SSH</option>
                                                            <option value="8" <?php echo (isset($detail->parametertype) && $detail->parametertype == '8') ? 'selected' : ''; ?>>SNMP</option>
                                                            <option value="9" <?php echo (isset($detail->parametertype) && $detail->parametertype == '9') ? 'selected' : ''; ?>>API</option>
                                                            <option value="10" <?php echo (isset($detail->parametertype) && $detail->parametertype == '10') ? 'selected' : ''; ?>>Key</option>
                                                            <option value="11" <?php echo (isset($detail->parametertype) && $detail->parametertype == '11') ? 'selected' : ''; ?>>Wireguard</option>
                                                            <option value="12" <?php echo (isset($detail->parametertype) && $detail->parametertype == '12') ? 'selected' : ''; ?>>SSTP</option>
                                                            <option value="13" <?php echo (isset($detail->parametertype) && $detail->parametertype == '13') ? 'selected' : ''; ?>>PTP</option>
                                                            <option value="14" <?php echo (isset($detail->parametertype) && $detail->parametertype == '14') ? 'selected' : ''; ?>>L2TP</option>
                                                            <option value="15" <?php echo (isset($detail->parametertype) && $detail->parametertype == '15') ? 'selected' : ''; ?>>UID</option>
                                                            <option value="16" <?php echo (isset($detail->parametertype) && $detail->parametertype == '16') ? 'selected' : ''; ?>>ACS</option>
                                                            <option value="17" <?php echo (isset($detail->parametertype) && $detail->parametertype == '17') ? 'selected' : ''; ?>>VLAN</option>
                                                            <option value="18" <?php echo (isset($detail->parametertype) && $detail->parametertype == '18') ? 'selected' : ''; ?>>Other</option>

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="segmentdetails[<?php echo $count; ?>][paravalue1]" value="<?php echo htmlspecialchars($detail->paravalue1); ?>" placeholder="ParaValue1">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="segmentdetails[<?php echo $count; ?>][paravalue2]" value="<?php echo htmlspecialchars($detail->paravalue2); ?>" placeholder="ParaValue2">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="segmentdetails[<?php echo $count; ?>][paravalue3]" value="<?php echo htmlspecialchars($detail->paravalue3); ?>" placeholder="ParaValue3">
                                                    </div>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-center">
                                                    <div class="form-group flex-grow-1 mr-2">
                                                        <input type="text" class="form-control" name="segmentdetails[<?php echo $count; ?>][paravalue4]" value="<?php echo htmlspecialchars($detail->paravalue4); ?>" placeholder="ParaValue4">
                                                    </div>
                                                    <button type="button" class="btn btn-danger remove-detail ml-2" <?php echo ($count == 0) ? 'style="display:none;"' : ''; ?>><i class="fa fa-trash"></i></button>
                                                </div>
                                            </div>
                                            <?php
                                                    $count++;
                                                }
                                            } else {
                                            ?>
                                            <div class="row detail-row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="segmentdetails[0][parameter]" placeholder="Parameter" required
                                                        <?php
                                                            // Always editable for new row (no value yet)
                                                            echo '';
                                                        ?>>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <select class="form-control" name="segmentdetails[0][parametertype]" required>
                                                            <option value="0" <?php echo (isset($detail->parametertype) && $detail->parametertype == '0') ? 'selected' : ''; ?>>Location</option>
                                                            <option value="1" <?php echo (isset($detail->parametertype) && $detail->parametertype == '1') ? 'selected' : ''; ?>>Type</option>
                                                            <option value="2" <?php echo (isset($detail->parametertype) && $detail->parametertype == '2') ? 'selected' : ''; ?>>IP-Address</option>
                                                            <option value="3" <?php echo (isset($detail->parametertype) && $detail->parametertype == '3') ? 'selected' : ''; ?>>Domain</option>
                                                            <option value="4" <?php echo (isset($detail->parametertype) && $detail->parametertype == '4') ? 'selected' : ''; ?>>Port</option>
                                                            <option value="5" <?php echo (isset($detail->parametertype) && $detail->parametertype == '5') ? 'selected' : ''; ?>>Username</option>
                                                            <option value="6" <?php echo (isset($detail->parametertype) && $detail->parametertype == '6') ? 'selected' : ''; ?>>Password</option>
                                                            <option value="7" <?php echo (isset($detail->parametertype) && $detail->parametertype == '7') ? 'selected' : ''; ?>>SSH</option>
                                                            <option value="8" <?php echo (isset($detail->parametertype) && $detail->parametertype == '8') ? 'selected' : ''; ?>>SNMP</option>
                                                            <option value="9" <?php echo (isset($detail->parametertype) && $detail->parametertype == '9') ? 'selected' : ''; ?>>API</option>
                                                            <option value="10" <?php echo (isset($detail->parametertype) && $detail->parametertype == '10') ? 'selected' : ''; ?>>Key</option>
                                                            <option value="12" <?php echo (isset($detail->parametertype) && $detail->parametertype == '11') ? 'selected' : ''; ?>>Wireguard</option>
                                                            <option value="12" <?php echo (isset($detail->parametertype) && $detail->parametertype == '12') ? 'selected' : ''; ?>>SSTP</option>
                                                            <option value="13" <?php echo (isset($detail->parametertype) && $detail->parametertype == '13') ? 'selected' : ''; ?>>PTP</option>
                                                            <option value="14" <?php echo (isset($detail->parametertype) && $detail->parametertype == '14') ? 'selected' : ''; ?>>L2TP</option>
                                                            <option value="15" <?php echo (isset($detail->parametertype) && $detail->parametertype == '15') ? 'selected' : ''; ?>>UID</option>
                                                            <option value="16" <?php echo (isset($detail->parametertype) && $detail->parametertype == '16') ? 'selected' : ''; ?>>ACS</option>
                                                            <option value="17" <?php echo (isset($detail->parametertype) && $detail->parametertype == '17') ? 'selected' : ''; ?>>VLAN</option>
                                                            <option value="18" <?php echo (isset($detail->parametertype) && $detail->parametertype == '18') ? 'selected' : ''; ?>>Other</option>

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
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="segmentdetails[0][paravalue4]" placeholder="ParaValue4">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger remove-detail" style="display:none;"><i class="fa fa-trash"></i></button>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <button type="button" class="btn btn-success" id="addDetail"><i class="fa fa-plus"></i> Add Detail</button>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">Update</button>
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
        var detailCount = <?php echo !empty($segmentDetails) ? count($segmentDetails) : 1; ?>;
        
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
                name = name.replace(/\[\d+\]/, '[' + detailCount + ']');
                $(this).attr('name', name);
            });
            // Remove readonly from parameter field for new row (for non-admins)
            newRow.find('input[name$="[parameter]"]').removeAttr('readonly');
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
        jQuery('#editSegment').on('submit', function(e) {
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