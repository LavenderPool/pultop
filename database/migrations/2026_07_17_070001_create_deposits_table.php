<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('currency', 16)->default('UZS');
            $table->string('rate_display')->nullable();
            $table->string('term_display')->nullable();
            $table->string('amount_display')->nullable();
            $table->unsignedSmallInteger('term_min_months')->nullable();
            $table->unsignedSmallInteger('term_max_months')->nullable();
            $table->unsignedBigInteger('amount_min')->nullable();
            $table->boolean('early_termination')->default(false);
            $table->boolean('partial_withdrawal')->default(false);
            $table->boolean('capitalization')->default(false);
            $table->boolean('is_online')->default(false);
            $table->longText('special_conditions')->nullable();
            $table->string('apply_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'currency']);
            $table->index(['bank_id', 'is_active']);
            $table->index(['is_active', 'term_min_months', 'term_max_months']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
