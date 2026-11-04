<?php
require_once __DIR__ . '/includes/config.php';

$slug = trim((string)($_GET['u'] ?? ''));
$pdo = db();
$stmt = $pdo->prepare('SELECT b.*, u.username, u.badge_pro, u.badge_id, u.badge_vip, u.status AS user_status
                        FROM blogs b JOIN users u ON u.id = b.user_id WHERE b.slug = ? LIMIT 1');
$stmt->execute([$slug]);
$blog = $stmt->fetch();

if (!$blog) {
    http_response_code(404);
    $page_title = 'Blog not found'; $page_desc = 'This blog does not exist.'; $page_path = 'blog.php'; $page_noindex = true;
    require __DIR__ . '/includes/page-start.php';
    echo '<main class="page" id="main"><section class="panel"><p>That blog doesn\'t exist or has been removed.</p></section></main>';
    require __DIR__ . '/includes/page-end.php';
    exit;
}

$hidden = $blog['status'] !== 'active' || $blog['user_status'] !== 'active';
$me = current_user();
$isOwner = $me && (int)$me['id'] === (int)$blog['user_id'];

if ($hidden && !$isOwner && !is_admin()) {
    http_response_code(404);
    $page_title = 'Blog unavailable'; $page_desc = 'This blog is not available.'; $page_path = 'blog.php'; $page_noindex = true;
    require __DIR__ . '/includes/page-start.php';
    echo '<main class="page" id="main"><section class="panel"><p>This blog is currently unavailable.</p></section></main>';
    require __DIR__ . '/includes/page-end.php';
    exit;
}

$type = valid_type($_GET['type'] ?? null);
$pageNum = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;
$sql = "SELECT p.*, b.slug AS blog_slug, b.color AS blog_color, b.avatar_path AS blog_avatar, u.badge_pro, u.badge_id, u.badge_vip,
        TIMESTAMPDIFF(MINUTE, p.published_at, NOW()) AS minutes_ago,
        (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) AS like_count,
        (SELECT COUNT(*) FROM posts rb WHERE rb.reblog_of_post_id = p.id) AS reblog_count
        FROM posts p JOIN blogs b ON b.id = p.blog_id JOIN users u ON u.id = b.user_id
        WHERE p.blog_id = ? AND p.status = 'published'";
$params = [$blog['id']];
if ($type !== 'all') { $sql .= ' AND p.type = ?'; $params[] = $type; }
$sql .= ' ORDER BY p.published_at DESC LIMIT ? OFFSET ?';
$stmt = $pdo->prepare($sql);
foreach ($params as $i => $v) { $stmt->bindValue($i + 1, $v, PDO::PARAM_STR); }
$stmt->bindValue(count($params) + 1, $perPage, PDO::PARAM_INT);
$stmt->bindValue(count($params) + 2, ($pageNum - 1) * $perPage, PDO::PARAM_INT);
$stmt->execute();
$posts = array_map('post_row_to_card', $stmt->fetchAll());

$followerStmt = $pdo->prepare('SELECT COUNT(*) FROM follows WHERE blog_id = ?'); $followerStmt->execute([$blog['id']]);
$followerCount = (int)$followerStmt->fetchColumn();
$postCountStmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE blog_id = ? AND status='published'"); $postCountStmt->execute([$blog['id']]);
$totalPosts = (int)$postCountStmt->fetchColumn();

$showProfile = (bool)$blog['show_profile'];
$following = $me && blog_followed_by((int)$me['id'], (int)$blog['id']);

$page_title = $blog['title'] . ($blog['tagline'] ? ' — ' . $blog['tagline'] : '');
$page_desc  = $blog['bio'] ? mb_substr(strip_tags($blog['bio']), 0, 155) : ($blog['tagline'] ?: (site_name() . ' blog: ' . $blog['title']));
$page_path  = 'blog.php?u=' . rawurlencode($blog['slug']);
$page_noindex = !$blog['searchable'];
require __DIR__ . '/includes/page-start.php';
?>
<main class="page" id="main">
  <header class="blog-head" style="--c:<?= e($blog['color']) ?>">
    <div class="blog-cover<?= $blog['cover_path'] ? '' : ' ph' ?>" <?= $blog['cover_path'] ? 'style="background-image:url(' . e(media_url($blog['cover_path'])) . ')"' : '' ?>></div>
    <div class="panel" style="margin-top:-46px;position:relative">
      <div style="display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap">
        <?php if ($blog['avatar_path']): ?>
          <img src="<?= e(media_url($blog['avatar_path'])) ?>" alt="" width="88" height="88" style="border-radius:50%;border:4px solid var(--black);object-fit:cover">
        <?php else: ?>
          <span class="av" style="--c:<?= e($blog['color']) ?>;width:88px;height:88px;font-size:34px;border:4px solid var(--black)"><?= e(initial($blog['slug'])) ?></span>
        <?php endif; ?>
        <div style="flex:1">
          <h1 style="font:800 26px var(--f-display);display:flex;align-items:center;gap:8px;flex-wrap:wrap"><?= e($blog['title']) ?> <?= badge(top_badge($blog)) ?> <?= $blog['is_main'] ? '' : '' ?></h1>
          <p style="color:var(--mute)">@<?= e($blog['slug']) ?><?= $blog['tagline'] ? ' · ' . e($blog['tagline']) : '' ?> · <?= $totalPosts ?> posts · <?= $followerCount ?> followers</p>
        </div>
        <?php if (!$isOwner && $me): ?>
          <button class="follow<?= $following ? ' on' : '' ?>" type="button" data-blog="<?= e($blog['slug']) ?>"><?= $following ? 'Following' : 'Follow' ?></button>
        <?php elseif (!$me): ?>
          <a class="btn btn-fill" href="<?= e(url('login.php')) ?>" data-modal="login">Sign in to follow</a>
        <?php elseif ($isOwner): ?>
          <a class="btn btn-ghost" href="<?= e(url('dashboard/blogs.php')) ?>">Edit blog</a>
        <?php endif; ?>
      </div>

      <?php if ($showProfile && ($blog['bio'] || $blog['website1'] || $blog['website2'] || $blog['website3'] || $blog['contact_email'])): ?>
        <div class="blog-profile" style="margin-top:18px;padding-top:18px;border-top:1px solid var(--line)">
          <?php if ($blog['bio']): ?><p><?= nl2br(e($blog['bio'])) ?></p><?php endif; ?>
          <p class="row-actions" style="margin-top:8px">
            <?php foreach ([$blog['website1'], $blog['website2'], $blog['website3']] as $w): if (!$w) continue; ?>
              <a class="btn-xs" href="<?= e($w) ?>" target="_blank" rel="nofollow noopener me"><?= icon('link') ?> <?= e(parse_url($w, PHP_URL_HOST) ?: $w) ?></a>
            <?php endforeach; ?>
            <?php if ($blog['contact_email']): ?><a class="btn-xs" href="mailto:<?= e($blog['contact_email']) ?>"><?= icon('mail') ?> <?= e($blog['contact_email']) ?></a><?php endif; ?>
          </p>
        </div>
      <?php endif; ?>

      <?php if (!$isOwner): ?>
        <button class="btn-xs report" type="button" data-post-id="0" onclick="reportBlog(<?= (int)$blog['id'] ?>)" style="margin-top:12px"><?= icon('flag') ?> Report this blog</button>
      <?php endif; ?>
    </div>
  </header>

  <section class="panel" style="margin-top:20px">
    <div class="row-actions" style="margin-bottom:16px">
      <a class="btn-xs<?= $type==='all'?' danger':'' ?>" href="?u=<?= e($blog['slug']) ?>">All</a>
      <?php foreach (post_types() as $k => [$label,]) : ?>
        <a class="btn-xs<?= $type===$k?' danger':'' ?>" href="?u=<?= e($blog['slug']) ?>&type=<?= $k ?>"><?= $label ?></a>
      <?php endforeach; ?>
    </div>
    <div class="feed" data-view="grid">
      <?php foreach ($posts as $p): require __DIR__ . '/includes/card.php'; endforeach; ?>
      <?php if (!$posts): ?><p>No posts here yet.</p><?php endif; ?>
    </div>
    <?php if ($totalPosts > $perPage): ?>
      <div class="row-actions" style="justify-content:center;margin-top:20px">
        <?php if ($pageNum > 1): ?><a class="btn-xs" href="?u=<?= e($blog['slug']) ?>&type=<?= $type ?>&page=<?= $pageNum - 1 ?>">← Newer</a><?php endif; ?>
        <?php if ($pageNum * $perPage < $totalPosts): ?><a class="btn-xs" href="?u=<?= e($blog['slug']) ?>&type=<?= $type ?>&page=<?= $pageNum + 1 ?>">Older →</a><?php endif; ?>
      </div>
    <?php endif; ?>
  </section>
</main>
<script>function reportBlog(id){ var reason=prompt('Why are you reporting this blog?'); if(reason===null) return;
  fetch((window.NINA.base||'')+'/actions/report.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams({csrf:window.NINA.csrf,target_type:'blog',target_id:id,reason:reason})}).then(r=>r.json()).then(d=>alert(d.message||'Reported.')); }</script>
<?php require __DIR__ . '/includes/page-end.php'; ?>
