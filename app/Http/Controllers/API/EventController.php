<?php

namespace App\Http\Controllers\API;

use App\Models\Event;
use App\Services\EventCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class EventController extends BaseController
{
    protected EventCacheService $cacheService;

    public function __construct(EventCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Display a listing of events with pagination, search, and filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $params = $request->only(['per_page', 'search', 'date', 'location']);
        $events = $this->cacheService->getCachedEvents($params);

        return $this->sendResponse($events, 'Events retrieved successfully.');
    }

    /**
     * Store a newly created event.
     */
// app/Http/Controllers/API/EventController.php

    public function store(Request $request): JsonResponse
    {
        // Additional security check - only organizers can create events

        if (auth()->user()->role !== 'organizer') {

            return $this->sendError('Access denied. Only organizers can create events.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date|after:now',
            'location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'location' => $request->location,
            'created_by' => auth()->id(),
        ]);

        // Clear cache after creating new event
        $this->cacheService->clearEventCache();

        return $this->sendResponse($event->load(['creator', 'tickets']), 'Event created successfully.');
    }


    /**
     * Display the specified event.
     */
    public function show(string $id): JsonResponse
    {
        $event = $this->cacheService->getCachedEvent((int) $id);

        if (is_null($event)) {
            return $this->sendError('Event not found.');
        }

        return $this->sendResponse($event, 'Event retrieved successfully.');
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $event = Event::find($id);

        if (is_null($event)) {
            return $this->sendError('Event not found.');
        }

        // Check if user owns the event or is admin
        if (auth()->user()->role !== 'admin' && $event->created_by !== auth()->id()) {

            return $this->sendError('Unauthorized action.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'date' => 'sometimes|date|after:now',
            'location' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $event->update($request->only(['title', 'description', 'date', 'location']));

        $this->cacheService->clearEventCache();

        return $this->sendResponse($event->load(['creator', 'tickets']), 'Event updated successfully.');
    }

    /**
     * Remove the specified event.
     */
    public function destroy(string $id): JsonResponse
    {
        $event = Event::find($id);

        if (is_null($event)) {
            return $this->sendError('Event not found.');
        }

        // Check if user owns the event or is admin
        if (auth()->user()->role !== 'admin' && $event->created_by !== auth()->id()) {
            return $this->sendError('Unauthorized action.', [], 403);
        }

        $event->delete();

        $this->cacheService->clearEventCache();

        return $this->sendResponse([], 'Event deleted successfully.');
    }
}
