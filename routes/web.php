<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\VerificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// User Profile & Multi-Blog Network (/u/username and /u/username/blog1/)
Route::get('/u/{username}', [BlogController::class, 'showUser'])->name('user.profile');
Route::get('/u/{username}/feed', [BlogController::class, 'userFeed'])->name('user.feed');
Route::get('/u/{username}/feed.php', [BlogController::class, 'userFeed']);
Route::get('/u/{username}/{slug}', [BlogController::class, 'showUserBlog'])->name('user.blog');
Route::get('/u/{username}/{slug}/feed', [BlogController::class, 'userFeed'])->name('user.blog.feed');
Route::get('/u/{username}/{slug}/feed.php', [BlogController::class, 'userFeed']);
Route::get('/u/{username}/{slug}/post/{id}', [PostController::class, 'show'])->name('user.blog.post');

// Public feed & explore
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/feed', [HomeController::class, 'feed'])->name('feed');
Route::get('/feed.php', [HomeController::class, 'feed']);
Route::get('/trending', [HomeController::class, 'trending'])->name('trending');
Route::get('/trending.php', [HomeController::class, 'trending']);
Route::get('/community', [CommunityController::class, 'index'])->name('community');
Route::get('/community.php', [CommunityController::class, 'index']);
Route::post('/community/join', [CommunityController::class, 'join'])->name('community.join');
Route::post('/community/post', [CommunityController::class, 'storePost'])->name('community.post.store');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/tag/{tag}', [HomeController::class, 'tag'])->name('tag.show');
Route::get('/featured-blogs', [BlogController::class, 'featuredList'])->name('featured.blogs');
Route::get('/featured-blogs.php', [BlogController::class, 'featuredList']);

// Content viewing & Canonical redirects
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/blog.php', [BlogController::class, 'legacyShow']);
Route::get('/post/{id}', [PostController::class, 'show'])->name('post.show');
Route::get('/post.php', [PostController::class, 'legacyShow']);

// Public Storage & Uploads Direct Media Server (Fallback for symlink/Apache restrictions)
Route::get('/storage/{path}', function ($path) {
    $cleanPath = ltrim($path, '/');
    $candidates = [
        storage_path('app/public/' . $cleanPath),
        public_path('storage/' . $cleanPath),
        public_path('uploads/' . $cleanPath),
        public_path('assets/' . $cleanPath),
        base_path('assets/' . $cleanPath),
    ];
    foreach ($candidates as $candidate) {
        if (is_file($candidate)) {
            $mime = mime_content_type($candidate) ?: 'application/octet-stream';
            return response()->file($candidate, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }
    }
    // Fallback to hero.jpg if an image upload is missing
    $hero = public_path('assets/hero.jpg');
    if (is_file($hero)) {
        return response()->file($hero, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
    abort(404);
})->where('path', '.*')->name('media.storage');

// Social Actions
Route::get('/actions/comments', [ActionController::class, 'getComments'])->name('action.comments');
Route::get('/actions/comments.php', [ActionController::class, 'getComments']);
Route::post('/actions/like', [ActionController::class, 'like'])->name('action.like');
Route::post('/actions/like.php', [ActionController::class, 'like']);
Route::post('/actions/follow', [ActionController::class, 'follow'])->name('action.follow');
Route::post('/actions/follow.php', [ActionController::class, 'follow']);
Route::post('/actions/reblog', [ActionController::class, 'reblog'])->name('action.reblog');
Route::post('/actions/reblog.php', [ActionController::class, 'reblog']);
Route::post('/actions/comment', [ActionController::class, 'comment'])->name('action.comment');
Route::post('/actions/comment.php', [ActionController::class, 'comment']);
Route::post('/actions/report', [ActionController::class, 'report'])->name('action.report');
Route::post('/actions/report.php', [ActionController::class, 'report']);
Route::post('/actions/delete-post', [ActionController::class, 'deletePost'])->name('action.delete-post');
Route::post('/actions/delete-post.php', [ActionController::class, 'deletePost']);
Route::post('/actions/contact', [ActionController::class, 'contact'])->name('action.contact');
Route::post('/actions/contact.php', [ActionController::class, 'contact']);
Route::post('/actions/message', [ActionController::class, 'sendPrivateMessage'])->name('action.message');
Route::post('/actions/message.php', [ActionController::class, 'sendPrivateMessage']);
Route::post('/actions/block', [ActionController::class, 'toggleBlock'])->name('action.block');
Route::post('/actions/block.php', [ActionController::class, 'toggleBlock']);

// Advertising & Promotions
Route::get('/advertising', [PromotionController::class, 'index'])->name('advertising');
Route::get('/advertising.php', [PromotionController::class, 'index']);
Route::post('/advertising/purchase', [PromotionController::class, 'purchase'])->name('advertising.purchase');
Route::post('/advertising/assign-post', [PromotionController::class, 'assignPost'])->name('advertising.assignPost');
Route::post('/advertising/refund', [PromotionController::class, 'refund'])->name('advertising.refund');

// Crypto Donations & Premium Tiers
Route::get('/premium', [DonationController::class, 'index'])->name('premium');
Route::get('/premium.php', [DonationController::class, 'index']);
Route::post('/premium/checkout', [DonationController::class, 'checkout'])->name('premium.checkout');
Route::post('/api/donations/nowpayments/direct-payment', [DonationController::class, 'directPayment'])->name('donations.nowpayments.direct');
Route::get('/api/donations/nowpayments/check/{orderId}', [DonationController::class, 'checkOrderStatus'])->name('donations.nowpayments.check');
Route::get('/api/donations/nowpayments/status', [DonationController::class, 'apiStatus'])->name('donations.nowpayments.status');
Route::get('/api/donations/nowpayments/currencies', [DonationController::class, 'currencies'])->name('donations.nowpayments.currencies');
Route::post('/api/donations/nowpayments/ipn', [DonationController::class, 'nowpaymentsIpn'])->name('donations.nowpayments.ipn');
Route::post('/api/nowpayments/ipn', [DonationController::class, 'nowpaymentsIpn']);
Route::post('/api/donations/bitcart/ipn', [DonationController::class, 'bitcartIpn']);
Route::post('/api/donations/bitcart/simulate', [DonationController::class, 'simulateBitcartPayment'])->name('donations.bitcart.simulate');

// Static / Marketing & Information Pages
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/about.php', [PageController::class, 'about']);
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/faq.php', [PageController::class, 'faq']);
Route::get('/compliance', [PageController::class, 'compliance'])->name('compliance');
Route::get('/2257', [PageController::class, 'compliance'])->name('compliance.2257');
Route::get('/2257.php', [PageController::class, 'compliance']);
Route::get('/safety', [PageController::class, 'safety'])->name('safety');
Route::get('/safety.php', [PageController::class, 'safety']);
Route::get('/support', [PageController::class, 'support'])->name('support');
Route::get('/support.php', [PageController::class, 'support']);
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/terms.php', [PageController::class, 'terms']);
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/privacy.php', [PageController::class, 'privacy']);
Route::get('/rules', [PageController::class, 'rules'])->name('rules');
Route::get('/rules.php', [PageController::class, 'rules']);
Route::get('/dmca', [PageController::class, 'dmca'])->name('dmca');
Route::get('/dmca.php', [PageController::class, 'dmca']);
Route::get('/cookies', [PageController::class, 'cookies'])->name('cookies');
Route::get('/cookies.php', [PageController::class, 'cookies']);
Route::get('/contact', [PageController::class, 'contactView'])->name('contact');
Route::get('/contact.php', [PageController::class, 'contactView']);
Route::get('/forgot', [PageController::class, 'forgotView'])->name('forgot');
Route::get('/forgot.php', [PageController::class, 'forgotView']);
Route::post('/auth/forgot', [PageController::class, 'postForgot'])->name('auth.forgot');
Route::post('/auth/forgot.php', [PageController::class, 'postForgot']);
Route::get('/reset-password', [PageController::class, 'resetPasswordView'])->name('password.reset');
Route::get('/reset-password.php', [PageController::class, 'resetPasswordView']);
Route::post('/auth/reset-password', [PageController::class, 'postResetPassword'])->name('password.update');
Route::post('/auth/reset-password.php', [PageController::class, 'postResetPassword']);

// Authentication & OAuth
Route::get('/login', [AuthController::class, 'loginView'])->name('login.view');
Route::get('/login.php', [AuthController::class, 'loginView']);
Route::get('/register', [AuthController::class, 'registerView'])->name('register.view');
Route::get('/register.php', [AuthController::class, 'registerView']);
Route::match(['get', 'post'], '/auth/login', [AuthController::class, 'login'])->name('login');
Route::match(['get', 'post'], '/auth/login.php', [AuthController::class, 'login']);
Route::match(['get', 'post'], '/auth/register', [AuthController::class, 'register'])->name('register');
Route::match(['get', 'post'], '/auth/register.php', [AuthController::class, 'register']);
Route::get('/auth/google', [AuthController::class, 'googleRedirect'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'googleCallback'])->name('auth.google.callback');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout.php', [AuthController::class, 'logout']);

// Email Verification
Route::get('/email/verify', [AuthController::class, 'verifyNotice'])->name('verification.notice');
Route::get('/email/verify.php', [AuthController::class, 'verifyNotice']);
Route::get('/verify-email', [AuthController::class, 'verifyNotice']);
Route::get('/email/verify/{id}/{token}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])->name('verification.send');
Route::post('/email/verify/resend', [AuthController::class, 'resendVerificationEmail']);
Route::post('/auth/resend-verification', [AuthController::class, 'resendVerificationEmail']);

// Dashboard (Protected)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/post', [DashboardController::class, 'createPost'])->name('dashboard.post.create');
    Route::get('/dashboard/post.php', [DashboardController::class, 'createPost']);
    Route::post('/dashboard/post', [DashboardController::class, 'storePost'])->name('dashboard.post.store');
    Route::get('/dashboard/post/{id}/edit', [DashboardController::class, 'editPost'])->name('dashboard.post.edit');
    Route::match(['get', 'post'], '/dashboard/post/{id}/edit.php', [DashboardController::class, 'editPost']);
    Route::post('/dashboard/post/{id}/update', [DashboardController::class, 'updatePost'])->name('dashboard.post.update');
    Route::get('/dashboard/posts', [DashboardController::class, 'posts'])->name('dashboard.posts');
    Route::get('/dashboard/posts.php', [DashboardController::class, 'posts']);
    Route::post('/dashboard/post/{id}/delete', [DashboardController::class, 'deletePost'])->name('dashboard.post.delete');
    Route::get('/dashboard/queue', [DashboardController::class, 'queue'])->name('dashboard.queue');
    Route::post('/dashboard/queue/{id}/publish', [DashboardController::class, 'publishQueued'])->name('dashboard.queue.publish');
    Route::post('/dashboard/queue/{id}/delete', [DashboardController::class, 'deleteQueued'])->name('dashboard.queue.delete');
    Route::get('/dashboard/blogs', [DashboardController::class, 'blogs'])->name('dashboard.blogs');
    Route::get('/dashboard/blogs.php', [DashboardController::class, 'blogs']);
    Route::post('/dashboard/blogs', [DashboardController::class, 'updateBlog'])->name('dashboard.blogs.update');
    Route::post('/dashboard/blogs/new', [DashboardController::class, 'storeNewBlog'])->name('dashboard.blogs.store');
    Route::post('/dashboard/blogs/{id}/delete', [DashboardController::class, 'deleteBlog'])->name('dashboard.blogs.delete');
    Route::get('/dashboard/referrals', [DashboardController::class, 'referrals'])->name('dashboard.referrals');
    Route::get('/dashboard/messages', [DashboardController::class, 'messages'])->name('dashboard.messages');
    Route::get('/dashboard/messages/attachment/{id}', [DashboardController::class, 'messageAttachment'])->name('dashboard.messages.attachment');
    Route::post('/dashboard/messages/{id}/delete', [DashboardController::class, 'deleteMessage'])->name('dashboard.messages.delete');
    Route::get('/dashboard/notifications', [DashboardController::class, 'notifications'])->name('dashboard.notifications');
    Route::post('/dashboard/notifications', [DashboardController::class, 'updateNotifications'])->name('dashboard.notifications.update');
    Route::get('/dashboard/verification', [VerificationController::class, 'show'])->name('dashboard.verification');
    Route::post('/dashboard/verification', [VerificationController::class, 'submit'])->name('dashboard.verification.submit');
});

// Admin Control Panel (Protected)
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin');
    Route::get('/index.php', [AdminController::class, 'index']);
    Route::get('/posts', [AdminController::class, 'posts'])->name('admin.posts');
    Route::get('/posts.php', [AdminController::class, 'posts']);
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users.php', [AdminController::class, 'users']);
    Route::get('/blogs', [AdminController::class, 'blogs'])->name('admin.blogs');
    Route::get('/ads', [AdminController::class, 'ads'])->name('admin.ads');
    Route::get('/slider', [AdminController::class, 'slider'])->name('admin.slider');
    Route::get('/verifications', [AdminController::class, 'verifications'])->name('admin.verifications');
    Route::get('/donations', [AdminController::class, 'donations'])->name('admin.donations');
    Route::get('/referrals', [AdminController::class, 'referrals'])->name('admin.referrals');
    Route::get('/referrals.php', [AdminController::class, 'referrals']);
    Route::get('/logins', [AdminController::class, 'logins'])->name('admin.logins');
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/reports.php', [AdminController::class, 'reports']);
    Route::get('/support', [AdminController::class, 'support'])->name('admin.support');
    Route::get('/support.php', [AdminController::class, 'support']);
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/settings/save', [AdminController::class, 'saveSettings'])->name('admin.settings.save');
    Route::get('/pages', [AdminController::class, 'pages'])->name('admin.pages');
    Route::post('/pages/save', [AdminController::class, 'savePage'])->name('admin.pages.save');
    Route::post('/smtp/test', [AdminController::class, 'testSmtp'])->name('admin.smtp.test');

    // Admin Actions
    Route::post('/posts/{id}/delete', [AdminController::class, 'deletePost'])->name('admin.posts.delete');
    Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggle');
    Route::post('/users/{id}/verify-email', [AdminController::class, 'toggleEmailVerification'])->name('admin.users.verify_email');
    Route::post('/users/{id}/badge', [AdminController::class, 'toggleBadge'])->name('admin.users.badge');
    Route::post('/users/{id}/plan', [AdminController::class, 'setUserPlan'])->name('admin.users.plan');
    Route::post('/users/{id}/password', [AdminController::class, 'updateUserPassword'])->name('admin.users.password');
    Route::post('/users/{id}/points', [AdminController::class, 'adjustPoints'])->name('admin.users.points');
    Route::post('/users/{id}/delete', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::post('/blogs/{id}/toggle', [AdminController::class, 'toggleBlogStatus'])->name('admin.blogs.toggle');
    Route::post('/blogs/{id}/feature', [AdminController::class, 'toggleBlogFeatured'])->name('admin.blogs.feature');
    Route::post('/blogs/{id}/delete', [AdminController::class, 'deleteBlog'])->name('admin.blogs.delete');
    Route::post('/ads/create', [AdminController::class, 'createAd'])->name('admin.ads.create');
    Route::post('/ads/{id}/delete', [AdminController::class, 'deleteAd'])->name('admin.ads.delete');
    Route::post('/ads/{id}/toggle', [AdminController::class, 'toggleAd'])->name('admin.ads.toggle');
    Route::post('/slider/add', [AdminController::class, 'addSlide'])->name('admin.slider.add');
    Route::post('/slider/{id}/delete', [AdminController::class, 'deleteSlide'])->name('admin.slider.delete');
    Route::post('/verifications/{id}/review', [AdminController::class, 'reviewVerification'])->name('admin.verifications.review');
    Route::post('/tickets/{id}/reply', [AdminController::class, 'replyTicket'])->name('admin.tickets.reply');
    Route::post('/reports/{id}/resolve', [AdminController::class, 'resolveReport'])->name('admin.reports.resolve');
    Route::post('/community/cover', [AdminController::class, 'updateCommunityCover'])->name('community.cover');
});

// SEO & Crawlers: robots.txt and sitemap.xml
Route::get('/robots.txt', function () {
    $sitemapUrl = url('/sitemap.xml');
    $content = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /dashboard\nDisallow: /api/\n\nSitemap: {$sitemapUrl}\n";
    return response($content, 200)->header('Content-Type', 'text/plain');
});

Route::get('/sitemap.xml', function () {
    $blogs = \App\Models\Blog::where('status', 'active')->where('searchable', 1)->get();
    $posts = \App\Models\Post::where('status', 'published')->latest('published_at')->take(500)->get();

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    $pages = [
        url('/'),
        url('/explore'),
        url('/trending'),
        url('/community'),
        url('/advertising'),
        url('/premium'),
        url('/about'),
        url('/faq'),
        url('/terms'),
        url('/privacy'),
        url('/rules'),
        url('/dmca'),
        url('/compliance'),
        url('/support'),
        url('/contact'),
    ];
    foreach ($pages as $p) {
        $xml .= '  <url><loc>' . htmlspecialchars($p) . '</loc><changefreq>daily</changefreq><priority>0.8</priority></url>' . "\n";
    }

    foreach ($blogs as $b) {
        $xml .= '  <url><loc>' . htmlspecialchars(route('blog.show', ['slug' => $b->slug])) . '</loc><lastmod>' . $b->updated_at->toAtomString() . '</lastmod><changefreq>daily</changefreq><priority>' . ($b->is_main ? '0.9' : '0.7') . '</priority></url>' . "\n";
    }

    foreach ($posts as $post) {
        $lastmod = $post->published_at ? \Carbon\Carbon::parse($post->published_at)->toAtomString() : $post->created_at->toAtomString();
        $xml .= '  <url><loc>' . htmlspecialchars(route('post.show', ['id' => $post->id])) . '</loc><lastmod>' . $lastmod . '</lastmod><changefreq>weekly</changefreq><priority>0.6</priority></url>' . "\n";
    }

    $xml .= '</urlset>';

    return response($xml, 200)->header('Content-Type', 'application/xml');
})->name('sitemap');

// Web Cron / HTTP Cron endpoints for automated external cron runners (cron-job.org, EasyCron, cPanel curl cron)
Route::match(['get', 'post'], '/cron/publish-scheduled', function () {
    $now = now();
    $duePosts = \App\Models\Post::where('status', 'queued')
        ->whereNotNull('published_at')
        ->where('published_at', '<=', $now)
        ->get();

    $count = 0;
    foreach ($duePosts as $post) {
        $post->status = 'published';
        $post->save();
        $count++;
    }

    return response()->json([
        'status' => 'success',
        'published_count' => $count,
        'timestamp' => $now->toDateTimeString(),
        'message' => "Published {$count} queued/scheduled post(s)."
    ]);
})->name('cron.publish');

Route::match(['get', 'post'], '/api/cron/publish-scheduled', function () {
    return redirect()->route('cron.publish');
});

