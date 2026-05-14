<div class="text-center">
    <h5><?php echo htmlspecialchars($session->name); ?>
        <small class="badge badge-info"><?php echo strtoupper($session->status); ?></small>
    </h5>
    <hr>
    <?php if ($qr_code): ?>
        <?php
        $qr_src = $qr_code;
        if ($qr_code && strpos($qr_code, 'data:image') !== 0 && strpos($qr_code, 'http') !== 0) {
            $qr_src = 'data:image/png;base64,' . $qr_code;
        }
        ?>
        <div class="mb-3">
            <img src="<?php echo $qr_src; ?>" alt="QR Code" style="max-width: 100%; height: auto; border: 2px solid #28a745; padding: 8px; background: #fff;">
            <div class="mt-2 text-success">Scan this QR code in WhatsApp to connect.</div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">No QR code available or QR code is invalid.</div>
    <?php endif; ?>
    <?php if ($pairing_code): ?>
        <div class="mb-3">
            <div class="alert alert-primary" style="font-size: 1.5em; letter-spacing: 2px; display: inline-block;">
                <?php echo htmlspecialchars($pairing_code); ?>
            </div>
            <div class="mt-2 text-info">Or enter this pairing code in WhatsApp.</div>
        </div>
    <?php endif; ?>
    <button class="btn btn-info btn-sm mb-2" id="refreshQrBtn" data-session="<?php echo htmlspecialchars($session->name); ?>">
        <i class="fa fa-sync"></i> Refresh
    </button>
    <div class="text-left mt-3" style="max-width:100%;overflow:auto;">
        <strong>QR API Response:</strong>
        <pre><?php print_r($debug_qr_response); ?></pre>
        <strong>Pairing Code API Response:</strong>
        <pre><?php print_r($debug_pairing_response); ?></pre>
    </div>
</div>
<script>
$(document).on('click', '#refreshQrBtn', function() {
    var sessionName = $(this).data('session');
    $('#qrModalBody').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
    $.get('<?php echo site_url('Whatsapp_controller/show_qr/'); ?>' + encodeURIComponent(sessionName), function(data) {
        $('#qrModalBody').html(data);
    });
});
</script> 