<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Post;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('posts:publish-scheduled', function () {
    $now = now();
    $this->info("Checking for matured queued/scheduled posts at {$now->toDateTimeString()}...");
Log::info('Artisan scheduler triggered: checking scheduled tasks.');
    $duePosts = Post::where('status', 'queued')
        ->whereNotNull('published_at')
        ->where('published_at', '<=', $now)
        ->get();

    $count = 0;
    foreach ($duePosts as $post) {
        $post->status = 'published';
        $post->save();
        $count++;
    }

    $this->info("Successfully published {$count} scheduled post(s).");
})->purpose('Publish all matured queued and scheduled posts');

// Alias for convenience
Artisan::command('queue:publish-due', function () {
    $this->call('posts:publish-scheduled');
})->purpose('Alias for posts:publish-scheduled');

// Run every minute when php artisan schedule:run is called via cron
Schedule::command('posts:publish-scheduled')->everyMinute();

