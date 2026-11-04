<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        ensure_private_message_tables();
    }

    public function down(): void
    {
        Schema::dropIfExists('user_blocks');

        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                foreach (['attachment_path', 'attachment_name', 'attachment_mime', 'attachment_size'] as $column) {
                    if (Schema::hasColumn('messages', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
