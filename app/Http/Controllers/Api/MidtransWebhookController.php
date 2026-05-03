<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\MidtransService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    public function __construct(
        protected MidtransService $midtransService,
        protected PaymentService $paymentService
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $payload = (array) $request->all();

        if (! $this->midtransService->verifyNotificationSignature($payload)) {
            return response()->json([
                'message' => 'Invalid Midtrans signature.',
            ], 403);
        }

        $orderId = trim((string) ($payload['order_id'] ?? ''));
        if ($orderId === '') {
            return response()->json([
                'message' => 'Order ID tidak ditemukan.',
            ], 400);
        }

        $payment = Payment::query()
            ->where('midtrans_order_id', $orderId)
            ->orWhere('transaction_code', $orderId)
            ->latest()
            ->first();

        if (! $payment) {
            return response()->json([
                'message' => 'Payment tidak ditemukan untuk order_id terkait.',
            ], 404);
        }

        $this->paymentService->syncFromMidtransWebhook($payment, $payload);

        return response()->json([
            'message' => 'Notification accepted.',
        ]);
    }
}

