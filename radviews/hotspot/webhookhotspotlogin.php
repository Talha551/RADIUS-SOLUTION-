<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * application/views/hotspot_login.php
 *
 * Minimal, self-contained HotSpot login page that posts to your webhook.
 * - Uses site_url() for the form action (switch to index.php variant if needed)
 * - Includes both hyphenated and underscored router fields
 * - Uses target="_top" so the browser navigates the top window (breaks out of frames)
 * - Auto-submit is commented out; enable if you want automatic POST from the router
 */

/* Router GET values */
$link_login = $this->input->get('link-login', TRUE);
$link_orig  = $this->input->get('link-orig', TRUE);
$mac        = $this->input->get('mac', TRUE);
$ip         = $this->input->get('ip', TRUE);
$pref_user  = $this->input->get('username', TRUE);

/* CSRF token if available (CI3/CI4 compatibility) */
$csrf_name = '';
$csrf_hash = '';
if (isset($this->security) && method_exists($this->security, 'get_csrf_token_name')) {
    $csrf_name = $this->security->get_csrf_token_name();
    $csrf_hash = $this->security->get_csrf_hash();
}

/* Choose the correct webhook URL for your installation:
   - If your site uses URL rewriting (index.php removed), use the first line.
   - If your site requires index.php in the URL, uncomment the second line instead.
*/
$webhook_url = site_url('Other_controller/mikrotik_webhook');
// $webhook_url = site_url('index.php/other_controller/mikrotik_webhook');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Hotspot Login</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{font-family:system-ui,Segoe UI,Roboto,Arial;margin:24px;background:#fff}
    .wrap{max-width:520px;margin:0 auto}
    label{display:block;margin:8px 0 4px}
    input[type=text], input[type=password]{width:100%;padding:8px;box-sizing:border-box}
    .btn{padding:10px 14px;margin-top:12px}
    .note{margin-top:12px;color:#666;font-size:0.9em}
    .small{font-size:0.85em;color:#888;margin-top:8px}
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Sign in to access the Internet</h1>

    <!-- Form posts to your webhook. target="_top" ensures the top window navigates back to the router. -->
    <form id="hotspotForm"
          method="post"
          action="<?= htmlspecialchars($webhook_url, ENT_QUOTES, 'UTF-8') ?>"
          target="_top"
          autocomplete="off"
          accept-charset="utf-8">

      <label for="username">Username</label>
      <input id="username" name="username" type="text" value="<?= htmlspecialchars($pref_user, ENT_QUOTES, 'UTF-8') ?>" required>

      <label for="password" class="small">Password (optional)</label>
      <input id="password" name="password" type="password" value="">

      <!-- Router callback fields (include both hyphenated and underscored names) -->
      <input type="hidden" name="link-login" value="<?= htmlspecialchars($link_login, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="link_login" value="<?= htmlspecialchars($link_login, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="link-orig" value="<?= htmlspecialchars($link_orig, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="link_orig" value="<?= htmlspecialchars($link_orig, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="mac" value="<?= htmlspecialchars($mac, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="ip" value="<?= htmlspecialchars($ip, ENT_QUOTES, 'UTF-8') ?>">

      <?php if (!empty($csrf_name) && !empty($csrf_hash)): ?>
        <input type="hidden" name="<?= htmlspecialchars($csrf_name, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars($csrf_hash, ENT_QUOTES, 'UTF-8') ?>">
      <?php endif; ?>

      <div>
        <button type="submit" class="btn">Login</button>
      </div>

      <div class="note">After submitting, the server must respond with a Location header to the router callback so the HotSpot session completes.</div>
    </form>

    <script>
      // Optional: auto-submit for captive portal flow.
      // Uncomment the next line if you want the router/client to auto-post immediately.
      // document.getElementById('hotspotForm').submit();
    </script>
  </div>
</body>
</html>
