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
        Schema::create('attribute_author', function (Blueprint $table) {
            $table->foreignId('attribute_id')->constrained()->cascadeOnDelete()->comment('属性ID');
            $table->foreignId('author_id')->constrained()->cascadeOnDelete()->comment('著者ID');
            $table->unique(['attribute_id', 'author_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_author');
    }
};
