<?php
declare(strict_types=1);
/** Likes, follows, reblogs, reports: small helper queries + the actions behind actions/*.php */

function post_liked_by(int $userId, int $postId): bool {
    $s = db()->prepare('SELECT 1 FROM likes WHERE user_id = ? AND post_id = ?');
    $s->execute([$userId, $postId]);
    return (bool)$s->fetchColumn();
}
function blog_followed_by(int $userId, int $blogId): bool {
    $s = db()->prepare('SELECT 1 FROM follows WHERE follower_user_id = ? AND blog_id = ?');
    $s->execute([$userId, $blogId]);
    return (bool)$s->fetchColumn();
}
/** True if $blogId belongs to $userId (used to hide the Follow button on your own posts). */
function main_or_owned_blog_ids_include(int $userId, int $blogId): bool {
    $s = db()->prepare('SELECT 1 FROM blogs WHERE id = ? AND user_id = ?');
    $s->execute([$blogId, $userId]);
    return (bool)$s->fetchColumn();
}

function toggle_like(int $userId, int $postId): bool {
    if (post_liked_by($userId, $postId)) {
        db()->prepare('DELETE FROM likes WHERE user_id = ? AND post_id = ?')->execute([$userId, $postId]);
        return false;
    }
    db()->prepare('INSERT IGNORE INTO likes (user_id, post_id) VALUES (?, ?)')->execute([$userId, $postId]);
    return true;
}
function toggle_follow(int $userId, int $blogId): bool {
    if (blog_followed_by($userId, $blogId)) {
        db()->prepare('DELETE FROM follows WHERE follower_user_id = ? AND blog_id = ?')->execute([$userId, $blogId]);
        return false;
    }
    db()->prepare('INSERT IGNORE INTO follows (follower_user_id, blog_id) VALUES (?, ?)')->execute([$userId, $blogId]);
    return true;
}

/** Reblog: creates a copy of the post on the acting user's main blog, pointing back at the original. */
function reblog_post(int $userId, int $postId): array {
    $blog = main_blog_of($userId);
    if (!$blog) return ['ok' => false, 'message' => 'No blog to reblog into.'];
    if ($blog['status'] !== 'active') return ['ok' => false, 'message' => 'Your blog is currently restricted.'];

    $orig = db()->prepare('SELECT * FROM posts WHERE id = ? AND status = "published"');
    $orig->execute([$postId]);
    $o = $orig->fetch();
    if (!$o) return ['ok' => false, 'message' => 'Post not found.'];

    $rootId = $o['reblog_of_post_id'] ?: $o['id']; // always point at the original, not a chain
    $cols = ['blog_id','type','heading','body','caption','alt_text','media_path','media_kind','duration',
             'quote_text','quote_source','link_title','link_url','link_domain','chat_json',
             'reblog_of_post_id','status','published_at'];
    $vals = [
        $blog['id'], $o['type'], $o['heading'], $o['body'], $o['caption'], $o['alt_text'], $o['media_path'],
        $o['media_kind'], $o['duration'], $o['quote_text'], $o['quote_source'], $o['link_title'], $o['link_url'],
        $o['link_domain'], $o['chat_json'], $rootId, 'published', date('Y-m-d H:i:s'),
    ];
    $placeholders = implode(',', array_fill(0, count($cols), '?'));
    db()->prepare('INSERT INTO posts (' . implode(',', $cols) . ') VALUES (' . $placeholders . ')')->execute($vals);
    $newId = (int)db()->lastInsertId(); // must be read before any other query -- some drivers reset it on the next statement

    // carry over tags
    $tags = db()->prepare('SELECT tag_id FROM post_tags WHERE post_id = ?');
    $tags->execute([$rootId]);
    $ins = db()->prepare('INSERT IGNORE INTO post_tags (post_id, tag_id) VALUES (?, ?)');
    foreach ($tags->fetchAll() as $t) { $ins->execute([$newId, $t['tag_id']]); }

    return ['ok' => true, 'post_id' => $newId];
}

function file_report(?int $reporterId, string $targetType, int $targetId, string $reason): void {
    $reason = trim($reason) !== '' ? trim($reason) : 'No reason given.';
    db()->prepare('INSERT INTO reports (reporter_user_id, target_type, target_id, reason) VALUES (?, ?, ?, ?)')
        ->execute([$reporterId, $targetType, $targetId, mb_substr($reason, 0, 500)]);
}

/** Attach/replace a post's tags from a raw comma/space separated string. Creates new tags and bumps `uses`. */
function set_post_tags(int $postId, string $rawTags): void {
    $pdo = db();
    $old = $pdo->prepare('SELECT tag_id FROM post_tags WHERE post_id = ?');
    $old->execute([$postId]);
    $oldIds = array_column($old->fetchAll(), 'tag_id');
    if ($oldIds) {
        $pdo->exec('UPDATE tags SET uses = GREATEST(0, uses - 1) WHERE id IN (' . implode(',', array_map('intval', $oldIds)) . ')');
        $pdo->prepare('DELETE FROM post_tags WHERE post_id = ?')->execute([$postId]);
    }
    $names = array_unique(array_filter(array_map(
        fn($t) => mb_substr(trim((string)$t, " \t\n\r\0\x0B#"), 0, 60),
        preg_split('/[,\n]+/', $rawTags) ?: []
    )));
    $insTag  = $pdo->prepare('INSERT INTO tags (name) VALUES (?) ON DUPLICATE KEY UPDATE uses = uses + 1');
    $findTag = $pdo->prepare('SELECT id FROM tags WHERE name = ?');
    $link    = $pdo->prepare('INSERT IGNORE INTO post_tags (post_id, tag_id) VALUES (?, ?)');
    foreach ($names as $name) {
        if ($name === '') continue;
        $insTag->execute([$name]);
        $findTag->execute([$name]);
        $tagId = $findTag->fetchColumn();
        if ($tagId) { $link->execute([$postId, (int)$tagId]); }
    }
}
