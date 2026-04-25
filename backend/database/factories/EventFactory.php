<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use App\Models\Spot;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'sport' => fake()->randomElement(['Football', 'Basketball', 'Tennis', 'Running']),
            'location' => fake()->address(),
            'date' => fake()->dateTimeBetween('now', '+1 month'),
            'max_participants' => fake()->numberBetween(5, 20),
            'status' => 'OPEN',
            'latitude' => fake()->latitude(36.7, 36.8),
            'longitude' => fake()->longitude(3.0, 3.1),
            'organizer_id' => User::factory(),
            'spot_id' => Spot::factory(),
        ];
    }
}
