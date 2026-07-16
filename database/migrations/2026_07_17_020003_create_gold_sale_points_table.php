<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gold_sale_points', function (Blueprint $table) {
            $table->id();
            $table->string('region');
            $table->string('bank_name');
            $table->string('address');
            $table->string('phone')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['region', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gold_sale_points');
    }
};
