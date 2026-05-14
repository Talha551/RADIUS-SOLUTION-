<?php $this->load->helper('url'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fab fa-whatsapp text-success"></i> WhatsApp — Baileys API</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="whatsBtnHealth" title="Ping Baileys /health">
                        <i class="fa fa-heartbeat"></i> API health
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" id="whatsBtnRefreshList">
                        <i class="fa fa-sync"></i> Refresh sessions
                    </button>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <?php if (!empty($api_error)): ?>
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo htmlspecialchars($api_error); ?>
                    <div class="small mt-1">Set <code>BAILEYS_API_BASE_URL</code> and <code>BAILEYS_API_KEY</code> (or edit <code>application/config/baileys.php</code>) to match your FastAPI server.</div>
                </div>
            <?php endif; ?>
            <?php if (isset($baileys_is_admin) && !$baileys_is_admin && isset($baileys_manager_login) && $baileys_manager_login !== ''): ?>
                <div class="alert alert-info alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Session naming:</strong> use <code><?php echo htmlspecialchars($baileys_manager_login); ?></code> or <code><?php echo htmlspecialchars($baileys_manager_login); ?>_something</code> (prefix before <code>_</code> is your login). You only see your own sessions here; <strong>admin</strong> sees all.
                </div>
            <?php endif; ?>

            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Create session</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-2">Enter a fixed session name (letters, digits, <code>_</code>, <code>-</code>, max 64 chars). Then scan the QR code to connect.</p>
                    <form id="whatsFormCreate" class="form-inline flex-wrap">
                        <label class="mr-2 mb-2">Session ID</label>
                        <input type="text" class="form-control mr-2 mb-2" id="whatsNewSessionId" placeholder="<?php echo (isset($baileys_is_admin) && $baileys_is_admin) ? 'e.g. phone-main' : htmlspecialchars(isset($baileys_manager_login) ? $baileys_manager_login . '_main' : 'yourlogin_main'); ?>" maxlength="64" pattern="[a-zA-Z0-9_-]{1,64}" required>
                        <button type="submit" class="btn btn-success mb-2"><i class="fa fa-plus"></i> Create session</button>
                    </form>
                    <div id="whatsCreateMsg" class="small mt-2"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sessions</h3>
                    <span class="small text-muted">Loaded from <code><?php echo htmlspecialchars($baileys_base_url); ?></code></span>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width:28%">Session ID</th>
                                <th style="width:18%">Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="whatsSessionsBody">
                            <?php if (!empty($sessions)): ?>
                                <?php foreach ($sessions as $row): ?>
                                    <?php
                                        $sid = isset($row['session_id']) ? $row['session_id'] : '';
                                        $st = isset($row['status']) ? $row['status'] : '';
                                        $ls = strtolower((string) $st);
                                        $bc = 'badge-light';
                                        if ($ls === 'connected') {
                                            $bc = 'badge-success';
                                        } elseif ($ls === 'qr_pending') {
                                            $bc = 'badge-info';
                                        } elseif ($ls === 'connecting' || $ls === 'starting') {
                                            $bc = 'badge-warning';
                                        } elseif ($ls === 'disconnected' || $ls === 'logged_out') {
                                            $bc = 'badge-secondary';
                                        }
                                    ?>
                                    <tr data-session="<?php echo htmlspecialchars($sid); ?>" data-baileys-status="<?php echo htmlspecialchars($st, ENT_QUOTES, 'UTF-8'); ?>">
                                        <td><code><?php echo htmlspecialchars($sid); ?></code></td>
                                        <td><span class="badge whats-status-badge <?php echo $bc; ?>"><?php echo htmlspecialchars($st); ?></span></td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm whats-btn-qr" data-session="<?php echo htmlspecialchars($sid); ?>"><i class="fa fa-qrcode"></i> QR</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm whats-btn-sendopen" data-session="<?php echo htmlspecialchars($sid); ?>"><i class="fa fa-paper-plane"></i> Send</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm whats-btn-msgopen" data-session="<?php echo htmlspecialchars($sid); ?>"><i class="fa fa-inbox"></i> Messages</button>
                                            <button type="button" class="btn btn-outline-danger btn-sm whats-btn-delete" data-session="<?php echo htmlspecialchars($sid); ?>"><i class="fa fa-trash"></i> Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr id="whatsEmptyRow"><td colspan="3" class="text-center text-muted">No sessions yet. Create one above.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card collapsed-card" id="whatsCardSend">
                        <div class="card-header">
                            <h3 class="card-title">Send text message</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="card-body" style="display:none;">
                            <form id="whatsFormSend">
                                <div class="form-group">
                                    <label>Session ID</label>
                                    <input type="text" class="form-control" id="whatsSendSessionId" maxlength="64" pattern="[a-zA-Z0-9_-]{1,64}" required>
                                </div>
                                <div class="form-group">
                                    <label>To (number without + or full JID)</label>
                                    <input type="text" class="form-control" id="whatsSendTo" placeholder="923349900957" required>
                                </div>
                                <div class="form-group">
                                    <label>Message</label>
                                    <textarea class="form-control" id="whatsSendText" rows="3" required></textarea>
                                </div>
                                <div class="form-row align-items-end">
                                    <div class="form-group col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="whatsSendTyping">
                                            <label class="form-check-label" for="whatsSendTyping">Typing indicator</label>
                                        </div>
                                        <small class="text-muted">Sends <code>typing</code> + optional delay before the message (Baileys API).</small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="whatsSendTypingMs">Typing duration (ms)</label>
                                        <input type="number" class="form-control" id="whatsSendTypingMs" value="1400" min="1" max="120000" placeholder="1400">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Send</button>
                            </form>
                            <div id="whatsSendMsg" class="small mt-2"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card collapsed-card" id="whatsCardMsgs">
                        <div class="card-header">
                            <h3 class="card-title">Incoming messages (poll)</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="card-body" style="display:none;">
                            <div class="form-row align-items-end">
                                <div class="form-group col-md-6">
                                    <label>Session ID</label>
                                    <input type="text" class="form-control" id="whatsMsgSessionId" maxlength="64" pattern="[a-zA-Z0-9_-]{1,64}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Limit</label>
                                    <input type="number" class="form-control" id="whatsMsgLimit" value="50" min="1" max="200">
                                </div>
                                <div class="form-group col-md-3">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" class="form-check-input" id="whatsMsgClear">
                                        <label class="form-check-label" for="whatsMsgClear">Clear queue</label>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary mb-3" id="whatsBtnPollMsgs"><i class="fa fa-download"></i> Fetch messages</button>
                            <pre id="whatsMsgOut" class="bg-light p-2 border rounded small" style="max-height:280px;overflow:auto;">{}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- QR Modal -->
<div class="modal fade" id="whatsQrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Connect WhatsApp — <span id="whatsQrTitleSid"></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <p class="small text-muted">Open WhatsApp on your phone → Linked devices → Link a device. Poll refreshes while status is <code>qr_pending</code>.</p>
                <div id="whatsQrStatusLine" class="mb-2"><span class="badge badge-secondary">—</span></div>
                <div id="whatsQrImgWrap">
                    <img id="whatsQrImg" alt="QR" class="img-fluid border p-2 bg-white" style="max-width:280px;display:none;">
                </div>
                <div id="whatsQrJsonHint" class="small text-muted mt-2"></div>
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
        create: '<?php echo site_url('Whatsapp_controller/whats_ajax_create_session'); ?>',
        health: '<?php echo site_url('Whatsapp_controller/whats_ajax_health'); ?>',
        send: '<?php echo site_url('Whatsapp_controller/whats_ajax_send'); ?>',
        status: function (sid) {
            return '<?php echo site_url('Whatsapp_controller/whats_ajax_session_status'); ?>/' + encodeURIComponent(sid);
        },
        qrJson: function (sid) {
            return '<?php echo site_url('Whatsapp_controller/whats_ajax_session_qr'); ?>/' + encodeURIComponent(sid);
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
        msgs: function (sid, limit, clear) {
            var q = '?limit=' + encodeURIComponent(limit || 50) + '&clear=' + (clear ? 'true' : 'false');
            return '<?php echo site_url('Whatsapp_controller/whats_ajax_messages'); ?>/' + encodeURIComponent(sid) + q;
        }
    };

    function badgeClass(status) {
        var s = (status || '').toLowerCase();
        if (s === 'connected') return 'badge-success';
        if (s === 'qr_pending') return 'badge-info';
        if (s === 'connecting' || s === 'starting') return 'badge-warning';
        if (s === 'disconnected' || s === 'logged_out') return 'badge-secondary';
        return 'badge-light';
    }

    function rowHtml(sid, status) {
        var stEsc = $('<div>').text(status || '').html();
        return '<tr data-session="' + $('<div>').text(sid).html() + '" data-baileys-status="' + stEsc + '">' +
            '<td><code>' + $('<div>').text(sid).html() + '</code></td>' +
            '<td><span class="badge whats-status-badge ' + badgeClass(status) + '">' + $('<div>').text(status || '').html() + '</span></td>' +
            '<td>' +
            '<button type="button" class="btn btn-info btn-sm whats-btn-qr" data-session="' + $('<div>').text(sid).html() + '"><i class="fa fa-qrcode"></i> QR</button> ' +
            '<button type="button" class="btn btn-outline-primary btn-sm whats-btn-sendopen" data-session="' + $('<div>').text(sid).html() + '"><i class="fa fa-paper-plane"></i> Send</button> ' +
            '<button type="button" class="btn btn-outline-secondary btn-sm whats-btn-msgopen" data-session="' + $('<div>').text(sid).html() + '"><i class="fa fa-inbox"></i> Messages</button> ' +
            '<button type="button" class="btn btn-outline-danger btn-sm whats-btn-delete" data-session="' + $('<div>').text(sid).html() + '"><i class="fa fa-trash"></i> Delete</button>' +
            '</td></tr>';
    }

    function flashErr(el, msg) {
        $(el).html('<span class="text-danger">' + $('<div>').text(msg).html() + '</span>');
    }
    function flashOk(el, msg) {
        $(el).html('<span class="text-success">' + $('<div>').text(msg).html() + '</span>');
    }

    function parseJsonSafe(r) {
        return r.json().catch(function () { return {}; });
    }

    window.whatsRecoverInFlight = window.whatsRecoverInFlight || {};

    function maybeAutoRecoverFromSessionsList(list, done) {
        var sids = [];
        (list || []).forEach(function (s) {
            if ((s.status || '').toLowerCase() === 'logged_out' && s.session_id) {
                sids.push(s.session_id);
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
                .then(function (r) { return parseJsonSafe(r).then(function (j) { return { ok: r.ok, j: j }; }); })
                .then(function (x) {
                    var j = x.j || {};
                    if (j.ok && j.recovered) {
                        anyRecovered = true;
                        sessionStorage.removeItem(ck);
                    } else if (j.ok && j.skipped) {
                        /* skip */
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

    function maybeAutoRecoverDashboardDom(done) {
        var sids = [];
        $('#whatsSessionsBody tr[data-baileys-status]').each(function () {
            if (($(this).attr('data-baileys-status') || '').toLowerCase() === 'logged_out') {
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
                .then(function (r) { return parseJsonSafe(r).then(function (j) { return { ok: r.ok, j: j }; }); })
                .then(function (x) {
                    var j = x.j || {};
                    if (j.ok && j.recovered) {
                        anyRecovered = true;
                        sessionStorage.removeItem(ck);
                    } else if (j.ok && j.skipped) {
                        /* skip */
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

    function reloadSessionsTable(skipRecoverChain) {
        fetch(U.list, { credentials: 'same-origin' })
            .then(function (r) { return parseJsonSafe(r).then(function (j) { return { ok: r.ok, j: j }; }); })
            .then(function (x) {
                if (!x.j.ok) {
                    alert(x.j.detail || 'Failed to list sessions');
                    return;
                }
                var tbody = $('#whatsSessionsBody');
                tbody.empty();
                var list = x.j.sessions || [];
                if (!list.length) {
                    tbody.append('<tr id="whatsEmptyRow"><td colspan="3" class="text-center text-muted">No sessions.</td></tr>');
                    return;
                }
                list.forEach(function (s) {
                    var sid = s.session_id || '';
                    var st = s.status || '';
                    tbody.append(rowHtml(sid, st));
                });
                if (!skipRecoverChain) {
                    maybeAutoRecoverFromSessionsList(list, function (didRecover) {
                        if (didRecover) {
                            reloadSessionsTable(true);
                        }
                    });
                }
            })
            .catch(function () { alert('Network error'); });
    }

    $('#whatsBtnRefreshList').on('click', function () {
        reloadSessionsTable(false);
    });

    $(function () {
        maybeAutoRecoverDashboardDom(function (didRecover) {
            if (didRecover) {
                reloadSessionsTable(true);
            }
        });
    });

    $('#whatsFormCreate').on('submit', function (e) {
        e.preventDefault();
        var sid = $('#whatsNewSessionId').val().trim();
        $('#whatsCreateMsg').empty();
        fetch(U.create, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ session_id: sid })
        })
            .then(function (r) { return parseJsonSafe(r).then(function (j) { return { ok: r.ok, status: r.status, j: j }; }); })
            .then(function (x) {
                if (!x.j.ok) {
                    flashErr('#whatsCreateMsg', x.j.detail || ('HTTP ' + x.status));
                    return;
                }
                flashOk('#whatsCreateMsg', 'Session created: ' + (x.j.data && x.j.data.session_id ? x.j.data.session_id : sid));
                $('#whatsBtnRefreshList').trigger('click');
            })
            .catch(function () { flashErr('#whatsCreateMsg', 'Network error'); });
    });

    $(document).on('click', '.whats-btn-delete', function () {
        var sid = $(this).data('session');
        if (!confirm('Delete session "' + sid + '" on the API (logout + remove stored auth)?')) return;
        fetch(U.del(sid), { method: 'DELETE', credentials: 'same-origin' })
            .then(function (r) { return parseJsonSafe(r).then(function (j) { return { ok: r.ok, j: j }; }); })
            .then(function (x) {
                if (!x.j.ok) {
                    alert(x.j.detail || 'Delete failed');
                    return;
                }
                $('#whatsBtnRefreshList').trigger('click');
            });
    });

    $(document).on('click', '.whats-btn-sendopen', function () {
        var sid = $(this).data('session');
        $('#whatsSendSessionId').val(sid);
        $('#whatsCardSend').CardWidget('expand');
        $('#whatsCardSend .card-body').show();
    });

    $(document).on('click', '.whats-btn-msgopen', function () {
        var sid = $(this).data('session');
        $('#whatsMsgSessionId').val(sid);
        $('#whatsCardMsgs').CardWidget('expand');
        $('#whatsCardMsgs .card-body').show();
    });

    $('#whatsFormSend').on('submit', function (e) {
        e.preventDefault();
        $('#whatsSendMsg').empty();
        var payload = {
            session_id: $('#whatsSendSessionId').val().trim(),
            to: $('#whatsSendTo').val().trim(),
            text: $('#whatsSendText').val()
        };
        if ($('#whatsSendTyping').is(':checked')) {
            payload.typing = true;
            var tms = parseInt($('#whatsSendTypingMs').val(), 10);
            if (!isNaN(tms) && tms > 0) {
                payload.typing_ms = tms;
            }
        }
        fetch(U.send, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(function (r) { return parseJsonSafe(r).then(function (j) { return { ok: r.ok, j: j }; }); })
            .then(function (x) {
                if (!x.j.ok) {
                    flashErr('#whatsSendMsg', x.j.detail || 'Send failed');
                    return;
                }
                flashOk('#whatsSendMsg', 'Message sent.');
            })
            .catch(function () { flashErr('#whatsSendMsg', 'Network error'); });
    });

    $('#whatsBtnPollMsgs').on('click', function () {
        var sid = $('#whatsMsgSessionId').val().trim();
        if (!sid) {
            alert('Enter session ID');
            return;
        }
        var lim = parseInt($('#whatsMsgLimit').val(), 10) || 50;
        var clr = $('#whatsMsgClear').is(':checked');
        fetch(U.msgs(sid, lim, clr), { credentials: 'same-origin' })
            .then(function (r) { return parseJsonSafe(r).then(function (j) { return { ok: r.ok, j: j }; }); })
            .then(function (x) {
                if (!x.j.ok) {
                    $('#whatsMsgOut').text(JSON.stringify(x.j, null, 2));
                    return;
                }
                $('#whatsMsgOut').text(JSON.stringify(x.j.data, null, 2));
            });
    });

    $('#whatsBtnHealth').on('click', function () {
        fetch(U.health, { credentials: 'same-origin' })
            .then(function (r) { return parseJsonSafe(r).then(function (j) { return j; }); })
            .then(function (j) {
                if (j.ok && j.data) {
                    alert('Baileys health: ' + JSON.stringify(j.data));
                } else {
                    alert(j.detail || 'Health check failed');
                }
            });
    });

    var qrTimer = null;
    var qrJsonPollTimer = null;
    var activeQrSid = null;

    function clearQrIntervals() {
        if (qrTimer) {
            clearInterval(qrTimer);
            qrTimer = null;
        }
        if (qrJsonPollTimer) {
            clearInterval(qrJsonPollTimer);
            qrJsonPollTimer = null;
        }
    }

    function stopQrPoll() {
        clearQrIntervals();
        activeQrSid = null;
    }

    // Pairing QR is refreshed on the server on a multi-second cadence (often ~20–60s). Poll QR JSON every 20s; poll status more often so “connected” appears soon after scan.
    var WHATS_QR_POLL_MS = 20000;
    var WHATS_STATUS_POLL_MS = 8000;
    var lastQrDataUrl = null;

    function buildQrJsonUrl(sid) {
        var base = '<?php echo site_url('Whatsapp_controller/whats_ajax_session_qr'); ?>/' + encodeURIComponent(sid);
        return base + (base.indexOf('?') >= 0 ? '&' : '?') + '_=' + Date.now();
    }

    function applyQrDataUrl($img, dataUrl) {
        if (!$img.length || !dataUrl) {
            return;
        }
        var el = $img[0];
        el.onload = function () { el.onload = null; };
        el.onerror = function () { el.onerror = null; };
        el.removeAttribute('src');
        void el.offsetWidth;
        el.src = dataUrl;
        $img.show();
    }

    /**
     * Baileys: GET .../qr as qr_image_base64. Re-fetch on an interval; clear + reassign src so the browser repaints when the code rotates.
     */
    function refreshQrFromJson() {
        if (!activeQrSid) {
            return;
        }
        fetch(buildQrJsonUrl(activeQrSid), { credentials: 'same-origin', cache: 'no-store' })
            .then(function (r) { return parseJsonSafe(r).then(function (j) { return { ok: r.ok, status: r.status, j: j }; }); })
            .then(function (x) {
                if (!x.j.ok) {
                    $('#whatsQrJsonHint').html('<span class="text-danger">' + $('<div>').text(x.j.detail || ('HTTP ' + x.status)).html() + '</span>');
                    return;
                }
                var d = x.j.data || {};
                var b64 = d.qr_image_base64 || d.qrImageBase64;
                if (b64 && typeof b64 === 'string') {
                    if (b64 !== lastQrDataUrl) {
                        lastQrDataUrl = b64;
                    }
                    applyQrDataUrl($('#whatsQrImg'), b64);
                    $('#whatsQrImg').off('error.whats').on('error.whats', function () {
                        $('#whatsQrJsonHint').text('Received QR data but image failed to render.');
                    });
                    $('#whatsQrJsonHint').empty();
                } else {
                    $('#whatsQrJsonHint').text('No QR payload yet — stay on this screen while status is qr_pending.');
                    var pngUrl = U.qrPng(activeQrSid, Date.now());
                    applyQrDataUrl($('#whatsQrImg'), pngUrl);
                }
            })
            .catch(function () {
                $('#whatsQrJsonHint').text('Could not reach QR endpoint.');
            });
    }

    $('#whatsQrModal').on('hidden.bs.modal', function () {
        stopQrPoll();
        lastQrDataUrl = null;
        $('#whatsQrImg').hide().removeAttr('src').off('error.whats');
        $('#whatsQrJsonHint').empty();
    });

    $(document).on('click', '.whats-btn-qr', function () {
        var sid = $(this).data('session');
        clearQrIntervals();
        lastQrDataUrl = null;
        activeQrSid = sid;
        $('#whatsQrTitleSid').text(sid);
        $('#whatsQrModal').modal('show');

        qrTimer = setInterval(function () {
            if (!activeQrSid) return;
            var baseSt = U.status(activeQrSid);
            var stUrl = baseSt + (baseSt.indexOf('?') >= 0 ? '&' : '?') + '_=' + Date.now();
            fetch(stUrl, { credentials: 'same-origin', cache: 'no-store' })
                .then(function (r) { return parseJsonSafe(r).then(function (j) { return { ok: r.ok, j: j }; }); })
                .then(function (x) {
                    if (!x.j.ok) {
                        $('#whatsQrStatusLine').html('<span class="badge badge-danger">' + (x.j.detail || 'error') + '</span>');
                        return;
                    }
                    var st = (x.j.data && x.j.data.status) ? x.j.data.status : '';
                    $('#whatsQrStatusLine').html('<span class="badge ' + badgeClass(st) + '">' + $('<div>').text(st).html() + '</span>');
                    if (st === 'connected') {
                        stopQrPoll();
                        $('#whatsBtnRefreshList').trigger('click');
                    }
                });
        }, WHATS_STATUS_POLL_MS);

        refreshQrFromJson();
        qrJsonPollTimer = setInterval(refreshQrFromJson, WHATS_QR_POLL_MS);
    });
})();
</script>
