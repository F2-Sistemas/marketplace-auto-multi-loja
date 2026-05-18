<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->foreignId('vehicle_model_id')->constrained('vehicle_models')->onDelete('cascade');
            $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
            $table->string('title');
            $table->string('slug');
            $table->string('version')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('year_manufacture');
            $table->integer('year_model');
            $table->integer('mileage');
            $table->string('color')->nullable();
            $table->string('transmission')->nullable(); // manual, automatico, cvt
            $table->string('fuel')->nullable(); // flex, gasolina, alcool, diesel, eletrico, hibrido
            $table->string('status')->default('draft'); // draft, published, sold, inactive
            $table->integer('views_count')->default(0);
            $table->timestamps();

            $table->index('store_id');
            $table->index('brand_id');
            $table->index('vehicle_model_id');
            $table->index('city_id');
            $table->index('status');
            $table->index('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
