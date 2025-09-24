<?php

namespace App\Http\Controllers\API;

use App\Models\Ticket;
use App\Models\Booking;
use App\Notifications\BookingCancelledNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class BookingController extends BaseController
{
    /**
     * Display user's bookings.
     */
    public function index(): JsonResponse
    {
        $bookings = Booking::with(['ticket.event', 'payment'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return $this->sendResponse($bookings, 'Bookings retrieved successfully.');
    }

    /**
     * Create a booking for a ticket.
     */
    public function store(Request $request, string $ticketId): JsonResponse
    {
        $ticket = Ticket::with('event')->find($ticketId);

        if (is_null($ticket)) {

            return $this->sendError('Ticket not found.');
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        // Check ticket availability
        if ($request->quantity > $ticket->available_quantity) {
            return $this->sendError('Insufficient tickets available.');
        }

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'ticket_id' => $ticketId,
            'quantity' => $request->quantity,
            'status' => 'pending',
        ]);

        return $this->sendResponse($booking->load(['ticket.event', 'user']), 'Booking created successfully.');
    }

    /**
     * Cancel a booking.
     */
    public function cancel(string $id): JsonResponse
    {
        $booking = Booking::find($id);

        if (is_null($booking)) {
            return $this->sendError('Booking not found.');
        }

        // Check if user owns the booking or is admin
        if (auth()->user()->role !== 'admin' && $booking->user_id !== auth()->id()) {
            return $this->sendError('Unauthorized action.', [], 403);
        }

        if ($booking->status === 'cancelled') {
            return $this->sendError('Booking already cancelled.');

        }

        $booking->update(['status' => 'cancelled']);

        $booking->user->notify(new BookingCancelledNotification($booking));

        return $this->sendResponse($booking->load(['ticket.event']), 'Booking cancelled successfully.');
    }
}
