<?php
/**
 * One post card. Expects $p (see post_row_to_card() in data.php for the keys).
 * Used by index.php, feed.php (infinite scroll) and trending.php.
 */
$color = $p['color'] ?? '#ff2bd6';
$type  = $p['type'];
$me    = current_user();
$liked     = $me && post_liked_by((int)$me['id'], $p['id']);
$following = $me && blog_followed_by((int)$me['id'], $p['blog_id']);
$isMine    = $me && main_or_owned_blog_ids_include((int)$me['id'], $p['blog_id']);
?>
<article class="card t-<?= e($type) ?>" data-type="<?= e($type) ?>" data-post-id="<?= (int)$p['id'] ?>" style="--c:<?= e($color) ?>">
  <header class="c-head">
    <a href="<?= e(url('blog.php?u=' . urlencode($p['blog']))) ?>" style="display:contents">
    <?php if (!empty($p['avatar'])): ?>
      <img class="av" src="<?= e(media_url($p['avatar'])) ?>" alt="" width="36" height="36" loading="lazy">
    <?php else: ?>
      <span class="av" style="--c:<?= e($color) ?>"><?= e(initial($p['blog'])) ?></span>
    <?php endif; ?>
    </a>
    <div class="c-who"><b><a href="<?= e(url('blog.php?u=' . urlencode($p['blog']))) ?>"><?= e($p['blog']) ?></a> <?= badge($p['badge'] ?? '') ?></b><small><?= e($type) ?> post</small></div>
    <?php if (!$isMine): ?>
    <button class="follow<?= $following ? ' on' : '' ?>" type="button" data-blog="<?= e($p['blog']) ?>"><?= $following ? 'Following' : 'Follow' ?></button>
    <?php endif; ?>
  </header>

<?php if ($type === 'photo' || $type === 'video'): ?>
  <figure class="media crop" style="<?= e(crop_style($p['crop'] ?? [])) ?>">
    <img src="<?= e(media_url($p['img'])) ?>" alt="<?= e($p['alt'] ?? $p['caption'] ?? '') ?>" loading="lazy" decoding="async">
    <?php if ($type === 'video'): ?>
      <video controls preload="none" poster="" style="display:none"></video>
      <button class="play" type="button" aria-label="Play video" data-video-src="<?= e(media_url($p['img'])) ?>"><?= icon('play') ?></button>
      <span class="dur"><?= e($p['duration'] ?? '') ?></span>
    <?php endif; ?>
  </figure>
  <div class="c-body"><p><?= e($p['caption'] ?? '') ?></p>

<?php elseif ($type === 'text'): ?>
  <div class="blk"><h3><?= e($p['heading']) ?></h3><p><?= nl2br(e($p['body'])) ?></p></div>
  <div class="c-body">

<?php elseif ($type === 'quote'): ?>
  <div class="c-body">
    <blockquote><p><?= e($p['quote']) ?></p><cite><?= e($p['source'] ?? '') ?></cite></blockquote>

<?php elseif ($type === 'link'): ?>
  <a class="lk" href="<?= e($p['url'] ?? '#') ?>" target="_blank" rel="nofollow noopener"><div class="lk-t"><?= icon('link') ?></div><div><b><?= e($p['title']) ?></b><small><?= e($p['domain'] ?? '') ?></small></div></a>
  <div class="c-body">

<?php elseif ($type === 'chat'): ?>
  <div class="chat">
    <?php foreach ($p['lines'] as [$who, $line]): ?>
      <p><b><?= e($who) ?>:</b><?= e($line) ?></p>
    <?php endforeach; ?>
  </div>
  <div class="c-body">
<?php endif; ?>

    <?php if (!empty($p['tags'])): ?>
      <div class="tags"><?php foreach ($p['tags'] as $t): ?><a href="<?= e(url('trending.php?tag=' . urlencode($t))) ?>">#<?= e($t) ?></a><?php endforeach; ?></div>
    <?php endif; ?>
  </div>

  <footer class="c-foot">
    <a class="act" href="<?= e(url('post.php?id=' . $p['id'])) ?>" aria-label="View post"><?= icon('reply') ?><span><?= fmt_count(0) ?></span></a>
    <button class="act reblog" type="button" aria-label="Reblog" data-post-id="<?= (int)$p['id'] ?>"><?= icon('repeat') ?><span><?= fmt_count((int)($p['reblog_count'] ?? 0)) ?></span></button>
    <button class="act like<?= $liked ? ' on' : '' ?>" type="button" aria-label="Like" data-post-id="<?= (int)$p['id'] ?>"><?= icon('heart') ?><span><?= fmt_count((int)($p['like_count'] ?? 0)) ?></span></button>
    <span class="ago"><?= e(time_ago((int)($p['minutes'] ?? 5))) ?> ago</span>
    <button class="act report" type="button" aria-label="Report" data-post-id="<?= (int)$p['id'] ?>" title="Report this post"><?= icon('flag') ?></button>
  </footer>
</article>
