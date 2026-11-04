# Setup (XAMPP / shared hosting)

## 1. Create an empty database
phpMyAdmin -> Databases -> create a database named `nina_sucks` (or any name you like).
**Do not import any SQL file** -- there isn't one. The app creates every table itself
the first time it's loaded.

## 2. Edit 4 lines
Open `includes/config.php` and fill in:

```php
const DB_HOST = '127.0.0.1';
const DB_PORT = '3306';
const DB_NAME = 'nina_sucks';   // the database you just created
const DB_USER = 'root';         // XAMPP default
const DB_PASS = '';             // XAMPP default (empty)
```

Also set `SITE_URL` (e.g. `http://localhost/nina` or your real domain) and `BASE_PATH`
if you're not installing at the web root.

If XAMPP's `root` user rejects an empty password on your machine (some installs do),
create a dedicated MySQL user in phpMyAdmin instead and use that.

## 3. Open the site
Visit the homepage in a browser. On that very first request the app creates all 15
database tables and a default admin account automatically. Nothing to import by hand.

**Default admin login:** `admin` / `ChangeMe123!`
**Change this password immediately** -- Admin panel -> Users, or Account settings once
logged in as admin. (The admin account doesn't show in the Users moderation list since
admins aren't moderated from there, but the login itself works right away.)

## 4. Optional: turn on extras
All of these are safe to skip -- the site works fully without them.

- **Google sign-in**: get a Client ID/Secret from console.cloud.google.com and fill in
  `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` in `includes/config.php`. Redirect URI to
  register with Google: `SITE_URL/auth/google-callback.php`.
- **Cloudflare Turnstile captcha**: fill in `TURNSTILE_SITE_KEY` / `TURNSTILE_SECRET_KEY`.
  Until you do, a placeholder box shows instead and the check always passes.
- **Scheduled/queued posts**: set up a cron job (cPanel "Cron Jobs", every 5 minutes) to hit
  `cron/publish-scheduled.php?key=YOUR_KEY` (set a random `CRON_KEY` inside that file first).
  Without this, queued posts stay queued and never auto-publish.
- **FFmpeg video transcoding**: off by default. Admin > Settings > Media to turn on
  (only works if the server actually has ffmpeg installed -- most shared hosting doesn't).

## What's fully working
Registration/login (email+password and Google), auto-created main "profile" blog,
unlimited additional blogs with per-blog color/branding, all 5 post types (text, photo,
video, link, quote, chat -- audio intentionally excluded per spec), tags, scheduled
post queue, follow/like/reblog/report, public crawlable blog + post pages, full admin
panel (user/blog/post moderation with suspend/ban/delete, badges, manual premium/points
grants, tags, categories, reports queue, support tickets, sitewide branding/colors/
logo/favicon/head-code, media size limits), CSRF protection throughout, local media
uploads with size/type limits.

## What's intentionally stubbed (see project notes for why)
- **Crypto checkout (NOWPayments/BitCart)**: settings fields exist to store API keys,
  but no checkout flow is wired up yet -- you said to hold this for later.
- **Cloud storage (S3/FTP/Cloudflare R2)**: local disk storage works fully; the setting
  exists but only "local" is implemented. Hook point is marked in `includes/uploads.php`.
- **FFmpeg execution**: the toggle and settings exist; actual transcode-on-upload isn't
  wired up (uploads always work as-is regardless).
- **Self-serve points marketplace** (members spending points to buy their own placement):
  admin can manually feature any blog/post for N days (Admin > Blogs / Posts), which
  covers the core "get featured" use case. The buy-it-yourself flow isn't built.
