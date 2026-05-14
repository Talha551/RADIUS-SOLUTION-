<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-users"></i> Cards Series List
                        <small>Add, Edit, Delete</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Cards Series</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <?php
            $nasListForSync = [];
            if (isset($nasList) && !empty($nasList)) {
                $nasListForSync = $nasList;
            } else {
                $CI =& get_instance();
                if (isset($CI->load)) {
                    $CI->load->database();
                    if (isset($CI->db)) {
                        $nasQuery = $CI->db->select('id, nasname, shortname')->from('nas')->order_by('shortname', 'ASC')->get();
                        if ($nasQuery) {
                            $nasListForSync = $nasQuery->result();
                        }
                    }
                }
            }
            ?>
            <div class="row">
                <div class="col-12 text-right">
                    <div class="form-group">
                        <a class="btn btn-primary" href="<?php echo base_url(); ?>genusers"><i class="fas fa-plus"></i> Generate Cards</a>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nasSelector">Select NAS</label>
                        <select id="nasSelector" class="form-control">
                            <option value="">-- Choose NAS --</option>
                            <?php if (!empty($nasListForSync)) { foreach ($nasListForSync as $nas) {
                                $nid = isset($nas->id) ? $nas->id : (isset($nas->nasid) ? $nas->nasid : '');
                                $short = isset($nas->shortname) ? $nas->shortname : (isset($nas->short) ? $nas->short : '');
                                $host = isset($nas->nasname) ? $nas->nasname : '';
                                echo '<option value="' . htmlspecialchars($nid, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($short . ' (' . $host . ')', ENT_QUOTES, 'UTF-8') . '</option>';
                            } } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-5 align-self-end">
                    <div class="alert alert-info py-2 mb-0">
                        Select a NAS, then use the Sync button to push batches to the selected NAS.
                    </div>
                </div>
                <div class="col-md-3 align-self-end text-right">
                    <button type="button" id="btnBulkSyncMikrotik" class="btn btn-success" disabled>
                        <i class="fas fa-network-wired"></i> Sync
                    </button>
                </div>
            </div>

            <div id="series-push-feedback" class="mb-3"></div>
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $this->load->helper('form');
                    $error = $this->session->flashdata('error');
                    if ($error) {
                    ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                    <?php } ?>
                    <?php
                    $success = $this->session->flashdata('success');
                    if ($success) {
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
                            <h3 class="card-title">Series List</h3>
                            
                            <div class="card-tools">
                                <form action="<?php echo base_url() ?>Userslist/usersBatchList" method="POST" id="searchList">
                                    <div class="input-group">
                                        <?php if ($this->session->userdata('name') <> 'admin' && $this->ismaster == 0) { ?>
                                            <input type="text" name="searchText" value="<?php echo $searchText; ?>" class="form-control form-control-sm" style="width: 150px; margin-left: 10px;" placeholder="Owner" readonly />
                                        <?php } else { ?>

                                            <select name="searchText" class="form-control form-control-sm" style="width: 150px; margin-left: 10px;">
                                                <option value="<?php echo $this->session->userdata('name'); ?>" <?php if ($this->session->userdata('name') == $searchText) {
                                                                                                                    echo 'selected=selected';
                                                                                                                } ?>><?php echo $this->session->userdata('name'); ?></option>
                                                <?php
                                                if (!empty($managerList)) {
                                                    $row_count = 0;
                                                    foreach ($managerList as $record) {
                                                        echo '<option value="';
                                                        echo $record->managername . '"';
                                                        if ($record->managername == $searchText) {
                                                            echo 'selected=selected';
                                                        }
                                                        echo '>';
                                                        echo $record->managername;
                                                        echo '</option>';
                                                        $row_count++;
                                                    }
                                                }
                                                ?>
                                            </select>

                                        <?php } ?>
                                        <input type="text" name="searchText1" id="searchText1" value="<?php echo $searchText1; ?>" class="form-control form-control-sm" style="width: 150px; margin-left: 10px;" autocomplete="off" placeholder="From Date(YYYY-MM-DD)" />
                                        <input type="text" name="searchText2" id="searchText2" value="<?php echo $searchText2; ?>" class="form-control form-control-sm" style="width: 150px; margin-left: 10px;" autocomplete="off" placeholder="To Date(YYYY-MM-DD)" />
                                        
                                        <select name="searchText3" class="form-control form-control-sm" style="width: 150px; margin-left: 10px;">
                                            <option value="0" <?php if ($searchText3 == 0) {
                                                                    echo "selected=selected";
                                                                } ?>>Group By - Series</option>
                                            <option value="1" <?php if ($searchText3 == 1) {
                                                                    echo "selected=selected";
                                                                } ?>>Group By - Owner</option>
                                            <option value="2" <?php if ($searchText3 == 2) {
                                                                    echo "selected=selected";
                                                                } ?>>Group By - Owner & Series</option>
                                            <option value="3" <?php if ($searchText3 == 3) {
                                                                    echo "selected=selected";
                                                                } ?>>Group By - Owner, Series & Month</option>
                                        </select>

                                        <div class="input-group-append">
                                            <button class="btn btn-sm btn-default" style="margin-right: 40px;"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div><!-- /.card-header -->

                        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
                        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

                        <script type="text/javascript">
                            $(function() {
                                $("#searchText1").datepicker({
                                    dateFormat: "yy-mm-dd"
                                });
                            });

                            $(function() {
                                $("#searchText2").datepicker({
                                    dateFormat: "yy-mm-dd"
                                });
                            });
                        </script>
                        
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Series</th>
                                        <th>Date</th>
                                        <th>Valid</th>
                                        <th>Owner</th>
                                        <th>Cards</th>
                                        <th>Price</th>
                                        <th>Type</th>
                                        <th>In Stock</th>
                                        <th>Sold</th>
                                        <th>Value</th>
                                        <th>Collect</th>
                                        <th class="text-center">Manage | Report</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($groupListing)) {
                                        $row_count = 1;
                                        $sales_value = 0;
                                        foreach ($groupListing as $record) {
                                    ?>
                                            <?php ($record->revoked > 0) ? $revokedColor = 'class="text-danger"' : $revokedColor = ''; ?>
                                            <?php ($record->unsold == 0) ? $allsoldColor = 'class="text-primary"' : $allsoldColor = ''; ?>

                                            <tr data-series="<?php echo htmlspecialchars($record->series, ENT_QUOTES, 'UTF-8'); ?>" <?php if($record->revoked > 0){echo $revokedColor;}elseif($record->unsold == 0){echo $allsoldColor;} ?>>
                                                <td><a class="btn btn-primary btn-block" href="<?php echo base_url() . 'userslist/usersCardsCleanList/' . $record->series; ?>" title="Edit User" role="button"><?php echo $record->series; ?></a></td>
                                                <td><?php echo $record->date ?></td>
                                                <td><?php echo $record->expiration ?></td>
                                                <td><?php if (!empty($record->owner)) {
                                                        echo $record->owner;
                                                    } else {
                                                        echo "admin";
                                                    } ?></td>
                                                <td><?php echo $record->cards ?></td>

                                                <?php ($record->timebaseexp == 3) ? $cardDuration = $record->expiretime." Month" : $cardDuration = $record->expiretime." Days" ; ?>
                                                
                                                <td><?php echo $record->price ?></td>
                                                
                                                <td><?php echo $cardDuration ?></td>

                                                <td><?php if($record->unsold == 0){ echo "ALL-SOLD"; }else{ echo $record->unsold; }?></td>

                                                <td><?php echo $record->sold ?></td>

                                                <td><?php echo round($record->value,0);
                                                            $sales_value = $sales_value + $record->value; ?></td>

                                                <?php (!empty($record->collection)) ? $activeColor = 'class="text-success"' : $activeColor = 'class="text-danger"'; ?>
                                                <td <?php echo $activeColor; ?>><?php if(!empty($record->collection)){ echo $record->collection; }else{ echo "0.00"; } ?></td>
                                                
                                                <td class="text-center">
                                                    <a class="btn btn-sm btn-primary" href="<?php echo base_url() . 'Invoices/batchpriceUpdate/' . $record->series; ?>" title="Change Price"><i class="fas fa-dollar-sign"></i></a> 
                                                    <a class="btn btn-sm btn-primary" href="<?php echo base_url() . 'Invoices/cardscollection_add/' . $record->series; ?>" title="Add Collection"><i class="fas fa-money-bill-alt"></i></a> 
                                                    
                                                    <?php ($record->revoked > 0)? $controllerMethod =  'Userslist/unlock_returncards/' : $controllerMethod = 'Userslist/revoke_returncards/' ; ?>
                                                    
                                                    <a class="btn btn-sm btn-primary" href="<?php echo base_url() . $controllerMethod . $record->series; ?>" title="Batch Lock & Unlock"><i class="<?php if($record->revoked > 0){ echo "fas fa-lock"; }else{ echo "fas fa-unlock"; }?>"></i></a> |
                                                    <a class="btn btn-sm btn-info" href="<?php echo base_url() . 'userslist/printCards/' . $record->series; ?>" title="Print"><i class="fas fa-print"></i></a>
                                                    <a class="btn btn-sm btn-info" href="<?php echo base_url() . 'userslist/export_cardstocsv/' . $record->series; ?>" title="Download"><i class="fas fa-download"></i></a>
                                                    <a class="btn btn-sm btn-info" href="<?php echo base_url() . 'userslist/usersCardsCleanList/' . $record->series; ?>" title="View Batch"><i class="fas fa-eye"></i></a>
                                                </td>
                                            </tr>
                                    <?php
                                            $row_count++;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div><!-- /.card-body -->
                        
                        <div class="card-footer clearfix">
                            <?php echo $this->pagination->create_links(); ?>
                        </div>
                    </div><!-- /.card -->
                </div>
            </div>
        </div>
    </section>
</div>

<input type="hidden" id="csrf_name" value="<?php echo $this->security->get_csrf_token_name(); ?>">
<input type="hidden" id="csrf_hash" value="<?php echo $this->security->get_csrf_hash(); ?>">

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('ul.pagination li a').click(function(e) {
            e.preventDefault();
            var link = jQuery(this).get(0).href;
            var value = link.substring(link.lastIndexOf('/') + 1);
            jQuery("#searchList").attr("action", baseURL + "serieslist/" + value);
            jQuery("#searchList").submit();
        });

        function setSyncButtonsState() {
            var hasNas = !!jQuery('#nasSelector').val();
            jQuery('#btnBulkSyncMikrotik').prop('disabled', !hasNas);
        }

        function escapeHtml(s) {
            return String(s).replace(/[&<>'"]/g, function(c) {
                return {'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c];
            });
        }

        function syncSeriesToMikrotik(nasId, seriesId, done) {
            console.log('[MikroTik Sync] Request start', { nas_id: nasId, series_id: seriesId });
            var csrfName = jQuery('#csrf_name').val();
            var csrfHash = jQuery('#csrf_hash').val();
            var postData = {
                nas_id: nasId,
                series_id: seriesId
            };
            postData[csrfName] = csrfHash;

            jQuery.ajax({
                url: '<?php echo site_url("Other_controller/pushCardsToMikrotik"); ?>',
                type: 'POST',
                dataType: 'json',
                data: postData,
                success: function(result) {
                    console.log('[MikroTik Sync] Response success', { series_id: seriesId, result: result });
                    if (result && result.csrf_hash) jQuery('#csrf_hash').val(result.csrf_hash);
                    if (result && result.success) {
                        done('success', result.message || 'Synced');
                        return;
                    }
                    if (result && result.message && /already pushed|already assigned|exists/i.test(result.message)) {
                        done('skipped', result.message);
                        return;
                    }
                    done('failed', (result && result.message) ? result.message : 'Sync failed');
                },
                error: function(xhr, status, err) {
                    console.error('[MikroTik Sync] Response error', { series_id: seriesId, status: status, error: err, xhr: xhr });
                    var resp = null;
                    try { resp = JSON.parse(xhr.responseText); } catch(e) { resp = xhr.responseJSON || null; }
                    if (resp && resp.csrf_hash) jQuery('#csrf_hash').val(resp.csrf_hash);
                    if (xhr.status === 409) {
                        done('skipped', (resp && resp.message) ? resp.message : 'Already assigned');
                        return;
                    }
                    done('failed', (resp && resp.message) ? resp.message : ('Failed to sync: ' + (err || status)));
                }
            });
        }

        jQuery('#nasSelector').on('change', function() {
            setSyncButtonsState();
        });

        jQuery('#btnBulkSyncMikrotik').on('click', function() {
            var nasId = jQuery('#nasSelector').val();
            if (!nasId) {
                alert('Please select a NAS first.');
                return;
            }

            var seriesIds = [];
            jQuery('tr[data-series]').each(function() {
                var seriesId = jQuery(this).data('series');
                if (seriesId) seriesIds.push(seriesId);
            });

            if (!seriesIds.length) {
                jQuery('#series-push-feedback').html('<div class="alert alert-warning">No series found for sync.</div>');
                return;
            }

            var $bulkBtn = jQuery(this);
            var originalHtml = $bulkBtn.html();
            $bulkBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Syncing...');
            jQuery('#series-push-feedback').html('<div class="alert alert-info">Sync is starting...</div>');
            console.log('[MikroTik Sync] Bulk sync start', { nas_id: nasId, total_series: seriesIds.length, series_ids: seriesIds });

            var index = 0;
            var successCount = 0;
            var skippedCount = 0;
            var failCount = 0;

            function next() {
                if (index >= seriesIds.length) {
                    console.log('[MikroTik Sync] Bulk sync complete', {
                        nas_id: nasId,
                        success: successCount,
                        skipped: skippedCount,
                        failed: failCount
                    });
                    jQuery('#series-push-feedback').html('<div class="alert alert-success">Sync complete. Success: ' + successCount + ', Skipped: ' + skippedCount + ', Failed: ' + failCount + '.</div>');
                    $bulkBtn.prop('disabled', false).html(originalHtml);
                    return;
                }

                syncSeriesToMikrotik(nasId, seriesIds[index++], function(state, message) {
                    console.log('[MikroTik Sync] Series processed', { state: state, message: message });
                    if (state === 'success') successCount++;
                    else if (state === 'skipped') skippedCount++;
                    else failCount++;
                    jQuery('#series-push-feedback').html('<div class="alert alert-info">Syncing... Success: ' + successCount + ', Skipped: ' + skippedCount + ', Failed: ' + failCount + '</div>');
                    next();
                });
            }

            next();
        });

        setSyncButtonsState();
    });
</script>