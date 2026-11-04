<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure MariaDB 10.5.29 and older MySQL index length compatibility
        Schema::defaultStringLength(191);

        try {
            ensure_private_message_tables();
            ensure_platform_tables();
        } catch (\Throwable $e) {
            // Schema is created again on the next request if the database is not ready yet.
        }

        // Share common settings with all views safely
        try {
            if (Schema::hasTable('settings')) {
                $siteSettings = DB::table('settings')->pluck('value', 'key')->all();
                View::share('siteSettings', $siteSettings);
            }
        } catch (\Throwable $e) {
            // Silently fall back before migrations run
        }
    }
}
