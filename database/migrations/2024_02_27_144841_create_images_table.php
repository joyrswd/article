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
        Schema::create('images', function (Blueprint $table) {
            $table->id()->comment('画像ID');
            $table->foreignId('article_id')->unique()->constrained()->cascadeOnDelete()->comment('記事ID');
            $table->string('path')->unique()->comment('画像保存先パス');
            $table->text('description')->comment('画像の説明テキスト');
            $table->string('size')->index()->comment('画像サイズ');
            $table->string('model_name')->index()->comment('生成モデル名');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
