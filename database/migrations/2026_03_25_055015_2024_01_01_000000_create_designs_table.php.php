<?php
// database/migrations/2024_01_01_000000_create_designs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('designs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('thumbnail')->nullable();
            $table->longText('design_data'); // JSON data for Konva
            $table->integer('width')->default(800);
            $table->integer('height')->default(600);
            $table->timestamps();
        });

        Schema::create('uploaded_images', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('path');
            $table->string('mime_type');
            $table->integer('size');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('designs');
        Schema::dropIfExists('uploaded_images');
    }
};