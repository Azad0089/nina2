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

// Public feed & explore
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/feed', [HomeController::class, 'feed'])->name('feed');
Route::get('/feed.php', [HomeController::class, 'feed']);
Route::get('/trending', [HomeController::class, 'trending'])->name('trending');
Route::get('/trending.php', [HomeController::class, 'trending']);
Route::get('/community', [CommunityController::class, 'index'])->name('community');
Route::get('/community.php', [CommunityController::class, 'index']);
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/tag/{tag}', [HomeController::class, 'tag'])->name('tag.show');

// Content viewing
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/blog.php', [BlogController::class, 'legacyShow']);
Route::get('/post/{id}', [PostController::class, 'show'])->name('post.show');
Route::get('/post.php', [PostController::class, 'legacyShow']);

// Social Actions
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
Route::post('/actions/contact', [ActionController::class, 'contact'])->name('action.contact');
Route::post('/actions/contact.php', [ActionController::class, 'contact']);

// Advertising & Promotions
Route::get('/advertising', [PromotionController::class, 'index'])->name('advertising');
Route::get('/advertising.php', [PromotionController::class, 'index']);
Route::post('/advertising/purchase', [PromotionController::class, 'purchase'])->name('advertising.purchase');

// Crypto Donations & Premium Tiers
Route::get('/premium', [DonationController::class, 'index'])->name('premium');
Route::get('/premium.php', [DonationController::class, 'index']);
Route::post('/premium/checkout', [DonationController::class, 'checkout'])->name('premium.checkout');
Route::post('/api/donations/nowpayments/ipn', [DonationController::class, 'nowpaymentsIpn']);
Route::post('/api/donations/bitcart/ipn', [DonationController::class, 'bitcartIpn']);

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

// Dashboard (Protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/post', [DashboardController::class, 'createPost'])->name('dashboard.post.create');
    Route::get('/dashboard/post.php', [DashboardController::class, 'createPost']);
    Route::post('/dashboard/post', [DashboardController::class, 'storePost'])->name('dashboard.post.store');
    Route::get('/dashboard/posts', [DashboardController::class, 'posts'])->name('dashboard.posts');
    Route::get('/dashboard/posts.php', [DashboardController::class, 'posts']);
    Route::get('/dashboard/queue', [DashboardController::class, 'queue'])->name('dashboard.queue');
    Route::post('/dashboard/queue/{id}/publish', [DashboardController::class, 'publishQueued'])->name('dashboard.queue.publish');
    Route::post('/dashboard/queue/{id}/delete', [DashboardController::class, 'deleteQueued'])->name('dashboard.queue.delete');
    Route::get('/dashboard/blogs', [DashboardController::class, 'blogs'])->name('dashboard.blogs');
    Route::get('/dashboard/blogs.php', [DashboardController::class, 'blogs']);
    Route::post('/dashboard/blogs', [DashboardController::class, 'updateBlog'])->name('dashboard.blogs.update');
    Route::post('/dashboard/blogs/new', [DashboardController::class, 'storeNewBlog'])->name('dashboard.blogs.store');
    Route::get('/dashboard/referrals', [DashboardController::class, 'referrals'])->name('dashboard.referrals');
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
    Route::get('/logins', [AdminController::class, 'logins'])->name('admin.logins');
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/reports.php', [AdminController::class, 'reports']);
    Route::get('/support', [AdminController::class, 'support'])->name('admin.support');
    Route::get('/support.php', [AdminController::class, 'support']);
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/settings/save', [AdminController::class, 'saveSettings'])->name('admin.settings.save');

    // Admin Actions
    Route::post('/posts/{id}/delete', [AdminController::class, 'deletePost'])->name('admin.posts.delete');
    Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggle');
    Route::post('/users/{id}/badge', [AdminController::class, 'toggleBadge'])->name('admin.users.badge');
    Route::post('/users/{id}/points', [AdminController::class, 'adjustPoints'])->name('admin.users.points');
    Route::post('/users/{id}/delete', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::post('/blogs/{id}/toggle', [AdminController::class, 'toggleBlogStatus'])->name('admin.blogs.toggle');
    Route::post('/blogs/{id}/feature', [AdminController::class, 'toggleBlogFeatured'])->name('admin.blogs.feature');
    Route::post('/ads/create', [AdminController::class, 'createAd'])->name('admin.ads.create');
    Route::post('/ads/{id}/delete', [AdminController::class, 'deleteAd'])->name('admin.ads.delete');
    Route::post('/ads/{id}/toggle', [AdminController::class, 'toggleAd'])->name('admin.ads.toggle');
    Route::post('/slider/add', [AdminController::class, 'addSlide'])->name('admin.slider.add');
    Route::post('/slider/{id}/delete', [AdminController::class, 'deleteSlide'])->name('admin.slider.delete');
    Route::post('/verifications/{id}/review', [AdminController::class, 'reviewVerification'])->name('admin.verifications.review');
    Route::post('/tickets/{id}/reply', [AdminController::class, 'replyTicket'])->name('admin.tickets.reply');
    Route::post('/reports/{id}/resolve', [AdminController::class, 'resolveReport'])->name('admin.reports.resolve');
});
