<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(
        protected TicketService $ticketService
    ) {
    }

    public function createPayment(Booking $booking, array $data): Payment
    {
        if (! in_array($booking->status, ['pending', 'confirmed'], true)) {
            throw ValidationException::withMessages([
                'booking_id' => ['Booking tidak dapat diproses untuk pembayaran.'],
            ]);
        }

        return DB::transaction(function () use ($booking, $data) {
            $payment = $booking->payments()
                ->where('payment_status', 'pending')
                ->latest()
                ->first();

            if (! $payment) {
                $payment = new Payment(['booking_id' => $booking->id]);
            }

            $payment->fill([
                'payment_method' => $data['payment_method'],
                'amount' => $booking->total_price,
                'payment_status' => 'pending',
            ]);

            if (! empty($data['proof_file'])) {
                $payment->proof_file = $data['proof_file']->store('payments', 'public');
            }

            $payment->save();

            return $payment->load('booking');
        });
    }

    public function verifyPayment(Payment $payment, array $data): Payment
    {
        return DB::transaction(function () use ($payment, $data) {
            $payment->update([
                'payment_status' => $data['payment_status'],
                'transaction_code' => $data['transaction_code'] ?? $payment->transaction_code,
                'paid_at' => $data['payment_status'] === 'paid' ? now() : null,
            ]);

            $booking = $payment->booking()->with('details.ticket')->firstOrFail();

            if ($payment->payment_status === 'paid') {
                $booking->update(['status' => 'confirmed']);
                $this->ticketService->issueForBooking($booking);
            }

            if ($payment->payment_status === 'refunded') {
                $booking->update(['status' => 'cancelled']);
            }

            return $payment->fresh('booking');
        });
    }
}
