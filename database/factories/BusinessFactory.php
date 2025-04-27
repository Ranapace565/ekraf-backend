<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Business>
 */
class BusinessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->company();
        return [
            'user_id' => \App\Models\User::factory(),
            'sector_id' => \App\Models\Sector::factory(),
            'business_name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'owner_name' => fake()->name(),
            'proof_photo' => 'proof.jpg',
            'description' => fake()->paragraph(),
            'location' => fake()->address(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'instagram' => '@' . fake()->userName(),
            'facebook' => fake()->userName(),
            'tiktok' => fake()->userName(),
            'status' => fake()->boolean(),
        ];
    }
}
