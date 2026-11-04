<?php
require_once __DIR__ . '/../includes/config.php';
require_admin();
require __DIR__ . '/_nav.php';
$app_title = 'Promotions'; $app_active = 'promotions';
$pdo = db();
$blogs = $pdo->query("SELECT slug, title, featured_until FROM blogs WHERE is_featured = 1 ORDER BY featured_until IS NULL DESC, featured_until ASC")->fetchAll();
$posts = $pdo->query("SELECT p.id, p.type, p.featured_until, b.slug AS blog_slug FROM posts p JOIN blogs b ON b.id=p.blog_id WHERE p.is_featured = 1 ORDER BY p.featured_until ASC")->fetchAll();
require __DIR__ . '/../includes/app-start.php';
?>
<div class="app-card">
  <h2>Promotion settings</h2>
  <p>Featured blogs and posts are granted here or manually via <a class="lnk" href="<?= e(url('admin/blogs.php')) ?>">Blogs</a> / <a class="lnk" href="<?= e(url('admin/posts.php')) ?>">Posts</a> (Feature button).
  Placement pricing (points and flat-rate packages shown on the <a class="lnk" href="<?= e(url('advertising.php')) ?>" target="_blank">public marketplace page</a>) is set in <a class="lnk" href="<?= e(url('admin/settings.php')) ?>">Settings</a>.</p>
  <p class="fine">Self-serve checkout (members spending points to buy a placement themselves) is not wired up yet — see the project notes. For now, promotions are admin-granted only, which still covers "feature this profile/post for N days."</p>
</div>
<div class="two" style="align-items:start">
  <div class="app-card">
    <h2>Currently featured blogs</h2>
    <div class="table-wrap"><table class="data-table"><thead><tr><th>Blog</th><th>Until</th></tr></thead><tbody>
      <?php foreach ($blogs as $b): ?><tr><td><?= e($b['title']) ?> (<?= e($b['slug']) ?>)</td><td><?= $b['featured_until'] ? e(date('M j, Y', strtotime($b['featured_until']))) : 'Indefinite' ?></td></tr><?php endforeach; ?>
      <?php if (!$blogs): ?><tr><td colspan="2">None right now.</td></tr><?php endif; ?>
    </tbody></table></div>
  </div>
  <div class="app-card">
    <h2>Currently featured posts</h2>
    <div class="table-wrap"><table class="data-table"><thead><tr><th>Post</th><th>Until</th></tr></thead><tbody>
      <?php foreach ($posts as $p): ?><tr><td>#<?= (int)$p['id'] ?> (<?= e($p['type']) ?>) on <?= e($p['blog_slug']) ?></td><td><?= $p['featured_until'] ? e(date('M j, Y', strtotime($p['featured_until']))) : 'Indefinite' ?></td></tr><?php endforeach; ?>
      <?php if (!$posts): ?><tr><td colspan="2">None right now.</td></tr><?php endif; ?>
    </tbody></table></div>
  </div>
</div>
<?php require __DIR__ . '/../includes/app-end.php'; ?>
