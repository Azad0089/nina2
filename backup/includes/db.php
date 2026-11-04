<?php
declare(strict_types=1);
/**
 * Database connection + automatic schema creation.
 * Included once by config.php. Nothing to run by hand: on the very first request
 * against an empty database this file creates every table and seeds default
 * settings and a default admin account, then never touches the schema again
 * (it checks for the `settings` table before doing any of that work).
 */

function db(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $ex) {
        http_response_code(500);
        exit('Database connection failed. Check DB_HOST / DB_NAME / DB_USER / DB_PASS in includes/config.php. (' . $ex->getMessage() . ')');
    }
    db_migrate($pdo);
    return $pdo;
}

function db_migrate(PDO $pdo): void {
    // Cheap check: if the settings table exists we've already migrated. Avoids
    // running ~15 CREATE TABLE statements on every single request.
    try {
        $pdo->query('SELECT 1 FROM settings LIMIT 1');
        return; // already installed
    } catch (PDOException $e) {
        // fall through and install
    }

    $charset = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

    $statements = [];

    $statements[] = "CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        username VARCHAR(30) NOT NULL,
        email VARCHAR(190) NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('user','admin') NOT NULL DEFAULT 'user',
        status ENUM('active','suspended','banned') NOT NULL DEFAULT 'active',
        google_id VARCHAR(64) NULL,
        avatar_path VARCHAR(255) NULL,
        is_premium TINYINT(1) NOT NULL DEFAULT 0,
        premium_tier ENUM('none','premium','premium_plus') NOT NULL DEFAULT 'none',
        premium_expires_at DATETIME NULL,
        points INT NOT NULL DEFAULT 0,
        badge_pro TINYINT(1) NOT NULL DEFAULT 0,
        badge_id TINYINT(1) NOT NULL DEFAULT 0,
        badge_vip TINYINT(1) NOT NULL DEFAULT 0,
        is_staff_pick TINYINT(1) NOT NULL DEFAULT 0,
        referral_code VARCHAR(16) NULL,
        referred_by INT UNSIGNED NULL,
        last_login_at DATETIME NULL,
        last_login_ip VARCHAR(45) NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uq_users_username (username),
        UNIQUE KEY uq_users_email (email),
        UNIQUE KEY uq_users_refcode (referral_code)
    ) $charset";

    $statements[] = "CREATE TABLE IF NOT EXISTS blogs (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id INT UNSIGNED NOT NULL,
        slug VARCHAR(50) NOT NULL,
        title VARCHAR(100) NOT NULL,
        type ENUM('personal','community') NOT NULL DEFAULT 'personal',
        is_main TINYINT(1) NOT NULL DEFAULT 0,
        tagline VARCHAR(200) NULL,
        color VARCHAR(9) NOT NULL DEFAULT '#ff2bd6',
        avatar_path VARCHAR(255) NULL,
        cover_path VARCHAR(255) NULL,
        show_profile TINYINT(1) NOT NULL DEFAULT 0,
        bio TEXT NULL,
        website1 VARCHAR(255) NULL,
        website2 VARCHAR(255) NULL,
        website3 VARCHAR(255) NULL,
        contact_email VARCHAR(190) NULL,
        status ENUM('active','suspended','banned') NOT NULL DEFAULT 'active',
        is_featured TINYINT(1) NOT NULL DEFAULT 0,
        featured_until DATETIME NULL,
        searchable TINYINT(1) NOT NULL DEFAULT 1,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uq_blogs_slug (slug),
        KEY ix_blogs_user (user_id)
    ) $charset";

    $statements[] = "CREATE TABLE IF NOT EXISTS posts (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        blog_id INT UNSIGNED NOT NULL,
        type ENUM('text','photo','video','link','quote','chat') NOT NULL,
        heading VARCHAR(200) NULL,
        body TEXT NULL,
        caption TEXT NULL,
        alt_text VARCHAR(255) NULL,
        media_path VARCHAR(255) NULL,
        media_kind ENUM('none','image','video') NOT NULL DEFAULT 'none',
        duration VARCHAR(10) NULL,
        quote_text TEXT NULL,
        quote_source VARCHAR(200) NULL,
        link_title VARCHAR(200) NULL,
        link_url VARCHAR(500) NULL,
        link_domain VARCHAR(150) NULL,
        chat_json TEXT NULL,
        reblog_of_post_id INT UNSIGNED NULL,
        status ENUM('published','scheduled','draft') NOT NULL DEFAULT 'published',
        scheduled_at DATETIME NULL,
        is_featured TINYINT(1) NOT NULL DEFAULT 0,
        featured_until DATETIME NULL,
        views INT UNSIGNED NOT NULL DEFAULT 0,
        published_at DATETIME NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY ix_posts_blog (blog_id),
        KEY ix_posts_status_pub (status, published_at),
        KEY ix_posts_type (type)
    ) $charset";

    $statements[] = "CREATE TABLE IF NOT EXISTS tags (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(60) NOT NULL,
        uses INT UNSIGNED NOT NULL DEFAULT 0,
        PRIMARY KEY (id),
        UNIQUE KEY uq_tags_name (name)
    ) $charset";

    $statements[] = "CREATE TABLE IF NOT EXISTS post_tags (
        post_id INT UNSIGNED NOT NULL,
        tag_id INT UNSIGNED NOT NULL,
        PRIMARY KEY (post_id, tag_id),
        KEY ix_pt_tag (tag_id)
    ) $charset";

    $statements[] = "CREATE TABLE IF NOT EXISTS follows (
        follower_user_id INT UNSIGNED NOT NULL,
        blog_id INT UNSIGNED NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (follower_user_id, blog_id),
        KEY ix_follows_blog (blog_id)
    ) $charset";

    $statements[] = "CREATE TABLE IF NOT EXISTS likes (
        user_id INT UNSIGNED NOT NULL,
        post_id INT UNSIGNED NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (user_id, post_id),
        KEY ix_likes_post (post_id)
    ) $charset";

    $statements[] = "CREATE TABLE IF NOT EXISTS reports (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        reporter_user_id INT UNSIGNED NULL,
        target_type ENUM('post','blog','user') NOT NULL,
        target_id INT UNSIGNED NOT NULL,
        reason VARCHAR(500) NOT NULL,
        status ENUM('open','resolved','dismissed') NOT NULL DEFAULT 'open',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY ix_reports_status (status)
    ) $charset";

    $statements[] = "CREATE TABLE IF NOT EXISTS support_tickets (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(190) NOT NULL,
        topic VARCHAR(30) NOT NULL,
        link VARCHAR(500) NULL,
        message TEXT NOT NULL,
        status ENUM('open','closed') NOT NULL DEFAULT 'open',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset";

    $statements[] = "CREATE TABLE IF NOT EXISTS categories (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(80) NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY uq_categories_name (name)
    ) $charset";

    $statements[] = "CREATE TABLE IF NOT EXISTS login_logs (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id INT UNSIGNED NOT NULL,
        ip VARCHAR(45) NULL,
        user_agent VARCHAR(255) NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY ix_login_logs_user (user_id)
    ) $charset";

    $statements[] = "CREATE TABLE IF NOT EXISTS referrals (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        referrer_user_id INT UNSIGNED NOT NULL,
        referred_user_id INT UNSIGNED NOT NULL,
        points_awarded INT NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY ix_ref_referrer (referrer_user_id)
    ) $charset";

    $statements[] = "CREATE TABLE IF NOT EXISTS promotions (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        target_type ENUM('post','blog') NOT NULL,
        target_id INT UNSIGNED NOT NULL,
        placement VARCHAR(40) NOT NULL,
        points_spent INT NOT NULL DEFAULT 0,
        starts_at DATETIME NOT NULL,
        ends_at DATETIME NOT NULL,
        granted_by_admin TINYINT(1) NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY ix_promo_active (ends_at)
    ) $charset";

    $statements[] = "CREATE TABLE IF NOT EXISTS password_resets (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id INT UNSIGNED NOT NULL,
        token_hash VARCHAR(64) NOT NULL,
        expires_at DATETIME NOT NULL,
        used TINYINT(1) NOT NULL DEFAULT 0,
        PRIMARY KEY (id),
        KEY ix_pwreset_user (user_id)
    ) $charset";

    $statements[] = "CREATE TABLE IF NOT EXISTS settings (
        skey VARCHAR(80) NOT NULL,
        svalue LONGTEXT NULL,
        PRIMARY KEY (skey)
    ) $charset";

    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    foreach ($statements as $sql) {
        $pdo->exec($sql);
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');

    db_seed($pdo);
}

function db_seed(PDO $pdo): void {
    // Default site settings (editable later from the admin panel).
    $defaults = [
        'site_name'              => 'Nina.Sucks',
        'meta_description'       => 'An 18+ blogging community. Unlimited blogs, follow, reblog and post text, photo, video, quote, link and chat.',
        'logo_path'               => '',
        'favicon_path'            => '',
        'primary_color'           => '#ff2bd6',
        'secondary_color'         => '#19e3ff',
        'head_code'               => '',
        'max_photo_mb'            => '15',
        'max_video_mb'            => '200',
        'ffmpeg_enabled'          => '0',
        'ffmpeg_path'             => '/usr/bin/ffmpeg',
        'storage_driver'          => 'local',
        'donations_enabled'       => '1',
        'premium_price_usd'       => '15',
        'premium_plus_price_usd'  => '30',
        'premium_monthly_points'  => '5000',
        'premium_plus_monthly_points' => '11000',
        'referral_points'         => '100',
        'max_queue_per_blog'      => '20',
        'nowpayments_api_key'     => '',
        'bitcart_api_key'         => '',
    ];
    $ins = $pdo->prepare('INSERT IGNORE INTO settings (skey, svalue) VALUES (?, ?)');
    foreach ($defaults as $k => $v) { $ins->execute([$k, $v]); }

    // Default admin account: admin / admin@example.com / ChangeMe123!
    // IMPORTANT: change this password immediately after first login (Admin > Users).
    $hash = password_hash('ChangeMe123!', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT IGNORE INTO users (username, email, password_hash, role, status, referral_code)
                    VALUES ('admin', 'admin@example.com', ?, 'admin', 'active', ?)")
        ->execute([$hash, bin2hex(random_bytes(4))]);

    // Starter tags/categories so admin screens aren't empty on first run.
    $pdo->exec("INSERT IGNORE INTO tags (name) VALUES ('neon'),('selfie'),('nightshift'),('cozy'),('bts')");
    $pdo->exec("INSERT IGNORE INTO categories (name) VALUES ('Photo'),('Video'),('Lifestyle'),('Chat')");
}
