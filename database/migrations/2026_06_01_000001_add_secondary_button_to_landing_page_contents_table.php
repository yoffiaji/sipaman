<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_page_contents', function (Blueprint $table) {
            $table->string('secondary_button_text', 100)->nullable()->after('button_url');
            $table->string('secondary_button_url', 500)->nullable()->after('secondary_button_text');
        });

        DB::table('landing_page_contents')
            ->where('section_key', 'hero')
            ->whereNull('secondary_button_text')
            ->whereNull('secondary_button_url')
            ->update([
                'secondary_button_text' => 'Lihat UMKM',
                'secondary_button_url' => '/umkm',
            ]);
    }

    public function down(): void
    {
        Schema::table('landing_page_contents', function (Blueprint $table) {
            $table->dropColumn(['secondary_button_text', 'secondary_button_url']);
        });
    }
};
