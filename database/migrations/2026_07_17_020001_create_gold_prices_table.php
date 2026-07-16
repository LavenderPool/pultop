<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gold_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('weight_grams')->unique();
            $table->decimal('sell_price', 16, 2);
            $table->decimal('buyback_good', 16, 2)->nullable();
            $table->decimal('buyback_damaged', 16, 2)->nullable();
            $table->decimal('diff', 16, 2)->nullable();
            $table->date('priced_on')->nullable();
            $table->timestamp('fetched_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gold_prices');
    }
};
