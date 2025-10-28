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
        Schema::create('libraries', function (Blueprint $table) {
            $table->id();
            $table->string('title_uz')->nullable();
            $table->string('title_en')->nullable();
            $table->string('title_ru')->nullable();
            $table->text('description_uz')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ru')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->string('image')->nullable();
            $table->string('file_path_ru')->nullable();
            $table->string('file_path_en')->nullable();
            $table->string('file_path_uz')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libraries');
    }
};
