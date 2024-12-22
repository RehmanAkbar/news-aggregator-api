<?php

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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id');
            $table->string('title');
            $table->text('content');
            $table->string('author')->nullable();
            $table->string('url')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamp('published_at');
            $table->timestamps();


            // indexes for better performance
            $table->index('published_at');
            $table->fulltext(['title', 'content']);
            $table->index('author');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
