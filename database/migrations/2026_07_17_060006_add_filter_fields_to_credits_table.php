<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->unsignedSmallInteger('term_min_months')->nullable()->after('amount_display');
            $table->unsignedSmallInteger('term_max_months')->nullable()->after('term_min_months');
            $table->unsignedBigInteger('amount_min')->nullable()->after('term_max_months');
            $table->unsignedBigInteger('amount_max')->nullable()->after('amount_min');

            $table->index(['is_active', 'currency']);
            $table->index(['bank_id', 'is_active']);
            $table->index(['is_active', 'term_min_months', 'term_max_months']);
        });
    }

    public function down(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'currency']);
            $table->dropIndex(['bank_id', 'is_active']);
            $table->dropIndex(['is_active', 'term_min_months', 'term_max_months']);
            $table->dropColumn([
                'term_min_months',
                'term_max_months',
                'amount_min',
                'amount_max',
            ]);
        });
    }
};
