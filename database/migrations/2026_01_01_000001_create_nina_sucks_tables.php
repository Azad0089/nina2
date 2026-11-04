<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 40)->unique();
            $table->string('email', 190)->unique();
            $table->string('password_hash', 255);
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->unsignedInteger('points')->default(0);
            $table->boolean('badge_pro')->default(false);
            $table->boolean('badge_id')->default(false);
            $table->boolean('badge_vip')->default(false);
            $table->enum('status', ['active', 'suspended', 'banned'])->default('active');
            $table->string('referral_code', 32)->nullable()->unique();
            $table->unsignedBigInteger('referred_by_user_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('slug', 40)->unique();
            $table->string('title', 120);
            $table->string('tagline', 200)->nullable();
            $table->string('color', 16)->default('#ff2bd6');
            $table->string('avatar_path', 255)->nullable();
            $table->string('cover_path', 255)->nullable();
            $table->boolean('is_main')->default(false);
            $table->enum('status', ['active', 'hidden', 'suspended'])->default('active');
            $table->boolean('searchable')->default(true);
            $table->boolean('show_profile')->default(true);
            $table->text('bio')->nullable();
            $table->string('website1', 255)->nullable();
            $table->string('website2', 255)->nullable();
            $table->string('website3', 255)->nullable();
            $table->string('contact_email', 190)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['text', 'photo', 'quote', 'link', 'chat', 'video']);
            $table->enum('status', ['draft', 'queued', 'published', 'removed'])->default('published');
            $table->string('heading', 255)->nullable();
            $table->mediumText('body')->nullable();
            $table->text('caption')->nullable();
            $table->string('media_path', 255)->nullable();
            $table->string('media_type', 32)->nullable();
            $table->json('media_meta')->nullable();
            $table->json('crop_meta')->nullable();
            $table->text('quote_text')->nullable();
            $table->string('quote_source', 255)->nullable();
            $table->text('link_url')->nullable();
            $table->string('link_title', 255)->nullable();
            $table->string('link_domain', 120)->nullable();
            $table->json('chat_payload')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedBigInteger('reblog_of_post_id')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['user_id', 'post_id']);
        });

        Schema::create('follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('blog_id')->constrained()->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['user_id', 'blog_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follows');
        Schema::dropIfExists('likes');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('users');
    }
};
