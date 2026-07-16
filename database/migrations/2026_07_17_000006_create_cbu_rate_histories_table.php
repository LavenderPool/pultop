<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cbu_rate_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->decimal('rate', 16, 4);
            $table->decimal('diff', 16, 4)->nullable();
            $table->date('rate_date');
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->unique(['currency_id', 'rate_date']);
            $table->index(['currency_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cbu_rate_histories');
    }
};
