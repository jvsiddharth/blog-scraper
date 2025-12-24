<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();
            $table->string('title');
            $table->longText('content');
            $table->json('references')->nullable();
            $table->string('model_used')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_articles');
    }
};

