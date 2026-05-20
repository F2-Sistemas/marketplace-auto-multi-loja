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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary');
            $table->longText('content');
            $table->string('image_url')->nullable();
            $table->string('category')->default('Notícias'); // Notícias, Testes, Comparativos, Vídeos, Dicas, Segredos
            $table->integer('views_count')->default(0);
            $table->json('related_links')->nullable();
            $table->string('status')->default('draft'); // draft, published
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('category');
            $table->index('status');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
