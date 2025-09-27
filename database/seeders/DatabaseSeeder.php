<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Attendee;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        // Create a test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create 10 events with 5 attendees each
        Event::factory(10)
            ->has(Attendee::factory()->count(5))
            ->create();
    }
}
