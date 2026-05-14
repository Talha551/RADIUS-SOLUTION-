<?php if (!empty($sessions)): ?>
    <?php foreach ($sessions as $i => $row): ?>
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
            <td><?php echo (int) $i + 1; ?></td>
            <td><code><?php echo htmlspecialchars($sid); ?></code></td>
            <td><span class="badge whats-sess-status <?php echo $bc; ?>"><?php echo htmlspecialchars($st); ?></span></td>
            <td>
                <?php if ($ls === 'connected'): ?>
                    <button type="button" class="btn btn-secondary btn-sm" disabled title="Connected">
                        <i class="fa fa-check"></i> Connected
                    </button>
                <?php elseif ($ls === 'qr_pending' || $ls === 'connecting' || $ls === 'starting'): ?>
                    <button type="button" class="btn btn-info btn-sm whats-sess-btn-qr" data-session="<?php echo htmlspecialchars($sid); ?>">
                        <i class="fa fa-qrcode"></i> Show QR
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-warning btn-sm whats-sess-btn-qr" data-session="<?php echo htmlspecialchars($sid); ?>">
                        <i class="fa fa-qrcode"></i> QR / Status
                    </button>
                <?php endif; ?>
            </td>
            <td>
                <button type="button" class="btn btn-info btn-sm whats-sess-btn-qr" data-session="<?php echo htmlspecialchars($sid); ?>" title="QR"><i class="fa fa-qrcode"></i></button>
                <button type="button" class="btn btn-outline-danger btn-sm whats-sess-btn-delete" data-session="<?php echo htmlspecialchars($sid); ?>" title="Delete session"><i class="fa fa-trash"></i></button>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="5" class="text-center text-muted">No sessions on Baileys API.</td></tr>
<?php endif; ?>
