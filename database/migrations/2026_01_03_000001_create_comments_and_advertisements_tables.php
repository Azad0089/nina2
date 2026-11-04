<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->text('comment');
            $table->timestamps();
        });

        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('sponsor_name', 100)->nullable();
            $table->string('link_url', 500);
            $table->string('image_url', 500)->nullable();
            $table->text('description')->nullable();
            $table->enum('placement', ['sidebar', 'feed', 'hero'])->default('sidebar');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('clicks_count')->default(0);
            $table->unsignedInteger('impressions_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
        Schema::dropIfExists('comments');
    }
};
