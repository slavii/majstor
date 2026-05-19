<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs_tracker', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['new', 'scheduled', 'in_progress', 'completed', 'cancelled'])->default('new');
            $table->date('scheduled_date')->nullable();
            $table->decimal('estimated_price', 10, 2)->nullable();
            $table->decimal('actual_price', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'scheduled_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs_tracker');
    }
};
