<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('seo_pages')) {
            return;
        }

        if (Schema::hasColumn('seo_pages', 'keywords')) {
            return;
        }

        Schema::table('seo_pages', function (Blueprint $table) {
            $table->string('keywords', 500)->nullable()->after('description');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('seo_pages') || ! Schema::hasColumn('seo_pages', 'keywords')) {
            return;
        }

        Schema::table('seo_pages', function (Blueprint $table) {
            $table->dropColumn('keywords');
        });
    }
};
