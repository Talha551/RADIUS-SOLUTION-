<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Wireguard VPN User Details <small class="text-muted" style="font-size:18px;">View user configuration</small></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('wireguard'); ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url('wireguard'); ?>">Wireguard VPN Users</a></li>
                        <li class="breadcrumb-item active">User Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title mb-0">User Configuration Details</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">Basic Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 150px;">Username</th>
                                    <td><?php echo $user->username; ?></td>
                                </tr>
                                <tr>
                                    <th>IP Address</th>
                                    <td><?php echo $user->ipaddress; ?></td>
                                </tr>
                                <tr>
                                    <th>Server</th>
                                    <td><?php echo $user->serveripaddress; ?></td>
                                </tr>
                                <tr>
                                    <th>Port</th>
                                    <td><?php echo $user->listenport; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Wireguard Configuration</h5>
                            <div class="mb-3">
                                <label class="form-label">Private Key</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="<?php echo $user->privatekey; ?>" readonly id="privateKeyInput">
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('privateKeyInput')">
                                        <i class="fa fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="form-label">Public Key</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="<?php echo $user->publickey; ?>" readonly id="publicKeyInput">
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('publicKeyInput')">
                                        <i class="fa fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">Wireguard Client Configuration</h5>
                            <label class="form-label">Configuration File Content</label>
                            <textarea class="form-control mb-3" rows="10" readonly>[Interface]
PrivateKey = <?php echo $user->privatekey; ?>

Address = <?php echo $user->ipaddress; ?>

ListenPort = <?php echo $user->listenport; ?>


[Peer]
PublicKey = <?php echo $user->serverpublickey; ?>

Endpoint = <?php echo $user->serveripaddress; ?>:<?php echo $user->serverport; ?>

AllowedIPs = 0.0.0.0/0

PersistentKeepalive = 25</textarea>
<?php
$configText = "[Interface]\n";
$configText .= "PrivateKey = {$user->privatekey}\n";
$configText .= "Address = {$user->ipaddress}\n";
$configText .= "ListenPort = {$user->listenport}\n\n";
$configText .= "[Peer]\n";
$configText .= "PublicKey = {$user->serverpublickey}\n";
$configText .= "Endpoint = {$user->serveripaddress}:{$user->serverport}\n";
$configText .= "AllowedIPs = 0.0.0.0/0\n";
$configText .= "PersistentKeepalive = 25";
$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($configText);
?>
<div class="mb-3">
    <label class="form-label">Scan with Wireguard App</label><br>
    <img src="<?php echo $qrUrl; ?>" alt="Wireguard QR Code" class="img-thumbnail" style="max-width:200px;">
</div>
                            <button class="btn btn-primary" onclick="downloadConfig()">
                                <i class="fa fa-download"></i> Download Configuration
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?php echo base_url('wireguard'); ?>" class="btn btn-default">Back to List</a>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function copyToClipboard(inputId) {
    var input = document.getElementById(inputId);
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices
    document.execCommand('copy');
}
function downloadConfig() {
    var config = document.querySelector('textarea').value;
    var blob = new Blob([config], { type: 'text/plain' });
    var url = window.URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = '<?php echo $user->username; ?>-wireguard.conf';
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
}
</script> 