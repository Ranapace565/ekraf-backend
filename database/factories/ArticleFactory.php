<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence();

        return [
            'user_id' => \App\Models\User::factory()->state(['role' => 'admin']),
            // 'title' => fake()->sentence(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'content' => fake()->paragraph(4),
            'thumbnail' => 'thumbnail.jpg',
        ];
    }
}
