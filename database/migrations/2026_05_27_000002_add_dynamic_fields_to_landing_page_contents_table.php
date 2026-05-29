<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_page_contents', function (Blueprint $table) {
            $table->string('subjudul', 255)->nullable();
            $table->string('image_path', 255)->nullable();
            $table->string('image_alt', 255)->nullable();
            $table->string('button_text', 100)->nullable();
            $table->string('button_url', 500)->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('landing_page_contents', function (Blueprint $table) {
            $table->dropColumn([
                'subjudul',
                'image_path',
                'image_alt',
                'button_text',
                'button_url',
                'is_active',
            ]);
        });
    }
};
