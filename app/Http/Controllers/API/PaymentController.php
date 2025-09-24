<?php

namespace App\Http\Controllers\API;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class PaymentController extends BaseController
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Process payment for a booking.
     */
    public function store(Request $request, string $bookingId): JsonResponse
    {
        $booking = Booking::with(['ticket.event', 'user'])->find($bookingId);

        if (is_null($booking)) {
            return $this->sendError('Booking not found.');
        }

        // Check if user owns the booking or is admin
        if (auth()->user()->role !== 'admin' && $booking->user_id !== auth()->id()) {
            return $this->sendError('Unauthorized action.', [], 403);
        }

        // Check if booking is in pending status
        if ($booking->status !== 'pending') {
            return $this->sendError('Payment can only be processed for pending bookings.');
        }

        // Check if payment already exists
        if ($booking->payment) {
            return $this->sendError('Payment already exists for this booking.');
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:credit_card,debit_card,paypal,bank_transfer',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        // Process payment through service
        $result = $this->paymentService->processPayment($booking, [
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
        ]);

        if (!$result['success']) {
            return $this->sendError($result['message'], [], 400);
        }

        return $this->sendResponse($result['payment']->load(['booking.ticket.event']), $result['message']);
    }

    /**
     * Display payment details.
     */
    public function show(string $id): JsonResponse
    {
        $payment = Payment::with(['booking.ticket.event', 'booking.user'])->find($id);

        if (is_null($payment)) {
            return $this->sendError('Payment not found.');
        }

        // Check if user owns the payment or is admin
        if (auth()->user()->role !== 'admin' && $payment->booking->user_id !== auth()->id()) {
            return $this->sendError('Unauthorized action.', [], 403);
        }

        return $this->sendResponse($payment, 'Payment retrieved successfully.');
    }

    /**
     * Process refund for a payment (admin only).
     */
    public function refund(Request $request, string $id): JsonResponse
    {
        $payment = Payment::with('booking')->find($id);

        if (is_null($payment)) {
            return $this->sendError('Payment not found.');
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'sometimes|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $result = $this->paymentService->processRefund(
            $payment,
            $request->get('reason', 'Admin requested refund')
        );

        if (!$result['success']) {
            return $this->sendError($result['message'], [], 400);
        }

        return $this->sendResponse($payment->fresh(), $result['message']);
    }
}
