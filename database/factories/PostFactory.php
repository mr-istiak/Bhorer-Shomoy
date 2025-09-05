<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'slug' => fake()->slug(),
            'content' => implode("\n\n", fake()->paragraphs()),
            'status' => PostStatus::PUBLISHED->value,
            'meta_description' => fake()->text(160),
            'author_id' => 1,
            'published_at' => now(),
            'featured_image' => MediaFactory::new(),
        ];
    }
}
