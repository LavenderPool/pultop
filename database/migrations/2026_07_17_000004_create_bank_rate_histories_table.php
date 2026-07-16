<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_rate_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained()->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->string('place', 16);
            $table->decimal('buy', 16, 4)->nullable();
            $table->decimal('sell', 16, 4)->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['currency_id', 'place', 'recorded_at']);
            $table->index(['bank_id', 'currency_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_rate_histories');
    }
};
