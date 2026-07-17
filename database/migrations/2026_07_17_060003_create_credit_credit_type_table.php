<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_credit_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_id')->constrained('credits')->cascadeOnDelete();
            $table->foreignId('credit_type_id')->constrained('credit_types')->cascadeOnDelete();
            $table->unique(['credit_id', 'credit_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_credit_type');
    }
};
