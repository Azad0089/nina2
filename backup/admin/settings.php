<?php
require_once __DIR__ . '/../includes/config.php';
require_admin();
require __DIR__ . '/_nav.php';
$app_title = 'Site settings'; $app_active = 'settings';
$pdo = db();
$ok = null; $err = null;

$fields = ['site_name','meta_description','head_code','primary_color','secondary_color',
    'max_photo_mb','max_video_mb','ffmpeg_enabled','ffmpeg_path','storage_driver',
    'donations_enabled','premium_price_usd','premium_plus_price_usd',
    'premium_monthly_points','premium_plus_monthly_points','referral_points','max_queue_per_blog',
    'nowpayments_api_key','bitcart_api_key'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {
    foreach ($fields as $f) {
        if (isset($_POST[$f])) {
            $v = (string)$_POST[$f];
            if (in_array($f, ['max_photo_mb','max_video_mb','premium_price_usd','premium_plus_price_usd','premium_monthly_points','premium_plus_monthly_points','referral_points','max_queue_per_blog'], true)) {
                $v = (string)max(0, (int)$v);
            }
            set_setting($f, $v);
        }
    }
    set_setting('ffmpeg_enabled', !empty($_POST['ffmpeg_enabled']) ? '1' : '0');
    set_setting('donations_enabled', !empty($_POST['donations_enabled']) ? '1' : '0');

    if (!empty($_FILES['logo']['name'])) { $up = handle_upload('logo', UPLOAD_DIR_BRAND, 'image'); if ($up['ok']) set_setting('logo_path', $up['path']); else $err = $up['message']; }
    if (!empty($_FILES['favicon']['name'])) { $up = handle_upload('favicon', UPLOAD_DIR_BRAND, 'image'); if ($up['ok']) set_setting('favicon_path', $up['path']); else $err = $up['message']; }

    $ok = 'Settings saved.';
}

$S = [];
foreach ($pdo->query('SELECT skey, svalue FROM settings') as $row) { $S[$row['skey']] = $row['svalue']; }

require __DIR__ . '/../includes/app-start.php';
?>
<?php if ($err): ?><p class="app-flash err"><?= e($err) ?></p><?php endif; ?>
<?php if ($ok): ?><p class="app-flash"><?= e($ok) ?> Some values (site name, colors, logo) apply next page load.</p><?php endif; ?>

<form method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>

  <div class="app-card">
    <h2>Branding</h2>
    <div class="two">
      <label class="field"><span>Site name</span><input type="text" name="site_name" value="<?= e($S['site_name'] ?? '') ?>"></label>
      <label class="field"><span>Meta description</span><input type="text" name="meta_description" value="<?= e($S['meta_description'] ?? '') ?>"></label>
    </div>
    <div class="two">
      <label class="field"><span>Primary color</span><div class="color-row"><input type="color" name="primary_color" value="<?= e($S['primary_color'] ?? '#ff2bd6') ?>"></div></label>
      <label class="field"><span>Secondary color</span><div class="color-row"><input type="color" name="secondary_color" value="<?= e($S['secondary_color'] ?? '#19e3ff') ?>"></div></label>
    </div>
    <div class="two">
      <label class="field"><span>Logo</span><input type="file" name="logo" accept="image/*"><?php if (!empty($S['logo_path'])): ?><p class="fine">Current: <?= e(basename($S['logo_path'])) ?></p><?php endif; ?></label>
      <label class="field"><span>Favicon</span><input type="file" name="favicon" accept="image/*"><?php if (!empty($S['favicon_path'])): ?><p class="fine">Current: <?= e(basename($S['favicon_path'])) ?></p><?php endif; ?></label>
    </div>
    <label class="field"><span>Sitewide &lt;head&gt; code (Google Analytics, verification meta tags, etc. — printed as-is on every page)</span>
      <textarea name="head_code" rows="5" style="font-family:monospace"><?= e($S['head_code'] ?? '') ?></textarea></label>
  </div>

  <div class="app-card">
    <h2>Media</h2>
    <div class="two">
      <label class="field"><span>Max photo size (MB)</span><input type="number" name="max_photo_mb" value="<?= (int)($S['max_photo_mb'] ?? 15) ?>"></label>
      <label class="field"><span>Max video size (MB)</span><input type="number" name="max_video_mb" value="<?= (int)($S['max_video_mb'] ?? 200) ?>"></label>
    </div>
    <div class="two">
      <label class="check" style="align-self:end"><input type="checkbox" name="ffmpeg_enabled" value="1" <?= !empty($S['ffmpeg_enabled']) && $S['ffmpeg_enabled']==='1' ? 'checked' : '' ?>><span>Enable FFmpeg transcoding (server must have ffmpeg installed — off by default; uploads work as-is regardless)</span></label>
      <label class="field"><span>FFmpeg binary path</span><input type="text" name="ffmpeg_path" value="<?= e($S['ffmpeg_path'] ?? '/usr/bin/ffmpeg') ?>"></label>
    </div>
    <label class="field"><span>Storage driver</span>
      <select name="storage_driver"><option value="local" <?= ($S['storage_driver'] ?? 'local')==='local'?'selected':'' ?>>Local disk</option>
        <option value="s3" disabled>Amazon S3 (not wired up yet)</option>
        <option value="ftp" disabled>FTP (not wired up yet)</option>
        <option value="cloudflare" disabled>Cloudflare R2 (not wired up yet)</option></select></label>
    <label class="field"><span>Max queued (scheduled) posts per blog</span><input type="number" name="max_queue_per_blog" value="<?= (int)($S['max_queue_per_blog'] ?? 20) ?>"></label>
  </div>

  <div class="app-card">
    <h2>Premium &amp; points</h2>
    <label class="check"><input type="checkbox" name="donations_enabled" value="1" <?= !empty($S['donations_enabled']) && $S['donations_enabled']==='1' ? 'checked' : '' ?>><span>Donations / Premium enabled sitewide</span></label>
    <div class="two">
      <label class="field"><span>Premium donation threshold (USD)</span><input type="number" name="premium_price_usd" value="<?= (int)($S['premium_price_usd'] ?? 15) ?>"></label>
      <label class="field"><span>Premium+ donation threshold (USD)</span><input type="number" name="premium_plus_price_usd" value="<?= (int)($S['premium_plus_price_usd'] ?? 30) ?>"></label>
    </div>
    <div class="two">
      <label class="field"><span>Premium monthly points</span><input type="number" name="premium_monthly_points" value="<?= (int)($S['premium_monthly_points'] ?? 5000) ?>"></label>
      <label class="field"><span>Premium+ monthly points</span><input type="number" name="premium_plus_monthly_points" value="<?= (int)($S['premium_plus_monthly_points'] ?? 11000) ?>"></label>
    </div>
    <label class="field"><span>Referral points per signup</span><input type="number" name="referral_points" value="<?= (int)($S['referral_points'] ?? 100) ?>" style="max-width:200px"></label>
  </div>

  <div class="app-card">
    <h2>Payment gateways <small style="color:var(--mute);font-weight:400">(crypto checkout not wired up yet — keys are stored for when it is)</small></h2>
    <div class="two">
      <label class="field"><span>NOWPayments API key</span><input type="text" name="nowpayments_api_key" value="<?= e($S['nowpayments_api_key'] ?? '') ?>"></label>
      <label class="field"><span>BitCart API key</span><input type="text" name="bitcart_api_key" value="<?= e($S['bitcart_api_key'] ?? '') ?>"></label>
    </div>
  </div>

  <button class="btn btn-fill" type="submit">Save all settings</button>
</form>
<?php require __DIR__ . '/../includes/app-end.php'; ?>
