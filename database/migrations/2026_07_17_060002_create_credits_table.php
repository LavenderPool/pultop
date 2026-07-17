<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('currency', 16)->default('sum');
            $table->string('rate_display')->nullable();
            $table->string('term_display')->nullable();
            $table->string('amount_display')->nullable();
            $table->string('down_payment')->nullable();
            $table->longText('special_conditions')->nullable();
            $table->string('apply_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};
