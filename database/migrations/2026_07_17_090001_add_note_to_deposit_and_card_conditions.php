<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposit_conditions', function (Blueprint $table) {
            $table->text('note')->nullable()->after('value');
        });

        Schema::table('card_conditions', function (Blueprint $table) {
            $table->text('note')->nullable()->after('value');
        });
    }

    public function down(): void
    {
        Schema::table('deposit_conditions', function (Blueprint $table) {
            $table->dropColumn('note');
        });

        Schema::table('card_conditions', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
};
