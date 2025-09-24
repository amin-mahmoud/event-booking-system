<?php

namespace App\Http\Middleware;

use App\Models\Booking;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventDoubleBookingMiddleware
{

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('post') && $request->route('ticket')) {
            $userId = auth()->id();
            $ticketId = $request->route('ticket');

            // Check if user already has an active booking for this ticket
            $existingBooking = Booking::where('user_id', $userId)
                ->where('ticket_id', $ticketId)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if ($existingBooking) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have an active booking for this ticket. Multiple bookings are not allowed.'
                ], 409); // Conflict status code
            }
        }

        return $next($request);
    }
}
