<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table for traditional mode (separate fields)
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
        });

        // Table for JSON mode (nested fields)
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('ubicacion')->nullable();
        });

        // Table for JSON mode (standard latitude/longitude)
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('location')->nullable();
        });

        // Table for bounds (traditional mode - separate fields)
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('sw_lat', 10, 8)->nullable();
            $table->decimal('sw_lng', 11, 8)->nullable();
            $table->decimal('ne_lat', 10, 8)->nullable();
            $table->decimal('ne_lng', 11, 8)->nullable();
            $table->json('bounds')->nullable(); // For JSON mode
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
        Schema::dropIfExists('stores');
        Schema::dropIfExists('places');
        Schema::dropIfExists('areas');
    }
};
