<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\MidtransService;
use App\Services\PaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentWebController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected MidtransService $midtransService
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

        if ($payment->payment_method === 'midtrans_snap') {
            if (! $payment->midtrans_redirect_url) {
                return redirect()
                    ->route('payments.show', $payment)
                    ->with('status', 'Gagal membuat sesi Midtrans. Coba ulang beberapa saat lagi.')
                    ->with('status_type', 'error');
            }

            return redirect()->away($payment->midtrans_redirect_url);
        }

        return redirect()
            ->route('payments.show', $payment)
            ->with('status', 'Pembayaran berhasil dikirim dengan status '.$payment->payment_status.'.')
            ->with('status_type', 'success');
    }

    public function show(Request $request, Payment $payment): View
    {
        $payment->load('booking.flight');
        abort_unless($payment->booking->user_id === $request->user()->id, 403);

        if ($payment->payment_method === 'midtrans_snap' && $payment->payment_status === 'pending') {
            try {
                $payment = $this->paymentService->syncFromMidtransGateway($payment, 6, 500);
                $payment->load('booking.flight');
            } catch (\Throwable) {
                // Keep current state if Midtrans status check fails.
            }
        }

        return view('user.payments.show', [
            'payment' => $payment,
            'midtransUrl' => $payment->payment_method === 'midtrans_snap'
                && $payment->payment_status === 'pending'
                && filled($payment->midtrans_redirect_url)
                ? $payment->midtrans_redirect_url
                : null,
        ]);
    }

    public function sync(Request $request, Payment $payment): JsonResponse
    {
        $payment->load('booking');
        abort_unless($payment->booking->user_id === $request->user()->id, 403);

        if ($payment->payment_method === 'midtrans_snap' && $payment->payment_status === 'pending') {
            try {
                $payment = $this->paymentService->syncFromMidtransGateway($payment, 8, 500);
                $payment->load('booking');
            } catch (\Throwable) {
                // Keep current state if status sync still fails.
            }
        }

        return response()->json([
            'payment_status' => $payment->payment_status,
            'booking_status' => $payment->booking->status,
            'midtrans_status_code' => $payment->midtrans_status_code,
        ]);
    }

    public function midtransFinish(Request $request): RedirectResponse
    {
        $orderId = trim((string) $request->query('order_id'));

        if ($orderId === '') {
            return redirect()
                ->route('my-bookings.index')
                ->with('status', 'Kembali dari Midtrans. Status pembayaran akan diperbarui otomatis.')
                ->with('status_type', 'info');
        }

        $payment = Payment::query()
            ->with('booking')
            ->where('midtrans_order_id', $orderId)
            ->orWhere('transaction_code', $orderId)
            ->latest()
            ->first();

        if (! $payment || $payment->booking->user_id !== $request->user()?->id) {
            return redirect()
                ->route('my-bookings.index')
                ->with('status', 'Kembali dari Midtrans. Status pembayaran akan diperbarui otomatis.')
                ->with('status_type', 'info');
        }

        if ($payment->payment_method === 'midtrans_snap' && $payment->payment_status === 'pending') {
            try {
                $payment = $this->paymentService->syncFromMidtransGateway($payment, 20, 1000);
            } catch (\Throwable) {
                // Keep normal redirect flow even if status sync fails.
            }
        }

        if ($payment->payment_method === 'midtrans_snap' && $payment->payment_status === 'pending') {
            $resultData = $request->query('result_data');
            $resultPayload = [];
            if (is_string($resultData) && $resultData !== '') {
                $decoded = json_decode($resultData, true);
                if (is_array($decoded)) {
                    $resultPayload = $decoded;
                }
            }

            $statusCode = (string) ($request->query('status_code', $resultPayload['status_code'] ?? ''));
            $transactionStatus = (string) ($request->query('transaction_status', $resultPayload['transaction_status'] ?? ''));

            if ($transactionStatus === '' && $statusCode === '200') {
                $transactionStatus = 'settlement';
            }

            $fallbackPayload = [
                'order_id' => $orderId,
                'status_code' => $statusCode,
                'transaction_status' => $transactionStatus,
                'fraud_status' => (string) $request->query('fraud_status', $resultPayload['fraud_status'] ?? ''),
                'transaction_id' => (string) $request->query('transaction_id', $resultPayload['transaction_id'] ?? ''),
                'payment_type' => (string) $request->query('payment_type', $resultPayload['payment_type'] ?? ''),
            ];

            $mappedStatus = $this->midtransService->mapMidtransStatusToPaymentStatus($fallbackPayload);
            if ($mappedStatus === 'paid') {
                $payment = $this->paymentService->syncFromMidtransWebhook($payment, $fallbackPayload);
            }
        }

        $statusMessage = $payment->payment_status === 'paid'
            ? 'Pembayaran Midtrans sudah sukses dan dikonfirmasi otomatis.'
            : 'Kembali dari Midtrans. Status pembayaran akan diperbarui otomatis.';
        $statusType = $payment->payment_status === 'paid' ? 'success' : 'info';

        return redirect()
            ->route('payments.show', $payment)
            ->with('status', $statusMessage)
            ->with('status_type', $statusType);
    }
}
