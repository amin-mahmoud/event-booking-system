<?php

namespace App\Http\Controllers\API;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class TicketController extends BaseController
{
    /**
     * Store a newly created ticket for an event.
     */
    public function store(Request $request, string $eventId): JsonResponse
    {
        $event = Event::find($eventId);

        if (is_null($event)) {
            return $this->sendError('Event not found.');
        }

        // Check if user owns the event or is admin
        if (auth()->user()->role !== 'admin' && $event->created_by !== auth()->id()) {
            return $this->sendError('Unauthorized action.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $ticket = Ticket::create([
            'type' => $request->type,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'event_id' => $eventId,
        ]);

        return $this->sendResponse($ticket->load('event'), 'Ticket created successfully.');
    }


    /**
     * Remove the specified ticket.
     */
    public function destroy(string $id): JsonResponse
    {
        $ticket = Ticket::with('event')->find($id);

        if (is_null($ticket)) {
            return $this->sendError('Ticket not found.');
        }

        // Check if user owns the event or is admin
        if (auth()->user()->role !== 'admin' && $ticket->event->created_by !== auth()->id()) {
            return $this->sendError('Unauthorized action.', [], 403);
        }

        $ticket->delete();

        return $this->sendResponse([], 'Ticket deleted successfully.');
    }
}
