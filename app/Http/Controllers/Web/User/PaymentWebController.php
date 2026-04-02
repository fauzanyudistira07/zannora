<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentWebController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {
    }

    public function create(Request $request): View|RedirectResponse
    {
        $bookingId = $request->integer('booking');

        $booking = Booking::query()
            ->with(['flight.airline', 'flight.departureAirport', 'flight.arrivalAirport', 'payments'])
            ->where('user_id', $request->user()->id)
            ->find($bookingId);

        if (! $booking) {
            return redirect()
                ->route('my-bookings.index')
                ->with('status', 'Booking tidak ditemukan.');
        }

        return view('user.payments.create', [
            'booking' => $booking,
            'latestPayment' => $booking->payments->sortByDesc('created_at')->first(),
        ]);
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $booking = Booking::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($request->integer('booking_id'));

        $payment = $this->paymentService->createPayment($booking, $request->validated());

        return redirect()
            ->route('my-bookings.show', $booking)
            ->with('status', 'Pembayaran berhasil dikirim dengan status '.$payment->payment_status.'.');
    }

    public function show(Request $request, Payment $payment): View
    {
        $payment->load('booking.flight');
        abort_unless($payment->booking->user_id === $request->user()->id, 403);

        return view('user.payments.show', [
            'payment' => $payment,
        ]);
    }
}

