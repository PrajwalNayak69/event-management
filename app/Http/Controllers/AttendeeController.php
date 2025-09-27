<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\Request;

class AttendeeController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/events/{event}/attendees",
     *     summary="Register an attendee for an event",
     *     tags={"Attendees"},
     *     @OA\Parameter(
     *         name="event",
     *         in="path",
     *         required=true,
     *         description="Event ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Attendee registered successfully"),
     *     @OA\Response(response=400, description="Event full or validation error"),
     *     @OA\Response(response=404, description="Event not found")
     * )
     */
    public function store(Request $request, $eventId)
    {
        $event = Event::find($eventId);
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        if ($event->attendees()->count() >= $event->capacity) {
            return response()->json(['message' => 'Event is full'], 400);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:attendees,email',
        ]);

        $attendee = $event->attendees()->create($validated);

        return response()->json($attendee, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/events/{event}/attendees",
     *     summary="List attendees of an event",
     *     tags={"Attendees"},
     *     @OA\Parameter(
     *         name="event",
     *         in="path",
     *         required=true,
     *         description="Event ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="List of attendees with pagination"),
     *     @OA\Response(response=404, description="Event not found")
     * )
     */
    public function index($eventId)
    {
        $event = Event::find($eventId);
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $attendees = $event->attendees()->paginate(10);
        return response()->json($attendees);
    }
}
