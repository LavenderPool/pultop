<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_rating_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('snapshot_id')->constrained('bank_rating_snapshots')->cascadeOnDelete();
            $table->unsignedInteger('sort_order');
            $table->string('row_type', 16);
            $table->unsignedSmallInteger('position')->nullable();
            $table->string('name');
            $table->unsignedBigInteger('assets')->nullable();
            $table->unsignedBigInteger('loans')->nullable();
            $table->unsignedBigInteger('capital')->nullable();
            $table->unsignedBigInteger('deposits')->nullable();
            $table->string('group_key', 16)->nullable();
            $table->timestamps();

            $table->index(['snapshot_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_rating_rows');
    }
};
