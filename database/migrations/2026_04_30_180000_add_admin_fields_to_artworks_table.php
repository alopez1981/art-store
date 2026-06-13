<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
            $table->string('technique')->nullable()->after('image_url');
            $table->string('dimensions')->nullable()->after('technique');
            $table->unsignedSmallInteger('year')->nullable()->after('dimensions');
            $table->boolean('is_published')->default(true)->after('year');
        });

        $artworks = DB::table('artworks')->select('id', 'title')->orderBy('id')->get();
        $usedSlugs = [];

        foreach ($artworks as $artwork) {
            $baseSlug = Str::slug($artwork->title) ?: 'obra';
            $slug = $baseSlug;
            $counter = 2;

            while (in_array($slug, $usedSlugs, true)) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }

            $usedSlugs[] = $slug;

            DB::table('artworks')
                ->where('id', $artwork->id)
                ->update(['slug' => $slug]);
        }

        Schema::table('artworks', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'technique', 'dimensions', 'year', 'is_published']);
        });
    }
};
