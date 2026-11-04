<?php
require_once __DIR__ . '/../includes/config.php';
require_admin();
require __DIR__ . '/_nav.php';
$app_title = 'Categories'; $app_active = 'categories';
$pdo = db();
$ok = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {
    $act = (string)($_POST['act'] ?? '');
    if ($act === 'create') {
        $name = trim((string)($_POST['name'] ?? ''));
        if ($name !== '') { $pdo->prepare('INSERT IGNORE INTO categories (name) VALUES (?)')->execute([$name]); $ok = 'Category added.'; }
    } elseif ($act === 'delete') {
        $pdo->prepare('DELETE FROM categories WHERE id=?')->execute([(int)($_POST['id'] ?? 0)]);
        $ok = 'Category removed.';
    }
}
$cats = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
require __DIR__ . '/../includes/app-start.php';
?>
<?php if ($ok): ?><p class="app-flash"><?= e($ok) ?></p><?php endif; ?>
<div class="app-card">
  <h2>Add a content category</h2>
  <form method="post" class="two"><?= csrf_field() ?><input type="hidden" name="act" value="create">
    <label class="field" style="margin:0"><span>Category name</span><input type="text" name="name" required></label>
    <div style="align-self:end"><button class="btn btn-fill" type="submit">Add</button></div>
  </form>
</div>
<div class="app-card">
  <div class="table-wrap"><table class="data-table"><thead><tr><th>Category</th><th></th></tr></thead><tbody>
    <?php foreach ($cats as $c): ?>
      <tr><td><?= e($c['name']) ?></td><td><form method="post" onsubmit="return confirm('Remove this category?')"><?= csrf_field() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><button class="btn-xs danger" type="submit"><?= icon('trash') ?></button></form></td></tr>
    <?php endforeach; ?>
    <?php if (!$cats): ?><tr><td colspan="2">No categories yet.</td></tr><?php endif; ?>
  </tbody></table></div>
</div>
<?php require __DIR__ . '/../includes/app-end.php'; ?>
