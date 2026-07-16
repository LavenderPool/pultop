<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained()->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->string('place', 16);
            $table->decimal('buy', 16, 4)->nullable();
            $table->decimal('sell', 16, 4)->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();

            $table->unique(['bank_id', 'currency_id', 'place']);
            $table->index(['currency_id', 'place']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_rates');
    }
};
