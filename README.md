# Nina.Sucks — Laravel 11 Conversion

This directory contains the complete **Laravel 11** conversion of the Nina.Sucks microblogging platform.

## Architecture

- **Models**: `app/Models/User.php`, `app/Models/Blog.php`, `app/Models/Post.php`, `app/Models/Like.php`, `app/Models/Follow.php`
- **Controllers**:
  - `app/Http/Controllers/HomeController.php`: Explore feed, infinite scroll (`/feed`), trending page.
  - `app/Http/Controllers/BlogController.php`: Custom blog subdomains/slugs, profile views, filtering.
  - `app/Http/Controllers/PostController.php`: Single post view, impression counter.
  - `app/Http/Controllers/ActionController.php`: Like, Follow, Reblog, Report, and Contact endpoints.
  - `app/Http/Controllers/DashboardController.php`: Post composition, multi-format posting (text, photo, video, quote, link, chat), blog customization.
  - `app/Http/Controllers/AuthController.php`: Authentication and blog initialization.
  - `app/Http/Controllers/PageController.php`: Legal and marketing pages (Advertising, Premium, Support, Terms, Privacy, DMCA, Rules).
- **Routes**: `routes/web.php`
- **Database Migrations**: `database/migrations/2026_01_01_000001_create_nina_sucks_tables.php`
- **Views**: Blade templates in `resources/views/`
- **Assets**: CSS (`public/css/style.css`, `public/css/app.css`) and JavaScript (`public/js/main.js`).

## Running with Laravel / PHP

```bash
cd laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve --port=8000
```
