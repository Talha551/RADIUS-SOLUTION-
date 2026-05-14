<?php if(!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-users"></i> Cards List</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Cards List</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12 text-right">
                    <div class="form-group">
                        <a class="btn btn-primary" href="<?php echo base_url(); ?>serieslist">
                            <i class="fas fa-user"></i> Series List
                        </a>
                    </div>
                </div>
            </div>

            <?php
            $seriesAssignedNasId = null;
            $seriesAssignedNasLabel = null;
            if (!empty($cardsListing)) {
                foreach ($cardsListing as $recordForAssign) {
                    $candId = null;
                    if (isset($recordForAssign->nas_id) && is_numeric($recordForAssign->nas_id) && (int)$recordForAssign->nas_id > 0) {
                        $candId = (int)$recordForAssign->nas_id;
                    } elseif (isset($recordForAssign->nasid) && is_numeric($recordForAssign->nasid) && (int)$recordForAssign->nasid > 0) {
                        $candId = (int)$recordForAssign->nasid;
                    }

                    if ($candId !== null) {
                        $seriesAssignedNasId = $candId;
                        if (!empty($recordForAssign->nas_label)) {
                            $seriesAssignedNasLabel = $recordForAssign->nas_label;
                        }
                        break;
                    }
                }
            }
            ?>

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nasSelector">Select NAS</label>
                        <select id="nasSelector" class="form-control" <?php echo ($seriesAssignedNasId !== null) ? 'disabled' : ''; ?>>
                            <option value="">-- Choose NAS --</option>
                            <?php
                            if (isset($nasList) && !empty($nasList)) {
                                foreach ($nasList as $nas) {
                                    $nid = isset($nas->id) ? $nas->id : (isset($nas->nasid) ? $nas->nasid : '');
                                    $short = isset($nas->shortname) ? $nas->shortname : (isset($nas->short) ? $nas->short : '');
                                    $host = isset($nas->nasname) ? $nas->nasname : '';
                                    $selected = ($seriesAssignedNasId !== null && (string)$seriesAssignedNasId === (string)$nid) ? ' selected' : '';
                                    echo '<option value="' . htmlspecialchars($nid, ENT_QUOTES, 'UTF-8') . '"' . $selected . '>' . htmlspecialchars($short . ' (' . $host . ')', ENT_QUOTES, 'UTF-8') . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4 align-self-end">
                    <div class="form-group">
                        <button id="btnPushToMikrotik" class="btn btn-success" <?php echo ($seriesAssignedNasId !== null) ? 'disabled' : 'style="display:none;"'; ?>>
                            <i class="fas fa-network-wired"></i> Push Cards to MikroTik
                        </button>
                    </div>
                </div>
                <div class="col-md-4 align-self-end">
                    <?php if ($seriesAssignedNasId !== null) : ?>
                        <div class="alert alert-info py-2 mb-0">
                            This batch is fixed to <?php echo htmlspecialchars(!empty($seriesAssignedNasLabel) ? $seriesAssignedNasLabel : ('NAS #' . $seriesAssignedNasId), ENT_QUOTES, 'UTF-8'); ?>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div id="push-feedback" class="mt-2"></div>

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
                <div class="col-12">
                    <div class="card">
                        <!-- Summary table (optional) -->
                        <div class="card-header">
                            <h3 class="card-title">Series List</h3>
                            <div class="card-tools">
                                <form action="<?php echo base_url() ?>managerGroup" method="POST" id="searchList">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="searchText" value="<?php echo isset($searchText) ? htmlspecialchars($searchText) : ''; ?>" class="form-control" placeholder="Search" />
                                        <div class="input-group-append">
                                            <button class="btn btn-default searchList"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Series</th>
                                        <th>Card Count</th>
                                        <th>Price</th>
                                        <th>Value</th>
                                        <th>Manager</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($cardsListingSummery)) : ?>
                                        <?php foreach ($cardsListingSummery as $record) : ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($record->series); ?></td>
                                                <td><?php echo htmlspecialchars($record->cardcount); ?></td>
                                                <td><?php echo htmlspecialchars($record->value); ?></td>
                                                <td><?php echo htmlspecialchars($record->value * $record->cardcount); ?></td>
                                                <td><?php echo htmlspecialchars($record->owner); ?></td>
                                                <?php
                                                $activeStatus = ($record->active == 1) ? "Active" : "In Stock";
                                                $activeColor = ($record->active == 1) ? 'text-success' : 'text-danger';
                                                ?>
                                                <td class="<?php echo $activeColor; ?>"><?php echo $activeStatus; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Detailed cards table -->
                        <div class="card-body table-responsive p-0 mt-3">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Transid</th>
                                        <th>Card Number</th>
                                        <th>Password</th>
                                        <th>Price</th>
                                        <th>Series</th>
                                        <th>Created On</th>
                                        <th>Manager</th>
                                        <th>Expire On</th>
                                        <th>Active On</th>
                                        <th>Last Active</th>
                                        <th>Uptime</th>
                                        <th>Card Status</th>
                                        <th>Push Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($cardsListing)) : ?>
                                        <?php foreach ($cardsListing as $record) : ?>
                                            <?php
                                                $rowCardnum = isset($record->cardnum) ? $record->cardnum : (isset($record->username) ? $record->username : '');
                                                $activeStatus = ($record->active == 1) ? "Active" : "In Stock";
                                                $activeClass = ($record->active == 1) ? 'text-success' : 'text-danger';
                                                $enableStatus = ($record->enableuser == 1) ? '' : '-Disabled';

                                                $activeOn = '';
                                                $activeOnFields = ['activeon', 'active_on', 'activationdate', 'activation_date', 'activated_on', 'usedon', 'firstlogin', 'first_login', 'authdate'];
                                                foreach ($activeOnFields as $fieldName) {
                                                    if (isset($record->$fieldName) && !empty($record->$fieldName)) {
                                                        $activeOn = $record->$fieldName;
                                                        break;
                                                    }
                                                }
                                                if ($activeOn === '' && (int)$record->active === 1 && !empty($record->date)) {
                                                    $activeOn = $record->date;
                                                }
                                            ?>
                                            <?php
                                            // Determine push state, prefer controller-merged record fields, then cardStates, then nasid on record
                                            $st = ['state' => 'not_pushed', 'nas_label' => null, 'nas_id' => null];

                                            if (!empty($record->nas_label)) {
                                                $st['state'] = 'pushed_known';
                                                $st['nas_label'] = $record->nas_label;
                                                $st['nas_id'] = isset($record->nas_id) ? (int)$record->nas_id : (isset($record->nasid) ? (int)$record->nasid : null);
                                            } elseif (isset($cardStates) && is_array($cardStates) && $rowCardnum && isset($cardStates[$rowCardnum])) {
                                                $st = $cardStates[$rowCardnum];
                                            } else {
                                                $maybeNas = null;
                                                if (isset($record->nasid) && is_numeric($record->nasid) && (int)$record->nasid > 0) $maybeNas = (int)$record->nasid;
                                                elseif (isset($record->nas_id) && is_numeric($record->nas_id) && (int)$record->nas_id > 0) $maybeNas = (int)$record->nas_id;
                                                if ($maybeNas !== null) {
                                                    $st = ['state' => 'pushed_known', 'nas_label' => null, 'nas_id' => $maybeNas];
                                                }
                                            }
                                            ?>
                                            <tr data-cardnum="<?php echo htmlspecialchars($rowCardnum, ENT_QUOTES, 'UTF-8'); ?>">
                                                <td><?php echo htmlspecialchars($record->transid); ?></td>
                                                <td><?php echo htmlspecialchars($record->cardnum); ?></td>
                                                <td><?php echo htmlspecialchars($record->password); ?></td>
                                                <td><?php echo htmlspecialchars($record->value); ?></td>
                                                <td><?php echo htmlspecialchars($record->series); ?></td>
                                                <td><?php echo htmlspecialchars($record->date); ?></td>
                                                <td><?php echo htmlspecialchars($record->owner); ?></td>
                                                <td><?php echo htmlspecialchars(!empty($record->expiration) ? $record->expiration : 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($activeOn !== '' ? $activeOn : 'N/A'); ?></td>
                                                <td>
                                                    <?php
                                                    $lastActive = '';
                                                    $lastActiveFields = ['last_active_on', 'lastactiveon', 'last_login', 'lastlogin', 'last_activity', 'lastactivity'];
                                                    foreach ($lastActiveFields as $fieldName) {
                                                        if (isset($record->$fieldName) && !empty($record->$fieldName)) {
                                                            $lastActive = $record->$fieldName;
                                                            break;
                                                        }
                                                    }
                                                    echo htmlspecialchars($lastActive !== '' ? $lastActive : 'N/A');
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $uptime = 'N/A';
                                                    if (isset($record->timebaseonline) && !empty($record->timebaseonline)) {
                                                        $seconds = (int)$record->timebaseonline;
                                                        if ($seconds > 0) {
                                                            $days = floor($seconds / 86400);
                                                            $hours = floor(($seconds % 86400) / 3600);
                                                            $mins = floor(($seconds % 3600) / 60);
                                                            $parts = [];
                                                            if ($days > 0) $parts[] = $days . 'd';
                                                            if ($hours > 0) $parts[] = $hours . 'h';
                                                            if ($mins > 0) $parts[] = $mins . 'm';
                                                            if (!empty($parts)) $uptime = implode(' ', $parts);
                                                        }
                                                    }
                                                    echo htmlspecialchars($uptime);
                                                    ?>
                                                </td>
                                                <td class="<?php echo $activeClass; ?>">
                                                    <?php echo htmlspecialchars($activeStatus . $enableStatus); ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if (isset($st['state']) && $st['state'] === 'not_pushed') {
                                                        echo '<span class="badge badge-secondary">Not pushed</span>';
                                                    } elseif (isset($st['state']) && $st['state'] === 'pushed_known') {
                                                        $labelText = !empty($st['nas_label']) ? $st['nas_label'] : ($st['nas_id'] ? 'NAS #' . (int)$st['nas_id'] : 'Already pushed');
                                                        echo '<span class="badge badge-success">Already pushed to ' . htmlspecialchars($labelText, ENT_QUOTES, 'UTF-8') . '</span>';
                                                    } elseif (isset($st['state']) && $st['state'] === 'pushed_unknown') {
                                                        echo '<span class="badge badge-warning">Already pushed — NAS unknown</span>';
                                                    } else {
                                                        echo '<span class="badge badge-light">Unknown</span>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer clearfix">
                            <?php if (isset($this->pagination)) echo $this->pagination->create_links(); ?>
                        </div>
                    </div>
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
        jQuery("#searchList").attr("action", baseURL + "managerGroup/" + value);
        jQuery("#searchList").submit();
    });

    // NAS dropdown toggle
    jQuery('#nasSelector').change(function() {
        var selectedNas = jQuery(this).val();
        if (selectedNas) {
            jQuery('#btnPushToMikrotik').show();
        } else {
            jQuery('#btnPushToMikrotik').hide();
        }
    });

    // Push button handler (AJAX)
    jQuery('#btnPushToMikrotik').off('click').on('click', function() {
        var nasId = jQuery('#nasSelector').val();
        var seriesId = '<?php echo isset($seriesID) ? htmlspecialchars($seriesID) : ""; ?>';

        if (!nasId || !seriesId) {
            alert('NAS or Series ID missing');
            return;
        }

        var $btn = jQuery(this);
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Pushing...');

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
                if (result && result.csrf_hash) jQuery('#csrf_hash').val(result.csrf_hash);

                if (result && result.success) {
                    showInlineSuccess(result.message || 'Cards pushed successfully.');
                    setTimeout(function(){ location.reload(); }, 700);
                    return;
                }

                var msg = (result && result.message) ? result.message : 'Push failed';
                showInlineError(msg);
                console.log(result);
                $btn.prop('disabled', false).html(originalHtml);
            },
            error: function(xhr, status, err) {
                var resp = null;
                try { resp = JSON.parse(xhr.responseText); } catch(e) { resp = xhr.responseJSON || null; }

                if (xhr.status === 409) {
                    var msg = (resp && resp.message) ? resp.message : 'Series already pushed to a NAS.';
                    var hint = (resp && resp.hint) ? resp.hint : null;
                    showInlineAssignmentMessage(msg, hint);
                    $btn.prop('disabled', true).html('Already assigned');
                    if (resp && resp.csrf_hash) jQuery('#csrf_hash').val(resp.csrf_hash);
                    return;
                }

                var errMsg = (resp && resp.message) ? resp.message : ('Failed to push cards: ' + (err || status));
                alert(errMsg);
                console.log(xhr);
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    function showInlineAssignmentMessage(message, hint) {
        var html = '<div class="alert alert-warning" role="alert"><strong>Notice: </strong>' + escapeHtml(message);
        if (hint) html += '<div class="small mt-1">' + escapeHtml(hint) + '</div>';
        html += '</div>';
        jQuery('#push-feedback').html(html);
    }

    function showInlineSuccess(message) {
        var html = '<div class="alert alert-success" role="alert">' + escapeHtml(message) + '</div>';
        jQuery('#push-feedback').html(html);
    }

    function showInlineError(message) {
        var html = '<div class="alert alert-danger" role="alert">' + escapeHtml(message) + '</div>';
        jQuery('#push-feedback').html(html);
    }

    function escapeHtml(s) {
        return String(s).replace(/[&<>'"]/g, function(c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c];
        });
    }

});
</script>
