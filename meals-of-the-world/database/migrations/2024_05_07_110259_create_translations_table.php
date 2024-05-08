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
        Schema::create('translations', function (Blueprint $table) {
            $table->string('translatable_type');
            $table->integer('translatable_id')->unsigned();
            $table->char('language_code', 2);
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->primary(['translatable_type', 'translatable_id', 'language_code']);
            $table->foreign('language_code')->references('code')->on('languages')->onDelete('cascade');
            $table->index(['translatable_type', 'translatable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
