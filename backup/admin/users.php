<?php
require_once __DIR__ . '/../includes/config.php';
$admin = require_admin();
require __DIR__ . '/_nav.php';
$app_title = 'Users'; $app_active = 'users';
$pdo = db();
$ok = null; $err = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {
    $id = (int)($_POST['id'] ?? 0);
    $act = (string)($_POST['act'] ?? '');
    $target = $pdo->prepare('SELECT * FROM users WHERE id = ?'); $target->execute([$id]); $u = $target->fetch();
    if (!$u) { $err = 'User not found.'; }
    elseif ($u['role'] === 'admin' && $act !== 'view') { $err = 'Cannot moderate another admin account here.'; }
    else {
        switch ($act) {
            case 'suspend': $pdo->prepare("UPDATE users SET status='suspended' WHERE id=?")->execute([$id]); $ok = 'User suspended.'; break;
            case 'unsuspend':
            case 'unban': $pdo->prepare("UPDATE users SET status='active' WHERE id=?")->execute([$id]); $ok = 'User reactivated.'; break;
            case 'ban': $pdo->prepare("UPDATE users SET status='banned' WHERE id=?")->execute([$id]); $ok = 'User banned.'; break;
            case 'delete':
                $pdo->prepare('DELETE FROM posts WHERE blog_id IN (SELECT id FROM blogs WHERE user_id=?)')->execute([$id]);
                $pdo->prepare('DELETE FROM blogs WHERE user_id=?')->execute([$id]);
                $pdo->prepare('DELETE FROM users WHERE id=?')->execute([$id]);
                $ok = 'User deleted permanently.';
                break;
            case 'toggle_badge_pro': $pdo->prepare('UPDATE users SET badge_pro = 1 - badge_pro WHERE id=?')->execute([$id]); $ok = 'Premium badge updated.'; break;
            case 'toggle_badge_id':  $pdo->prepare('UPDATE users SET badge_id = 1 - badge_id WHERE id=?')->execute([$id]);   $ok = 'Identity badge updated.'; break;
            case 'toggle_badge_vip': $pdo->prepare('UPDATE users SET badge_vip = 1 - badge_vip WHERE id=?')->execute([$id]); $ok = 'VIP badge updated.'; break;
            case 'toggle_staff':     $pdo->prepare('UPDATE users SET is_staff_pick = 1 - is_staff_pick WHERE id=?')->execute([$id]); $ok = 'Staff pick updated.'; break;
            case 'set_plan':
                $tier = in_array($_POST['tier'] ?? '', ['none','premium','premium_plus'], true) ? $_POST['tier'] : 'none';
                if ($tier === 'none') {
                    $pdo->prepare("UPDATE users SET premium_tier='none', is_premium=0, premium_expires_at=NULL, badge_pro=0 WHERE id=?")->execute([$id]);
                } else {
                    $pdo->prepare("UPDATE users SET premium_tier=?, is_premium=1, badge_pro=1, premium_expires_at=DATE_ADD(NOW(), INTERVAL 1 YEAR) WHERE id=?")->execute([$tier, $id]);
                    $pts = $tier === 'premium_plus' ? setting_int('premium_plus_monthly_points', 11000) : setting_int('premium_monthly_points', 5000);
                    $pdo->prepare('UPDATE users SET points = points + ? WHERE id=?')->execute([$pts, $id]);
                }
                $ok = 'Plan updated.';
                break;
            case 'add_points':
                $n = (int)($_POST['points'] ?? 0);
                $pdo->prepare('UPDATE users SET points = GREATEST(0, points + ?) WHERE id=?')->execute([$n, $id]);
                $ok = 'Points adjusted.';
                break;
        }
    }
}

$q = trim((string)($_GET['q'] ?? ''));
$sql = 'SELECT * FROM users';
$params = [];
if ($q !== '') { $sql .= ' WHERE username LIKE ? OR email LIKE ?'; $params = ["%$q%", "%$q%"]; }
$sql .= ' ORDER BY created_at DESC LIMIT 200';
$stmt = $pdo->prepare($sql); $stmt->execute($params); $users = $stmt->fetchAll();

require __DIR__ . '/../includes/app-start.php';
?>
<?php if ($err): ?><p class="app-flash err"><?= e($err) ?></p><?php endif; ?>
<?php if ($ok): ?><p class="app-flash"><?= e($ok) ?></p><?php endif; ?>

<div class="app-card">
  <form method="get" class="two" style="margin-bottom:0">
    <label class="field" style="margin:0"><span>Search username or email</span><input type="text" name="q" value="<?= e($q) ?>"></label>
    <div style="align-self:end"><button class="btn btn-line" type="submit">Search</button></div>
  </form>
</div>

<div class="app-card">
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th>User</th><th>Status</th><th>Plan</th><th>Points</th><th>Badges</th><th>Last login</th><th style="min-width:340px">Actions</th></tr></thead>
      <tbody>
      <?php foreach ($users as $u): if ($u['role'] === 'admin') continue; ?>
        <tr>
          <td><b><?= e($u['username']) ?></b><br><small style="color:var(--mute)"><?= e($u['email']) ?></small></td>
          <td><span class="pill pill-<?= $u['status']==='active'?'active':($u['status']==='suspended'?'suspended':'banned') ?>"><?= e($u['status']) ?></span></td>
          <td>
            <form method="post" class="row-actions">
              <?= csrf_field() ?><input type="hidden" name="act" value="set_plan"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
              <select name="tier" onchange="this.form.submit()" class="btn-xs">
                <option value="none" <?= $u['premium_tier']==='none'?'selected':'' ?>>Free</option>
                <option value="premium" <?= $u['premium_tier']==='premium'?'selected':'' ?>>Premium</option>
                <option value="premium_plus" <?= $u['premium_tier']==='premium_plus'?'selected':'' ?>>Premium+</option>
              </select>
            </form>
          </td>
          <td>
            <?= (int)$u['points'] ?>
            <form method="post" class="row-actions" style="margin-top:4px">
              <?= csrf_field() ?><input type="hidden" name="act" value="add_points"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
              <input type="number" name="points" placeholder="±100" style="width:70px;background:var(--black);border:1px solid var(--line);color:var(--text);padding:4px 6px">
              <button class="btn-xs" type="submit">Apply</button>
            </form>
          </td>
          <td class="row-actions">
            <form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="toggle_badge_pro"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><button class="btn-xs<?= $u['badge_pro']?' danger':'' ?>" type="submit" title="Premium badge"><?= icon('crown') ?></button></form>
            <form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="toggle_badge_id"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><button class="btn-xs<?= $u['badge_id']?' danger':'' ?>" type="submit" title="Identity verified"><?= icon('verified') ?></button></form>
            <form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="toggle_badge_vip"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><button class="btn-xs<?= $u['badge_vip']?' danger':'' ?>" type="submit" title="VIP"><?= icon('star') ?></button></form>
            <form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="toggle_staff"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><button class="btn-xs<?= $u['is_staff_pick']?' danger':'' ?>" type="submit" title="Staff pick"><?= icon('flame') ?></button></form>
          </td>
          <td><small><?= $u['last_login_at'] ? e(date('M j, Y', strtotime($u['last_login_at']))) : '—' ?><br><?= e($u['last_login_ip'] ?? '') ?></small></td>
          <td class="row-actions">
            <?php if ($u['status'] === 'active'): ?>
              <form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="suspend"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><button class="btn-xs" type="submit">Suspend</button></form>
            <?php else: ?>
              <form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="unsuspend"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><button class="btn-xs" type="submit">Reactivate</button></form>
            <?php endif; ?>
            <?php if ($u['status'] !== 'banned'): ?>
              <form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="ban"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><button class="btn-xs danger" type="submit">Ban</button></form>
            <?php endif; ?>
            <form method="post" onsubmit="return confirm('Permanently delete this user, their blogs and posts? This cannot be undone.')">
              <?= csrf_field() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><button class="btn-xs danger" type="submit"><?= icon('trash') ?></button>
            </form>
            <a class="btn-xs" href="<?= e(url('admin/logins.php?user=' . $u['id'])) ?>">IP log</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/../includes/app-end.php'; ?>
