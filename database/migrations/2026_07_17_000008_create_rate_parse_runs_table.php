<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_parse_runs', function (Blueprint $table) {
            $table->id();
            $table->string('status', 32);
            $table->unsignedInteger('ok_count')->default(0);
            $table->unsignedInteger('fail_count')->default(0);
            $table->text('error_summary')->nullable();
            $table->json('details')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_parse_runs');
    }
};
