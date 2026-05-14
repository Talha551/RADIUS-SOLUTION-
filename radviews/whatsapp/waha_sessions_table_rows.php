<?php if (!empty($sessions)): ?>
    <?php foreach ($sessions as $i => $session): ?>
        <tr>
            <td><?php echo $i+1; ?></td>
            <td><?php echo htmlspecialchars($session->session_name); ?></td>
            <td><?php echo htmlspecialchars($session->managername); ?></td>
            <td>
                <span class="badge badge-<?php
                    echo ($session->waha_status == 'CONNECTED' || $session->waha_status == 'WORKING') ? 'success' :
                         ($session->waha_status == 'STARTING' ? 'warning' :
                         ($session->waha_status == 'SCAN_QR_CODE' || $session->waha_status == 'SCAN QR CODE' || $session->waha_status == 'SCAN_QR' ? 'info' : 'secondary'));
                ?>">
                    <?php echo ucfirst(strtolower($session->waha_status)); ?>
                </span>
            </td>
            <td>
                <?php if (strtoupper($session->waha_status) === 'STOPPED'): ?>
                    <button class="btn btn-success btn-sm waha-action" data-action="start" data-name="<?php echo htmlspecialchars($session->session_name); ?>">
                        <i class="fa fa-play"></i> Start
                    </button>
                <?php elseif (strtoupper($session->waha_status) === 'SCAN_QR_CODE' || strtoupper($session->waha_status) === 'SCAN QR CODE' || strtoupper($session->waha_status) === 'SCAN_QR'): ?>
                    <button class="btn btn-info btn-sm show-qr-btn" data-id="<?php echo htmlspecialchars($session->session_name); ?>">
                        <i class="fa fa-qrcode"></i> Show QR
                    </button>
                <?php else: ?>
                    <button class="btn btn-secondary btn-sm" disabled>
                        <i class="fa fa-check"></i> Connected
                    </button>
                <?php endif; ?>
            </td>
            <td>
                <button class="btn btn-outline-primary btn-sm waha-action" data-action="start" data-name="<?php echo htmlspecialchars($session->session_name); ?>" title="Start"><i class="fa fa-play"></i></button>
                <button class="btn btn-outline-secondary btn-sm waha-action" data-action="stop" data-name="<?php echo htmlspecialchars($session->session_name); ?>" title="Stop"><i class="fa fa-stop"></i></button>
                <button class="btn btn-outline-info btn-sm waha-action" data-action="restart" data-name="<?php echo htmlspecialchars($session->session_name); ?>" title="Restart"><i class="fa fa-sync"></i></button>
                <button class="btn btn-outline-warning btn-sm waha-action" data-action="logout" data-name="<?php echo htmlspecialchars($session->session_name); ?>" title="Logout"><i class="fa fa-sign-out-alt"></i></button>
                <button class="btn btn-outline-success btn-sm waha-action" data-action="screenshot" data-name="<?php echo htmlspecialchars($session->session_name); ?>" title="Screenshot"><i class="fa fa-camera"></i></button>
                <button class="btn btn-outline-danger btn-sm waha-action" data-action="delete" data-name="<?php echo htmlspecialchars($session->session_name); ?>" title="Delete"><i class="fa fa-trash"></i></button>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="5" class="text-center">No sessions found.</td></tr>
<?php endif; ?> 