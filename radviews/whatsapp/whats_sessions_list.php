<?php $this->load->helper('url'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fab fa-whatsapp text-success"></i> WhatsApp Sessions (Baileys)</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="<?php echo site_url('Whatsapp_controller/whats_dashboard'); ?>" class="btn btn-outline-primary btn-sm"><i class="fa fa-th-large"></i> Full dashboard</a>
                    <button type="button" class="btn btn-primary btn-sm" id="whatsSessBtnRefresh"><i class="fa fa-sync"></i> Refresh</button>
                    <a href="<?php echo site_url('Whatsapp_controller/whats_add'); ?>" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Add session</a>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($api_error)): ?>
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo htmlspecialchars($api_error); ?>
                    <div class="small mt-1">Check <code>application/config/baileys.php</code> and Baileys server.</div>
                </div>
            <?php endif; ?>
            <?php if (isset($baileys_is_admin) && !$baileys_is_admin && isset($baileys_manager_login) && $baileys_manager_login !== ''): ?>
                <div class="alert alert-info alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Session naming:</strong> each session id must be your login <code><?php echo htmlspecialchars($baileys_manager_login); ?></code> or start with <code><?php echo htmlspecialchars($baileys_manager_login); ?>_</code> (text before the first underscore is treated as the owner). Examples: <code><?php echo htmlspecialchars($baileys_manager_login); ?>_1</code>, <code><?php echo htmlspecialchars($baileys_manager_login); ?>_area2</code>. Only the <strong>admin</strong> account sees every session.
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Session list</h3>
                    <p class="small text-muted mb-0">API: <code><?php echo htmlspecialchars($baileys_base_url); ?></code> — if a session is <strong>logged_out</strong> (unlinked in WhatsApp), it is automatically removed and recreated with the same name so a new QR can appear.</p>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th style="width:5%">#</th>
                                <th style="width:28%">Session ID</th>
                                <th style="width:18%">Status</th>
                                <th>API</th>
                                <th style="width:22%">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="whatsSessionsListBody">
                            <?php
                                $row_data = array('sessions' => isset($sessions) ? $sessions : array());
                                $this->load->view('whatsapp/whats_sessions_table_rows', $row_data);
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- QR modal (Baileys JSON QR + status poll; same behaviour as dashboard) -->
<div class="modal fade" id="whatsSessQrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Connect — <span id="whatsSessQrTitle"></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <p class="small text-muted">WhatsApp → Linked devices → Link a device.</p>
                <div id="whatsSessQrStatusLine" class="mb-2"><span class="badge badge-secondary">—</span></div>
                <img id="whatsSessQrImg" alt="QR" class="img-fluid border p-2 bg-white" style="max-width:280px;display:none;">
                <div id="whatsSessQrHint" class="small text-muted mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var U = {
        list: '<?php echo site_url('Whatsapp_controller/whats_ajax_list_sessions'); ?>',
        status: function (sid) {
            return '<?php echo site_url('Whatsapp_controller/whats_ajax_session_status'); ?>/' + encodeURIComponent(sid);
        },
        qrPng: function (sid, t) {
            var u = '<?php echo site_url('Whatsapp_controller/whats_session_qr_png'); ?>/' + encodeURIComponent(sid);
            return t ? (u + '?t=' + t) : u;
        },
        del: function (sid) {
            return '<?php echo site_url('Whatsapp_controller/whats_ajax_delete_session'); ?>/' + encodeURIComponent(sid);
        },
        recover: function (sid) {
            return '<?php echo site_url('Whatsapp_controller/whats_ajax_recover_logged_out_session'); ?>/' + encodeURIComponent(sid);
        },
        table: '<?php echo site_url('Whatsapp_controller/whats_ajax_sessions_table'); ?>'
    };

    window.whatsRecoverInFlight = window.whatsRecoverInFlight || {};

    /**
     * logged_out means device unlinked — Baileys will not show QR until session is removed and recreated with same id.
     */
    function maybeAutoRecoverLoggedOutFromDom(done) {
        var sids = [];
        $('#whatsSessionsListBody tr[data-baileys-status]').each(function () {
            var st = ($(this).attr('data-baileys-status') || '').toLowerCase();
            if (st === 'logged_out') {
                sids.push($(this).data('session'));
            }
        });
        if (!sids.length) {
            if (done) {
                done(false);
            }
            return;
        }
        var anyRecovered = false;
        function run(i) {
            if (i >= sids.length) {
                if (done) {
                    done(anyRecovered);
                }
                return;
            }
            var sid = sids[i];
            if (window.whatsRecoverInFlight[sid]) {
                run(i + 1);
                return;
            }
            var ck = 'whatsRecoverCooldown_' + sid;
            var last = parseInt(sessionStorage.getItem(ck) || '0', 10);
            if (Date.now() - last < 90000 && last > 0) {
                run(i + 1);
                return;
            }
            window.whatsRecoverInFlight[sid] = true;
            fetch(U.recover(sid), {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' }
            })
                .then(function (r) { return r.json().catch(function () { return ({}); }); })
                .then(function (j) {
                    if (j.ok && j.recovered) {
                        anyRecovered = true;
                        sessionStorage.removeItem(ck);
                    } else if (j && j.ok && j.skipped) {
                        /* already not logged_out */
                    } else {
                        sessionStorage.setItem(ck, Date.now().toString());
                    }
                })
                .catch(function () {
                    sessionStorage.setItem(ck, Date.now().toString());
                })
                .finally(function () {
                    delete window.whatsRecoverInFlight[sid];
                    run(i + 1);
                });
        }
        run(0);
    }

    // QR rotates on the server ~every 20–60s; refresh JSON every 20s. Status polled every 8s to detect linked devices without hammering the API.
    var WHATS_QR_POLL_MS = 20000;
    var WHATS_STATUS_POLL_MS = 8000;
    var lastQrDataUrl = null;

    function buildQrJsonUrl(sid) {
        var base = '<?php echo site_url('Whatsapp_controller/whats_ajax_session_qr'); ?>/' + encodeURIComponent(sid);
        return base + (base.indexOf('?') >= 0 ? '&' : '?') + '_=' + Date.now();
    }

    function applyQrDataUrl($img, urlOrData) {
        if (!$img.length || !urlOrData) {
            return;
        }
        var el = $img[0];
        el.onload = function () { el.onload = null; };
        el.onerror = function () { el.onerror = null; };
        el.removeAttribute('src');
        void el.offsetWidth;
        el.src = urlOrData;
        $img.show();
    }

    function badgeClass(status) {
        var s = (status || '').toLowerCase();
        if (s === 'connected') return 'badge-success';
        if (s === 'qr_pending') return 'badge-info';
        if (s === 'connecting' || s === 'starting') return 'badge-warning';
        if (s === 'disconnected' || s === 'logged_out') return 'badge-secondary';
        return 'badge-light';
    }

    function refreshTable(skipRecover) {
        $.get(U.table, function (html) {
            $('#whatsSessionsListBody').html(html);
            if (skipRecover) {
                return;
            }
            maybeAutoRecoverLoggedOutFromDom(function (didRecover) {
                if (didRecover) {
                    $.get(U.table, function (html2) {
                        $('#whatsSessionsListBody').html(html2);
                    });
                }
            });
        });
    }

    $('#whatsSessBtnRefresh').on('click', function () { refreshTable(false); });
    setInterval(function () { refreshTable(false); }, 15000);

    $(function () {
        maybeAutoRecoverLoggedOutFromDom(function (didRecover) {
            if (didRecover) {
                $.get(U.table, function (html) {
                    $('#whatsSessionsListBody').html(html);
                });
            }
        });
    });

    var qrTimer = null;
    var qrJsonPollTimer = null;
    var activeSid = null;

    function clearQrIntervals() {
        if (qrTimer) { clearInterval(qrTimer); qrTimer = null; }
        if (qrJsonPollTimer) { clearInterval(qrJsonPollTimer); qrJsonPollTimer = null; }
    }

    function stopQrPoll() {
        clearQrIntervals();
        activeSid = null;
    }

    function refreshQrFromJson() {
        if (!activeSid) return;
        fetch(buildQrJsonUrl(activeSid), { credentials: 'same-origin', cache: 'no-store' })
            .then(function (r) { return r.json().catch(function () { return {}; }); })
            .then(function (j) {
                if (!j.ok) {
                    $('#whatsSessQrHint').html('<span class="text-danger">' + (j.detail || 'QR error') + '</span>');
                    return;
                }
                var d = j.data || {};
                var b64 = d.qr_image_base64 || d.qrImageBase64;
                if (b64 && typeof b64 === 'string') {
                    if (b64 !== lastQrDataUrl) {
                        lastQrDataUrl = b64;
                    }
                    applyQrDataUrl($('#whatsSessQrImg'), b64);
                    $('#whatsSessQrHint').empty();
                } else {
                    $('#whatsSessQrHint').text('Waiting for QR…');
                    applyQrDataUrl($('#whatsSessQrImg'), U.qrPng(activeSid, Date.now()));
                }
            })
            .catch(function () {
                $('#whatsSessQrHint').text('Could not load QR.');
            });
    }

    $('#whatsSessQrModal').on('hidden.bs.modal', function () {
        stopQrPoll();
        lastQrDataUrl = null;
        $('#whatsSessQrImg').hide().removeAttr('src');
        $('#whatsSessQrHint').empty();
    });

    $(document).on('click', '.whats-sess-btn-qr', function () {
        var sid = $(this).data('session');
        clearQrIntervals();
        lastQrDataUrl = null;
        activeSid = sid;
        $('#whatsSessQrTitle').text(sid);
        $('#whatsSessQrModal').modal('show');

        qrTimer = setInterval(function () {
            if (!activeSid) return;
            var baseSt = U.status(activeSid);
            var stUrl = baseSt + (baseSt.indexOf('?') >= 0 ? '&' : '?') + '_=' + Date.now();
            fetch(stUrl, { credentials: 'same-origin', cache: 'no-store' })
                .then(function (r) { return r.json().catch(function () { return {}; }); })
                .then(function (j) {
                    if (!j.ok) {
                        $('#whatsSessQrStatusLine').html('<span class="badge badge-danger">' + (j.detail || 'error') + '</span>');
                        return;
                    }
                    var st = (j.data && j.data.status) ? j.data.status : '';
                    $('#whatsSessQrStatusLine').html('<span class="badge ' + badgeClass(st) + '">' + $('<div>').text(st).html() + '</span>');
                    if (st === 'connected') {
                        stopQrPoll();
                        refreshTable();
                    }
                });
        }, WHATS_STATUS_POLL_MS);

        refreshQrFromJson();
        qrJsonPollTimer = setInterval(refreshQrFromJson, WHATS_QR_POLL_MS);
    });

    $(document).on('click', '.whats-sess-btn-delete', function () {
        var sid = $(this).data('session');
        if (!confirm('Delete Baileys session "' + sid + '" (logout + remove stored auth)?')) return;
        fetch(U.del(sid), { method: 'DELETE', credentials: 'same-origin' })
            .then(function (r) { return r.json().catch(function () { return {}; }); })
            .then(function (j) {
                if (!j.ok) {
                    alert(j.detail || 'Delete failed');
                    return;
                }
                refreshTable();
            });
    });
})();
</script>
