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

    /**
     * @OA\Post(
     *     path="/api/events",
     *     summary="Create a new event",
     *     tags={"Events"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","location","start_time","end_time","capacity"},
     *             @OA\Property(property="title", type="string", example="Tech Conference 2025"),
     *             @OA\Property(property="description", type="string", example="Annual tech conference"),
     *             @OA\Property(property="location", type="string", example="Convention Center"),
     *             @OA\Property(property="start_time", type="string", format="date-time", example="2025-10-01T09:00:00+05:30"),
     *             @OA\Property(property="end_time", type="string", format="date-time", example="2025-10-01T17:00:00+05:30"),
     *             @OA\Property(property="capacity", type="integer", example=100)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Event created successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/events/{id}",
     *     summary="Get event details with attendees",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Event ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Event details"),
     *     @OA\Response(response=404, description="Event not found")
     * )
     */
    public function show(Event $event)
    {
        return $event->load('attendees');
    }

    /**
     * @OA\Put(
     *     path="/api/events/{id}",
     *     summary="Update an event",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Event ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated Tech Conference 2025"),
     *             @OA\Property(property="description", type="string", example="Updated conference details"),
     *             @OA\Property(property="location", type="string", example="New Convention Center"),
     *             @OA\Property(property="start_time", type="string", format="date-time", example="2025-10-01T10:00:00+05:30"),
     *             @OA\Property(property="end_time", type="string", format="date-time", example="2025-10-01T18:00:00+05:30"),
     *             @OA\Property(property="capacity", type="integer", example=150)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Event updated successfully"),
     *     @OA\Response(response=404, description="Event not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/events/{id}",
     *     summary="Delete an event",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Event ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Event deleted successfully"),
     *     @OA\Response(response=404, description="Event not found")
     * )
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json(null, 204);
    }
}
