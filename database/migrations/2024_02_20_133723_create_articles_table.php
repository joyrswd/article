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
            $table->id()->comment('記事ID');
            $table->foreignId('author_id')->constrained()->cascadeOnDelete()->comment('著者ID');
            $table->string('title')->nullable()->comment('記事タイトル');
            $table->text('content')->comment('記事本文');
            $table->string('locale')->index()->comment('言語');
            $table->string('llm_name')->index()->comment('生成モデル名');
            $table->timestamps();
            $table->softDeletes();
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
