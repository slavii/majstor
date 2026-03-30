<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cached_listings', function (Blueprint $table) {
            $table->id();
            $table->string('olx_id')->unique();
            $table->string('title');
            $table->integer('price')->nullable();
            $table->string('currency', 10)->default('RON');
            $table->string('location')->nullable();
            $table->string('image_url')->nullable();
            $table->string('listing_url');
            $table->boolean('has_delivery')->default(false);
            $table->string('condition')->nullable();
            $table->string('diameter')->nullable();
            $table->string('bolt_pattern')->nullable();
            $table->text('description')->nullable();
            $table->string('search_query')->index();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->index(['search_query', 'has_delivery']);
            $table->index('price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cached_listings');
    }
};
