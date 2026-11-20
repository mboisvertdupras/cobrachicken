<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('title', 80);
            $table->string('url', 2048)->nullable();
            $table->text('text')->nullable();
            $table->string('province', 2)->nullable()->index();
            $table->boolean('is_political')->default(false);
            $table->integer('points')->default(1);
            $table->integer('comments_count')->default(0);
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
