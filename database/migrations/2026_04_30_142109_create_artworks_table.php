<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('artworks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('price');
            $table->string('image_url');
            $table->timestamp('vendido_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropTable('artworks');
    }
};
