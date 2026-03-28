<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_images', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('media_type', 16)->default('image');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['review_id', 'sort_order']);
        });

        if (! Schema::hasTable('reviews') || ! Schema::hasColumn('reviews', 'images')) {
            return;
        }

        DB::table('reviews')
            ->select(['id', 'images'])
            ->whereNotNull('images')
            ->orderBy('id')
            ->chunkById(100, function ($reviews): void {
                $records = [];
                $timestamp = now();

                foreach ($reviews as $review) {
                    $legacyImages = is_string($review->images)
                        ? json_decode($review->images, true)
                        : $review->images;

                    if (! is_array($legacyImages)) {
                        continue;
                    }

                    foreach (array_values($legacyImages) as $index => $filePath) {
                        if (! is_string($filePath) || $filePath === '') {
                            continue;
                        }

                        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                        $records[] = [
                            'review_id' => $review->id,
                            'file_path' => $filePath,
                            'media_type' => in_array($extension, ['mp4', 'mov', 'webm'], true) ? 'video' : 'image',
                            'sort_order' => $index,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ];
                    }
                }

                if ($records !== []) {
                    DB::table('review_images')->insert($records);
                }
            }, 'id');
    }

    public function down(): void
    {
        Schema::dropIfExists('review_images');
    }
};
