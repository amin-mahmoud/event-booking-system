<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\PaymentFailedNotification;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Process payment for a booking (mocked implementation)
     */
    public function processPayment(Booking $booking, array $paymentData): array
    {
        try {
            DB::beginTransaction();

            // Validate payment amount matches booking total
            $expectedAmount = $booking->total_amount;
            $providedAmount = $paymentData['amount'];

            if (abs($expectedAmount - $providedAmount) > 0.01) {
                throw new Exception('Payment amount does not match booking total');
            }

            // Simulate payment processing (mock external payment gateway)
            $paymentResult = $this->simulatePaymentGateway($paymentData);

            if (!$paymentResult['success']) {
                throw new Exception($paymentResult['message']);
            }

            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'amount' => $providedAmount,
                'status' => $paymentResult['status'],
            ]);

            // Update booking status to confirmed if payment successful
            if ($paymentResult['status'] === 'success') {

                $booking->update(['status' => 'confirmed']);
                $booking->user->notify(new BookingConfirmedNotification($booking));
            } else {

                $booking->user->notify(new PaymentFailedNotification($booking, $paymentResult['message']));
            }

            DB::commit();

            Log::info('Payment processed successfully', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'amount' => $providedAmount
            ]);

            return [
                'success' => true,
                'payment' => $payment,
                'message' => 'Payment processed successfully'
            ];

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Payment processing failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'payment_data' => $paymentData
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Simulate external payment gateway processing
     */
    private function simulatePaymentGateway(array $paymentData): array
    {
        // Simulate processing delay
        usleep(500000); // 0.5 seconds

        // Mock different payment scenarios
        $amount = $paymentData['amount'];
        $paymentMethod = $paymentData['payment_method'] ?? 'credit_card';

        // Simulate failure for amounts ending in .13 (unlucky number for testing)
        if (fmod($amount * 100, 100) == 13) {
            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Payment gateway declined the transaction'
            ];
        }

        // Simulate refund status for amounts ending in .99
        if (fmod($amount * 100, 100) == 99) {
            return [
                'success' => true,
                'status' => 'refunded',
                'message' => 'Payment processed but refunded due to policy'
            ];
        }

        // Default success case
        return [
            'success' => true,
            'status' => 'success',
            'message' => 'Payment processed successfully via ' . $paymentMethod,
            'transaction_id' => 'tx_' . uniqid(),
            'gateway_response' => [
                'reference' => 'ref_' . time(),
                'auth_code' => strtoupper(substr(md5(uniqid()), 0, 8))
            ]
        ];
    }

    /**
     * Process refund for a payment
     */
    public function processRefund(Payment $payment, string $reason = 'User requested'): array
    {
        try {
            if ($payment->status === 'refunded') {
                throw new Exception('Payment has already been refunded');
            }

            if ($payment->status !== 'success') {
                throw new Exception('Only successful payments can be refunded');
            }

            // Simulate refund processing
            $refundResult = $this->simulateRefundGateway($payment);

            if ($refundResult['success']) {
                $payment->update(['status' => 'refunded']);
                $payment->booking->update(['status' => 'cancelled']);
            }

            Log::info('Refund processed', [
                'payment_id' => $payment->id,
                'reason' => $reason,
                'result' => $refundResult
            ]);

            return $refundResult;

        } catch (Exception $e) {
            Log::error('Refund processing failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Simulate refund gateway processing
     */
    private function simulateRefundGateway(Payment $payment): array
    {
        // Simulate processing delay
        usleep(300000); // 0.3 seconds

        // Mock refund success (90% success rate)
        $success = rand(1, 10) <= 9;

        if ($success) {
            return [
                'success' => true,
                'message' => 'Refund processed successfully',
                'refund_id' => 'rf_' . uniqid(),
                'refunded_amount' => $payment->amount
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Refund failed - unable to process with payment gateway'
            ];
        }
    }

    /**
     * Get payment statistics for reporting
     */
    public function getPaymentStats(array $filters = []): array
    {
        $query = Payment::query();

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return [
            'total_payments' => $query->count(),
            'total_amount' => $query->sum('amount'),
            'successful_payments' => $query->where('status', 'success')->count(),
            'failed_payments' => $query->where('status', 'failed')->count(),
            'refunded_payments' => $query->where('status', 'refunded')->count(),
            'success_rate' => $query->count() > 0
                ? round(($query->where('status', 'success')->count() / $query->count()) * 100, 2)
                : 0
        ];
    }
}
