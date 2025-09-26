<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\Request;

class AttendeeController extends Controller
{
    // List attendees for an event
    public function index(Event $event)
    {
        return $event->attendees()->paginate(10); // Bonus: pagination
    }

    // Register attendee
    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        // Check duplicate
        if ($event->attendees()->where('email', $validated['email'])->exists()) {
            return response()->json(['message' => 'Attendee already registered'], 409);
        }

        // Check capacity
        if ($event->attendees()->count() >= $event->capacity) {
            return response()->json(['message' => 'Event is full'], 400);
        }

        $attendee = $event->attendees()->create($validated);

        return response()->json($attendee, 201);
    }
}
