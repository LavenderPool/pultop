<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_rating_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('as_of_date')->nullable();
            $table->string('unit')->default('млрд. сум');
            $table->string('source_url');
            $table->timestamp('parsed_at');
            $table->boolean('is_current')->default(false);
            $table->timestamps();

            $table->index('is_current');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_rating_snapshots');
    }
};
