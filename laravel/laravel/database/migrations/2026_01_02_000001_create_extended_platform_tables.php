<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->mediumText('value')->nullable();
            $table->timestamps();
        });

        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('document_path', 255);
            $table->string('full_name', 190)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('post_id')->nullable();
            $table->unsignedBigInteger('blog_id')->nullable();
            $table->string('package_name', 120);
            $table->enum('placement', ['explore_top', 'trending_hero', 'sidebar'])->default('explore_top');
            $table->unsignedInteger('points_spent')->default(0);
            $table->unsignedInteger('target_views')->default(0);
            $table->unsignedInteger('current_views')->default(0);
            $table->unsignedInteger('duration_days')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();
        });

        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('gateway', 40);
            $table->string('order_id', 100)->unique();
            $table->string('payment_id', 100)->nullable();
            $table->decimal('amount_usd', 10, 2);
            $table->enum('plan', ['premium', 'premium_plus']);
            $table->enum('status', ['pending', 'confirmed', 'failed', 'refunded'])->default('pending');
            $table->string('crypto_currency', 20)->nullable();
            $table->decimal('crypto_amount', 18, 8)->nullable();
            $table->string('pay_address', 255)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('target_type', ['post', 'blog', 'user']);
            $table->unsignedBigInteger('target_id');
            $table->string('reason', 255);
            $table->text('details')->nullable();
            $table->enum('status', ['open', 'resolved', 'dismissed'])->default('open');
            $table->timestamps();
        });

        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name', 120);
            $table->string('email', 190);
            $table->string('topic', 100);
            $table->text('message');
            $table->text('admin_reply')->nullable();
            $table->enum('status', ['open', 'replied', 'closed'])->default('open');
            $table->timestamps();
        });

        Schema::create('user_logins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->timestamp('logged_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_logins');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('donations');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('verifications');
        Schema::dropIfExists('settings');
    }
};
