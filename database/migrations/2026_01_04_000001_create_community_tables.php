<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('posts', 'is_community')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->boolean('is_community')->default(false)->after('status')->index();
            });
        }

        if (!Schema::hasTable('community_members')) {
            Schema::create('community_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->timestamp('created_at')->useCurrent();
                $table->unique('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('community_members');
        if (Schema::hasColumn('posts', 'is_community')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('is_community');
            });
        }
    }
};
