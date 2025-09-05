<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'filename' => 'image.jpg',
            'original_name' => 'image.jpg',
            'title' => fake()->sentence(),
            'alt' => fake()->sentence(),
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'path' => $this->faker->imageUrl(640, 480),
        ];
    }
}
