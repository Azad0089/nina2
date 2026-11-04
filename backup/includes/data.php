<?php
/**
 * Site lists. Static marketing content (nav, footer, hero slides, badge legend) stays as
 * plain arrays. Anything that is actual user content (posts, blogs, tags) is loaded from
 * the database here so every template (card.php, index.php, feed.php, trending.php) can
 * keep using a plain foreach with no changes.
 */

function nav_items(): array {
    return [
        ['key' => 'explore',     'label' => 'Explore',     'href' => url(),                   'icon' => 'compass'],
        ['key' => 'trending',    'label' => 'Trending',    'href' => url('trending.php'),     'icon' => 'flame'],
        ['key' => 'advertising', 'label' => 'Advertising', 'href' => url('advertising.php'),  'icon' => 'megaphone'],
        ['key' => 'premium',     'label' => 'Premium',     'href' => url('premium.php'),      'icon' => 'crown'],
        ['key' => 'support',     'label' => 'Support',     'href' => url('support.php'),      'icon' => 'help'],
    ];
}

function footer_links(): array {
    return [
        ['label' => 'Terms',        'href' => url('terms.php')],
        ['label' => 'Privacy',      'href' => url('privacy.php')],
        ['label' => 'Rules',        'href' => url('rules.php')],
        ['label' => '2257 & DMCA',  'href' => url('dmca.php')],
        ['label' => 'Cookies',      'href' => url('cookies.php')],
        ['label' => 'Contact',      'href' => url('contact.php')],
    ];
}

/** Hero slides on the home page. Marketing copy; edit the array to change it. */
function hero_slides(): array {
    return [
        [
            'color' => '#ff2bd6', 'img' => 'hero.jpg', 'alt' => 'Neon-lit desk with laptop and city lights',
            'x' => 10, 'y' => 22, 'z' => 1.5,  'mx' => 44, 'my' => 30, 'mz' => 1.25,
            'title' => 'Post it loud.',
            'text'  => 'Text, photos, quotes, links, chats and video on a blog that looks exactly how you want it.',
            'cta'   => [
                ['label' => 'Create your blog', 'href' => url('register.php'), 'class' => 'btn-fill',  'modal' => 'register'],
                ['label' => 'Explore posts',    'href' => '#feed',             'class' => 'btn-ghost'],
            ],
        ],
        [
            'color' => '#19e3ff', 'img' => 'hero.jpg', 'alt' => 'Neon sign glowing on a window',
            'x' => 62, 'y' => 0,  'z' => 2.3,  'mx' => 72, 'my' => 0,  'mz' => 1.5,
            'title' => 'One login. Unlimited blogs.',
            'text'  => 'Every blog gets its own colors, cover and subdomain. Your first blog doubles as your profile, with bio, links and contact.',
            'cta'   => [
                ['label' => 'Start a blog',     'href' => url('register.php'), 'class' => 'btn-fill',  'modal' => 'register'],
                ['label' => 'See how it works', 'href' => url('support.php'),  'class' => 'btn-ghost'],
            ],
        ],
        [
            'color' => '#ffd60a', 'img' => 'hero.jpg', 'alt' => 'Sleeping cat, candle and cushion',
            'x' => 93, 'y' => 91, 'z' => 2.2,  'mx' => 94, 'my' => 90, 'mz' => 1.5,
            'title' => 'Boost what you make.',
            'text'  => 'Premium members earn monthly points to feature posts and profiles on Explore. Buy views or flat-rate placements in the ad marketplace.',
            'cta'   => [
                ['label' => 'Open the marketplace', 'href' => url('advertising.php'), 'class' => 'btn-fill'],
                ['label' => 'Go premium',           'href' => url('premium.php'),     'class' => 'btn-ghost'],
            ],
        ],
        [
            'color' => '#8a3dff', 'img' => 'hero.jpg', 'alt' => 'Full neon bedroom scene',
            'x' => 50, 'y' => 50, 'z' => 1.04, 'mx' => 52, 'my' => 40, 'mz' => 1.12,
            'title' => 'Scroll all night.',
            'text'  => 'The front page never locks. Read from the newest post to the oldest with no login wall in your way.',
            'cta'   => [
                ['label' => 'Start scrolling', 'href' => '#feed',             'class' => 'btn-fill'],
                ['label' => 'Sign in',         'href' => url('login.php'),    'class' => 'btn-ghost', 'modal' => 'login'],
            ],
        ],
    ];
}

function badge_legend(): array {
    return [
        ['badge' => 'pro', 'text' => 'Premium: yearly supporter'],
        ['badge' => 'id',  'text' => 'Verified: ID-checked creator'],
        ['badge' => 'vip', 'text' => 'VIP: community favorite'],
    ];
}

function post_types(): array {
    return [
        'photo' => ['Photo', 'photo'], 'text' => ['Text', 'text'], 'quote' => ['Quote', 'quote'],
        'link'  => ['Link', 'link'],   'chat' => ['Chat', 'chat'], 'video' => ['Video', 'video'],
    ];
}

function crop_preset(string $name): array {
    static $p = [
        'face'   => ['x' => 43, 'y' => 22, 'z' => 1.6, 'ratio' => '4/5'],
        'laptop' => ['x' => 20, 'y' => 72, 'z' => 1.8, 'ratio' => '1/1'],
        'sign'   => ['x' => 68, 'y' => 6,  'z' => 3.2, 'ratio' => '16/10'],
        'cat'    => ['x' => 96, 'y' => 92, 'z' => 1.9, 'ratio' => '1/1'],
        'notes'  => ['x' => 70, 'y' => 96, 'z' => 2.4, 'ratio' => '5/4'],
        'books'  => ['x' => 4,  'y' => 30, 'z' => 1.45,'ratio' => '4/5'],
        'mug'    => ['x' => 6,  'y' => 88, 'z' => 1.9, 'ratio' => '1/1'],
        'scene'  => ['x' => 50, 'y' => 50, 'z' => 1,   'ratio' => '16/10'],
    ];
    return $p[$name] ?? ['x' => 50, 'y' => 50, 'z' => 1];
}

function valid_type(?string $t): string {
    return ($t !== null && isset(post_types()[$t])) ? $t : 'all';
}

/** Badge shown on a card/profile: highest-prestige badge wins the single icon slot. */
function top_badge(array $u): string {
    if (!empty($u['badge_vip'])) return 'vip';
    if (!empty($u['badge_id']))  return 'id';
    if (!empty($u['badge_pro'])) return 'pro';
    return '';
}

/** Turn one posts+blogs+users DB row into the array shape includes/card.php expects. */
function post_row_to_card(array $r): array {
    $p = [
        'id'      => (int)$r['id'],
        'type'    => $r['type'],
        'blog'    => $r['blog_slug'],
        'blog_id' => (int)$r['blog_id'],
        'color'   => $r['blog_color'] ?: '#ff2bd6',
        'badge'   => top_badge($r),
        'avatar'  => $r['blog_avatar'] ?: null,
        'minutes' => max(0, (int)($r['minutes_ago'] ?? 0)),
        'notes'    => (int)($r['like_count'] ?? 0) + (int)($r['reblog_count'] ?? 0),
        'like_count'   => (int)($r['like_count'] ?? 0),
        'reblog_count' => (int)($r['reblog_count'] ?? 0),
        'tags'    => post_tags_for($r['id']),
    ];
    switch ($r['type']) {
        case 'photo':
        case 'video':
            $p['img']      = $r['media_path'];
            $p['crop']     = ['x' => 50, 'y' => 50, 'z' => 1];
            $p['caption']  = $r['caption'];
            $p['alt']      = $r['alt_text'] ?: $r['caption'];
            $p['duration'] = $r['duration'];
            break;
        case 'text':
            $p['heading'] = $r['heading'];
            $p['body']    = $r['body'];
            break;
        case 'quote':
            $p['quote']  = $r['quote_text'];
            $p['source'] = $r['quote_source'];
            break;
        case 'link':
            $p['title']  = $r['link_title'];
            $p['domain'] = $r['link_domain'];
            $p['url']    = $r['link_url'];
            break;
        case 'chat':
            $p['lines'] = $r['chat_json'] ? (json_decode((string)$r['chat_json'], true) ?: []) : [];
            break;
    }
    return $p;
}

function post_tags_for(int $postId): array {
    static $cache = [];
    if (isset($cache[$postId])) return $cache[$postId];
    $stmt = db()->prepare('SELECT t.name FROM tags t JOIN post_tags pt ON pt.tag_id = t.id WHERE pt.post_id = ? ORDER BY t.name');
    $stmt->execute([$postId]);
    return $cache[$postId] = array_column($stmt->fetchAll(), 'name');
}

const POST_SELECT = "SELECT p.*, b.slug AS blog_slug, b.color AS blog_color, b.avatar_path AS blog_avatar,
        u.badge_pro, u.badge_id, u.badge_vip,
        TIMESTAMPDIFF(MINUTE, p.published_at, NOW()) AS minutes_ago,
        (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) AS like_count,
        (SELECT COUNT(*) FROM posts rb WHERE rb.reblog_of_post_id = p.id) AS reblog_count
    FROM posts p
    JOIN blogs b ON b.id = p.blog_id
    JOIN users u ON u.id = b.user_id
    WHERE p.status = 'published' AND b.status = 'active' AND u.status = 'active'";

/** Newest-first page of published posts. $type = all|photo|text|quote|link|chat|video. */
function feed_posts(int $offset, int $limit, string $type = 'all'): array {
    $sql = POST_SELECT;
    $params = [];
    if ($type !== 'all') { $sql .= ' AND p.type = ?'; $params[] = $type; }
    $sql .= ' ORDER BY p.published_at DESC, p.id DESC LIMIT ? OFFSET ?';
    $params[] = $limit; $params[] = $offset;

    $stmt = db()->prepare($sql);
    foreach ($params as $i => $v) {
        $stmt->bindValue($i + 1, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->execute();
    return array_map('post_row_to_card', $stmt->fetchAll());
}

/** Most-liked posts (Trending page). */
function top_posts(int $limit = 6): array {
    $sql = POST_SELECT . ' ORDER BY like_count DESC, p.published_at DESC LIMIT ?';
    $stmt = db()->prepare($sql);
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return array_map('post_row_to_card', $stmt->fetchAll());
}

/** One post by id, or null. */
function get_post(int $id): ?array {
    $stmt = db()->prepare(POST_SELECT . ' AND p.id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ? post_row_to_card($row) : null;
}

/** Featured/promoted blogs for the home page + trending page. Falls back to newest blogs if none are featured. */
function featured_blogs(int $limit = 4): array {
    $pdo = db();
    $sql = "SELECT b.*, u.badge_pro, u.badge_id, u.badge_vip FROM blogs b JOIN users u ON u.id = b.user_id
            WHERE b.status = 'active' AND (b.is_featured = 1 OR (b.featured_until IS NOT NULL AND b.featured_until > NOW()))
            ORDER BY RAND() LIMIT ?";
    $stmt = $pdo->prepare($sql); $stmt->bindValue(1, $limit, PDO::PARAM_INT); $stmt->execute();
    $rows = $stmt->fetchAll();
    if (!$rows) {
        $sql2 = "SELECT b.*, u.badge_pro, u.badge_id, u.badge_vip FROM blogs b JOIN users u ON u.id = b.user_id
                 WHERE b.status = 'active' ORDER BY b.created_at DESC LIMIT ?";
        $stmt = $pdo->prepare($sql2); $stmt->bindValue(1, $limit, PDO::PARAM_INT); $stmt->execute();
        $rows = $stmt->fetchAll();
    }
    return array_map(function ($b) {
        return [
            'name' => $b['slug'], 'color' => $b['color'] ?: '#ff2bd6', 'badge' => top_badge($b),
            'tagline' => $b['tagline'] ?: '', 'img' => $b['cover_path'] ?: 'hero.jpg',
            'x' => 50, 'y' => 50, 'z' => 1, 'href' => url('blog.php?u=' . urlencode($b['slug'])),
        ];
    }, $rows);
}

/** Suggested blogs to follow, shown in the right rail. */
function suggested_blogs(int $limit = 4): array {
    $stmt = db()->prepare("SELECT id, slug, color, is_featured FROM blogs WHERE status = 'active' ORDER BY RAND() LIMIT ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT); $stmt->execute();
    return array_map(fn($b) => [
        'id' => (int)$b['id'], 'name' => $b['slug'], 'color' => $b['color'] ?: '#ff2bd6',
        'note' => $b['is_featured'] ? 'Featured' : 'Suggested',
    ], $stmt->fetchAll());
}

/** Trending tags this week: [tag, uses]. */
function trending_tags(int $limit = 14): array {
    $stmt = db()->prepare('SELECT name, uses FROM tags ORDER BY uses DESC, name ASC LIMIT ?');
    $stmt->bindValue(1, $limit, PDO::PARAM_INT); $stmt->execute();
    $rows = $stmt->fetchAll();
    return $rows ? array_map(fn($t) => [$t['name'], (int)$t['uses']], $rows) : [['welcome', 1]];
}
