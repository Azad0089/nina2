<?php
require_once __DIR__ . '/../includes/config.php';
$me = require_login();
require __DIR__ . '/_nav.php';
$app_title = 'Overview'; $app_active = 'overview';

$blog = main_blog_of((int)$me['id']);
$pdo = db();
$stmt = $pdo->prepare('SELECT COUNT(*) FROM posts p JOIN blogs b ON b.id=p.blog_id WHERE b.user_id=? AND p.status="published"'); $stmt->execute([$me['id']]); $postCount = (int)$stmt->fetchColumn();
$stmt = $pdo->prepare('SELECT COUNT(*) FROM posts p JOIN blogs b ON b.id=p.blog_id WHERE b.user_id=? AND p.status="scheduled"'); $stmt->execute([$me['id']]); $queuedCount = (int)$stmt->fetchColumn();
$stmt = $pdo->prepare('SELECT COUNT(*) FROM follows f JOIN blogs b ON b.id=f.blog_id WHERE b.user_id=?'); $stmt->execute([$me['id']]); $followerCount = (int)$stmt->fetchColumn();
$stmt = $pdo->prepare('SELECT COUNT(*) FROM likes l JOIN posts p ON p.id=l.post_id JOIN blogs b ON b.id=p.blog_id WHERE b.user_id=?'); $stmt->execute([$me['id']]); $likeCount = (int)$stmt->fetchColumn();
$stmt = $pdo->prepare('SELECT COUNT(*) FROM blogs WHERE user_id=?'); $stmt->execute([$me['id']]); $blogCount = (int)$stmt->fetchColumn();

require __DIR__ . '/../includes/app-start.php';
?>
<?php if (!empty($_GET['welcome'])): ?>
  <div class="app-card"><h2>Welcome to <?= e(site_name()) ?>!</h2>
    <p>Your blog <b><?= e($blog['slug'] ?? $me['username']) ?></b> is live. Add a bio and links on the <a class="lnk" href="<?= e(url('dashboard/profile.php')) ?>">Profile</a> page, or jump straight to your <a class="lnk" href="<?= e(url('dashboard/post.php')) ?>">first post</a>.</p>
  </div>
<?php endif; ?>

<div class="stat-row">
  <div class="stat"><b><?= $postCount ?></b><span>Published posts</span></div>
  <div class="stat"><b><?= $queuedCount ?></b><span>Queued posts</span></div>
  <div class="stat"><b><?= $followerCount ?></b><span>Followers</span></div>
  <div class="stat"><b><?= $likeCount ?></b><span>Likes received</span></div>
  <div class="stat"><b><?= $blogCount ?></b><span>Blogs</span></div>
  <div class="stat"><b><?= (int)$me['points'] ?></b><span>Points <?= icon('coins') ?></span></div>
</div>

<div class="app-card">
  <h2>Your account</h2>
  <p>Signed in as <b><?= e($me['username']) ?></b> (<?= e($me['email']) ?>).
  <?php if ($me['premium_tier'] !== 'none'): ?>
    Plan: <b><?= $me['premium_tier'] === 'premium_plus' ? 'Premium+' : 'Premium' ?></b> <?= badge(top_badge($me)) ?>
    <?php if ($me['premium_expires_at']): ?>(renews or expires <?= e(date('M j, Y', strtotime($me['premium_expires_at']))) ?>)<?php endif; ?>
  <?php else: ?>
    Plan: <b>Free</b> — <a class="lnk" href="<?= e(url('premium.php')) ?>">see Premium benefits</a>
  <?php endif; ?>
  </p>
</div>

<div class="tile-wide" style="margin-top:0">
  <a class="tile" href="<?= e(url('dashboard/post.php')) ?>" style="--c:var(--pink)"><?= icon('plus') ?><h3>New post</h3><p>Text, photo, video, link, quote or chat.</p></a>
  <a class="tile" href="<?= e(url('dashboard/blogs.php')) ?>" style="--c:var(--cyan)"><?= icon('compass') ?><h3>Manage blogs</h3><p>Create additional blogs, edit colors and covers.</p></a>
  <a class="tile" href="<?= e(url('blog.php?u=' . urlencode($blog['slug'] ?? $me['username']))) ?>" style="--c:var(--yellow)" target="_blank"><?= icon('eye') ?><h3>View my blog</h3><p>See your public profile as visitors see it.</p></a>
</div>
<?php require __DIR__ . '/../includes/app-end.php'; ?>
