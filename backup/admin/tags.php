<?php
require_once __DIR__ . '/../includes/config.php';
require_admin();
require __DIR__ . '/_nav.php';
$app_title = 'Tags'; $app_active = 'tags';
$pdo = db();
$ok = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {
    $act = (string)($_POST['act'] ?? '');
    if ($act === 'create') {
        $name = trim((string)($_POST['name'] ?? ''));
        if ($name !== '') { $pdo->prepare('INSERT IGNORE INTO tags (name) VALUES (?)')->execute([$name]); $ok = 'Tag added.'; }
    } elseif ($act === 'rename') {
        $id = (int)($_POST['id'] ?? 0); $name = trim((string)($_POST['name'] ?? ''));
        if ($name !== '') { $pdo->prepare('UPDATE tags SET name=? WHERE id=?')->execute([$name, $id]); $ok = 'Tag renamed.'; }
    } elseif ($act === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare('DELETE FROM post_tags WHERE tag_id=?')->execute([$id]);
        $pdo->prepare('DELETE FROM tags WHERE id=?')->execute([$id]);
        $ok = 'Tag deleted.';
    }
}
$tags = $pdo->query('SELECT * FROM tags ORDER BY uses DESC, name')->fetchAll();
require __DIR__ . '/../includes/app-start.php';
?>
<?php if ($ok): ?><p class="app-flash"><?= e($ok) ?></p><?php endif; ?>
<div class="app-card">
  <h2>Add a tag</h2>
  <form method="post" class="two"><?= csrf_field() ?><input type="hidden" name="act" value="create">
    <label class="field" style="margin:0"><span>Tag name</span><input type="text" name="name" required></label>
    <div style="align-self:end"><button class="btn btn-fill" type="submit">Add</button></div>
  </form>
</div>
<div class="app-card">
  <div class="table-wrap"><table class="data-table"><thead><tr><th>Tag</th><th>Uses</th><th>Actions</th></tr></thead><tbody>
    <?php foreach ($tags as $t): ?>
      <tr>
        <td>#<?= e($t['name']) ?></td><td><?= (int)$t['uses'] ?></td>
        <td class="row-actions">
          <form method="post" class="row-actions"><?= csrf_field() ?><input type="hidden" name="act" value="rename"><input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
            <input type="text" name="name" value="<?= e($t['name']) ?>" style="width:140px;background:var(--black);border:1px solid var(--line);color:var(--text);padding:4px 6px">
            <button class="btn-xs" type="submit">Save</button></form>
          <form method="post" onsubmit="return confirm('Delete tag #<?= e($t['name']) ?>?')"><?= csrf_field() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= (int)$t['id'] ?>"><button class="btn-xs danger" type="submit"><?= icon('trash') ?></button></form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody></table></div>
</div>
<?php require __DIR__ . '/../includes/app-end.php'; ?>
