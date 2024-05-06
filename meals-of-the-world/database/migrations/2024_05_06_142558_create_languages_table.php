<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->char('code', 2)->primary();
            $table->string('name', 50)->nullable(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
