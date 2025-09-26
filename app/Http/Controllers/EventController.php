<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/events",
     *     summary="List all events",
     *     tags={"Events"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response"
     *     )
     * )
     */
    public function index()
    {
        return Event::paginate(10); // Bonus: pagination
    }

    // Create a new event
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'capacity' => 'required|integer|min:1',
        ]);

        // Store in UTC always
        $validated['start_time'] = \Carbon\Carbon::parse($validated['start_time'])->utc();
        $validated['end_time'] = \Carbon\Carbon::parse($validated['end_time'])->utc();

        $event = Event::create($validated);

        return response()->json($event, 201);
    }

    // Show single event (with attendees)
    public function show(Event $event)
    {
        return $event->load('attendees');
    }

    // Update event
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'location' => 'sometimes|string|max:255',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time',
            'capacity' => 'sometimes|integer|min:1',
        ]);

        if (isset($validated['start_time'])) {
            $validated['start_time'] = \Carbon\Carbon::parse($validated['start_time'])->utc();
        }
        if (isset($validated['end_time'])) {
            $validated['end_time'] = \Carbon\Carbon::parse($validated['end_time'])->utc();
        }

        $event->update($validated);

        return response()->json($event);
    }

    // Delete event
    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json(null, 204);
    }
}
