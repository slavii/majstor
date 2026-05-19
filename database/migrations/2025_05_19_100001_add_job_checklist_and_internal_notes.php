<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs_tracker', function (Blueprint $table) {
            $table->json('checklist')->nullable()->after('notes');
            $table->text('internal_notes')->nullable()->after('checklist');
        });
    }

    public function down(): void
    {
        Schema::table('jobs_tracker', function (Blueprint $table) {
            $table->dropColumn(['checklist', 'internal_notes']);
        });
    }
};
