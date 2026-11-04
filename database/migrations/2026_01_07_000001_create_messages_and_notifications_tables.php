<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add notification preference columns to users table if missing
        if (!Schema::hasColumn('users', 'notify_new_follower')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('notify_new_follower')->default(true)->after('status');
            });
        }

        if (!Schema::hasColumn('users', 'notify_private_message')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('notify_private_message')->default(true)->after('notify_new_follower');
            });
        }

        // Create messages table if missing
        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade');
                $table->string('subject', 190)->nullable();
                $table->text('body');
                $table->boolean('is_read')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');

        if (Schema::hasColumn('users', 'notify_private_message')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('notify_private_message');
            });
        }

        if (Schema::hasColumn('users', 'notify_new_follower')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('notify_new_follower');
            });
        }
    }
};
