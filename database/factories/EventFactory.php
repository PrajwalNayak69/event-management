<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $startTime = Carbon::now()->addDays(fake()->numberBetween(1, 30));
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(2),
            'location' => fake()->address(),
            'start_time' => $startTime->format('Y-m-d H:i:s'),
            'end_time' => $startTime->addHours(fake()->numberBetween(2, 8))->format('Y-m-d H:i:s'),
            'capacity' => fake()->numberBetween(20, 200),
        ];
    }
}