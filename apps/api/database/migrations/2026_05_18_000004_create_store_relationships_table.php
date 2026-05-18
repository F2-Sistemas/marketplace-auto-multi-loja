<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('store_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('related_store_id')->constrained('stores')->onDelete('cascade');
            $table->string('type')->default('filial'); // matriz, filial, grupo, parceira
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();

            $table->unique(['store_id', 'related_store_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_relationships');
    }
};
