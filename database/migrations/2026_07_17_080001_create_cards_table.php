<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('currency', 16)->default('sum');
            $table->string('payment_system', 64)->nullable();
            $table->string('card_type', 32)->nullable();
            $table->string('category', 64)->nullable();
            $table->string('issue_cost_display')->nullable();
            $table->string('validity_display')->nullable();
            $table->longText('special_conditions')->nullable();
            $table->string('apply_url')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('is_active');
            $table->index('bank_id');
            $table->index('currency');
            $table->index('payment_system');
            $table->index('card_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
