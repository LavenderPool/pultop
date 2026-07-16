<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gold_price_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('weight_grams');
            $table->decimal('price', 16, 2);
            $table->decimal('diff', 16, 2)->nullable();
            $table->date('price_date');
            $table->timestamps();

            $table->unique(['weight_grams', 'price_date']);
            $table->index(['weight_grams', 'price_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gold_price_histories');
    }
};
