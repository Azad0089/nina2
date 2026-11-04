<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        ensure_platform_tables();
    }

    public function down(): void
    {
        // Tables stay in place so existing communities and ads are not removed.
    }
};
