<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('jobs_tracker')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_status_history');
    }
};
