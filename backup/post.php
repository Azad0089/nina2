<?php
require_once __DIR__ . '/includes/config.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare("SELECT b.status AS blog_status, b.searchable, u.status AS user_status FROM posts p
                        JOIN blogs b ON b.id = p.blog_id JOIN users u ON u.id = b.user_id WHERE p.id = ? LIMIT 1");
$stmt->execute([$id]);
$meta = $stmt->fetch();
$p = $meta ? get_post($id) : null;

$me = current_user();
$hidden = !$meta || $meta['blog_status'] !== 'active' || $meta['user_status'] !== 'active';

if (!$p || ($hidden && !is_admin())) {
    http_response_code(404);
    $page_title = 'Post not found'; $page_desc = 'This post does not exist.'; $page_path = 'post.php'; $page_noindex = true;
    require __DIR__ . '/includes/page-start.php';
    echo '<main class="page" id="main"><section class="panel"><p>That post doesn\'t exist or has been removed.</p></section></main>';
    require __DIR__ . '/includes/page-end.php';
    exit;
}

// count a view (best-effort, once per session per post)
if (empty($_SESSION['viewed'][$id])) {
    db()->prepare('UPDATE posts SET views = views + 1 WHERE id = ?')->execute([$id]);
    $_SESSION['viewed'][$id] = 1;
}

$preview = $p['heading'] ?? $p['caption'] ?? $p['quote'] ?? $p['title'] ?? ucfirst($p['type']) . ' post';
$page_title = $preview . ' — @' . $p['blog'];
$page_desc  = mb_substr(strip_tags((string)($p['body'] ?? $p['caption'] ?? $p['quote'] ?? '')), 0, 155) ?: (site_name() . ' post');
$page_path  = 'post.php?id=' . $id;
$page_noindex = empty($meta['searchable']);
require __DIR__ . '/includes/page-start.php';
?>
<main class="page" id="main">
  <section class="panel" style="max-width:640px;margin:0 auto">
    <div class="feed" data-view="list">
      <?php require __DIR__ . '/includes/card.php'; ?>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/page-end.php'; ?>
