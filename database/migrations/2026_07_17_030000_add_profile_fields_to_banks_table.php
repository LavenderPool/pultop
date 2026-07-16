<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->text('address')->nullable()->after('logo_path');
            $table->text('description')->nullable()->after('address');
            $table->string('license')->nullable()->after('website');
            $table->string('mfo')->nullable()->after('license');
            $table->string('inn')->nullable()->after('mfo');
        });
    }

    public function down(): void
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->dropColumn(['address', 'description', 'license', 'mfo', 'inn']);
        });
    }
};
